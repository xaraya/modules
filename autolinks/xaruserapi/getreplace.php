<?php

/**
 * get the replacement text for an autolink
 * @param $args['lid'] id of the link to fetch; or
 * @param $args['link'] link array if already fetched
 * @returns array
 * @return array 'status': true/false; 'replace': replace string; 'error': error message
 */
function autolinks_userapi_getreplace($args)
{
    // TODO: this will eventually select the template based on the
    // autolink type and feed DD property values into the template
    // as defined for the autolink type.

    extract($args);

    // Start with the assumption the process will fail.
    $result = array('status' => false);

    // Either a lid or a pre-fetched autolink detail has been passed in.
    if (!isset($lid) && !isset($link)) {
        $msg = xarML('Invalid Parameter Count',
                    'userapi', 'getreplace', 'autolinks');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    if (isset($lid)) {
        $link = xarModAPIFunc(
            'autolinks', 'user', 'get',
            array('lid' => $lid)
        );
    }

    if (!$link) {
        if (isset($lid)) {
            $result['error'] = xarML('Link ID ('.$lid.') invalid');
        } else {
            $result['error'] = xarML('Link details not supplied');
        }
        return $result;
    }

    $template_base = 'link';
    // TODO: get this from the autolink type.
    $template_name = 'standard';

    // Check if we want special link styles
    $decoration = xarModGetVar('autolinks', 'decoration');
    if ($decoration) {
        $style = 'text-decoration: '.$decoration.';';
    } else {
        $style = '';
    }

    // Check if we want to open in a new window
    // TODO: this will be a DD property on the item
    if (xarModGetVar('autolinks', 'newwindow')) {
        $target = '_blank';
    } else {
        $target = '';
    }
    
    // Build an array of data for the template.
    // TODO: build this from the DD property values for the autolink item.
    $template_data = array(
        'match' => '$1',
        'url' => $link['url'],
        'title' => $link['title'],
        'attributes' => array(
            'href' => $link['url'],
            'title' => $link['title'],
            'target' => $target,
            'style' => $style
        )
    );

    // Execute the template.
    $result['replace'] = xarTplModule('Autolinks', $template_base, $template_name, $template_data);

    // Catch any exceptions.
    if (xarExceptionValue()) {
        $result['error'] = xarExceptionRender('text');
        xarExceptionFree();
    } else {
        // Set success status.
        $result['status'] = true;
    }

    return $result;
}

?>