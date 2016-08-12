$(function() {
	$('#dir_tree').jstree({
		'core': {
			'dblclick_toggle': false,
			'data': window.tree_json,
		},
		"checkbox": {
            "keep_selected_style": false
        },
        "plugins": ["checkbox"]
	});
	$('#dir_tree').on("changed.jstree", function(e, data) {
		console.log(data.selected);
	});


	// style tree box
	
	$(window).resize(function (){
		resize_tree_container();
	});
	resize_tree_container();

	function resize_tree_container(){
		// var el = $("#treemain")[0];
		// el.style['maxHeight'] = document.documentElement.clientHeight + "px";

		var treewrap = $("#treemain");
		treewrap.css("max-height", document.documentElement.clientHeight + "px");
		treewrap.css("background-color", '#'+Math.floor(Math.random()*16777215).toString(16));
	}
});