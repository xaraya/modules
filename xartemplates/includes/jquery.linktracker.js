/**
 * JQuery Linktracker
 * Inspired by http://www.glennjones.net/Post/811/AjaxLinkTracker22Thedownload.htm
 * Written for jQuery: Phill Brown (phill@consil.co.uk)
 */

(function(jQuery) {
    // Initialise the page by preparing the links on the page.
    jQuery.fn.linktracker = function(options) {
        // Prepare each anchor on the page to be tracked.
        jQuery("a").each(function(i) {
            var $this = jQuery(this);

            // Add an id if there is not already one.
            if (!$this.attr('id')) {
                $this.attr({id: "link_"+i});
            }

            // Set the handler
            $this.click(function(e) {
                jQuery.fn.linktracker.addClick(this, e);
            });
        });
    }

    // Log a click.
    jQuery.fn.linktracker.addClick = function(el, ev) {
        var tag;
        var page, linkid, target, label;
        var ajaxLink;

        // Set defaults
        label = jQuery(el).text();
        target = el.href;
        linkid = el.id;
        page = document.location.href;

        // Image link
        jQuery(el).children("img").each(function() {
            if (this.alt) {
                label = this.alt;
            } else {
                label = 'image';
            }
        });

        // The default tracker script.
        // TODO: pass this in as an option.
        ajaxLink = "modules/jquery/xarincludes/addclick.php";
        jQuery.get(ajaxLink, {linkid: linkid, target: target, label: label})
    }
})(jQuery);
