<?php
/**
 * Main user function to display list of all existing forums
 * And existing categories
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarbb Module
 * @link http://xaraya.com/index.php/release/300.html
 * @author John Cox
 */
/**
 * Configure forums and categories for display
 * @author John Cox
 * @author Roger Raymond
 * @author Carl Corliss (help)
 * @author Jo dalle Nogare
 * @access public
 * @param int startnum used for the pager
 * @param int catid when not on top level forum
 * @param isset read
 * @return array
 */
function xarbb_user_main()
{
    // Get parameters from whatever input we need
    if (!xarVarFetch('startnum', 'id', $startnum, NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('catid', 'id', $catid, NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('read', 'isset', $read, NULL, XARVAR_DONT_SET)) return;

    // No need for security check, as we catch lack of forums in the template.
    //if (!xarSecurityCheck('ViewxarBB', 1, 'Forum')) return;

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
    $sitename           = xarModGetVar('themes', 'SiteName');
    $xarbbtitle         = xarModGetVar('xarbb', 'xarbbtitle');
    $data['xarbbtitle'] = (isset($xarbbtitle) ? $xarbbtitle : '');

    // List the categories available as well

    // A category that the user has selected.
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

        // Count of categories
        $totalitems = count($items);

        // Loop for the selected categories (all one of it!)
        for ($i = 0; $i < $totalitems; $i++) {
            // Assume category category array is zero-based index.
            $item = $items[$i];

            // The user API function is called
            $args['basecat'] = $item['cid'];
            $cats = xarModAPIfunc('categories', 'user', 'getchildren', array('cid' => $item['cid']));
            $items[$i]['cbchild'] = array();
            foreach($cats as $cat) {
                if (xarSecurityCheck('ViewxarBB', 0, 'Forum', $cat['cid'] . ':All')) {
                    $items[$i]['cbchild'][] = $cat;
                }
            }

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
                        xarModAPIfunc(
                            'xarbb', 'admin', 'set_cookie',
                            array('name' => 'f_' . $forum['fid'], 'value' => $now)
                        );

                        // Also set the topic tracking flags, so there are no topics within
                        // the forum marked as unread.

                        // We can simply clear all entries in the topic tracking array, since we
                        // have set the last visited time on the forum itself.
                        $topic_tracking = array();
                        xarModAPIfunc(
                            'xarbb', 'admin', 'set_cookie',
                            array('name' => 'topics_' . $forum['fid'], 'value' => serialize($topic_tracking))
                        );
                    }
                }

                $args = $items[$i]['forums'];
                $items[$i]['forums'] = xarbb_user_main__getforuminfo($args);
            }
        }
    } else {
        // Base categories - the user has not selected a category.
        // Get an array of assigned category details for a specific item

        $cats = xarModAPIfunc('categories', 'user', 'getallcatbases', $args);
        if (empty($cats)) $cats = array();

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
            // Apply privileges to the child categories before accepting and displaying them.
            $args['basecat'] = $item['cid'];
            $child_cats = xarModAPIfunc('categories', 'user', 'getchildren', array('cid' => $item['cid']));
            $items[$i]['cbchild'] = array();
            foreach($child_cats as $child_cat) {
                if (xarSecurityCheck('ViewxarBB', 0, 'Forum', $child_cat['cid'] . ':All')) {
                    $items[$i]['cbchild'][] = $child_cat;
                }
            }

            // The user API function is called
            $forums = xarModAPIFunc('xarbb', 'user', 'getallforums', array('catid' => $item['cid']));

            // Security check: remove forums the user should not see
            $forumcount = count($forums);
            $items[$i]['forums'] = array();
            foreach($forums as $forum) {
                if (xarSecurityCheck('ViewxarBB', 0, 'Forum', $item['cid'] . ':' . $forum['fid'])) {
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

    // Add the array of items to the template variables
    $data['items'] = $items;

    // Check the cookie for the date to display
    //$lastvisitdate = xarModAPIfunc('xarbb', 'admin', 'get_cookie', array('name' => 'lastvisit'));
    //$data['lastvisitdate'] = (!empty($lastvisitdate) ? $lastvisitdate : $now);
    // v1.3.2 fix for Bug 5774
    // using a module user var to track last visit
    $data['lastvisitdate'] = xarModGetUserVar('xarbb', 'lastvisit');
    xarTplSetPageTitle(xarML('Forum Index'));

    return $data;
}

/**
 * Configure forums and categories for display
 *
 * @access  private
 * @param  array args contains that catid of forums
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
            // Get the name of the poster.
            if (!empty($forum['fposter']) && ($username = @xarUserGetVar('name', $forum['fposter']))) {
                // TODO: Does it make sense to move this to the API, since it is called so often?
                $forums[$i]['name'] = $username;
            } else {
                $forums[$i]['name'] = '-';
            }
        }

        // TODO: this is already unserialized in the API; no need to do it again.
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