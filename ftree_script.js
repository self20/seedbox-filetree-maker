$(function() {
	$('#dir_tree').jstree({
		'core': {
			'dblclick_toggle': false,
			'data': window.tree_json,
			// 'data': [
			// 		'Simple root node', {
			// 			'text': 'Root node 2',
			// 			'state': {
			// 				'opened': true,
			// 				'selected': true
			// 			},
			// 			'children': [{
			// 					'text': 'Child 1'
			// 				},
			// 				'Child 2'
			// 			]
			// 		}
			// 	]
		},
		"checkbox": {
            "keep_selected_style": false
        },
        "plugins": ["checkbox"]
	});
	$('#dir_tree').on("changed.jstree", function(e, data) {
		console.log(data.selected);
	});

	//example interaction
	$('button').on('click', function() {
		$('#dir_tree').jstree(true).select_node('child_node_1');
		$('#dir_tree').jstree('select_node', 'child_node_1');
		$.jstree.reference('#dir_tree').select_node('child_node_1');
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