<?php

/**
 * File: $Id$
 *
 * Implement comments API backend
 *
 * @package modules
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage comments
 * @author Carl P. Corliss <rabbitt@xaraya.com>
*/


/**
 * include module constants and definitions
 *
 */

include_once('modules/comments/xarincludes/defines.php');

/*
 * Main function for comments module
 *
 */
function comments_user_main($args) {


}

/**
 * Displays a comment or set of comments
 *
 * @author   Carl P. Corliss (aka rabbitt)
 * @access   public
 * @param    integer    $args['modid']              the module id
 * @param    string     $args['objectid']           the item id
 * @param    string     $args['returnurl']          the url to return to
 * @param    integer    [$args['selected_cid']]     optional: the cid of the comment to view (only for displaying single comments)
 * @param    integer    [$args['preview']]          optional: an array containing a single (preview) comment used with adding/editing comments
 * @returns  array      returns whatever needs to be parsed by the BlockLayout engine
 */
function comments_user_display($args) {

    if (!xarSecurityCheck('Comments-Read',0))
        return;

// FIXME: simplify all this header, package, receipt stuff

    $header   = xarRequestGetVar('header');
    $package  = xarRequestGetVar('package');
    $receipt  = xarRequestGetVar('receipt');

    $package['settings'] = xarModAPIFunc('comments','user','getoptions');

    // FIXME: clean up return url handling

    $settings_uri = "&amp;depth={$package['settings']['depth']}"
                  . "&amp;order={$package['settings']['order']}"
                  . "&amp;sortby={$package['settings']['sortby']}"
                  . "&amp;render={$package['settings']['render']}";

    if (isset($args['modid'])) {
        $header['modid'] = $args['modid'];
    } elseif (isset($header['modid'])) {
        $args['modid'] = $header['modid'];
    } else {
        xarVarFetch('modid','isset',$modid,NULL,XARVAR_NOT_REQUIRED);
        if (empty($modid)) {
            $modid = xarModGetIDFromName(xarModGetName());
        }
        $args['modid'] = $modid;
        $header['modid'] = $modid;
    }

    if (isset($args['objectid'])) {
        $header['objectid'] = $args['objectid'];
    } elseif (isset($header['objectid'])) {
        $args['objectid'] = $header['objectid'];
    } else {
        xarVarFetch('objectid','isset',$objectid,NULL,XARVAR_NOT_REQUIRED);
        $args['objectid'] = $objectid;
        $header['objectid'] = $objectid;
    }

    if (isset($args['selected_cid'])) {
        $header['selected_cid'] = $args['selected_cid'];
    } elseif (isset($header['selected_cid'])) {
        $args['selected_cid'] = $header['selected_cid'];
    } else {
        xarVarFetch('selected_cid','isset',$selected_cid,NULL,XARVAR_NOT_REQUIRED);
        $args['selected_cid'] = $selected_cid;
        $header['selected_cid'] = $selected_cid;
    }


    if (!isset($receipt['returnurl']['raw'])) {
        if (empty($args['extrainfo'])) {
            $modinfo = xarModGetInfo($args['modid']);
            $receipt['returnurl']['raw'] = xarModURL($modinfo['name'],'user','main');
        } elseif (is_array($args['extrainfo']) && isset($args['extrainfo']['returnurl'])) {
            $receipt['returnurl']['raw'] = $args['extrainfo']['returnurl'];
        } elseif (is_string($args['extrainfo'])) {
            $receipt['returnurl']['raw'] = $args['extrainfo'];
        }
        if (!stristr($receipt['returnurl']['raw'],'?')) {
            $receipt['returnurl']['raw'] .= '?';
        }
        $receipt['returnurl']['decoded'] = $receipt['returnurl']['raw'] . $settings_uri;
        $receipt['returnurl']['encoded'] = rawurlencode($receipt['returnurl']['decoded']);
    } else {
        if (!stristr($receipt['returnurl']['raw'],'?')) {
            $receipt['returnurl']['raw'] .= '?';
        }
        $receipt['returnurl']['encoded'] = rawurlencode($receipt['returnurl']['raw']);
        $receipt['returnurl']['decoded'] = $receipt['returnurl']['raw'] . $settings_uri;
    }

    // TODO: handle item types

    if (!xarModLoad('comments','renderer')) {
        $msg = xarML('Unable to load #(1) #(2)','comments','renderer');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'UNABLE_TO_LOAD',
            new SystemException(__FILE__.'('.__LINE__.'):  '.$msg));
        return;
    }

    if (!isset($header['selected_cid'])) {
        $package['comments'] = xarModAPIFunc('comments','user','get_multiple',$header);
        if (count($package['comments']) > 1) {
            $package['comments'] = comments_renderer_array_sort(
                                                                 $package['comments'],
                                                                 $package['settings']['sortby'],
                                                                 $package['settings']['order']
                                                               );
        }
    } else {
        $header['cid'] = $header['selected_cid'];
        $package['settings']['render'] = _COM_VIEW_FLAT;
        $package['comments'] = xarModAPIFunc('comments','user','get_one', $header);
    }

    $package['comments'] = comments_renderer_array_prune_excessdepth(
                            array('array_list'  => $package['comments'],
                                  'cutoff'      => $package['settings']['depth']));

    if ($package['settings']['render'] == _COM_VIEW_THREADED) {
        $package['comments'] = comments_renderer_array_maptree($package['comments']);
    }

    // run text and title through transform hooks
    if (!empty($package['comments'])) {
        foreach ($package['comments'] as $key => $comment) {
            // say which pieces of text (array keys) you want to be transformed
            if ($package['settings']['render'] != _COM_VIEW_THREADED) {
                $comment['transform'] = array('xar_title', 'xar_text');
            } else {
                $comment['transform'] = array('xar_title');
            }
            // call the item transform hooks
            // Note : we need to tell Xaraya explicitly that we want to invoke the hooks for 'comments' here (last argument)
            $package['comments'][$key] = xarModCallHooks('item','transform',$comment['xar_cid'],$comment,'comments');
        }
    }
    $header['input-title']            = xarML('Post a new comment');

    $package['settings']['max_depth'] = _COM_MAX_DEPTH;
    $package['uid']                   = xarUserGetVar('uid');
    $package['uname']                 = xarUserGetVar('uname');
    $package['name']                  = xarUserGetVar('name');
    $package['new_title']             = xarVarGetCached('Comments.title', 'title');

    $receipt['post_url']              = xarModURL('comments','user','reply');
    $receipt['action']                = 'display';

    $hooks = comments_user_formhooks();

    $output['hooks']   = $hooks;
    $output['header']  = $header;
    $output['package'] = $package;
    $output['receipt'] = $receipt;

    return $output;

}


