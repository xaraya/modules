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
 * Append the child categories to the parent category ([parent]child)
 * 
 * @private
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['parentcid'] the category id of the parent
 * @param $args['numcats']  number of categories
 * @returns array
 * @return $childCategories
 */
function newsletter_adminapi_appendchildcategories($args)
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
                    join(', ',$invalid), 'adminapi', 'appendchildcategories', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Make sure there are categories available
    if ($numcats == 0)
        return;

    // Get parent category info
    $parentCategory = xarModAPIFunc('categories',
                                    'user',
                                    'getcatinfo',
                                    Array('cid' => $parentcid));

    if (!$parentCategory)
        return;

    // Get the categories directly under the parent category
    $childCategories = array();
    $categories = xarModAPIFunc('categories',
                                     'user',
                                     'getchildren',
                                      Array('cid' => $parentcid,
                                            'return_itself' => false));

    if (isset($categories)) {
        // Categories are stored in the array by 'cid' so create a new
        // array starting with 0
        $idx=0;
        foreach ($categories as $child) {
            $childCategories[$idx] = $child;
            $idx++;
        }

        // Loop through and append parent category name
        for($idx = 0; $idx < count($childCategories); $idx++) {
            $name = $childCategories[$idx]['name'];
            $childCategories[$idx]['name'] = "[" . $parentCategory['name'] . "] " . $name;
        }
    }

    // Check to see if we should display the category names
    // alphabetically or by cid
    $alphaSort = xarModGetVar('newsletter', 'categorysort');
    if ($alphaSort) {
        // Sort this array by category name
        usort( $childCategories, "acc__sortcategorybyname" );
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
function acc__sortcategorybyname($a, $b) 
{
    $cmp1 = trim(strtolower($a['name']));
    $cmp2 = trim(strtolower($b['name']));
    return strcmp($cmp1, $cmp2);
}

?>
