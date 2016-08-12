<?php
/*
 ZMO
*/

if (!(include "config.php")){
	$new_url = full_url( $_SERVER ) . "/setup.php?required";
	header('Location: '. $new_url);
}

Auth_check("Main_operation");

function Auth_check ($success_callback){
	$allheaders = getallheaders();
	if (isset($allheaders['Authorization'])){

		$header_authreq = $allheaders['Authorization'];

		$ignore_len = strlen("Basic ");
		
		$code = substr($header_authreq, $ignore_len);

		$h_code = hash("sha256", $code);
		
		// find auth code
		if ($h_code === FtreeMakerCfg::$AUTH_CLIENT_STRING){
			return call_user_func($success_callback);
		}
	}
	$auth_header_string = "WWW-Authenticate: Basic realm=\"Private\"";
	header($auth_header_string);
}

function Main_operation(){
	if(array_key_exists('raw', $_REQUEST)){
		return Main_listing(true);
	}else{
		return Main_listing(false);
	}
}

function Main_listing($text_only){
	if (!is_dir(FtreeMakerCfg::$LIST_DIR)){
		echo "Failed to find\"FtreeMakerCfg::\$LIST_DIR\" [ = \"" . FtreeMakerCfg::$LIST_DIR . "\" ]";
		return;
	}
	$root_dir = FtreeMakerCfg::$LIST_DIR;
	$root_dir_len = strlen($root_dir);
	$dlist = array($root_dir);

	$tree = array();
	$l_count = 0 ;
	while (true){
		// end of loop
		if (count($dlist) < 1){
			break;
		}
		// iteration cap
		if ($l_count > 900){
			exit("TO MANY FOLDERS OR ITERATION ERROR");
			break;
		}

		$directory = array_pop($dlist);

		$scan_result = scan_dir($directory);
		$dirs = $scan_result["dirs"];
		$files = $scan_result["files"];

		// Canonical directory name, aliasing $LIST_DIR as root ("/")
		$directory_short = substr($directory, $root_dir_len);
		if ($directory_short == ""){
			$directory_short = "/";
		}

		// add new dirs to the loop var "$dlist"
		$short_dirs = array();
		foreach($dirs as $dname){
			array_push($dlist, path_join($directory, $dname));
			array_push($short_dirs, path_join($directory_short, $dname));
		}

		$tree[$directory_short] = array("dirs" => $short_dirs, "files" => $files);

		$l_count++;
	}

	if ($text_only){
		return Render_main_text_only($tree);
	}else{
		$jstree = convert_tree_to_treejs($tree);
		$tree_json = json_encode($jstree);

		$text_dump = "";//make_text_dump($tree);

		$render_data = array(
			// "text_dump" => $text_dump,
			"json" => $tree_json,
			"url_prefix" => FtreeMakerCfg::$PREFIX_URL,
			);

		return Render_main($render_data);
	}
}

function Render_main_text_only($tree){
	header("Content-type: text/plain");
	foreach($tree as $foldername => $value){
		$files = $value["files"];
		$folders = $value["dirs"];
		echo "# $foldername\n";
		foreach($files as $filename){
			$fileUrl = make_file_url(path_join($foldername, $filename));
			echo $fileUrl . "\n";
		}
	}
}



function convert_tree_to_treejs($tree){
		//convert tree to treejs format
	$jstree = array();
	foreach ($tree as $key => $value){
		// Deal with root ID naming
		if ($key == "/"){
			$parent = "#";
			$dirname = $key;
		}else{
			$parent = substr($key, 0, strrpos($key, "/"));
			$dirname = $key . "/";
			$parent = $parent . "/";
		}

		$folder_entry = array( 
						"id" => $dirname,
						"text" => $dirname,
						"parent" => $parent,
						);
		// if ($dirname == "/"){
		// 	$folder_entry["state"] = array("opened" => true);
		// }

		array_push($jstree, $folder_entry);



		$file_entries = array();
		foreach( $value['files'] as $file){
			$nfile = array(
						"id" => $dirname.$file,
						"text" => $file,
						"parent" => $dirname,
						"icon" => "jstree-file", 
						"a_attr" => make_file_url($dirname.$file),
						);
			array_push($file_entries, $nfile);
		}

		foreach( $file_entries as $file_entry){ array_push($jstree, $file_entry);}
	}
	return $jstree;
}


