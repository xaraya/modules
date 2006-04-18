<?php

/**
 * Main user function to display list of all existing forums
 * And existing categories
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/

/**
 * Configure forums and categories for display
 * @author John Cox
 * @author Roger Raymond
 * @author Carl Corliss (help)
 * @author Jo dalle Nogare 
 * @access  public
 * @param   startnum used for the pager
 * @param   catid when not on top level forum
 * @return  array
*/

function xarbb_user_main()
{
   // Get parameters from whatever input we need
    if (!xarVarFetch('startnum', 'id', $startnum, NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('catid', 'id', $catid, NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('read', 'isset', $read, NULL, XARVAR_DONT_SET)) return;

    // Security Check
    if (!xarSecurityCheck('ViewxarBB', 1, 'Forum')) return;

    $now = time();

    // Variable Needed for output
    $args               = array();
    $args['modid']      = xarModGetIDfromName('xarbb');
    $args['itemtype']   = 0;
    $data               = array();
    $data['pager']      = '';
    $data['uid']        = xarUserGetVar('uid');
    $data['catid']      = $catid;
    $data['now']        = $now;
    $data['items']      = array();
    $sitename           = xarModGetVar('themes', 'SiteName', 0);
    $xarbbtitle         = xarModGetVar('xarbb', 'xarbbtitle', 0);
    $data['xarbbtitle'] = isset($xarbbtitle) ? $xarbbtitle : '';

    // Login
    $data['return_url'] = xarModURL('xarbb', 'user', 'main');
    $data['submitlabel']= xarML('Submit');

    // List the categories available as well

    // Regular Categories
    if (isset($catid)) {
        $args['cid'] = $catid;
        $cats = xarModAPIfunc('categories', 'user', 'getcat', $args);

        // Security check: remove categories the user should not see
        $items = array();
        $catcount = count($cats);

        foreach($cats as $cat) {
            if (xarSecurityCheck('ViewxarBB', 0, 'Forum', $cat['cid'] . ':All')) {
                $items[] = $cat;
            }
        }

        $totalitems = count($items);

        for ($i = 0; $i < $totalitems; $i++) {
            // Assume category category array is zero-based index.
            $item = $items[$i];

            // The user API function is called
            $args['basecat'] = $item['cid'];
            $items[$i]['cbchild'] = xarModAPIfunc('categories', 'user', 'getchildren', array('cid' => $item['cid']));
            $forums = xarModAPIFunc('xarbb', 'user', 'getallforums', array('catid' => $item['cid']));

            // Security check: remove forums the user should not see
            $forumcount = count($forums);
            $items[$i]['forums'] = array();
            foreach($forums as $forum) {
                if (xarSecurityCheck('ViewxarBB', 0, 'Forum', $item['cid'] . ':' . $forum['fid'])) {
                    $items[$i]['forums'][] = $forum;

                    // This is the 'mark all forums as read' option.
                    if (isset($read)) {
                        // Reset the time last visit for the forum.
                        xarModAPIfunc('xarbb', 'admin', 'set_cookie', array('name' => 'f_' . $forum['fid'], 'value' => $now));

                        // Also set the topic tracking flags, so there are no topics within
                        // the forum marked as unread.

                        // We can simple clear all entried in the topic tracking array, since we 
                        // have set the last visited time on the forum itself.
                        $topic_tracking = array();
                        xarModAPIfunc('xarbb', 'admin', 'set_cookie', array('name' => 'topics_' . $forum['fid'], 'value' => serialize($topic_tracking)));
                    }
                }

                $args = $items[$i]['forums'];
                $items[$i]['forums'] = xarbb_user_main__getforuminfo($args);
            }
        }
    } else {
        // Base Categories
        // Get an array of assigned category details for a specific item
        $cats = xarModAPIfunc('categories', 'user', 'getallcatbases', $args);
        if (empty($cats)) {
            $cats = array();
        }

        // Security check: remove categories the user should not see
        $items = array();
        $catcount = count($cats);
        foreach($cats as $cat) {
            if (xarSecurityCheck('ViewxarBB', 0, 'Forum', $cat['cid'] . ':All')) {
                $items[] = $cat;
            }
        }

        $totalitems = count($items);
        for ($i = 0; $i < $totalitems; $i++) {
            $item = $items[$i];

            // Get an array of assigned category details for a specific item
            $args['basecat'] = $item['cid'];
            $items[$i]['cbchild'] = xarModAPIfunc('categories', 'user', 'getchildren', array('cid' => $item['cid']));

            // The user API function is called
            $forums = xarModAPIFunc('xarbb', 'user', 'getallforums', array('catid' => $item['cid']));

            // Security check: remove forums the user should not see
            $forumcount = count($forums);
            $items[$i]['forums'] = array();
            foreach($forums as $forum) {
                if (xarSecurityCheck('ViewxarBB', 0, 'Forum', 'All:' . $forum['fid'])) {
                    $items[$i]['forums'][] = $forum;

                    // Reset the last visited time if we want to mark all forums as read.
                    if (isset($read)) {
                        xarModAPIfunc('xarbb', 'admin', 'set_cookie', array('name' => 'f_' . $forum['fid'], 'value' => $now));
                        $topic_tracking = array();
                        xarModAPIfunc('xarbb', 'admin', 'set_cookie', array('name' => 'topics_' . $forum['fid'], 'value' => serialize($topic_tracking)));
                    }
                }
            }

            $args = $items[$i]['forums'];
            $items[$i]['forums'] = xarbb_user_main__getforuminfo($args);
        }
    }

    // Debug
    // $pre = var_export($items, true); echo "<pre>$pre</pre>"; return;
    // Add the array of items to the template variables
    $data['items'] = $items;

    // Don't really need to do this for visitors, just users.
    if (xarUserIsLoggedIn()) {
        // Check the cookie for the date to display
        $lastvisitsession = xarModAPIfunc('xarbb', 'admin', 'get_cookie', array('name' => 'lastvisit'));

        if (!empty($lastvisitsession)){
            $data['lastvisitdate'] = $lastvisitsession;
        } else {
            $data['lastvisitdate'] = time();
        }
    }

    xarTplSetPageTitle(xarML('Forum Index'));

    return $data;
}

/**
 * Configure forums and categories for display
 *
 * @access  private
 * @param   args contains that catid of forums
 * @return  array of forum information
*/
function xarbb_user_main__getforuminfo($args)
{
    $forums = $args;
    $totalforums = count($forums);

    for ($i = 0; $i < $totalforums; $i++) {
        $forum = $forums[$i];
        //bug #4070 - all posts, topics deleted by last poster still there
        if ($forum['ftopics'] > 0) {
            $getname = array();
            if (!empty($forum['fposter'])) {
                // Get the name of the poster.  Does it make sense to split this
                // to the API, since it is called so often?
                $getname = xarModAPIFunc('roles', 'user', 'get', array('uid' => $forum['fposter']));
            }
            if (!empty($getname['name'])) {
                $forums[$i]['name'] = $getname['name'];
            } else {
                $forums[$i]['name'] = '-';
            }
        }

        if (!empty($forum['foptions']) && is_string($forum['foptions'])) {
            $forums[$i]['foptions'] = unserialize($forum['foptions']);
        }

        // Forum Options
        // Check to see if forum is locked
        if ($forum['fstatus'] == 1) {
            $forums[$i]['timeimage'] = 1;
        } else {
            // Either the latest post is greater than the last visit time to the forum,
            // or we have never visited it, or there is at least one unread topic.

            $lastvisitforum = xarModAPIfunc('xarbb', 'admin', 'get_cookie', array('name' => 'f_' . $forum['fid']));

            if (empty($lastvisitforum) || $forum['fpostid'] > $lastvisitforum) {
                $unread_flag = true;
            } else {
                // Get the topic tracking list for this forum.
                $topic_tracking = xarModAPIfunc('xarbb', 'admin', 'get_cookie', array('name' => 'topics_' . $forum['fid']));
                if (!empty($topic_tracking)) $topic_tracking = unserialize($topic_tracking);

                if (empty($topic_tracking)) {
                    // There is no topic tracking set, so we know nothing about what is inside the forum.
                    // But we do know that there are no new posts since we last visited, so assume there
                    // is nothing new in it.
                    $unread_flag = false;
                } elseif (is_array($topic_tracking) && in_array(0, $topic_tracking)) {
                    $unread_flag = true;
                } else {
                    $unread_flag = false;
                }
            }

            // Set the image for the forum, according to whether it contains unread items.
            // TODO: replace these numbers with strings or module defines. The numbers mean
            // nothing when looking at this code.
            if ($unread_flag) {
                $forums[$i]['timeimage'] = 3;
            } else {
                $forums[$i]['timeimage'] = 2;
            }
        }
    }

    return $forums;
}

?>