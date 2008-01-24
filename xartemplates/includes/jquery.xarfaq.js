/**
 * xarfaq plugin.
 * Show and hide FAQ questions inline, using dt/dd pairs.
 */

(function(jQuery) {
	jQuery.fn.xarfaq = function(options){
		/* Hide all the answers to start with. */
		jQuery(this).find('dt + dd').hide();

		/* Open or close the answer when the question is clicked on. */

		/* Only match next element if it is a dd */
		jQuery(this).find('dt + dd').prev().click(function() {
			var answer = jQuery(this).next();
			if (answer.is(':visible')) {
				answer.slideUp();
			} else {
				answer.slideDown();
			}
		});

		/* Open or close all questions by clicking on a special 'dt.open-all-faqs' element. */

		jQuery(this).find('dt.open-all-faqs').click(function() {
			/* Check the state of the first answer, and toggle the whole lot accordingly. */
			if (jQuery(this).parent().find('dt + dd:first').is(':visible')) {
				jQuery(this).parent().find('dt + dd').hide();
			} else {
				jQuery(this).parent().find('dt + dd').show();
			}
		});

	}
})(jQuery);