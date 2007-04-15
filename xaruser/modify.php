<?php
/**
 * Comments module - Allows users to post comments on items
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Comments Module
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
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
function comments_user_modify()
{
    $header                       = xarRequestGetVar('header');
    $package                      = xarRequestGetVar('package');
    $receipt                      = xarRequestGetVar('receipt');
    $receipt['post_url']          = xarModURL('comments','user','modify');
    $header['input-title']        = xarML('Modify Comment');

    if (!xarVarFetch('id', 'int:1:', $id, 0, XARVAR_NOT_REQUIRED)) return;
    if (!empty($id)) {
        $header['id'] = $id;
    }

    $comments = xarModAPIFunc('comments','user','get_one', array('id' => $header['id']));
    $author_id = $comments[0]['uid'];

    if ($author_id != xarUserGetVar('uid')) {
        if (!xarSecurityCheck('EditComments'))
            return;
    }

    if (!isset($package['postanon'])) {
        $package['postanon'] = 0;
    }
    xarVarValidate('checkbox', $package['postanon']);
    if (!isset($header['itemtype'])) {
        $header['itemtype'] = 0;
    }

    $header['modid'] = $comments[0]['modid'];
    $header['itemtype'] = $comments[0]['itemtype'];
    $header['objectid'] = $comments[0]['objectid'];

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
            $package = xarModCallHooks('item', 'transform-input', 0, $package,
                                       'comments', 0);
            xarModAPIFunc('comments','user','modify',
                                        array('id'      => $header['id'],
                                              'text'     => $package['text'],
                                              'title'    => $package['title'],
                                              'postanon' => $package['postanon'],
                                              'authorid' => $author_id));

            xarResponseRedirect($receipt['returnurl']['decoded']);
            return true;
        case 'modify':
            list($comments[0]['transformed-text'],
                 $comments[0]['transformed-title']) =
                        xarModCallHooks('item',
                                        'transform',
                                         $header['id'],
                                         array($comments[0]['text'],
                                               $comments[0]['title']));

            $comments[0]['transformed-text']    = xarVarPrepHTMLDisplay($comments[0]['transformed-text']);
            $comments[0]['transformed-title']   = xarVarPrepForDisplay($comments[0]['transformed-title']);
            $comments[0]['text']            = xarVarPrepHTMLDisplay($comments[0]['text']);
            $comments[0]['title']           = xarVarPrepForDisplay($comments[0]['title']);

            $package['comments']                = $comments;
            $package['title']                   = $comments[0]['title'];
            $package['text']                    = $comments[0]['text'];
            $package['comments'][0]['id']  = $header['id'];
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

            $comments[0]['text']     = $package['text'];
            $comments[0]['title']    = $package['title'];
            $comments[0]['modid']    = $header['modid'];
            $comments[0]['itemtype'] = $header['itemtype'];
            $comments[0]['objectid'] = $header['objectid'];
            $comments[0]['pid']      = $header['pid'];
            $comments[0]['author']   = ((xarUserIsLoggedIn() && !$package['postanon']) ? xarUserGetVar('name') : 'Anonymous');
            $comments[0]['id']      = 0;
            $comments[0]['postanon'] = $package['postanon'];
            // FIXME Delete after time putput testing
            // $comments[0]['date']     = xarLocaleFormatDate("%d %b %Y %H:%M:%S %Z",time());
            $comments[0]['date']     = time();

            $forwarded = xarServerGetVar('HTTP_X_FORWARDED_FOR');
            if (!empty($forwarded)) {
                $hostname = preg_replace('/,.*/', '', $forwarded);
            } else {
                $hostname = xarServerGetVar('REMOTE_ADDR');
            }

            $comments[0]['hostname'] = $hostname;
            $package['comments']         = $comments;
            $receipt['action']           = 'modify';

            break;

    }

    $hooks = xarModAPIFunc('comments','user','formhooks');
/*
    // Call modify hooks for categories, dynamicdata etc.
    $args['module'] = 'comments';
    $args['itemtype'] = 0;
    $args['itemid'] = $header['id'];
    // pass along the current module & itemtype for pubsub (urgh)
// FIXME: handle 2nd-level hook calls in a cleaner way - cfr. categories navigation, comments add etc.
    $args['id'] = 0; // dummy category
    $modinfo = xarModGetInfo($header['modid']);
    $args['current_module'] = $modinfo['name'];
    $args['current_itemtype'] = $header['itemtype'];
    $args['current_itemid'] = $header['objectid'];
    $hooks['iteminput'] = xarModCallHooks('item', 'modify', $header['id'], $args);
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
