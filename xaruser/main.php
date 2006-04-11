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
    if (!xarVarFetch('catid', 'isset', $catid, NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('read', 'isset', $read, NULL, XARVAR_DONT_SET)) return;

    // Security Check
    if (!xarSecurityCheck('ViewxarBB', 1, 'Forum')) return;

    // Variable Needed for output
    $args               = array();
    $args['modid']      = xarModGetIDfromName('xarbb');
    $args['itemtype']   = 0;
    $data               = array();
    $data['pager']      = '';
    $data['uid']        = xarUserGetVar('uid');
    $data['catid']      = $catid;
    $data['now']        = time();
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
            if(xarSecurityCheck('ViewxarBB', 0, 'Forum', $cat['cid'] . ':All')) {
                $items[] = $cat;
            }
        }

        $totalitems = count($items);

        for ($i = 0; $i < $totalitems; $i++) {
            $item = $items[$i];
            // The user API function is called
            $args['basecat'] = $item['cid'];
            $items[$i]['cbchild'] = xarModAPIfunc('categories', 'user', 'getchildren', array('cid' => $item['cid']));
            $forums = xarModAPIFunc('xarbb', 'user', 'getallforums', array('catid' => $item['cid']));
            // Security check: remove forums the user should not see
            $forumcount = count($forums);
            $items[$i]['forums'] = array();
            foreach($forums as $forum){
                if(xarSecurityCheck('ViewxarBB',0,'Forum','All:'.$forum['fid'])) {
                    $items[$i]['forums'][] = $forum;
                }
                if (isset($read)){
                    xarSessionSetVar(xarModGetVar('xarbb', 'cookiename') . '_f_' . $forum['fid'], time());
                }
                $args = $items[$i]['forums'];
                $items[$i]['forums'] = xarbb_user__getforuminfo($args);
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
                if(xarSecurityCheck('ViewxarBB',0,'Forum','All:'.$forum['fid'])) {
                    $items[$i]['forums'][] = $forum;
                }
            }

            $args = $items[$i]['forums'];
            $items[$i]['forums'] = xarbb_user__getforuminfo($args);
            if (isset($read)) {
                xarSessionSetVar(xarModGetVar('xarbb', 'cookiename') . '_f_' . $forum['fid'], time());
            }
        }
    }
    // Debug
    // $pre = var_export($items, true); echo "<pre>$pre</pre>"; return;
    // Add the array of items to the template variables
    $data['items'] = $items;

    // Don't really need to do this for visitors, just users.
    if (xarUserIsLoggedIn()){
        // Check the cookie for the date to display
        $lastvisitsession = xarSessionGetVar(xarModGetVar('xarbb', 'cookiename') . 'lastvisit');
        if (isset($lastvisitsession)){
            $data['lastvisitdate'] = xarSessionGetVar(xarModGetVar('xarbb', 'cookiename') . 'lastvisit');
        } else {
            $data['lastvisitdate'] = time();
        }
    }
    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('Forum Index')));
    return $data;
}
/**
 * Configure forums and categories for display
 *
 * @access  private
 * @param   args contains that catid of forums
 * @return  array of forum information
*/
function xarbb_user__getforuminfo($args)
{
    $forums = $args;
    $totalforums = count($forums);

    for ($i = 0; $i < $totalforums; $i++) {
        $forum = $forums[$i];
        //bug #4070 - all posts, topics deleted by last poster still there
        if ($forum['ftopics']>0) {
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

        if (!empty($forum['foptions']) && is_string($forum['foptions'])){
            $forums[$i]['foptions'] = unserialize($forum['foptions']);
        }

        // Forum Options
        // Check to see if forum is locked
        if ($forum['fstatus'] == 1) {
            $forums[$i]['timeimage'] = 1;
        } else {
            if (xarUserIsLoggedIn()) {
                // Here we can check the updated images or standard ones.
                $lastvisitforumsession = xarSessionGetVar(xarModGetVar('xarbb', 'cookiename') . '_f_' . $forum['fid']);
                if (isset($lastvisitforumsession)){
                    $forumtimecompare = xarSessionGetVar(xarModGetVar('xarbb', 'cookiename') . '_f_' . $forum['fid']);
                } else {
                    $forumtimecompare = '';
                }

                //$time_compare = max($alltimecompare, $forumtimecompare);

                $time_compare = $forumtimecompare;

                if ($time_compare > $forum['fpostid']) {
                    $forums[$i]['timeimage'] = 2;
                } else {
                    $forums[$i]['timeimage'] = 3;
                }
            } else {
                $forums[$i]['timeimage'] = 2;
            }
        }
    }
    return $forums;
}

?>