/**
 * processes comment replies and then redirects back to the
 * appropriate module/objectid (aka page)
 *
 * @author   Carl P. Corliss (aka rabbitt)
 * @access   public
 * @returns  array      returns whatever needs to be parsed by the BlockLayout engine
 */

function comments_user_reply() {

    if (!xarSecurityCheck('Comments-Post'))
        return;

    $header                       = xarRequestGetVar('header');
    $package                      = xarRequestGetVar('package');
    $receipt                      = xarRequestGetVar('receipt');
    $receipt['post_url']          = xarModURL('comments','user','reply');
    $header['input-title']        = xarML('Post a reply');

    if (!isset($package['postanon'])) {
        $package['postanon'] = 0;
    }

    xarVarValidate('checkbox', $package['postanon']);

    switch (strtolower($receipt['action'])) {
        case 'submit':
            if (empty($package['title'])) {
                $msg = xarML('Missing [#(1)] field on new #(2)','title','comment');
                xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_FIELD', new SystemException($msg));
                return;
            }

            if (empty($package['text'])) {
                $msg = xarML('Missing [#(1)] field on new #(2)','body','comment');
                xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_FIELD', new SystemException($msg));
                return;
            }
            xarModAPIFunc('comments','user','add',
                                        array('modid'    => $header['modid'],
                                              'objectid' => $header['objectid'],
                                              'pid'      => $header['pid'],
                                              'comment'  => $package['text'],
                                              'title'    => $package['title'],
                                              'postanon' => $package['postanon']));

            xarResponseRedirect($receipt['returnurl']['decoded']);
            return true;
        case 'reply':

            $comments = xarModAPIFunc('comments','user','get_one',
                                       array('cid' => $header['pid']));

            if (eregi('^(re\:|re\([0-9]+\))',$comments[0]['xar_title'])) {
                if (eregi('^re\:',$comments[0]['xar_title'])) {
                    $new_title = preg_replace("'re\:'i",
                                              'Re(1):',
                                              $comments[0]['xar_title'],
                                              1
                                             );
                } else {
                    preg_match("/^re\(([0-9]+)?/i",$comments[0]['xar_title'], $matches);
                    $new_title = preg_replace("'re\([0-9]+\)\:'i",
                                              'Re('.($matches[1] + 1).'):',
                                              $comments[0]['xar_title'],
                                              1
                                             );
                }
            } else {
                $new_title = 'Re: '.$comments[0]['xar_title'];
            }

            list($comments[0]['xar_text'],
                 $comments[0]['xar_title']) =
                        xarModCallHooks('item',
                                        'transform',
                                         $header['pid'],
                                         array($comments[0]['xar_text'],
                                               $comments[0]['xar_title']));


            $package['comments']             = $comments;
            $package['new_title']            = $new_title;
            $receipt['action']               = 'reply';
            $output['header']                = $header;
            $output['package']               = $package;
            $output['receipt']               = $receipt;

            break;
        case 'preview':
        default:
            list($package['transformed-text'],
                 $package['transformed-title']) = xarModCallHooks('item',
                                                      'transform',
                                                      $header['pid'],
                                                      array($package['text'],
                                                            $package['title']));

            $comments[0]['xar_text']     = $package['text'];
            $comments[0]['xar_title']    = $package['title'];
            $comments[0]['xar_modid']    = $header['modid'];
            $comments[0]['xar_objectid'] = $header['objectid'];
            $comments[0]['xar_pid']      = $header['pid'];
            $comments[0]['xar_author']   = ((xarUserIsLoggedIn() && !$package['postanon']) ? xarUserGetVar('name') : 'Anonymous');
            $comments[0]['xar_cid']      = 0;
            $comments[0]['xar_postanon'] = $package['postanon'];
            $comments[0]['xar_date']     = xarLocaleFormatDate("%d %b %Y %H:%M:%S %Z",time());
            $comments[0]['xar_hostname'] = 'somewhere';

            $package['comments']         = $comments;
            $package['new_title']        = $package['title'];
            $receipt['action']           = 'reply';

            break;

    }

    $hooks = comments_user_formhooks();

    $output['hooks']              = $hooks;
    $output['header']             = $header;
    $output['package']            = $package;
    $output['package']['date']    = time();
    $output['package']['uid']     = ((xarUserIsLoggedIn() && !$package['postanon']) ? xarUserGetVar('uid') : 2);
    $output['package']['uname']   = ((xarUserIsLoggedIn() && !$package['postanon']) ? xarUserGetVar('uname') : 'anonymous');
    $output['package']['name']    = ((xarUserIsLoggedIn() && !$package['postanon']) ? xarUserGetVar('name') : 'Anonymous');
    $output['receipt']            = $receipt;
    return $output;
}
/**
 * Modify a comment, dependant on the following criteria:
 * 1. user is the owner of the comment, or
 * 2. user has a minimum of moderator permissions for the
 *    specified comment
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access private
 * @returns mixed description of return
 */
