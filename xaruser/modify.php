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
    if (!xarVar::fetch('parent_url', 'str', $parent_url, 0, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('adminreturn', 'str', $data['adminreturn'], null, xarVar::NOT_REQUIRED)) {
        return;
    }

    # --------------------------------------------------------
    # Bail if the proper args were not passed
#
    if (!xarVar::fetch('comment_id', 'int:1:', $data['comment_id'], 0, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (empty($data['comment_id'])) {
        return xarResponse::NotFound();
    }

    # --------------------------------------------------------
    # Create the comment object and get the item to modify
#
    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(['name' => 'comments_comments']);
    $data['object']->getItem(['itemid' => $data['comment_id']]);

    # --------------------------------------------------------
    # Check that this user can modify this comment
#
    if ($data['object']->properties['author']->value != xarUser::getVar('id')) {
        if (!xarSecurity::check('EditComments')) {
            return;
        }
    }

    $header['moduleid'] = $data['object']->properties['moduleid']->value;
    $header['itemtype'] = $data['object']->properties['itemtype']->value;
    $header['itemid']   = $data['object']->properties['itemid']->value;

    // get the title and link of the original object
    $modinfo = xarMod::getInfo($data['object']->properties['moduleid']->value);
    try {
        $itemlinks = xarMod::apiFunc(
            $modinfo['name'],
            'user',
            'getitemlinks',
            ['itemtype' => $header['itemtype'],
                                         'itemids' => [$header['itemid']], ]
        );
    } catch (Exception $e) {
    }
    if (!empty($itemlinks) && !empty($itemlinks[$header['itemid']])) {
        $url = $itemlinks[$header['itemid']]['url'];
        $header['objectlink'] = $itemlinks[$header['itemid']]['url'];
        $header['objecttitle'] = $itemlinks[$header['itemid']]['label'];
    } else {
        $url = xarController::URL($modinfo['name'], 'user', 'main');
    }
    /*if (empty($receipt['returnurl'])) {
        $receipt['returnurl'] = array('encoded' => rawurlencode($url),
                                      'decoded' => $url);
    }*/

    $package['settings'] = xarMod::apiFunc('comments', 'user', 'getoptions', $header);

    # --------------------------------------------------------
    # Take appropriate action
#
    if (!xarVar::fetch('comment_action', 'str', $data['comment_action'], 'modify', xarVar::NOT_REQUIRED)) {
        return;
    }
    switch ($data['comment_action']) {
        case 'submit':
            # --------------------------------------------------------
            # Get the values from the form
#
            $valid = $data['object']->checkInput();

            // call transform input hooks
            // should we look at the title as well?
            $package['transform'] = ['text'];

            if (empty($package['settings']['edittimelimit'])
               or (time() <= ($package['comments'][0]['xar_date'] + ($package['settings']['edittimelimit'] * 60)))
               or xarSecurity::check('AdminComments')) {
                $package = xarModHooks::call(
                    'item',
                    'transform-input',
                    0,
                    $package,
                    'comments',
                    0
                );
                # --------------------------------------------------------
                # If something is wrong, redisplay the form
#
                if (!$valid) {
                    return xarTpl::module('comments', 'user', 'modify', $data);
                }

                # --------------------------------------------------------
                # Everything is go: update and go to the next page
#
                $data['comment_id'] = $data['object']->updateItem();
            }

            if (isset($data['adminreturn']) && $data['adminreturn'] == 'yes') { // if we got here via the admin side
                xarController::redirect(xarController::URL('comments', 'admin', 'view'));
            } else {
                xarController::redirect($data['object']->properties['parent_url']->value . '#' . $data['comment_id']);
            }
            return true;
        case 'modify':
            $title =& $data['object']->properties['title']->value;
            $text  =& $data['object']->properties['text']->value;
            [$transformed_text,
                $transformed_title] =
                       xarModHooks::call(
                           'item',
                           'transform',
                           $data['comment_id'],
                           [$text,
                                              $title, ]
                       );

            $data['transformed_text']    = xarVar::prepHTMLDisplay($transformed_text);
            $data['transformed_title']   = xarVar::prepForDisplay($transformed_title);
            $data['text']                = xarVar::prepHTMLDisplay($text);
            $data['title']               = xarVar::prepForDisplay($title);
            $data['comment_action']      = 'submit';

            break;
        case 'preview':
        default:
            [$package['transformed-text'],
                $package['transformed-title']] = xarModHooks::call(
                    'item',
                    'transform',
                    $header['parent_id'],
                    [$package['text'],
                                                                       $package['title'], ]
                );

            $package['transformed-text']  = xarVar::prepHTMLDisplay($package['transformed-text']);
            $package['transformed-title'] = xarVar::prepHTMLDisplay($package['transformed-title']);
            $package['text']              = xarVar::prepForDisplay($package['text']);
            $package['title']             = xarVar::prepForDisplay($package['title']);

            $comments[0]['text']     = $package['text'];
            $comments[0]['title']    = $package['title'];
            $comments[0]['moduleid']    = $header['moduleid'];
            $comments[0]['itemtype'] = $header['itemtype'];
            $comments[0]['itemid'] = $header['itemid'];
            $comments[0]['parent_id']      = $header['parent_id'];
            $comments[0]['author']   = ((xarUser::isLoggedIn() && !$package['postanon']) ? xarUser::getVar('name') : 'Anonymous');
            $comments[0]['id']      = 0;
            $comments[0]['postanon'] = $package['postanon'];
            // FIXME Delete after time putput testing
            // $comments[0]['date']     = xarLocale::formatDate("%d %b %Y %H:%M:%S %Z",time());
            $comments[0]['date']     = time();

            $forwarded = xarServer::getVar('HTTP_X_FORWARDED_FOR');
            if (!empty($forwarded)) {
                $hostname = preg_replace('/,.*/', '', $forwarded);
            } else {
                $hostname = xarServer::getVar('REMOTE_ADDR');
            }

            $comments[0]['hostname'] = $hostname;
            $package['comments']         = $comments;
            $data['comment_action']      = 'modify';

            break;
    }

    $hooks = xarMod::apiFunc('comments', 'user', 'formhooks');
    /*
        // Call modify hooks for categories, dynamicdata etc.
        $args['module'] = 'comments';
        $args['itemtype'] = 0;
        $args['itemid'] = $header['id'];
        // pass along the current module & itemtype for pubsub (urgh)
    // FIXME: handle 2nd-level hook calls in a cleaner way - cfr. categories navigation, comments add etc.
        $args['id'] = 0; // dummy category
        $modinfo = xarMod::getInfo($header['moduleid']);
        $args['current_module'] = $modinfo['name'];
        $args['current_itemtype'] = $header['itemtype'];
        $args['current_itemid'] = $header['itemid'];
        $hooks['iteminput'] = xarModHooks::call('item', 'modify', $header['id'], $args);
    */

    $data['hooks']              = $hooks;
    return $data;
}
