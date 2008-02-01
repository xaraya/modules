/**
 * xarfaq plugin.
 * Show and hide FAQ questions inline, using dt/dd pairs.
 */

(function(jQuery) {
	jQuery.fn.xarfaq = function(options){
		/* Hide all the answers to start with. Also wrap the contents of each dt with an anchor. */
		jQuery(this).find('dt + dd').hide().prev().wrapInner('<a href="#" />');

		/* Open or close the answer when the question is clicked on. */

		/* Only match next element if it is a dd */
		jQuery(this).find('dt + dd').prev().children().click(function() {
			var answer = jQuery(this).parent().next();
			if (answer.is(':visible')) {
				answer.slideUp();
			} else {
				answer.slideDown();
			}
			/* Ensure the anchor destination is not followed. */
			return false;
		});

		/* Open or close all questions by clicking on a special 'dt.open-all-faqs' element. */

		jQuery(this).find('dt.open-all-faqs').click(function() {
			/* Check the state of the first answer, and toggle the whole lot accordingly. */
			if (jQuery(this).parent().find('dt + dd:first').is(':visible')) {
				jQuery(this).parent().find('dt + dd').hide();
			} else {
				jQuery(this).parent().find('dt + dd').show();
			}
			/* Ensure the anchor destination is not followed. */
			return false;
		});

	}
})(jQuery);