function comments_user_modify() {

    $header                       = xarRequestGetVar('header');
    $package                      = xarRequestGetVar('package');
    $receipt                      = xarRequestGetVar('receipt');

    $receipt['post_url']          = xarModURL('comments','user','modify');
    $header['input-title']        = xarML('Modify Comment');

    $comments = xarModAPIFunc('comments','user','get_one', array('cid' => $header['cid']));
    $author_id = $comments[0]['xar_uid'];

    if ($author_id != xarUserGetVar('uid')) {
        if (!xarSecurityCheck('Comments-Edit'))
            return;
    }

    if (!isset($package['postanon'])) {
        $package['postanon'] = 0;
    }
    xarVarValidate('checkbox', $package['postanon']);

    switch (strtolower($receipt['action'])) {
        case 'submit':
            if (empty($package['title'])) {
                $msg = xarML('Missing [#(1)] field on new #(2)','title','comment');
                xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_FIELD', new SystemException($msg));
                return;
            }

            if (empty($package['text'])) {
                $msg = xarML('Missing [#(1)] field on new #(2)','text','comment');
                xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_FIELD', new SystemException($msg));
                return;
            }
            xarModAPIFunc('comments','user','modify',
                                        array('cid'      => $header['cid'],
                                              'text'     => $package['text'],
                                              'title'    => $package['title'],
                                              'postanon' => $package['postanon']));

            xarResponseRedirect($receipt['returnurl']['decoded']);
            return true;
        case 'modify':
            list($comments[0]['transformed-text'],
                 $comments[0]['transformed-title']) =
                        xarModCallHooks('item',
                                        'transform',
                                         $header['cid'],
                                         array($comments[0]['xar_text'],
                                               $comments[0]['xar_title']));


            $package['comments']                = $comments;
            $package['title']                   = $comments[0]['xar_title'];
            $package['text']                    = $comments[0]['xar_text'];
            $package['comments'][0]['xar_cid']  = $header['cid'];
            $receipt['action']                  = 'modify';

            $output['header']                   = $header;
            $output['package']                  = $package;
            $output['receipt']                  = $receipt;

            break;
        case 'preview':
        default:
            list($package['transformed-text'],
                 $package['transformed-title']) = xarModCallHooks('item',
                                                                  'transform',
                                                                  $header['pid'],
                                                                  array($package['text'],
                                                                        $package['title']));

            $comments[0]['xar_text']     = $package['text'];
            $comments[0]['xar_title']    = $package['title'];
            $comments[0]['xar_modid']    = $header['modid'];
            $comments[0]['xar_objectid'] = $header['objectid'];
            $comments[0]['xar_pid']      = $header['pid'];
            $comments[0]['xar_author']   = ((xarUserIsLoggedIn() && !$package['postanon']) ? xarUserGetVar('name') : 'Anonymous');
            $comments[0]['xar_cid']      = 0;
            $comments[0]['xar_postanon'] = $package['postanon'];
            $comments[0]['xar_date']     = xarLocaleFormatDate("%d %b %Y %H:%M:%S %Z",time());

            $forwarded = xarServerGetVar('HTTP_X_FORWARDED_FOR');
            if (!empty($forwarded)) {
                $hostname = preg_replace('/,.*/', '', $forwarded);
            } else {
                $hostname = xarServerGetVar('REMOTE_ADDR');
            }

            $comments[0]['xar_hostname'] = $hostname;
            $package['comments']         = $comments;
            $receipt['action']           = 'modify';

            break;

    }

    $hooks = comments_user_formhooks();

    $output['hooks']              = $hooks;
    $output['header']             = $header;
    $output['package']            = $package;
    $output['package']['date']    = time();
    $output['package']['uid']     = ((xarUserIsLoggedIn() && !$package['postanon']) ? xarUserGetVar('uid') : 2);
    $output['package']['uname']   = ((xarUserIsLoggedIn() && !$package['postanon']) ? xarUserGetVar('uname') : 'anonymous');
    $output['package']['name']    = ((xarUserIsLoggedIn() && !$package['postanon']) ? xarUserGetVar('name') : 'Anonymous');
    $output['receipt']            = $receipt;
    return $output;

}

