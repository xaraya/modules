<?php

/**
 * $Id$
 * transform text
 * @param $args['extrainfo'] string or array of text items
 * @returns string
 * @return string or array of transformed text items
 */
function autolinks_userapi_transform($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($extrainfo)) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ', $invalid), 'userapi', 'transform', 'Autolinks');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    if (is_array($extrainfo)) {
        if (isset($extrainfo['transform']) && is_array($extrainfo['transform'])) {
            foreach ($extrainfo['transform'] as $key) {
                if (isset($extrainfo[$key])) {
                    $extrainfo[$key] = xarModAPIFunc(
                        'autolinks', 'user', '_transform',
                        array($extrainfo[$key])
                    );
                }
            }
            return $extrainfo;
        }
        $transformed = array();
        foreach($extrainfo as $text) {
            $transformed[] = xarModAPIFunc(
                'autolinks', 'user', '_transform',
                array($text)
            );
        }
    } else {
        $transformed = xarModAPIFunc(
            'autolinks', 'user', '_transform',
            array($text)
        );
    }

    return $transformed;
}

?>