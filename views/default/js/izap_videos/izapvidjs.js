define(function(require) {
	var $ = require('jquery');
	require('jquery.colorbox');

	function init() {
		if ($(".izapvid-river-lightbox").length) {
			$(".izapvid-river-lightbox").colorbox({
				width:'640px',
				maxWidth:'95%',
				maxHeight:'95%',
				onComplete: function() {
					$(this).colorbox.resize();
				}
			});
			$("#cboxOverlay").css("z-index", "10100");
			$("#colorbox").css("z-index", "10101");
		}
	}

	init();
});
