<?php
/*
 * File: $Id: $
 *
 * Newsletter 
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003-2004 by the Xaraya Development Team
 * @link http://www.xaraya.com
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
*/


/**
 * For each issue, prepend the publication title
 * 
 * @private
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['issues'] an array of issues
 * @returns array
 * @return $issues
 */
function newsletter_adminapi_addpubtitle($args)
{
    // Extract args
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($issues)) {
        $invalid[] = 'issues';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'adminapi', 'addpubtitle', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // For each issue, prepend the publication title
    for ($idx = 0; $idx < count($issues); $idx++) {
        // Get publication
        $pubItem = xarModAPIFunc('newsletter',
                                 'user',
                                 'getpublication',
                                 array('id' => $issues[$idx]['pid']));

        // Check for exceptions
        if (!isset($pubItem) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
            return; // throw back

        // Prepend publication title
        if (!empty($pubItem['title'])) {
            $issueTitle = $issues[$idx]['title'];
            $issues[$idx]['title'] = $pubItem['title'] . " " . $issueTitle;
        }
    }

    return $issues;
}

?>
