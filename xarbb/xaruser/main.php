<?php
/**
 * File: $Id$ 
 * 
 * Main user function to display list of all existing forums
 * And existing categories
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox, Roger Raymond, Carl Corliss (help)
*/

/**
 * Configure forums and categories for display
 *
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
    if(!xarSecurityCheck('ViewxarBB',1,'Forum')) return;

    // Lets deal with the cookie in a more sane manner
    if (isset($read)){
        $time    = serialize(time());
        // Easier to set a cookie for the last visit than it is
        // roll through all the forums to check the time set.
        setcookie(xarModGetVar('xarbb', 'cookiename') . 'lastvisit', $time, time()+60*60*24*120, xarModGetVar('xarbb', 'cookiepath'), xarModGetVar('xarbb', 'cookiedomain'), 0);
    }

    // Variable Needed for output
    $args               = array();
    $args['modid']      = xarModGetIDfromName('xarbb');
    $args['itemtype']   = 0;
    $data               = array();
    $data['pager']      = '';    
    $data['uid']        = xarUserGetVar('uid');
    $data['catid']      = $catid;
    $data['items']      = array();
    // Cookie
    $data['now']        = time();
    $sitename           = xarModGetVar('themes', 'SiteName', 0);
    // Login
    $data['return_url'] = xarModURL('xarbb', 'user', 'main');
    $data['submitlabel']= xarML('Submit');

    // List the categories available as well
 
    // Regular Categories
    if (isset($catid)){
        $args['cid'] = $catid;
        $items = xarModAPIfunc('categories', 'user', 'getcat', $args);

        $totalitems = count($items);
        for ($i = 0; $i < $totalitems; $i++) {
            $item = $items[$i];
                    // The user API function is called
            $args['basecat'] = $item['cid'];
            $items[$i]['cbchild'] = xarModAPIfunc('categories', 'user', 'getchildren', array('cid' => $item['cid']));
            $items[$i]['forums'] = xarModAPIFunc('xarbb',
                                    'user',
                                    'getallforums',
                                     array('catid' => $item['cid'],
                                           'startnum' => $startnum,
                                            'numitems' => xarModGetVar('xarbb',
                                                                    'forumsperpage')));

            $args = $items[$i]['forums'];
            $items[$i]['forums'] = xarbb_user__getforuminfo($args);
        }
    } else {
        // Base Categories
        // Get an array of assigned category details for a specific item
        $items = xarModAPIfunc('categories', 'user', 'getallcatbases', $args);
        $totalitems = count($items);
        for ($i = 0; $i < $totalitems; $i++) {
            $item = $items[$i];
            // Get an array of assigned category details for a specific item
            $args['basecat'] = $item['cid'];
            $items[$i]['cbchild'] = xarModAPIfunc('categories', 'user', 'getchildren', array('cid' => $item['cid']));

                    // The user API function is called
            $items[$i]['forums'] = xarModAPIFunc('xarbb',
                                    'user',
                                    'getallforums',
                                     array('catid' => $item['cid'],
                                           'startnum' => $startnum,
                                            'numitems' => xarModGetVar('xarbb',
                                                                    'forumsperpage')));

            $args = $items[$i]['forums'];
            $items[$i]['forums'] = xarbb_user__getforuminfo($args);
        }
    }
    // Debug
    // $pre = var_export($items, true); echo "<pre>$pre</pre>"; return;
    // Add the array of items to the template variables
    $data['items'] = $items;
    // Add a pager
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('xarbb', 'user', 'countforums'),
        xarModURL('xarbb', 'user', 'main', array('startnum' => '%%')),
        xarModGetVar('xarbb', 'forumsperpage'));

    // Don't really need to do this for visitors, just users.
    if (xarUserIsLoggedIn()){
        // Cookie Name for Last Visit
        $cookie_name_last_visit = xarModGetVar('xarbb', 'cookiename') . 'lastvisit';
        // Check the cookie for the date to display
        if (isset($_COOKIE["$cookie_name_last_visit"])){
            $data['lastvisitdate'] = unserialize($_COOKIE["$cookie_name_last_visit"]);
        } else {
            $data['lastvisitdate'] = time();
        }
    }

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

    // Cookie Name for Mark All Read
    $cookie_name_all_read = xarModGetVar('xarbb', 'cookiename') . 'lastvisit';

    for ($i = 0; $i < $totalforums; $i++) {
        $forum = $forums[$i];
        // Get the name of the poster.  Does it make sense to split this 
        // to the API, since it is called so often?
        $getname = xarModAPIFunc('roles',
                                 'user',
                                 'get',
                                 array('uid' => $forum['fposter']));
        $forums[$i]['name'] = $getname['name'];
        
        // Forum Options
        // Check to see if forum is locked
        if ($forum['fstatus'] == 1){
            $forums[$i]['timeimage'] = '<img src="' . xarTplGetImage('new/folder_lock.gif') . '" alt="'.xarML('Forum Locked').'" />';
        } else {
            if (xarUserIsLoggedIn()){
                // Here we can check the updated images or standard ones.
                // Images
                if (isset($_COOKIE[$cookie_name_all_read])){
                    $alltimecompare = unserialize($_COOKIE[$cookie_name_all_read]);
                } else {
                    $alltimecompare = '';
                }
                $cookie_name_this_forum_read = xarModGetVar('xarbb', 'cookiename') . '_f_' . $forum['fid'];
                if (isset($_COOKIE[$cookie_name_this_forum_read])){
                    $forumtimecompare = unserialize($_COOKIE[$cookie_name_this_forum_read]);
                } else {
                    $forumtimecompare = '';
                }

                $time_compare = max($alltimecompare, $forumtimecompare);
                if ($time_compare > $forum['fpostid']) {
                    $forums[$i]['timeimage'] = '<img src="' . xarTplGetImage('new/folder.gif') . '" alt="'.xarML('No New posts').'" />';
                } else {
                    $forums[$i]['timeimage'] = '<img src="' . xarTplGetImage('new/folder_new.gif') . '" alt="'.xarML('New post').'" />';
                }
            } else { 
                $forums[$i]['timeimage'] = '<img src="' . xarTplGetImage('new/folder.gif') . '" alt="'.xarML('No New posts').'" />';
            }
        }
    }
    return $forums;
}
?>
