<?
function escape_for_php($instr){
	$instr = addslashes($instr);
	$instr = str_replace("$", "\\$", $instr);
	return $instr;
}
function proccess_form(){
	$authstring = "<# NOT SET!!! #>";
	if( isset($_POST["username"]) && isset ($_POST["password"]) ){
		$authstring = $_POST["username"].":".$_POST["password"];
		$authstring = hash("sha256", base64_encode($authstring));

	}

	$prefix = "<# NOT SET!!! #>";
	if (isset($_POST["prefix"]) ){
		$prefix = escape_for_php($_POST["prefix"]);
	}

	$ddir = "<# NOT SET!!! #>";
	if (isset($_POST["ddir"]) ){	
		$ddir = escape_for_php($_POST["ddir"]);
	}

	$qmark = "?";
	$ltmark = "&lt;";
	$gtmark = "&gt;";

	$cfg_text_out = <<<EOTc3BlZWQ
$ltmark$qmark
	class FtreeMakerCfg
	{
		//Regarding Paths
		// if \$LIST_DIR == "/xyz/foo", \$PREFIX_URL == "bar://foo",
		// and  (REAL FILE PATH)   == "/xyz/foo/some_folder/some_file.abc"
		// then (FINAL OUTPUT URL) == "bar://foo/some_folder/some_file.abc"


		//Directory To Start From ( NO TRAILING SLASH! )
		public static \$LIST_DIR = "$ddir";
		// URL to prefix file urls with. ( NO TRAILING SLASH! )
		public static \$PREFIX_URL = "$prefix";

		
		//Authentication 
		public static \$AUTH_CLIENT_STRING = "$authstring";
	}
$qmark$gtmark
EOTc3BlZWQ;

	return $cfg_text_out;
}
?>


<!doctype html>
<html>
<head>

<link rel="stylesheet" href="./ftree_style.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.1/jquery.js" type="text/javascript"></script>
</head>
<body>
<div id="navmenu" class="wrap">
	<ul>
 		<li><h1> Setup Helper</h1></li>
	</ul>
</div>
<div class="wrap info">
<?php if (isset($_REQUEST["required"])): ?>
<p>You've been redirect here because... </p>
<? endif; ?>
<p>You must first create a "config.php" file.</p>
<p>This form will help you to create it.</p>
</div>
<div id="main" class="wrap">
<form id="setup-form" action="setup.php" method="post" accept-charset="utf-8">
	<label>
	Username:
	<input type="text" name="username" value="<?if (isset($_POST["username"]) ){echo $_POST["username"];}?>" placeholder="username">
	</label>
	<br/>
	<label>
	Password:
	<input type="password" name="password" value="<?if (isset($_POST["password"]) ){echo $_POST["password"];}?>">
	</label>
	<br/>
	<label>
	List DIR:
	<input type="text" name="ddir" value="<?if (isset($_POST["ddir"]) ){echo $_POST["ddir"];}?>" placeholder="/home/username/downloads">
	</label>
	<br/>
	<label>
	Prefix URL:
	<input type="text" name="prefix" value="<?if (isset($_POST["prefix"]) ){echo $_POST["prefix"];}?>" placeholder="http://example.com/downloads">
	</label>
	<br/>
	<input type="submit" value="Submit">
</form>

<?php if (isset($_POST["username"])):?>

	<p class="info-config-text">
	Copy the following into a new file named "config.php" include in the same directory as _THIS_ file "setup.php":
	<p>
	<pre class="config-text"><? echo proccess_form(); ?></pre>

<? endif; ?>
</div>
</body>
</html>