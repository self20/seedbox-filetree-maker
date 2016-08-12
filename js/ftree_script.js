$(function() {
	var main_selection = [];

	$('#dir_tree').jstree({
		'core': {
			'dblclick_toggle': false,
			'data': window.ftreemaker_tree_json,
		},
		"checkbox": {
			"keep_selected_style": false
		},
		"plugins": ["checkbox"]
	});
	$('#dir_tree').on("changed.jstree", function(e, data) {
		console.log(data.selected);
		main_selection = data.selected;
	});

	$('#show_content').click(function show_urls(){
		out_urls = [];
		for( var i = 0; i < main_selection.length; i ++){
			var fname = main_selection[i];
			if (fname.substr(-1) != "/"){
				out_urls.push( make_ouput_url( fname ) );
			}
		}
		$("#output_text pre").text(out_urls.join('\n'));
		$("#output_text").show();
	});
	$("button#hide_output").click(function(){
		$("#output_text").hide();
	});
	
	$(window).resize(function (){
		resize_tree_container();
	});
	resize_tree_container();

	function resize_tree_container(){
		var treewrap = $("#treemain");
		// treewrap.css("max-height", document.documentElement.clientHeight + "px");
		treewrap.css("background-color", '#'+Math.floor(Math.random()*16777215).toString(16));
	}

	
	function make_ouput_url(fname){
		return window.ftreemaker_url_prefix + fname;
	}

	
});