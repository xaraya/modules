
/*
 * QATool v0.1.0 - jQuery form widget
 * Copyright (c) 2008 Jason Judge
 *
 * Dual licensed under the MIT and GPL licenses:
 * 	http://www.opensource.org/licenses/mit-license.php
 * 	http://www.gnu.org/licenses/gpl.html
 *
 * SUMMARY: create a tool that can be used to lead end user through
 * a series of steps to reach a conclusion. Each step is based on
 * simple yes/no or choice questions, with each answer leading to
 * a further step. A form may be used to build up data that can then
 * be submitted right at the end. The tool is created using very simple
 * HTML, so can be maintained with moderate technical knowledge.
 *
 * CHANGELOG: 
 * BUGS: see inline comment
 */

(function($) {
	$.fn.qatool = function(options){
		// TODO: need to be able to disable form submit unless there is 
		// an explicit submit button. We don't want the form submitted
		// too early by accident (e.g. by pressing 'enter' in a form field).

		// TODO: fix keyboard navigation; a 'form submit' is triggered for each
		// radio button when the user scans down a list of radio buttons.
		// This means they never get to any button other than the first
		// or second in a list, when using the keyboard. This only happens
		// when a form is used.

		// set up default options
		// TODO: support alternative easing functions
		var defaults = {
			startClass: 'start',
			fadeOutSpeed: 'fast',
			fadeInSpeed: 'medium'
		};

		$.extend(defaults, options);

		return this.each(function(){
			var list = this;
			// Three options for initial hiding. Hide all but one of these
			// (checked in order):
			// 1. The hash-ID element in the URL (#foo)
			// 2. The element with the class 'start'.
			// 3. The first (li) element in the list.
			// To prevent the list flashing up, the list items can be hidden
			// initially through CSS with the 'start' item alone being visible.

			var url_hash = window.location.hash;
			var hide = '';
			var show = '';

			if (url_hash && $(this).find('> li' + url_hash)) {
				hide = '> li:not(' + url_hash + '):visible';
				show = '> li' + url_hash + ':hidden';
			} else if (defaults.startClass && $(this).find('> li.' + defaults.startClass).length) {
				hide = '> li:not(.start):visible';
				show = '> li.start:hidden';
			} else {
				hide = '> li:not(:first):visible';
				show = '> li:first:hidden';
			}
			// Hide all the list items but one.
			$(this).find(hide).hide();
			$(this).find(show).show();

			// Now we need some triggers.
			// If a radio button or checkbox is selected, then hide the
			// current list item, and show the one the form item points to.
			// Also applies to simple anchors.
			$(this).find('input[type=radio],input[type=checkbox],a').each(function(){
				var tagName = this.tagName;
				var hash;

				if (tagName == 'A') {
					hash = $(this).attr('href');
				} else {
					hash = '#' + this.value;
				}
				if (hash != undefined && hash.length > 1 && hash.indexOf('#') == 0 && $(list).find('> li' + hash)) {

					$(this).click(function(){
						// Hide this list item and show the new one.
						// Actually, hide all list items that are not the new one.
						// Fade out the old one before fading in the new one
						$(list).find('> li:not(' + hash + '):visible').fadeOut(defaults.fadeOutSpeed,
							function(){jQuery('li' + hash).fadeIn(defaults.fadeInSpeed);}
						);
						// Override the default bahaviour of the anchor.
						if (tagName == 'A') return(false);
					});
				}
			});
		});
	};
}) (jQuery);
