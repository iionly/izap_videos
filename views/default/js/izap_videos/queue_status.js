define(function(require) {
	var elgg = require("elgg");
	var $ = require("jquery");

	$(document).ready(function() {
		var refreshId = setInterval(function() {
			$('#videoQueue').load(elgg.normalize_url('ajax/view/izap_videos/admin/getQueue'));
		}, 5000);
	});
});
