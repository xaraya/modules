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
 * processes comment replies and then redirects back to the
 * appropriate module/objectid (aka page)
 *
 * @author   Carl P. Corliss (aka rabbitt)
 * @access   public
 * @returns  array      returns whatever needs to be parsed by the BlockLayout engine
 */

function comments_user_reply()
{
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
    if (!isset($header['itemtype'])) {
        $header['itemtype'] = 0;
    }

    if (empty($receipt['action'])) {
        $receipt['action'] = 'reply';
    }

    switch (strtolower($receipt['action'])) {
        case 'submit':
            if (empty($package['title'])) {
                $msg = xarML('Missing [#(1)] field on new #(2)','title','comment');
                xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_FIELD', new SystemException($msg));
                return;
            }

            if (empty($package['text'])) {
                $msg = xarML('Missing [#(1)] field on new #(2)','body','comment');
                xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_FIELD', new SystemException($msg));
                return;
            }
            // call transform input hooks
            // should we look at the title as well?
            $package['transform'] = array('text');
            $package = xarModCallHooks('item', 'transform-input', 0, $package,
                                       'comments', 0);

            xarModAPIFunc('comments','user','add',
                           array('modid'    => $header['modid'],
                                 'itemtype' => $header['itemtype'],
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

            $header['modid'] = $comments[0]['xar_modid'];
            $header['itemtype'] = $comments[0]['xar_itemtype'];
            $header['objectid'] = $comments[0]['xar_objectid'];

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

            list($comments[0]['xar_text'],
                 $comments[0]['xar_title']) =
                        xarModCallHooks('item',
                                        'transform',
                                         $header['pid'],
                                         array($comments[0]['xar_text'],
                                               $comments[0]['xar_title']));


            $comments[0]['xar_text']         = xarVarPrepHTMLDisplay($comments[0]['xar_text']);
            $comments[0]['xar_title']        = xarVarPrepForDisplay($comments[0]['xar_title']);

            $package['comments']             = $comments;
            $package['new_title']            = xarVarPrepForDisplay($new_title);
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

            $package['transformed-text']  = xarVarPrepHTMLDisplay($package['transformed-text']);
            $package['transformed-title'] = xarVarPrepForDisplay($package['transformed-title']);
            $package['text']              = xarVarPrepHTMLDisplay($package['text']);
            $package['title']             = xarVarPrepForDisplay($package['title']);

            $comments[0]['xar_text']      = $package['text'];
            $comments[0]['xar_title']     = $package['title'];
            $comments[0]['xar_modid']     = $header['modid'];
            $comments[0]['xar_itemtype']  = $header['itemtype'];
            $comments[0]['xar_objectid']  = $header['objectid'];
            $comments[0]['xar_pid']       = $header['pid'];
            $comments[0]['xar_author']    = ((xarUserIsLoggedIn() && !$package['postanon']) ? xarUserGetVar('name') : 'Anonymous');
            $comments[0]['xar_cid']       = 0;
            $comments[0]['xar_postanon']  = $package['postanon'];
            // FIXME delete after time output testing
            // $comments[0]['xar_date']      = xarLocaleFormatDate("%d %b %Y %H:%M:%S %Z",time());
            $comments[0]['xar_date']      = time();
            $comments[0]['xar_hostname']  = 'somewhere';

            $package['comments']          = $comments;
            $package['new_title']         = $package['title'];
            $receipt['action']            = 'reply';

            break;

    }

    $hooks = xarModAPIFunc('comments','user','formhooks');
/*
    // Call new hooks for categories, dynamicdata etc.
    $args['module'] = 'comments';
    $args['itemtype'] = 0;
    $args['itemid'] = 0;
    // pass along the current module & itemtype for pubsub (urgh)
// FIXME: handle 2nd-level hook calls in a cleaner way - cfr. categories navigation, comments add etc.
    $args['cid'] = 0; // dummy category
    $modinfo = xarModGetInfo($header['modid']);
    $args['current_module'] = $modinfo['name'];
    $args['current_itemtype'] = $header['itemtype'];
    $args['current_itemid'] = $header['objectid'];
    $hooks['iteminput'] = xarModCallHooks('item', 'new', 0, $args);
*/

    $anonuid = xarConfigGetVar('Site.User.AnonymousUID');
    $output['hooks']              = $hooks;
    $output['header']             = $header;
    $output['package']            = $package;
    $output['package']['date']    = time();
    $output['package']['uid']     = ((xarUserIsLoggedIn() && !$package['postanon']) ? xarUserGetVar('uid') : $anonuid);
    $output['package']['uname']   = ((xarUserIsLoggedIn() && !$package['postanon']) ? xarUserGetVar('uname') : 'anonymous');
    $output['package']['name']    = ((xarUserIsLoggedIn() && !$package['postanon']) ? xarUserGetVar('name') : 'Anonymous');
    $output['receipt']            = $receipt;
    return $output;
}

?>
