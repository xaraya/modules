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


# --------------------------------------------------------
# Take appropriate action
#
    if (!xarVarFetch('comment_action', 'str', $data['comment_action'], 'reply', XARVAR_NOT_REQUIRED)) return;
    switch (strtolower($data['comment_action'])) {
        case 'submit':
# --------------------------------------------------------
# Get the values from the form
#
            $data['reply'] = DataObjectMaster::getObject(array('name' => 'comments_comments'));
            $valid = $data['reply']->checkInput();

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
# If something is wrong, represent the form
#
            if (!$valid) {
                return xarTpl::module('comments','user','reply',$data);
            }

# --------------------------------------------------------
# Everything is go: if there is a comment, create and go to the next page
#
            if (!empty($data['reply']->properties['text']->value))
                $data['comment_id'] = $data['reply']->createItem();
            xarController::redirect($data['reply']->properties['parent_url']->value.'#'.$data['comment_id']);
            return true;

        case 'reply':
# --------------------------------------------------------
# Bail if the proper args were not passed
#
            if (!xarVarFetch('comment_id', 'int:1:', $data['comment_id'], 0, XARVAR_NOT_REQUIRED)) return;
            if (empty($data['comment_id'])) return xarResponse::NotFound();    

# --------------------------------------------------------
# Create the comment object
#
            sys::import('modules.dynamicdata.class.objects.master');
            $data['object'] = DataObjectMaster::getObject(array('name' => 'comments_comments'));
            $data['object']->getItem(array('itemid' => $data['comment_id']));

            // replace the deprecated eregi stuff below
            $title =& $data['object']->properties['title']->value;
            $text  =& $data['object']->properties['text']->value;
            $title = preg_replace('/^re:/i','',$title);
            $new_title = 'Re: ' . $title;

            // get the title and link of the original object
            $modinfo = xarMod::getInfo($data['object']->properties['moduleid']->value);
            try{
                $itemlinks = xarMod::apiFunc($modinfo['name'],'user','getitemlinks',
                                           array('itemtype' => $data['object']->properties['itemtype']->value,
                                                 'itemids' => array($data['object']->properties['itemid']->value)));
            } catch (Exception $e) {}
            if (!empty($itemlinks) && !empty($itemlinks[$data['object']->properties['itemid']->value])) {
                $url = $itemlinks[$header['itemid']]['url'];
                $header['objectlink'] = $itemlinks[$data['object']->properties['itemid']->value]['url'];
                $header['objecttitle'] = $itemlinks[$data['object']->properties['itemid']->value]['label'];
            } else {
                $url = xarModURL($modinfo['name'],'user','main');
            }
/*
            list($text,
                 $title) =
                        xarModCallHooks('item',
                                        'transform',
                                         $data['object']->properties['parent_id']->value,
                                         array($text,
                                               $title));
*/
            $text         = xarVarPrepHTMLDisplay($text);
            $title        = xarVarPrepForDisplay($title);

            $package['new_title']            = xarVarPrepForDisplay($new_title);
            $data['package']               = $package;

            // Create an object item for the reply
            $data['reply'] = DataObjectMaster::getObject(array('name' => 'comments_comments'));
            $data['reply']->properties['title']->value = $new_title;
            $data['reply']->properties['position']->reference_id = $data['comment_id'];
            $data['reply']->properties['position']->position = 3;
            $data['reply']->properties['moduleid']->value = $data['object']->properties['moduleid']->value;
            $data['reply']->properties['itemtype']->value = $data['object']->properties['itemtype']->value;
            $data['reply']->properties['itemid']->value = $data['object']->properties['itemid']->value;
            $data['reply']->properties['parent_url']->value = $data['object']->properties['parent_url']->value;
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
    $modinfo = xarMod::getInfo($header['moduleid']);
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
    $data['package']            = $package;
    $data['package']['date']    = time();
    $data['package']['role_id']     = ((xarUserIsLoggedIn() && !$data['object']->properties['anonpost']->value) ? xarUserGetVar('id') : $anonuid);
    $data['package']['uname']   = ((xarUserIsLoggedIn() && !$data['object']->properties['anonpost']->value) ? xarUserGetVar('uname') : 'anonymous');
    $data['package']['name']    = ((xarUserIsLoggedIn() && !$data['object']->properties['anonpost']->value) ? xarUserGetVar('name') : 'Anonymous');

    return $data;
}

?>
