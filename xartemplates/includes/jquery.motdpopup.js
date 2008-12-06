/*
 * motdPopup - Pop up a modal message just once for a visitor.
 *   (http://www.consil.co.uk/dev/jquery)
 *
 * Copyright (c) 2008 Jason Judge <jason.judge@consil.co.uk>
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 *
 * Required plugins:
 *   jquery.cookie.js
 *   jqModal.js
 *
 * Recommended plugin:
 *   jquery.crc32.js
 *
 * Examples:-
 *
 * Display the html page as a pop-up on the 5th page visited:
 *
 * jQuery('#motdModal').motdPopup({jqm: {ajax: 'path-to-message-N.html'}});
 * <div id="motdModal" class="jqmWindow"></div>
 *
 * Display the message text and give it a hash key of 123. Changing the hash
 * will cause it to be displayed again:
 *
 * jQuery('#motdModal').motdPopup({hash: '123'});
 * <div id="motdModal" class="jqmWindow">Message text</div>
 *
 * Display the message text on the first page visited, leave the plugin to
 * work out its own hash. Provide a link to allow the user to bring up a
 * message again:
 *
 * jQuery('#motdModal').motdPopup({countStart: 1});
 * <div id="motdModal" class="jqmWindow">Message text</div>
 * <a href="#" class="motdModal">Display message again</a>
 * 
 * $Version: 
 *
 * todo: support multiple pop-up message streams using some kind of index.
 * todo: if the cookie gets corrupted, then find a way to detect and reset it.
 *
 */

(function($) {
	$.fn.motdPopup = function(pOpts) {

		var opts = {
			// The number of inclusive pages to display before popping up the message.
			// Setting this to 1 will pop the message up immediately (on the first page).
			// If the browser does not allow cookies to be set, then using
			// a countdown will prevent the popup from appearing on every page.
			countStart: 5,
			// Default selector for the tag that will hold the popup message.
			// Alternatively, supply a chained selector, e.g. $('#foo').motdPopup(jqm: {ajax:'bar.html'});
			selector: '#motdModal',
			// The hash value of the URL or popup text.
			// If you do not supply your own hash, an attempt will be made to calculate one.
			hash: '',
			// Extend jqModal options here.
			jqm: {},
			// Extend the cookie options here.
			cookie: {}
		};

		var jqmOpts = {
			// Do not allow the message to be dismissed by clicking on the border.
			modal: true,
			// A link of this class will manually pop up the motd.
			trigger: '.motdModal',
			// The AJAX URL.
			ajax: '',
			toTop: true
		}

		var cookieOpts = {
			// The name of the cookie to use.
			// Since browsers are limited in the number of cookies they can store for
			// each domain and path, I am always reluctant to create yet another cookie
			// on the browser.
			name: 'motdPopup',
			// Long expiry (in days) to prevent messages coming back too often.
			expires: 365,
			// Put the cookie at the root so it applies to all pages on the domain.
			path: '/'
		}

		// Extend the default options.
		opts = $.extend(opts, pOpts);
		jqmOpts = $.extend(jqmOpts, opts.jqm);
		cookieOpts = $.extend(cookieOpts, opts.cookie);

		// If either of the two required plugins are not installed, then stop.
		if (!$.isFunction($.fn.jqm) || !$.isFunction($.cookie)) return this;

		// Check whether we have already displayed this message by checking the
		// hash stored in the cookie.
		var cookie = $.cookie(cookieOpts.name) || '0|';
		var cookieParts = cookie.split('|', 3);

		// If the cookie has not split into the correct number of parts, then reset it.
		if (cookieParts.length != 2) cookieParts = new Array(0, '');

		var savedCount = cookieParts[0];
		var savedHash = cookieParts[1];

		// If the selector failed to match anything, then stop immediately.
		if (this.length == 0) return this;

		// If no selector was passed in, then use the default
		// Prevent endless loops if the default happens to be the document too.
		if (this[0].nodeName == '#document') {
			// If no default selector, or the selector did not match anything, then stop.
			if (typeof opts.selector != 'string' || opts.selector.length == 0 || opts.selector == '#document') return this;

			// Start again with the default selector.
			return $(opts.selector).motdPopup(pOpts);
		}

		// Set up the modal trigger.
		// Even if we fail to find a hash value, or the countdown has not yet
		// reached zero, the popup can still be manually triggered.
		// Only process the first element that matches the jQuery selector.
		// We are asking for trouble if we allow more than one modal popup at the same time
		// when they are triggered automatically.
		var modalObj = $(this[0]).jqm(jqmOpts);

		// Calculate the hash for the current message.
		// Is it an AJAX URL?
		if (opts.hash == '' && jqmOpts['ajax'] != '') opts.hash = jqmOpts['ajax'];
		// Is it a block in the page?
		if (opts.hash == '') {
			// If the CRC32 plugin is available, then use that, otherwise just take
			// the length of the string and a short sample from the start.
			if ($.isFunction($.crc32)) {
				opts.hash = $.crc32(modalObj[0].innerHTML);
			} else {
				var strLen = modalObj[0].innerHTML.length;
				opts.hash = strLen.toString()
					+ ':'
					+ modalObj[0].innerHTML.substring(0, (strLen > 20 ? 20 : strLen));
			}
		}

		if (opts.hash != '' && opts.hash != savedHash) {
			// We have a message that has not been displayed yet.

			// If the countdown is zero or less, then we need to start a new countdown.
			if (savedCount < 1) {savedCount = opts.countStart;}

			// Start counting down.
			savedCount -= 1;

			if (savedCount < 1) {
				// The countdown has reached zero - time to display the message.

				// Show the message.
				modalObj.jqmShow();

				// Flag that we have seen this message now by storing the URL displayed in the cookie.
				savedHash = opts.hash;
			}

			// Save the cookie if anything has changed.
			if (savedCount != cookieParts[0] || savedHash != cookieParts[1])
			{
				$.cookie(cookieOpts.name, savedCount + '|' + savedHash, cookieOpts);
			}
		}

		// Maintain the chain.
		return this;
	}
})(jQuery);
