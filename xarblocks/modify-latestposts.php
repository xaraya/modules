<?php
/**
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
*/
/**
 * Modify block settings for latest xarbb topics and posts
 * @author jojodee
 */

function xarbb_latestpostsblock_modify($blockinfo)
{
    // Get current content
    $vars = @unserialize($blockinfo['content']);
    // Defaults
    if (!isset($vars['addtopics'])) {
        $vars['addtopics'] = 'on';
    }
    if (!isset($vars['addposts'])) {
        $vars['addposts'] = 'on';
    }
    if (!isset($vars['latestpost'])) {
        $vars['latestpost'] = 'off';
    }
    if (empty($vars['howmany'])) {
        $vars['howmany'] = 10;
    }
    if (empty($vars['forumid'])) {
        $vars['forumid'] = Array(0 =>'all');
    }
    if (!isset($vars['addauthor'])) {
        $vars['addauthor'] = '2';
    }
    if (!isset($vars['addlink'])) {
        $vars['addlink'] = '2';
    }
    if (!isset($vars['addobject'])) {
        $vars['addobject'] = '1';
    }
    if (!isset($vars['adddate'])) {
        $vars['adddate'] = 'on';
    }
    if (empty($vars['truncate'])) {
        $vars['truncate'] = 20;
    }
    if (!isset($vars['forumlink'])) {
        $vars['forumlink'] = '2';
    }
/*  Later - need to make sure 'modified' and linebreaks are not captured
    if (empty($vars['titleortext'])) {
        $vars['titleortext'] = 'on';
    }
*/

    // We have getallforums function now, so let's use that and clean out this silly mess
    $forumlist = array();
    $forumset = array();
    $forumset = xarModAPIFunc('xarbb', 'user', 'getallforums');
    $forumlist[0] = 'all';
    if (!empty($forumset)) {
        foreach ($forumset as $forumitem) {
            $fid=$forumitem['fid'];
            $forumitem[$fid] = ' - ' . $forumitem['fname'];
            $forumlist[$fid] = $forumitem[$fid];
        }
    }


    // Send content to template
    $output = array(
        'addtopics'   => $vars['addtopics'],
        'addposts'    => $vars['addposts'],
        'latestpost'  => $vars['latestpost'],
        'howmany'     => $vars['howmany'],
        'forumid'     => $vars['forumid'],
        'forumlist'   => $forumlist,
        'addauthor'   => $vars['addauthor'],
        'addlink'     => $vars['addlink'],
        'addobject'   => $vars['addobject'],
        'adddate'     => $vars['adddate'],
        //'titleortext' => $vars['titleortext'],
        'truncate'    => $vars['truncate'],
        'forumlink'   => $vars['forumlink']
    );

    // Return output
    return $output;
}

/**
 * update block settings
 */
function xarbb_latestpostsblock_update($blockinfo)
{
    if (!xarVarFetch('addtopics', 'checkbox', $vars['addtopics'],false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('addposts', 'checkbox', $vars['addposts'], false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('latestpost', 'checkbox', $vars['latestpost'], false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('howmany', 'int:1:', $vars['howmany'], '10', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('truncate', 'int:1:', $vars['truncate'], '20', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('forumid','isset', $vars['forumid'],'all',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('addauthor', 'int:0:2', $vars['addauthor'], '0',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('addlink', 'int:1:2', $vars['addlink'], '2',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('forumlink', 'int:0:2', $vars['forumlink'], '2',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('adddate', 'checkbox', $vars['adddate'], false, XARVAR_NOT_REQUIRED)) return;

    $blockinfo['content'] = serialize($vars);

    return $blockinfo;
}

?>