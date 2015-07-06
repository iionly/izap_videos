define(function(require) {
	var elgg = require("elgg");
	var $ = require("jquery");

	$(document).ready(function() {
		$('#video_form').submit(function() {
			$('#submit_button').hide();
			$('#progress_button').show();
		});
	});
});