/**
 * Delete a comment or a group of comments
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access private
 * @returns mixed description of return
 */
function comments_user_delete() {

    if (!xarSecurityCheck('Comments-Delete'))
        return;

    $header = xarRequestGetVar('header');
    $receipt = xarRequestGetVar('receipt');

    // Make sure some action was submitted
    if (!array_key_exists('action', $receipt))
        $receipt['action'] = 'confirm-delete';

    $output['header'] = $header;
    $output['receipt'] = $receipt;
    $output['package']['delete_url'] = xarModURL('comments','user','delete');

    switch(strtolower($receipt['action'])) {
        default:
        case 'confirm-delete':
            break;
        case 'reparent':
            xarModAPIFunc('comments','admin','delete_node',
                          array('node' => $header['cid'],
                                'pid'  => $header['pid']));
            xarResponseRedirect($receipt['returnurl']['decoded']);
            break;
        case 'thread':
            xarModAPIFunc('comments','admin','delete_branch',
                          array('node' => $header['cid']));
            xarResponseRedirect($receipt['returnurl']['decoded']);
            break;
    }
    return $output;
}

function comments_user_usermenu() {

    // Security Check
    if (xarSecurityCheck('Comments-Read',0)) {

        $phase = xarRequestGetVar('phase');

        xarTplSetPageTitle(xarModGetVar('themes', 'SiteName').' :: '.
                           xarVarPrepForDisplay(xarML('Comments'))
                           .' :: '.xarVarPrepForDisplay(xarML('Your Account Preferences')));

        if (empty($phase)){
            $phase = 'menu';
        }

        switch(strtolower($phase)) {
        case 'menu':

            $icon = xarTplGetImage('comments.gif', 'comments');
            $data = xarTplModule('comments','user', 'usermenu_icon',
                array('icon' => $icon,
                      'usermenu_form_url' => xarModURL('comments', 'user', 'usermenu', array('phase' => 'form'))
                     ));
            break;

        case 'form':

            $settings = xarModAPIFunc('comments','user','getoptions');
            $settings['max_depth'] = _COM_MAX_DEPTH - 1;
            $authid = xarSecGenAuthKey();
            $data = xarTplModule('comments','user', 'usermenu_form', array('authid'   => $authid,
                                                                           'settings' => $settings));
            break;

        case 'update':

            $settings = xarVarCleanFromInput('settings');

            if (!isset($settings) || count($settings) <= 0) {
                $msg = xarML('Settings passed from form are empty!');
                xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
                return;
            }

            // Confirm authorisation code.
            if (!xarSecConfirmAuthKey())
                return;

            xarModAPIFunc('comments','user','setoptions',$settings);

            // Redirect
            xarResponseRedirect(xarModURL('roles', 'user', 'account'));

            break;
        }

        return $data;
    }


}

