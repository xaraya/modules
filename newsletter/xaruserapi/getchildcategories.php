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
 * Retrieve the categories under a parent category
 * 
 * @private
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['parentcid'] the category id of the parent
 * @param $args['numcats']  number of categories
 * @returns array
 * @return $childCategories
 */
function newsletter_userapi_getchildcategories($args)
{
    // Extract args
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($parentcid) || !is_numeric($parentcid)) {
        $invalid[] = 'parent category id';
    }
    if (!isset($numcats) || !is_numeric($numcats)) {
        $invalid[] = 'number of categories';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'userapi', 'getchildcategories', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Make sure there are categories available
    if ($numcats == 0)
        return;

    // Get only the categories directly under the master category
    $childCategories = xarModAPIFunc('categories',
                                     'user',
                                     'getchildren',
                                     Array('cid' => $parentcid,
                                           'return_itself' => false));

    if (!$childCategories) 
        return;

    // Check to see if we should display the category names
    // alphabetically or by cid
    $alphaSort = xarModGetVar('newsletter', 'categorysort');
    if ($alphaSort) {
        // Sort this array by category name
        usort( $childCategories, "gcc__sortcategorybyname" );
    }

    return $childCategories;
}

/**
 * Comparision functions for sorting by name
 *
 * @private
 * @author Richard Cave
 * @param a multi-dimensional array
 * @param b multi-dimensional array
 * @returns strcmp
 */
function gcc__sortcategorybyname ($a, $b) 
{
    $cmp1 = trim(strtolower($a['name']));
    $cmp2 = trim(strtolower($b['name']));
    return strcmp($cmp1, $cmp2);
}


?>
