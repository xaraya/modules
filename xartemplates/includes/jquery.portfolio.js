
/**
 * Portfolio plugin.
 * Based on the article and code by Trevor Davis here:
 * http://net.tutsplus.com/tutorials/javascript-ajax/creating-a-filterable-portfolio-with-jquery/
 * This plugin extends the idea a bit by allowing a more flexible approach to tags and classes.
 * Author: Jason Judge
 * Web: http://www.consil.co.uk/blog
 * Version: 0.5.1
 * Last update: 2001-05-21 judgej
 * Dependancies: jQuery 1.3.2
 * TODO: support animations for showing and hiding portfolio items.
 * TODO: accept a jQuery selector and use that for context, allowing the same class selectors to be used
 * several times on a page without interferring.
 */

(function($) {
    //
    // plugin definition
    //
    $.fn.portfolio = function(options) {
        // Build main options before element iteration
        var opts = $.extend({}, $.fn.portfolio.defaults, options);

        $(opts.filterSelector).click(function() {
            $(this).css('outline', 'none');
			// The 'closest' method was introduced in jQuery 1.3.2
            $(opts.filterSelector).closest(opts.filterStyledWrapper).filter('.' + opts.filterCurrentClass).removeClass(opts.filterCurrentClass);

            $(this).closest(opts.filterStyledWrapper).addClass(opts.filterCurrentClass);
            // Use the 'href' attribute value if set, otherwise the text content after normalising it.
            // The href must be set as a '#foo' style value.
            var filterVal = '';
            var filterValParts = this.href.split('#', 2);
            if (filterValParts.length == 2 && filterValParts[1] != '') {
                // See if this portfolio item exists. If it does, then use this as the filter class name.
                if ($(opts.itemSelector + '.' + filterValParts[1]).length) {
                    filterVal = filterValParts[1];
                }
            }

            // If we don't have a filter class name yet, then use the text content, but normalise it to a more
            // valid class name. Note if the content is complex, then this is unlikely to be useful, but if it
			// is just a few words, then it will be fine.
			// e.g. "!Yorkshire &amp; Humber!" will be converted to a class name "yorkshire-humber".
            if (filterVal == '') {
                filterVal = $(this).text().toLowerCase().replace(/(^[^a-z]+|[^a-z0-9]+$)/ig, '').replace(/[^a-z0-9]+/ig, '-');
            }

			// If the filter class still does not match, then display all the portfolio items.
            if (!$(opts.itemSelector).filter('.' + filterVal).length) {
                // Unhide the hidden items.
                $(opts.itemSelector + '.' + opts.itemHiddenClass).fadeIn(opts.fadeInSpeed).removeClass(opts.itemHiddenClass);
            } else {
                $(opts.itemSelector).each(function() {
                    if (!$(this).hasClass(filterVal)) {
                        $(this).filter(':not(.' + opts.itemHiddenClass + ')').fadeOut(opts.fadeOutSpeed).addClass(opts.itemHiddenClass);
                    } else {
                        $(this).filter('.' + opts.itemHiddenClass).fadeIn(opts.fadeInSpeed).removeClass(opts.itemHiddenClass);
                    }
                });
            }
            
            return false;
        });
    };

    //
    // Plugin defaults to support the following structure:
    //
	// The filter links (note the first link looks for, and enabled, content items with class "text-filter"
	// and the second link looks for content items with class "byurl"):
    // <ul id="filter"><li><a href="#">Text Filter</a><a href="#byurl">URL Filter</a></ul>
	//
	// The filtered content items:
    // <ul id="portfolio"><li class="text-filter">Item 1</li><li class="byurl">Item 2</li><li class="text-filter byurl">Item 3</li></ul>
    //
    // Classes are defined for:
    //
    //   ul#filter li.current (to highlight the current filter link)
    //   ul#portfolio li.hidden (to hide non-selected portflio items)
	//
	// The options allow you to use any alternate structure or classes you like.

    $.fn.portfolio.defaults = {
        // Selector for a single 'filter' link.
        filterSelector: 'ul#filter a',
        // The wrapper tag around the filter link that takes the 'current' styling.
        // Set it to the same as the filterSelector (e.g. 'a') if not using wrappers,
        // for example if not using an unordered list for the links.
        filterStyledWrapper: 'li',
        // The class applied to the current filter link.
        filterCurrentClass: 'current',
        // The class applied to portfolio items to hide them (after they have been faded out).
        itemHiddenClass: 'hidden',

        // Selector for a single portfolio item.
        itemSelector: 'ul#portfolio li',
        
        // Animation speeds.
        fadeInSpeed: 'slow',
        fadeOutSpeed: 'normal'
    };

    //
    // end of closure
    //
})(jQuery);