/**
 * Searches all -active- comments based on a set criteria
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access private
 * @returns mixed description of return
 */
function comments_user_search( $args ) {

    if(!xarVarFetch('startnum', 'isset', $startnum,  NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('header',   'isset', $header,    NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('q',        'isset', $q,         NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('bool',     'isset', $bool,      NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('sort',     'isset', $sort,      NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('author',   'isset', $author,    NULL, XARVAR_DONT_SET)) {return;}


    $postinfo   = array('q' => $q, 'author' => $author);
    $data       = array();
    $search     = array();

    // TODO:  check 'q' and 'author' for '%' value
    //        and sterilize if found
    if (!isset($q) || strlen(trim($q)) <= 0) {
        if (isset($author) && strlen(trim($author)) > 0) {
            $q = $author;
        } else {
            $data['header']['text']     = 1;
            $data['header']['title']    = 1;
            $data['header']['author']   = 1;
            return $data;
        }
    }

    $q = "%$q%";

    // Default parameters
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = 20;
    }

    if (isset($header['title'])) {
        $search['title'] = $q;
        $postinfo['header[title]'] = 1;
        $header['title'] = 1;
    } else {
        $header['title'] = 0;
        $postinfo['header[title]'] = 0;
    }

    if (isset($header['text'])) {
        $search['text'] = $q;
        $postinfo['header[text]'] = 1;
        $header['text'] = 1;
    } else {
        $header['text'] = 0;
        $postinfo['header[text]'] = 0;
    }

    if (isset($header['author'])) {
        $postinfo['header[author]'] = 1;
        $header['author'] = 1;

        // need to get the user's uid from the name
        // FIXME:  this should be an api function in the roles module
        list($dbconn) = xarDBGetConn();
        $xartable = xarDBGetTables();

        // Get user information
        $rolestable = $xartable['roles'];
        $query = "SELECT xar_uid
                  FROM $rolestable
                  WHERE xar_uname = '" . xarVarPrepForStore($author) . "'";
        $result =& $dbconn->Execute($query);
        if (!$result) return;

        // if we found the uid add it to the search list,
        // otherwise we won't bother searching for it
        if (!$result->EOF) {
            $uids = $result->fields;
            $search['uid'] = $uids[0];
            $search['author'] = $author;
        }

        $result->Close();
    } else {
        $postinfo['header[author]'] = 0;
        $header['author'] = 0;
    }


    $package['comments'] = xarModAPIFunc('comments', 'user', 'search', $search);

    if (!empty($package['comments'])) {

        $header['modid'] = $package['comments'][0]['xar_modid'];
        $header['objectid'] = $package['comments'][0]['xar_objectid'];
        $receipt['returnurl']['decoded'] = xarModURL('comments','user','display', $postinfo);
        $receipt['returnurl']['encoded'] = rawurlencode($receipt['returnurl']['decoded']);

        $data['package'] = $package;
        $data['receipt'] = $receipt;


    }

    if (!isset($data['package'])){
        $data['receipt']['status'] = xarML('No Comments Found Matching Search');
    }

    $data['header'] = $header;
    return $data;
}

function comments_user_formhooks()
{

    $hooks = array();
    $hooks['formaction']              = xarModCallHooks('item', 'formaction', '', array(), 'comments');
    $hooks['formdisplay']             = xarModCallHooks('item', 'formdisplay', '', array(), 'comments');

    if (empty($hooks['formaction'])){
        $hooks['formaction'] = '';
    } elseif (is_array($hooks['formaction'])) {
        $hooks['formaction'] = join('',$hooks['formaction']);
    }

    if (empty($hooks['formdisplay'])){
        $hooks['formdisplay'] = '';
    } elseif (is_array($hooks['formdisplay'])) {
        $hooks['formdisplay'] = join('',$hooks['formdisplay']);
    }

    return $hooks;
}


/**
 * Collapse a comment branch and store the parent where
 * the collapsing begins in a uservar
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access private
 * @returns mixed description of return
 */
function comments_userapi_collapse( ) {

    $headers = xarRequestGetVar('headers');
    $package = xarRequestGetVar('package');
    $receipt = xarRequestGetVar('receipt');
    $package['settings'] = xarModAPIFunc('comments','user','getoptions');

    if (xarUserIsLoggedIn()) {

        $branches = unserialize(xarModGetUserVar('comments','CollapsedBranches'));

        if (!array_key_exists($header['cid'], $branches)) {
            $branches[$header['cid']] = $header['cid'];
            xarModSetUserVar('comments','CollapsedBranches',serialize($branches));
        }
    }

    $args['header[modid]']               = $header['modid'];
    $args['header[objectid]']            = $header['objectid'];

    if (isset($header['selected_cid'])) {
        $args['header[selected_cid]']        = $header['selected_cid'];
    }

    if (isset($header['branchout'])) {
        $args['header[branchout]']           = $header['branchout'];
        $args['header[cid]']                 = $header['cid'];
    }

    $args['receipt[returnurl][encoded]'] = $receipt['returnurl']['encoded'];
    $args['receipt[returnurl][decoded]'] = $receipt['returnurl']['decoded'];

    $url = $args['receipt[returnurl][decoded]'];

    foreach ($args as $k=>$v) {
        $url .= "&amp;$k=$v";
    }

    xarResponseRedirect($url);
}


/**
 * Expand a previously collapsed branch
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access private
 * @returns mixed description of return
 */
function comments_userapi_expand( ) {

    $headers = xarRequestGetVar('headers');
    $package = xarRequestGetVar('package');
    $receipt = xarRequestGetVar('receipt');
    $package['settings'] = xarModAPIFunc('comments','user','getoptions');

    if (xarUserIsLoggedIn()) {

        $branches = unserialize(xarModGetUserVar('comments','CollapsedBranches'));

        if (array_key_exists($header['cid'], $branches)) {
            unset($branches[$header['cid']]);
            xarModSetUserVar('comments','CollapsedBranches',serialize($branches));
        }
    }

    $args['header[modid]']               = $header['modid'];
    $args['header[objectid]']            = $header['objectid'];

    if (isset($header['selected_cid'])) {
        $args['header[selected_cid]']        = $header['selected_cid'];
    }

    if (isset($header['branchout'])) {
        $args['header[branchout]']           = $header['branchout'];
        $args['header[cid]']                 = $header['cid'];
    }

    $args['receipt[returnurl][encoded]'] = $receipt['returnurl']['encoded'];
    $args['receipt[returnurl][decoded]'] = $receipt['returnurl']['decoded'];

    $url = $args['receipt[returnurl][decoded]'];

    foreach ($args as $k=>$v) {
        $url .= "&amp;$k=$v";
    }

    xarResponseRedirect($url);
}

// FIXME: why is this written as a hook/GUI function instead of a block function ?
function comments_user_displayall($args) {

    $modarray = array();
    if (empty($args['modid'])) {
        $args['modid'] = xarVarCleanFromInput('modid');
        if (!$args['modid']) {
            $modarray[] = 'all';
        }  else      {
            $modarray = $args['modid'];
        }
    }   else {     
        $modarray=$args['modid'];
    }        

   
    if (empty($args['order'])) {
        $args['order'] = xarVarCleanFromInput('order');
        if (!$args['order']) {
            $args['order']='DESC';
        }
    }
    if (empty($args['howmany'])) {
            $args['howmany'] = xarVarCleanFromInput('howmany');
            if (!$args['howmany']) {
                $args['howmany']='20';
            }
    }
    if (empty($args['first'])) {
            $args['first'] = xarVarCleanFromInput('first');
            if (!$args['first']) {
                $args['first']='1';
            }
    }
    if (empty($args['block_is_calling'])) {
        $args['block_is_calling']=0;              
    } 
    if (empty($args['truncate'])) {
            $args['truncate']='';              
    }
    if (empty($args['addmodule'])) {
                $args['addmodule']='off';              
    }
    if (empty($args['addobject'])) {
                    $args['addobject']=1;              
    }
    if (empty($args['addcomment'])) {
                    $args['addcomment']=20;              
    }
    if (empty($args['adddate'])) {
            $args['adddate']='on';              
    }
    if (empty($args['adddaysep'])) {
                $args['adddaysep']='on';              
    }
    if (empty($args['addauthor'])) {
                $args['addauthor']=1;              
    }
    if (empty($args['addprevious'])) {
                $args['addprevious']=0;              
    }

    $args['returnurl'] = '';
/*
    // TODO: not sure all of this is necessary but let's keep it for now --Andrea
    // As this function is also used as a hook function we need to
    // check the extrainfo
    if (empty($args['returnurl']) && empty($args['extrainfo'])) {
        $args['returnurl'] = xarVarCleanFromInput('returnurl');
        if (!$args['returnurl']) {
            $modinfo = xarModGetInfo($args['modid']);
            $args['returnurl'] = xarModURL($modinfo['name'],'user','main');
            $args['extrainfo'] = $args['returnurl'];
        }
    } else {
        $args['returnurl'] = $args['extrainfo'];
    }
*/

    // get the list of modules+itemtypes that comments is hooked to
    $hookedmodules = xarModAPIFunc('modules', 'admin', 'gethookedmodules',
                                   array('hookModName' => 'comments'));

    // initialize list of module and pubtype names    
    $modlist = array();
    $modname = array();
    $modurl = array();
    $modview = array();
    $modlist['all'] = xarML('All');
    if (isset($hookedmodules) && is_array($hookedmodules)) {
        foreach ($hookedmodules as $module => $value) {
            $modid = xarModGetIDFromName($module);
            $modname[$modid] = $module;
            $modurl[$modid] = xarModURL($module,'user','display');
            $modview[$modid] = xarModURL($module,'user','view');
            // we have hooks for individual item types here
            if (!isset($value[0])) {
                // Get the list of all item types for this module (if any)
                $mytypes = xarModAPIFunc($module,'user','getitemtypes',
                                         // don't throw an exception if this function doesn't exist
                                         array(), 0);
                foreach ($value as $itemtype => $val) {
                    if (isset($mytypes[$itemtype])) {
                        $type = $mytypes[$itemtype]['label'];
                    } else {
                        $type = xarML('type #(1)',$itemtype);
                    }
                    $modlist["$module.$itemtype"] = ucwords($module) . ' - ' . $type;
                }
            } else {
                $modlist[$module] = ucwords($module);
            }
        }
    }

    $args['modarray']=$modarray;

    // check is supported modules are hooked and are requested
    $supported=array();
    if ( isset($modname['151'])  )  {
        foreach ($modarray as $mod)    {
            if (substr($mod,0,8)=='articles' || $mod=='all') {
                $supported['articles']=1;
                break;
            }
        }
    }
    
    if ( isset($modname['23'])  )  {
        foreach ($modarray as $mod)    {
            if ($mod=='polls' || $mod=='all') {
                $supported['polls']=1;
                break;
            }
        }
    }
        
    $args['supported']=$supported;
    $comments = xarModAPIFunc('comments','user','get_multipleall',$args);
    $settings = xarModAPIFunc('comments','user','getoptions');

    if (!empty($args['order'])) {
        $settings['order']=$args['order'];        
    }

    $hoursnow=strftime("%H","now");
    $timenow=time("now");   

    // add url and truncate comments if requested
    for ($i=0;$i<sizeof($comments);$i++) {   
        
        $comments[$i]['modname']=$modname[$comments[$i]['xar_modid']];
        $comments[$i]['modurl']=$modurl[$comments[$i]['xar_modid']];
        $comments[$i]['modview']=$modview[$comments[$i]['xar_modid']];
// TODO: use getitemlinks() here
        // provide object url when supported
        if ($comments[$i]['xar_modid'] == '151' )   {
            $comments[$i]['objurl']=$comments[$i]['modurl'].'&aid='.$comments[$i]['xar_objectid'];
        }  
        elseif ($comments[$i]['xar_modid'] == '23' ) {
            $comments[$i]['objurl']=$comments[$i]['modurl'].'&pid='.$comments[$i]['xar_objectid'];
        }
        else {
            $comments[$i]['objurl']=$comments[$i]['modurl'];
        }

        $comments[$i]['returnurl'] = urlencode(xarModURL($comments[$i]['modname'],'user','main'));
        if ($args['truncate']) {             
            if ( strlen($comments[$i]['xar_subject']) >$args['truncate']+3 )  {
                $comments[$i]['xar_subject']=substr($comments[$i]['xar_subject'],0,$args['truncate']).'...';
            }
            if ( !empty($comments[$i]['xar_title']) && strlen($comments[$i]['xar_title']) >$args['truncate']-3 ) {
                $comments[$i]['xar_title']=substr($comments[$i]['xar_title'],0,$args['truncate']).'...';
            }
        }        
        $dateprev = '';
        if ($args['adddaysep']=='on') {
        // find out whether to change day separator        
            $msgunixtime=strtotime($comments[$i]['xar_datetime']);
            $msgdate=strftime("%b %d, %Y",$msgunixtime);
            $msgday=strftime("%A",$msgunixtime);                        

            $comments[$i]['daychange']=0;
            
            $hoursdiff=($timenow - $msgunixtime)/3600;                      
            if($hoursdiff<$hoursnow && $msgdate!=$dateprev) {
                $comments[$i]['daychange']=xarML('Today');
                $dateprev=$msgdate;
            }
            elseif($hoursdiff>=$hoursnow && $hoursdiff<$hoursnow+24 && ($msgdate!=$dateprev) ) {
                $comments[$i]['daychange']=xarML('Yesterday');
                $dateprev=$msgdate;
            }
            elseif($hoursdiff>=$hoursnow+24 && $hoursdiff<$hoursnow+48 && $msgdate!=$dateprev) {
                $comments[$i]['daychange']=xarML('Two days ago');
                $dateprev=$msgdate;
            }
            elseif ($hoursdiff>=$hoursnow+48 && $hoursdiff<$hoursnow+144 && $msgdate!=$dateprev) {
                $comments[$i]['daychange']=xarML("$msgday");
                $dateprev=$msgdate;
            }
            elseif ($hoursdiff>=$hoursnow+144 && $msgdate!=$dateprev) {
                $comments[$i]['daychange']=$msgdate;
                $dateprev=$msgdate;
            }
        }                      
    }                                     
    // prepare for output
    $templateargs['order']          =$args['order'];
    $templateargs['howmany']        =$args['howmany'];
    $templateargs['first']          =$args['first'];
    $templateargs['adddate']        =$args['adddate'];
    $templateargs['adddaysep']      =$args['adddaysep'];
    $templateargs['addauthor']      =$args['addauthor'];
    $templateargs['addmodule']      =$args['addmodule'];    
    $templateargs['addcomment']     =$args['addcomment'];
    $templateargs['addobject']      =$args['addobject'];
    $templateargs['addprevious']    =$args['addprevious'];
    $templateargs['supported']      =$args['supported'];
    $templateargs['modarray']       =$modarray;
    $templateargs['modid']          =$modarray;
    $templateargs['modlist']        =$modlist;
    $templateargs['decoded_returnurl'] = rawurldecode(xarModURL('comments','user','displayall'));
    $templateargs['decoded_nexturl'] = xarModURL('comments','user','displayall',array(
                                                                            'first'=>$args['first']+$args['howmany'],
                                                                            'howmany'=>$args['howmany'],
                                                                            'modid'=>$modarray                                                                            
                                                                            )
                                                            );
    $templateargs['commentlist']    =$comments;
    $templateargs['order']          =$settings['order'];
    
    if ($args['block_is_calling']==0 )   {         
        $output=xarTplModule('comments', 'user','displayall', $templateargs);    
    } else {                          
        $templateargs['olderurl']=xarModURL('comments','user','displayall', 
                                            array(
                                                'first'=>   $args['first']+$args['howmany'],
                                                'howmany'=> $args['howmany'],
                                                'modid'=>$args['modid'] 
                                                )
                                            );
        $output=xarTplBlock('comments', 'latestcommentsblock', $templateargs );    
    }
    
    return $output;
}

?>
