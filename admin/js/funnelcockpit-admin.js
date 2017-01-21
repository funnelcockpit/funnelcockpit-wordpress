(function( $ ) {
	'use strict';

	$(function() {

		var funnelPageInput = $(':input[name="funnelpage_id"]');
		var funnelInput = $(':input[name="funnel_id"]');
		if (funnelPageInput.length) {
			var funnelPages = funnelPageInput.find('option').clone();

			if (!funnelInput.val()) {
				funnelPageInput.attr('disabled', true);
			}

			funnelInput.change(function(e) {
				funnelPageInput.attr('disabled', !$(this).val());
				if ($(this).val()) {
					var funnelId = $(this).find(':selected').data('funnelId');
					console.log(funnelId);
					funnelPageInput.empty();
					$.each(funnelPages, function() {
						if (!$(this).data('funnelId') || $(this).data('funnelId') == funnelId) {
							funnelPageInput.append($(this));
						}
					});
				}
			});

			funnelPageInput.change(function(e) {
				var postTitle = $('input[name="post_title"]');
				var title = $(this).find(':selected').data('title');
				if (!postTitle.length) {
					$('#post').append('<input type="hidden" name="post_title" value="' + title + '">');
				} else {
					postTitle.val(title);
				}
			});
		}

	});

})( jQuery );
