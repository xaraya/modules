<?php
/**
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage comments
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
/**
 * Modify a comment
 *
 * This is dependant on the following criteria:
 * 1. user is the owner of the comment, or
 * 2. user has a minimum of moderator permissions for the
 *    specified comment
 * 3. we haven't reached the edit time limit if it is set
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access private
 * @return mixed description of return
 */
function comments_user_modify()
{
    $header                       = xarRequestGetVar('header');
    $package                      = xarRequestGetVar('package');
    $receipt                      = xarRequestGetVar('receipt');
    $receipt['post_url']          = xarModURL('comments','user','modify');
    $header['input-title']        = xarML('Modify Comment');

    if (!xarVarFetch('cid', 'int:1:', $cid, 0, XARVAR_NOT_REQUIRED)) return;
    if (!empty($cid)) {
        $header['cid'] = $cid;
    }

    $comments = xarModAPIFunc('comments','user','get_one', array('cid' => $header['cid']));
    
    /*if (empty($package['settings']['edittimelimit']) or (time() <= ($comments[0]['xar_date'] + ($package['settings']['edittimelimit'] * 60)))) {
       return;
    }*/
    $author_id = $comments[0]['xar_uid'];

    if ($author_id != xarUserGetVar('uid')) {
        if (!xarSecurityCheck('Comments-Edit'))
            return;
    }

    if (!isset($package['postanon'])) {
        $package['postanon'] = 0;
    }
    xarVarValidate('checkbox', $package['postanon']);
    if (!isset($header['itemtype'])) {
        $header['itemtype'] = 0;
    }

    $header['modid'] = $comments[0]['xar_modid'];
    $header['itemtype'] = $comments[0]['xar_itemtype'];
    $header['objectid'] = $comments[0]['xar_objectid'];

    if (empty($receipt['action'])) {
        $receipt['action'] = 'modify';
    }

    // get the title and link of the original object
    $modinfo = xarModGetInfo($header['modid']);
    $itemlinks = xarModAPIFunc($modinfo['name'],'user','getitemlinks',
                               array('itemtype' => $header['itemtype'],
                                     'itemids' => array($header['objectid'])),
                               // don't throw an exception if this function doesn't exist
                               0);
    if (!empty($itemlinks) && !empty($itemlinks[$header['objectid']])) {
        $url = $itemlinks[$header['objectid']]['url'];
        $header['objectlink'] = $itemlinks[$header['objectid']]['url'];
        $header['objecttitle'] = $itemlinks[$header['objectid']]['label'];
    } else {
        $url = xarModURL($modinfo['name'],'user','main');
    }
    if (empty($receipt['returnurl'])) {
        $receipt['returnurl'] = array('encoded' => rawurlencode($url),
                                      'decoded' => $url);
    }

    $package['settings'] = xarModAPIFunc('comments','user','getoptions',$header);

    switch (strtolower($receipt['action'])) {
        case 'submit':
            if (empty($package['title'])) {
                $msg = xarML('Missing [#(1)] field on new #(2)','title','comment');
                xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_FIELD', new SystemException($msg));
                return;
            }

            if (empty($package['text'])) {
                $msg = xarML('Missing [#(1)] field on new #(2)','text','comment');
                xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_FIELD', new SystemException($msg));
                return;
            }
            // call transform input hooks
            // should we look at the title as well?
            $package['transform'] = array('text');
            
            if (empty($package['settings']['edittimelimit']) 
               or (time() <= ($package['comments'][0]['xar_date'] + ($package['settings']['edittimelimit'] * 60)))
               or xarSecurityCheck('Comments-Admin')) {
       
            $package = xarModCallHooks('item', 'transform-input', 0, $package,
                                       'comments', 0);
            xarModAPIFunc('comments','user','modify',
                                        array('cid'      => $header['cid'],
                                              'text'     => $package['text'],
                                              'title'    => $package['title'],
                                              'postanon' => $package['postanon'],
                                              'authorid' => $author_id));
            }
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

            $comments[0]['transformed-text']    = xarVarPrepHTMLDisplay($comments[0]['transformed-text']);
            $comments[0]['transformed-title']   = xarVarPrepForDisplay($comments[0]['transformed-title']);
            $comments[0]['xar_text']            = xarVarPrepHTMLDisplay($comments[0]['xar_text']);
            $comments[0]['xar_title']           = xarVarPrepForDisplay($comments[0]['xar_title']);

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

            $package['transformed-text']  = xarVarPrepHTMLDisplay($package['transformed-text']);
            $package['transformed-title'] = xarVarPrepHTMLDisplay($package['transformed-title']);
            $package['text']              = xarVarPrepForDisplay($package['text']);
            $package['title']             = xarVarPrepForDisplay($package['title']);

            $comments[0]['xar_text']     = $package['text'];
            $comments[0]['xar_title']    = $package['title'];
            $comments[0]['xar_modid']    = $header['modid'];
            $comments[0]['xar_itemtype'] = $header['itemtype'];
            $comments[0]['xar_objectid'] = $header['objectid'];
            $comments[0]['xar_pid']      = $header['pid'];
            $comments[0]['xar_author']   = ((xarUserIsLoggedIn() && !$package['postanon']) ? xarUserGetVar('name') : 'Anonymous');
            $comments[0]['xar_cid']      = 0;
            $comments[0]['xar_postanon'] = $package['postanon'];
            // FIXME Delete after time putput testing
            // $comments[0]['xar_date']     = xarLocaleFormatDate("%d %b %Y %H:%M:%S %Z",time());
            $comments[0]['xar_date']     = time();

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

    $hooks = xarModAPIFunc('comments','user','formhooks');
/*
    // Call modify hooks for categories, dynamicdata etc.
    $args['module'] = 'comments';
    $args['itemtype'] = 0;
    $args['itemid'] = $header['cid'];
    // pass along the current module & itemtype for pubsub (urgh)
// FIXME: handle 2nd-level hook calls in a cleaner way - cfr. categories navigation, comments add etc.
    $args['cid'] = 0; // dummy category
    $modinfo = xarModGetInfo($header['modid']);
    $args['current_module'] = $modinfo['name'];
    $args['current_itemtype'] = $header['itemtype'];
    $args['current_itemid'] = $header['objectid'];
    $hooks['iteminput'] = xarModCallHooks('item', 'modify', $header['cid'], $args);
*/

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
?>
