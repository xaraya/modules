<?php
/**
 * Surveys table definitions function
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Surveys
 * @author Surveys module development team
 */
/*
 * Short Description [REQUIRED one line description]
 *
 * Long Description [OPTIONAL one or more lines]
 *
 * @author     Jason Judge <jason.judge@academe.co.uk>
 * @author     Another Author <another@example.com>          [REQURIED]
 * @param string $arg1  the string used                      [OPTIONAL A REQURIED]
 * @param int    $arg2  an integer and use description
 *                      Identing long comments               [OPTIONAL A REQURIED]
 *
 * @return int  type and name returned                       [OPTIONAL A REQURIED]
 *
 * @throws      exceptionclass  [description]                [OPTIONAL A REQURIED]
 *
 * @access      public                                       [OPTIONAL A REQURIED]
 * @static                                                   [OPTIONAL]
 * @link       link to a reference                           [OPTIONAL]
 * @see        anothersample(), someotherlinke [reference to other function, class] [OPTIONAL]
 * @since      [Date of first inclusion long date format ]   [REQURIED]
 * @deprecated Deprecated [release version here]             [AS REQUIRED]
 */
/*
 * Get the complete structure for a user survey map.
 * This returns the structure used to display a complete
 * map of a survey instance for a specific user.
 *
 */

