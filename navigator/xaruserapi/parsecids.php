<?php
/*
 * File: $Id: $
 *
 * CHSF Content Navigation Block
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2004 by the Schwab Foundation
 * @link http://wwwk.schwabfoundation.org
 *
 * @subpackage navigator module
 * @author Richard Cave <caveman : rcave@xaraya.com>
*/

/**
 * Parse an article category ids by primary group
 * and secondary group.  This function assumes that
 * there are only 2 cids present.
 *
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['cids'] cids of an article
 * @returns array, or false on failure
 * @raise BAD_DATA
 */
function navigator_userapi_parsecids($args)
{
    // Get arguments
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($cids) || !is_array($cids)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'cids', 'userapi', 'parsecids', 'navigator');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_DATA', new SystemException($msg));
        return;
    }

    // Check if cids is invalid
    if (empty($cids))
        return;

    // Initialize variables
    $primary_cid = 0;
    $secondary_cid = 0;

    // See if we have two cids
    if (count($cids) == 1) {
        // Add dummy cid
        $cids[] = $secondary_cid;
    }

    // Get default parents
    $primary_list = @unserialize(xarModGetVar('navigator', 'categories.list.primary'));

    if (!is_array($primary_list)) {
        $primary_list = array();
    }

    // Check if default parent list is empty
    if (empty($primary_list)) {
        // Assume that the first cid is the primary group and the
        // second cid is the secondary group
        $primary_cid = $cids[0];
        $secondary_cid = $cids[1];
    } else {
        // Assuming only two category ids are passed to block
        if (array_key_exists($cids[0], $primary_list)) {
            // Set the primary group and secondary group
            $primary_cid = $cids[0];
            $secondary_cid = $cids[1];
        } elseif (array_key_exists($cids[1], $primary_list)) {
            // Set the primary group and secondary group
            $primary_cid = $cids[1];
            $secondary_cid = $cids[0];
        } else {
            // Assume that the first cid is the primary group and the
            // second cid is the secondary group
            $primary_cid = $cids[0];
            $secondary_cid = $cids[1];
        }
    }

    // Return array of ($primary_cid, $secondary_cid)
    return array($primary_cid, $secondary_cid);
}

?>
