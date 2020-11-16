<?php

/**
 * This function will split the 'body' of a page into sections
 * and provide an index at the top, by default as a set of
 * tabs.
 * @todo: provide an 'all sections' printable version.
 * @todo: add a CSS/JS-based template sample
 * @todo: add support for parameters (alt field name, behaviour settings, etc.)
 */

function xarpages_funcapi_page_tabs($args)
{
    $c_body_name = 'body';
    $c_template = 'page_tabs';
    $c_section_head = '/<!-- section: (.+) -->/';

    // The 'tab' parameter gives the tab number to enable.
    xarVarFetch('tab', 'int:1:99', $tab, 1, XARVAR_NOT_REQUIRED);

    // What we want to do:
    // - If the page has 'sections' in it, then split the page into sections.
    // - Create a [tab-based] menu for the sections
    // - Display just one of the sections by removing the other sections
    // Note: to allow the choice of whether to remove the other sections, or
    // just hide them using styles, the second two stages will be done by
    // template.

    // We operate on the 'body' DD field only.
    if (empty($args['current_page']['dd'][$c_body_name])) {
        // No body field is available.
        return;
    }
    $body =& $args['current_page']['dd'][$c_body_name];

    // Each section is marked using:
    // <!-- section: {name of section} -->

    $section_count = preg_match_all($c_section_head, $body, $matches);
    if (empty($section_count)) {
        // No sections were found
        return;
    }

    // If requested tab drops off the end, then knock it back.
    if ($tab > $section_count) {
        $tab = $section_count;
    }

    // Extract the headings from the section markers.
    $section_titles = $matches[1];

    // Split the body up into an array of sections.
    $sections = preg_split($c_section_head, $body);

    // Remove the first section if there are too many.
    // We don't want anything before the first section marker
    // (or perhaps we can use it as a repeated header?)
    if (count($sections) > $section_count) {
        $header = trim(array_shift($sections));
    }

    // Now render the new body tag.
    // TODO: allow different temlates to be used.
    // Options could be set using further comments.
    $new_body = xarTplModule(
        'xarpages',
        'func',
        $c_template,
        array(
            'sections' => $sections,
            'section_count' => $section_count,
            'section_titles' => $section_titles,
            'tab' => $tab,
            'header' => $header
        )
    );

    // if the 'new_body' is not empty, then use it to replace the current body.
    $new_body = trim($new_body);
    if (!empty($new_body)) {
        $body = $new_body;
    }

    return $args;
}