function surveys_userapi_getsurveymap($args) {
    // Expand the arguments.
    extract($args);

    // Get the user survey details.
    // Accepts combinations of $usid, uid, $name (survey name), $sid
    $usersurvey = xarModAPIfunc('surveys', 'user', 'getusersurvey', $args);
    if (empty($usersurvey)) {return;}

    // Get the survey details.
    $survey = xarModAPIfunc(
        'surveys', 'user', 'getsurvey',
        array('sid' => $usersurvey['sid'])
    );

    $lang_suffix = xarModAPIfunc('surveys', 'user', 'getlanguagesuffix');

    // Get the full list of groups for the survey.
    // Additional details will be hung off this list.
    // TODO: join to the groups within this API function
    // so that there are no gaps.
    $map = xarModAPIfunc(
        'surveys', 'user', 'getgroups',
        array('gid' => $survey['gid'], 'lang_suffix' => $lang_suffix)
    );
    if (empty($map)) {return;}
    $mapitems =& $map['items'];

    // Get the group statuses within this user survey,
    // i.e. the completion status for each user group.
    $usergroups = xarModAPIfunc(
        'surveys', 'user', 'getusergroups',
        array('usid' => $usersurvey['usid'])
    );

    // Add the completion statuses to the groups tree.
    foreach($map['items'] as $gid => $group) {
        if (isset($usergroups[$gid])) {
            $status = $usergroups[$gid]['status'];
            $map['items'][$gid]['status'] = $status;
        } else {
            // Default status where the group has not been visited.
            $map['items'][$gid]['status'] = 'NORESPONSE';
        }
    }

    // Get counts of questions for each group in this user survey.
    $counts = xarModAPIfunc(
        'surveys', 'user', 'countuserquestions',
        array('usid' => $usersurvey['usid'])
    );
    if (empty($counts)) {return;}

    // Now we have counts of questions at each group node, we can prune the tree
    // with respect to empty tree nodes.
    // Note: we prune the tree using the status; we do not remove any items. At render
    // time the admin may wish to see all nodes, whether they are enabled or not.
    foreach($counts as $countgroupid => $count) {
        // Save the total count in the tree for use when rendering.
        // Example usage when rendering: for any group node, if count < count_desc, then
        // there are questions in descendant groups.
        $count_this = $count['response'] + $count['noresponse'];
        $count_desc = $count['response_desc'] + $count['noresponse_desc'];

        $mapitems[$countgroupid]['count'] = $count_this;
        $mapitems[$countgroupid]['count_desc'] = $count_desc;
        $mapitems[$countgroupid]['count_response'] = $count['response'];
        $mapitems[$countgroupid]['count_noresponse'] = $count['noresponse'];
        $mapitems[$countgroupid]['count_response_desc'] = $count['response_desc'];
        $mapitems[$countgroupid]['count_noresponse_desc'] = $count['noresponse_desc'];

        if ($count['response_desc'] == 0 && $count['noresponse_desc'] == 0) {
            // No questions at all for this group node and its descendants.
            // Set the group to 'Not Applicable'.
            $mapitems[$countgroupid]['status'] = 'NA';

            // Remove the entry from the children list too.
            // I have a hunch we will need to check isset here before we attempt
            // to unset it - but it seems to be holding up. Perhaps just an
            // error suppression (@) prefix would be sufficient.
            unset($map['children'][$mapitems[$countgroupid]['parent']][$countgroupid]);
        }

        // Set groups with no questions of their own, but
        // with questions in their descendants, to 'COMPLETE' This would
        // allow nodes to hold descendant node questions, but without
        // having to be visited themselves.
        if ($mapitems[$countgroupid]['status'] != 'COMPLETE' && $count_this == 0 && $count_desc > 0) {
            $mapitems[$countgroupid]['status'] = 'COMPLETE';
        }
    }

    // Now create a doubly-linked list, by adding a 'next' and 'prev' gid link
    // to each active node.
    // While doing this, count up the different statuses for the whole survey.
    // Note: these counts are for the groups, not the questions, but will make
    // a good progress bar nonetheless.
    $group_counts = array(
        'NA' => 0,
        'INVALID' => 0,
        'COMPLETE' => 0,
        'NORESPONSE' => 0,
        'ACTIVE' => 0
    );

    // Loop for each item.
    $first_gid = 0;
    $last_gid = 0;
    foreach($mapitems as $gid => $mapitem) {
        // Increment the count for the relevant group.
        $group_counts[$mapitem['status']] += 1;

        if ($mapitem['status'] != 'NA') {
            // Increment active count.
            $group_counts['ACTIVE'] += 1;
        }

        if ($mapitem['status'] != 'NA' && $mapitem['count'] > 0) {
            // Set defaults.
            $mapitems[$gid]['next'] = 0;
            $mapitems[$gid]['prev'] = 0;

            // If we have not started yet, and this is an active group,
            // then set it as the first.
            if (empty($first_gid)) {
                $first_gid = $gid;
            }

            // If we are not the first then set forward and backward links.
            if (!empty($last_gid)) {
                // Set the last group to point to this one (forward link).
                $mapitems[$last_gid]['next'] = $gid;

                // Set this item to point back to the previous.
                $mapitems[$gid]['prev'] = $last_gid;
            }

            // Assume this will be the last.
            // $last_gid will remain set when the 'music stops'.
            $last_gid = $gid;

            if ($mapitem['status'] != 'COMPLETE') {
                // This item is INVALID, so set a flag on all its ancestors
                // so that we know at the higher levels this node is sitting
                // there with an error.
                $crawl = $mapitems[$gid]['parent'];
                while ($crawl > 0) {
                    if (isset($mapitems[$crawl]['incomplete_desc'])) {break;}
                    $mapitems[$crawl]['incomplete_desc'] = true;
                    $crawl = $mapitems[$crawl]['parent'];
                }
            }
        }

        if ($mapitem['status'] != 'NA') {
            // Count up the children for this group - useful when rendering.
            if (empty($map['children'][$gid])) {
                $childcount = 0;
            } else {
                $childcount = count($map['children'][$gid]);
            }
            $mapitems[$gid]['childcount'] = $childcount;
            //echo " $childcount ";
        }
    }

    // Include the first and last node pointers in the map.
    $map['first'] = $first_gid;
    $map['last'] = $last_gid;

    // TODO: we would like a flags on each node to indicate whether there are
    // invalid and/or unresponded nodes within its descendant list. These need
    // to be highlighted on the map, so the user knows there are nodes that
    // need to be visited or revisited.

    // Include the group counts with the map.
    $map['group_counts'] = $group_counts;

    return $map;
}

?>