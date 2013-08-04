<?php
/**
 * Comments Module
 *
 * @package modules
 * @subpackage comments
 * @category Third Party Xaraya Module
 * @version 2.4.0
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * processes comment replies and then redirects back to the
 * appropriate module/object itemid (aka page)
 *
 * @author   Carl P. Corliss (aka rabbitt)
 * @access   public
 * @returns  array      returns whatever needs to be parsed by the BlockLayout engine
 */

 /*
    generally speaking...
    $package = the comment data
    $header = info describing the item that we're commenting on
    $receipt = particulars of the form submission
    $parent_url = the url of the parent of these comments
 */

function comments_user_reply()
{
    if (!xarSecurityCheck('PostComments')) return;

# --------------------------------------------------------
# Get all the relevant info from the submitted comments form
#
    xarVarFetch('receipt',   'array', $receipt,           array(), XARVAR_NOT_REQUIRED);
    $receipt['post_url']          = xarModURL('comments','user','reply');

# --------------------------------------------------------
# Bail if the proper args were not passed
#
//    if (empty(!$valid))    
//        return xarTpl::module('comments','user','errors',array('layout' => 'no_direct_access'));

# --------------------------------------------------------
# Create the comment object
#
    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(array('name' => 'comments_comments'));

# --------------------------------------------------------
# Take appropriate action
#
    if (!xarVarFetch('comment_action', 'str', $data['comment_action'], 'submit', XARVAR_NOT_REQUIRED)) return;
    switch (strtolower($data['comment_action'])) {
        case 'submit':
# --------------------------------------------------------
# Get the values from the form
#
            $valid = $data['object']->checkInput();

            // call transform input hooks
            // should we look at the title as well?
            $package['transform'] = array('text');
            $package = xarModCallHooks('item', 'transform-input', 0, $package,
                                       'comments', 0);

            if (xarModVars::get('comments','AuthorizeComments') || xarSecurityCheck('AddComments')) {
                $status = _COM_STATUS_ON;
            } else {
                $status = _COM_STATUS_OFF;
            }

# --------------------------------------------------------
# If something is wrong, do what?
#
            if (!$valid) {
            }

# --------------------------------------------------------
# Everything is go: update and go to the next page
#
            $data['comment_id'] = $data['object']->createItem();
            xarController::redirect($data['object']->properties['parent_url']->value.'#'.$data['comment_id']);
            return true;

        case 'reply':

            xarVarFetch('comment_id', 'int', $data['comment_id'], 0, XARVAR_NOT_REQUIRED);
            $data['object']->getItem(array('itemid' => $data['comment_id']));
            $comments = xarMod::apiFunc('comments','user','get_one',
                                       array('id' => $data['object']->properties['id']->value));

            // replace the deprecated eregi stuff below
            $comments[0]['title'] = preg_replace('/^re:/i','',$comments[0]['title']);
            $new_title = 'Re: '.$comments[0]['title'];

            /*if (eregi('^(re\:|re\([0-9]+\))',$comments[0]['title'])) {
                if (eregi('^re\:',$comments[0]['title'])) {
                    $new_title = preg_replace("'re\:'i",
                                              'Re(1):',
                                              $comments[0]['title'],
                                              1
                                             );
                } else {
                    preg_match("/^re\(([0-9]+)?/i",$comments[0]['title'], $matches);
                    $new_title = preg_replace("'re\([0-9]+\)\:'i",
                                              'Re('.($matches[1] + 1).'):',
                                              $comments[0]['title'],
                                              1
                                             );
                }
            } else {
                $new_title = 'Re: '.$comments[0]['title'];
            }*/

            $header['moduleid'] = $comments[0]['moduleid'];
            $header['itemtype'] = $comments[0]['itemtype'];
            $header['itemid']   = $comments[0]['itemid'];

            // get the title and link of the original object
            $modinfo = xarModGetInfo($header['moduleid']);
            try{
                $itemlinks = xarMod::apiFunc($modinfo['name'],'user','getitemlinks',
                                           array('itemtype' => $header['itemtype'],
                                                 'itemids' => array($header['itemid'])));
            } catch (Exception $e) {}
            if (!empty($itemlinks) && !empty($itemlinks[$header['itemid']])) {
                $url = $itemlinks[$header['itemid']]['url'];
                $header['objectlink'] = $itemlinks[$header['itemid']]['url'];
                $header['objecttitle'] = $itemlinks[$header['itemid']]['label'];
            } else {
                $url = xarModURL($modinfo['name'],'user','main');
            }
            /*if (empty($receipt['returnurl'])) {
                $receipt['returnurl'] = array('encoded' => rawurlencode($url),
                                              'decoded' => $url);
            }*/

            list($comments[0]['text'],
                 $comments[0]['title']) =
                        xarModCallHooks('item',
                                        'transform',
                                         $header['parent_id'],
                                         array($comments[0]['text'],
                                               $comments[0]['title']));

            $comments[0]['text']         = xarVarPrepHTMLDisplay($comments[0]['text']);
            $comments[0]['title']        = xarVarPrepForDisplay($comments[0]['title']);

            $package['comments']             = $comments;
            $package['new_title']            = xarVarPrepForDisplay($new_title);
            $receipt['action']               = 'reply';
            $data['header']                = $header;
            $data['package']               = $package;
            $data['receipt']               = $receipt;

            break;
        case 'preview':
        default:
            list($package['transformed-text'],
                 $package['transformed-title']) = xarModCallHooks('item',
                                                      'transform',
                                                      $header['parent_id'],
                                                      array($package['text'],
                                                            $package['title']));

            $package['transformed-text']  = xarVarPrepHTMLDisplay($package['transformed-text']);
            $package['transformed-title'] = xarVarPrepForDisplay($package['transformed-title']);
            $package['text']              = xarVarPrepHTMLDisplay($package['text']);
            $package['title']             = xarVarPrepForDisplay($package['title']);

            $comments[0]['text']      = $package['text'];
            $comments[0]['title']     = $package['title'];
            $comments[0]['moduleid']  = $header['moduleid'];
            $comments[0]['itemtype']  = $header['itemtype'];
            $comments[0]['itemid']    = $header['itemid'];
            $comments[0]['parent_id'] = $header['parent_id'];
            $comments[0]['author']    = ((xarUserIsLoggedIn() && !$package['postanon']) ? xarUserGetVar('name') : 'Anonymous');
            $comments[0]['id']       = 0;
            $comments[0]['postanon']  = $package['postanon'];
            // FIXME delete after time output testing
            // $comments[0]['date']      = xarLocaleFormatDate("%d %b %Y %H:%M:%S %Z",time());
            $comments[0]['date']      = time();
            $comments[0]['hostname']  = 'somewhere';

            $package['comments']          = $comments;
            $package['new_title']         = $package['title'];
            $receipt['action']            = 'reply';

            break;

    }

    $hooks = xarMod::apiFunc('comments','user','formhooks');
/*
    // Call new hooks for categories, dynamicdata etc.
    $args['module'] = 'comments';
    $args['itemtype'] = 0;
    $args['itemid'] = 0;
    // pass along the current module & itemtype for pubsub (urgh)
// FIXME: handle 2nd-level hook calls in a cleaner way - cfr. categories navigation, comments add etc.
    $args['id'] = 0; // dummy category
    $modinfo = xarModGetInfo($header['moduleid']);
    $args['current_module'] = $modinfo['name'];
    $args['current_itemtype'] = $header['itemtype'];
    $args['current_itemid'] = $header['itemid'];
    $hooks['iteminput'] = xarModCallHooks('item', 'new', 0, $args);
*/

# --------------------------------------------------------
# Pass args to the form template
#
    $anonuid = xarConfigVars::get(null,'Site.User.AnonymousUID');
    $data['hooks']              = $hooks;
    $data['header']             = $header;
    $data['package']            = $package;
    $data['package']['date']    = time();
    $data['package']['role_id']     = ((xarUserIsLoggedIn() && !$package['postanon']) ? xarUserGetVar('id') : $anonuid);
    $data['package']['uname']   = ((xarUserIsLoggedIn() && !$package['postanon']) ? xarUserGetVar('uname') : 'anonymous');
    $data['package']['name']    = ((xarUserIsLoggedIn() && !$package['postanon']) ? xarUserGetVar('name') : 'Anonymous');
    $data['receipt']            = $receipt;

    return $data;
}

?>
