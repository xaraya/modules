<?php

/**
 * Display a magazine from the mag module within a html xarpage.
 *
 * Note: one thing to watch out for: ensure any module stylesheets called up
 * within the mag module have the module="mag" attribute set, otherwise the
 * core will be looking for the stylesheets in the xarpages module. The same
 * goes for included templates.
 *
 * @todo Make this more generic, so a xarpages page type can call up the main page
 * for any generic module, using standard rules. The rules need standardising, naturally.
 *
 */

function xarpages_funcapi_mag($args)
{
    // Allow the magazine reference to be locked in by creating a 'mid'
    $mid = 0;

    // (magazine ID) DD property.
    if (!empty($args['current_page']['dd']['mid'])) {
        $mid = $args['current_page']['dd']['mid'];
    } elseif(!empty($args['current_page']['dd']['body']) && is_numeric($args['current_page']['dd']['body'])) {
        // If just using a standard 'html' page type, then the mag ID can be put into the body.
        $mid = (int)$args['current_page']['dd']['body'];
    }

    // Fall-back message, in case the default html template is used by accident.
    $args['current_page']['dd']['body'] = xarML('Use the "html-external" page type with this function.');

    // Call up the main mag module function.
    // Check the module is installed first.
    if (xarModIsAvailable('mag')) {
        $args['content'] = xarModfunc('mag', 'user', 'main', array('pid' => $args['pid'], 'mid' => $mid));
    } else {
        $args['content'] = xarML('Module "mag" is not installed or available.');
    }

    return $args;
}

?>