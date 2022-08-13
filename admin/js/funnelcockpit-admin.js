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

			var toggleFunnelPageInput = function () {
				funnelPageInput.attr('disabled', !funnelInput.val());
				if (funnelInput.val()) {
					var funnelId = funnelInput.find(':selected').data('funnelId');
					funnelPageInput.empty();

					$.each(funnelPages, function() {
						if (!$(this).data('funnelId') || $(this).data('funnelId') === funnelId) {
							funnelPageInput.append($(this));
						}
					});
				}
			};

      var updatePostTitle = function() {
        var postTitle = $('input[name="post_title"]');
				var title = funnelPageInput.find(':selected').data('title');
				if (!postTitle.length) {
					$('#post').append('<input type="hidden" name="post_title" value="' + title + '">');
				} else {
					postTitle.val(title);
				}
      };

			toggleFunnelPageInput();

			funnelInput.change(function(e) {
				toggleFunnelPageInput();
        updatePostTitle();
			});

			funnelPageInput.change(function(e) {
				updatePostTitle();
			});

      updatePostTitle();
		}

	});

})( jQuery );
