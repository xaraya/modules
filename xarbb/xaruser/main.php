<?php
/**
 * File: $Id$
 * 
 * Main user function to display list of all existing forums
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/
function xarbb_user_main()
{
   // Get parameters from whatever input we need
    if (!xarVarFetch('startnum', 'id', $startnum, NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('catid', 'isset', $catid, NULL, XARVAR_DONT_SET)) return;
    //$catid = xarVarCleanFromInput('catid');
    // Security Check
    if(!xarSecurityCheck('ViewxarBB',1,'Forum')) return;

    // Get parameters from whatever input we need
    $data = array();
    $data['pager'] = '';    
    $data['uid']    = xarUserGetVar('uid');
    $data['items'] = array();

    // List the categories available as well
    $args['modid'] = xarModGetIDfromName('xarbb');
    $args['itemtype'] = 1;
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
    //$pre = var_export($items, true); echo "<pre>$pre</pre>"; return;


    // TODO, need to check if new topics have been updated since last visit.
    $data['folder']       = '<img src="' . xarTplGetImage('folder.gif') . '" alt="'.xarML('Folder').'"/>';

    // Add a pager
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('xarbb', 'user', 'countforums'),
        xarModURL('xarbb', 'user', 'main', array('startnum' => '%%')),
        xarModGetVar('xarbb', 'forumsperpage'));

    // Add the array of items to the template variables
    $data['items'] = $items;

    // User Specifics
    $data['now']    = time();
    //xarModSetVar('xarbb', 'lastvisitdate', $data['now']);
    $sitename = xarModGetVar('themes', 'SiteName', 0);

    if (isset($_COOKIE["xarbb_lastvisit"])){
        $data['lastvisitdate'] = unserialize($_COOKIE["xarbb_lastvisit"]);
    } else {
        $data['lastvisitdate'] = 1;
    }

    // Images
    // These are dependant on the time functions being changed
    $data['newpost']    = '<img src="' . xarTplGetImage('new/folder_new.gif') . '" alt="'.xarML('New post').'" />';
    $data['nonewpost']  = '<img src="' . xarTplGetImage('new/folder.gif') . '" alt="'.xarML('No New posts').'" />';
    $data['locked']     = '<img src="' . xarTplGetImage('new/folder_lock.gif') . '" alt="'.xarML('No New posts').'" />';

    // Login
    $data['return_url']      = xarModURL('xarbb', 'user', 'main');
    $data['submitlabel']    = xarML('Submit');

    // Return the template variables defined in this function
    return $data;
}

function xarbb_user__getforuminfo($args)
{
    $forums = $args;

    $totalforums = count($forums);
    for ($i = 0; $i < $totalforums; $i++) {
        $forum = $forums[$i];

        $getname = xarModAPIFunc('roles',
                                 'user',
                                 'get',
                                 array('uid' => $forum['fposter']));

        $forums[$i]['name'] = $getname['name'];

        // Check to see if forum is locked
        if ($forum['fstatus'] == 1){
            $forums[$i]['timeimage'] = '<img src="' . xarTplGetImage('new/folder_lock.gif') . '" alt="'.xarML('Forum Locked').'" />';
        } else {
            // Here we can check the updated images or standard ones.

            // Images
            if (isset($_COOKIE["xarbb_all"])){
                $alltimecompare = unserialize($_COOKIE["xarbb_all"]);
            } else {
                $alltimecompare = '';
            }
            $fid = $forum['fid'];
            if (isset($_COOKIE["xarbb_f_$fid"])){
                $forumtimecompare = unserialize($_COOKIE["xarbb_f_$fid"]);
            } else {
                $forumtimecompare = '';
            }

            if (($alltimecompare > $forum['fpostid']) || ($forumtimecompare > $forum['fpostid'])){
                $forums[$i]['timeimage'] = '<img src="' . xarTplGetImage('new/folder.gif') . '" alt="'.xarML('No New posts').'" />';
            } else {
                $forums[$i]['timeimage'] = '<img src="' . xarTplGetImage('new/folder_new.gif') . '" alt="'.xarML('New post').'" />';
            }
        }
    }
    return $forums;
}
?>