function scan_dir($directory){
	$found_dirs = array();
	$found_files = array();

	$scres = scandir ($directory);
	foreach ($scres as $fdname){
		$full_path = path_join($directory, $fdname);
		if (is_dir($full_path)){
			//dir
			if ($fdname != "." && $fdname != ".."){
				array_push($found_dirs, $fdname);
			}
		}else{
			//file
			array_push($found_files, $fdname);
		}
	}
	return array("dirs" => $found_dirs, "files" => $found_files);
}


function make_file_url($fname){
	return path_join(FtreeMakerCfg::$PREFIX_URL, $fname);
}


// #################################################

function path_join($p1, $p2){
	$p1last = substr($p1, -1);
	if ($p1last == "/" or $p1last == "\\"){
		// subtract last chracter
		$p1 = substr($p1, 0 , -1 );
	}
	$p2first = (string)$p2[0];
	if ($p2first == "/" or $p2first == "\\"){		
		$p2 = substr($p2, 1);
	}

	return $p1 . '/' . $p2;
}

if (!function_exists('getallheaders')) 
{ 
	function getallheaders() 
	{ 
		   $headers = ''; 
	   foreach ($_SERVER as $name => $value) 
	   { 
		   if (substr($name, 0, 5) == 'HTTP_') 
		   { 
			   $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value; 
		   } 
	   } 
	   return $headers; 
	} 
} 

function url_origin( $s, $use_forwarded_host = false )
{
    $ssl      = ( ! empty( $s['HTTPS'] ) && $s['HTTPS'] == 'on' );
    $sp       = strtolower( $s['SERVER_PROTOCOL'] );
    $protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );
    $port     = $s['SERVER_PORT'];
    $port     = ( ( ! $ssl && $port=='80' ) || ( $ssl && $port=='443' ) ) ? '' : ':'.$port;
    $host     = ( $use_forwarded_host && isset( $s['HTTP_X_FORWARDED_HOST'] ) ) ? $s['HTTP_X_FORWARDED_HOST'] : ( isset( $s['HTTP_HOST'] ) ? $s['HTTP_HOST'] : null );
    $host     = isset( $host ) ? $host : $s['SERVER_NAME'] . $port;
    return $protocol . '://' . $host;
}

function full_url( $s, $use_forwarded_host = false )
{
    return url_origin( $s, $use_forwarded_host ) . $s['REQUEST_URI'];
}



// #####################################################################
// #################### Render #########################################
// #####################################################################


function Render_main($render_data){

	$absolute_url = full_url( $_SERVER );

?>
<!doctype html>
<html>
<head>
<link rel="stylesheet" href="./js/themes/default/style.min.css" />
<link rel="stylesheet" href="./ftree_style.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.1/jquery.js" type="text/javascript"></script>
<script src="./js/jstree.min.js" type="text/javascript"></script>

</head>
<body>
<div id="navmenu">
	<ul>
 		<li> File Tree </li>
 		<li><a href="<? echo "$absolute_url?raw"; ?>"> Plain Text Vesion </a></li>
	</ul>
</div>
  <div id="treemain">
  	<div id="dir_tree">

	</div>
  </div>

<?	if (isset($render_data[text_dump])): ?>
	   <div id="text_dump">
	   <h2> Text Dump </h2>
	   <pre><?

		echo $render_data[text_dump];

	?></pre></div>
<?	endif; ?>

<?	if (isset($render_data[debug_text])): ?>
	  <div id="debug">
	  <h2> Debug </h2>
	  <pre><?

		echo $render_data[debug_text];

	?></pre></div>
<?	endif; ?>

<script>
window.tree_json = <?
	if (isset($render_data[json])):
		echo $render_data[json];
	endif;
?>;

window.url_prefix = "<?
	if (isset($render_data[url_prefix])):
		echo $render_data[url_prefix];
	endif;
?>";

</script>

<script src="./js/ftree_script.js" type="text/javascript"></script>
</body>
</html>
<?
	return;
}

?>