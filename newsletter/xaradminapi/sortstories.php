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
 * Sort stories by category and date (newest to oldest) within a category
 *
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['stories'] array of stories
 * @return array
 * @returns array of sorted topics 
 */
function newsletter_adminapi_sortstories ($args) 
{
    // Get arguments
    extract($args);

    if (!isset($stories) || !is_array($stories)) {
        return;  // throw back
    }

    $topics = array();

    // Loop through the stories, retrieving each story
    foreach ($stories as $story) {
        if ($story['cid'] == 0) {
            $storyLeftCID = $story['cid'];
        } else {
            // Get category information
            $category = xarModAPIFunc('categories',
                                      'user',
                                      'getcatinfo',
                                      array('cid' => $story['cid']));

            $storyLeftCID = $category['left'];
        }

        // Add story info to topics array
        $storyDate = $story['storyDate'];
        $topics[] = array('leftcid' => $storyLeftCID,
                          'storyId' => $story['id'],
                          'cid' => $story['cid'],
                          'storyDate' => $storyDate['timestamp']);
    }    

    // Now the fun begins.  First sort by category.  Then sort
    // each story in a category by the story date.
    usort($topics, "newsletter_adminapi__compare");

    reset($topics);

    return $topics;
}


// PRIVATE


/**
 * Comparision function for sorting by values
 *
 * @private
 * @author Richard Cave
 * @param a multi-dimensional array
 * @param b multi-dimensional array
 * @return array
 * @returns sorted array 
 */
function newsletter_adminapi__compare($a, $b) 
{
    return newsletter_adminapi__comparerecords(0, $a, $b);
}

/**
 * Comparision function for sorting by 'leftcid' and 'storyDate'
 *
 * @private
 * @author Richard Cave
 * @param i index withing array
 * @param a multi-dimensional array
 * @param b multi-dimensional array
 * @return array
 * @returns sorted array 
 */
function newsletter_adminapi__comparerecords($i, $a, $b) 
{
    $sortArr = array('["leftcid"]', '["storyDate"]');

    if ($i == sizeof($sortArr))
        return 0;

    $avalue = '$avalue = $a'.$sortArr[$i].';';
    $bvalue = '$bvalue = $b'.$sortArr[$i].';';

    eval($avalue);
    eval($bvalue);

    if($avalue == $bvalue) {
        return newsletter_adminapi__comparerecords($i+1, $a, $b);
    } else {
        if ($i == 0) {
            // If comparing "leftcid" sort lowest to highest
            return ($avalue > $bvalue) ? (1) : (-1);
        } else {
            // If comparing "storyDate" sort highest to lowest
            return ($avalue < $bvalue) ? (1) : (-1);
        }
    }
}


?>
