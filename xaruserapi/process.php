<?php

/**
 * TODO: capture simplepie errors and pass to Xaraya error handler
 * TODO: fix the entity encoding on the links in the parser, not here
 */

if ( !function_exists('htmlspecialchars_decode') )
{
   function htmlspecialchars_decode($text)
   {
       return strtr($text, array_flip(get_html_translation_table(HTML_SPECIALCHARS)));
   }
}

function simplepie_userapi_process($args)
{
    extract($args);

    // Little trick for headlines.
    if (empty($url) && !empty($feedfile)) {
        $url = $feedfile;
    }

    if (empty($url)) {
        $msg = xarML('Missing feed URL');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Set mandatory and optional parameters for the parser.
    $rss_args = array('feed_url' => $url);
    if (isset($cache_max_minutes)) $rss_args['cache_max_minutes'] = $cache_max_minutes;

    // Set the feed up.
    $rss = xarModAPIfunc('simplepie', 'user', 'newfeed', $rss_args);

    // Set the output encoding.
    // SimplePie public methods seem to change in each version.
    if (method_exists($rss, 'output_encoding')) $rss->output_encoding(xarMLSGetCharsetFromLocale(xarMLSGetCurrentLocale()));
    if (method_exists($rss, 'set_output_encoding')) $rss->set_output_encoding(xarMLSGetCharsetFromLocale(xarMLSGetCurrentLocale()));

    // Fetch the feed.
    $rss->init();

    $data = array();

    // Channel properties.
    if (method_exists($rss, 'get_feed_title')) $data['chantitle'] = $rss->get_feed_title();
    if (method_exists($rss, 'get_title')) $data['chantitle'] = $rss->get_title();

    if (method_exists($rss, 'get_feed_description')) $data['chandesc'] = $rss->get_feed_description();
    if (method_exists($rss, 'get_description')) $data['chandesc'] = $rss->get_description();

    if (method_exists($rss, 'get_feed_link')) $data['chanlink'] = htmlspecialchars_decode($rss->get_feed_link());
    if (method_exists($rss, 'get_link')) $data['chanlink'] = htmlspecialchars_decode($rss->get_link());

    $image_url_check = $rss->get_image_url();
    if (!empty($image_url_check)) {
        $data['image'] = array();
        $data['image']['title'] = $rss->get_image_title();
        $data['image']['url'] = $rss->get_image_url();
        $data['image']['link'] = htmlspecialchars_decode($rss->get_image_link());
        $data['image']['width'] = $rss->get_image_width();
        $data['image']['height'] = $rss->get_image_height();
    }

    // TODO: allow number of items to be selected.
    $items = $rss->get_items();
    $feed_items = array();

    if (empty($items)) $items = array();

    foreach($items as $item) {
        $feed_item = array();

        $feed_item['id'] = $item->get_id();
        $feed_item['title'] = $item->get_title();
        $feed_item['link'] = htmlspecialchars_decode($item->get_link());
        //$feed_item['links'] = $item->get_links();
        $feed_item['permalink'] = $item->get_permalink();
        $feed_item['description'] = $item->get_description();
        $feed_item['date'] = $item->get_date('U');

        $feed_item['enclosure'] = $item->get_enclosure();

        // Multiple enclosures are not supported by RSS2.0
        $enclosures = $item->get_enclosures();
        if (!empty($enclosures)) {
            $feed_item['enclosures'] = array();

            foreach($enclosures as $enclosure) {
                $item_enclosure = array();

                $item_enclosure['link'] = htmlspecialchars_decode($enclosure->get_link());
                $item_enclosure['extension'] = $enclosure->get_extension();
                $item_enclosure['type'] = $enclosure->get_type();
                $item_enclosure['length'] = $enclosure->get_length();
                $item_enclosure['size'] = $enclosure->get_size();

                // Don't use the built-in embed functionality, as it
                // makes all sorts of incorrect assumptions
                //$item_enclosure['embed'] = $enclosure->embed();
                //$item_enclosure['native_embed'] =  $enclosure->native_embed();

                $feed_item['enclosures'][] = $item_enclosure;
            }
        }

        $authors = $item->get_authors();
        if (!empty($authors)) {
            $feed_item['authors'] = array();

            foreach($authors as $author) {
                $item_author = array();

                $item_author['name'] = $author->get_name();
                $item_author['link'] = htmlspecialchars_decode($author->get_link());
                $item_author['email'] = $author->get_email();
            }
        }

        $feed_item['categories'] = $item->get_categories();

        $feed_items[] = $feed_item;
    }

    $data['feedcontent'] = $feed_items;

    return $data;
}

?>
