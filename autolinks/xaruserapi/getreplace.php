<?php

/**
 * get the replacement text for an autolink
 * @param $args['lid'] id of the link to fetch; or
 * @param $args['link'] link array if already fetched
 * @returns array
 * @return string: replace string; either a parsed template or a text array definition
 */
function autolinks_userapi_getreplace($args)
{
    // TODO: this will eventually select the template based on the
    // autolink type and feed DD property values into the template
    // as defined for the autolink type.

    extract($args);

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
    // TODO: add to this from the DD property values for the autolink item.

    // Fixed/standard values available to the template
    $template_data = array(
        'match' => '$1',
        'url' => $link['url'],
        'title' => $link['title'],
        'style' => $style,
        'target' => $target
    );

    // Additional values for the 'standard' template.
    $template_data['stdattributes'] = array(
        'href' => $link['url'],
        'title' => $link['title'],
        'target' => $target,
        'style' => $style
    );

    // Either execute the template now (if cachable) or return the expression used to
    // execute the template in an expression-based preg_replace.
    // Executing now will give us a simple replace string, creating the expression
    // will give us a function with array-based parameters.

    if ($link['dynamic_replace']) {
        // Dynamic templates are executed later on, when the link match is made.

        // Create the PHP expression, but don't execute the template at this stage.
        $result = xarModAPIfunc('autolinks', 'user', 'varexport', $template_data);
    } else {
        // Non-dynamic templates can be executed and cached for later use.

        // Execute the template.
        $result = xarTplModule(
            'Autolinks',
            xarModGetVar('autolinks', 'templatebase'),
            $template_name = $link['template_name'],
            $template_data
        );

        // Catch any exceptions.
        if (xarExceptionValue()) {
            $error = xarExceptionRender('text');

            // Free the exception since we have handled it.
            xarExceptionFree();

            // Do we want the error displayed in-line?
            if (xarModGetVar('autolinks', 'showerrors') || xarVarGetCached('autolinks', 'showerrors')) {
                // Pass the error through the error template.
                // This mode of operation is used during setup.
                $result = xarTplModule('Autolinks', 'error', 'match',
                    array(
                        'match' => '$1',
                        'template_base' => xarModGetVar('autolinks', 'templatebase'),
                        'template_name' => $link['template_name'],
                        'error_text' => xarVarPrepHTMLdisplay(xarExceptionRender($error))
                    )
                );
            } else {
                // Don't highlight the error - just return the matched text.
                // This is the normal mode of operation.
                $result = '$1';
            }
        }
    }

    return $result;
}

?>