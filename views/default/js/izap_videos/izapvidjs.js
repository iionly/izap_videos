define(function(require) {
	var elgg = require("elgg");
	var $ = require('jquery');
	var hooks = require("elgg/hooks");
	require('jquery.colorbox');

	function boxstyle() {
		var opts = {};
		var defaults = elgg.data.lightbox;
		if (!defaults.reposition) {
			// don't move colorbox on small viewports https://github.com/Elgg/Elgg/issues/5312
			defaults.reposition = $(window).height() > 600;
		}
		var settings = $.extend({}, defaults, opts);
		var values = hooks.trigger('getOptions', 'ui.lightbox', null, settings);
		$(".izapvid-river-lightbox").colorbox(values);
	}

	function init() {
		if ($(".izapvid-river-lightbox").length) {
			$(".izapvid-river-lightbox").colorbox({
				width:'640px',
				maxWidth:'95%',
				maxHeight:'95%',
				onOpen: boxstyle(),
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
