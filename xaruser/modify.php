<?php
/**
 * @package modules
 * @copyright (C) 2002-2007 The copyright-placeholder
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
    $header   = xarController::getVar('header');
    $package   = xarController::getVar('package');
    $receipt   = xarController::getVar('receipt');
    $receipt['post_url']          = xarModURL('comments','user','modify');
    $header['input-title']        = xarML('Modify Comment');

    if (!xarVarFetch('objecturl', 'str', $objecturl, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('adminreturn', 'str', $data['adminreturn'], NULL, XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('id', 'int:1:', $id, 0, XARVAR_NOT_REQUIRED)) return;
    if (!empty($id)) {
        $header['id'] = $id;
    }

    $comments = xarMod::apiFunc('comments','user','get_one', array('id' => $header['id']));

    $author_id = $comments[0]['role_id'];

    if ($author_id != xarUserGetVar('id')) {
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
    try{
        $itemlinks = xarMod::apiFunc($modinfo['name'],'user','getitemlinks',
                                   array('itemtype' => $header['itemtype'],
                                         'itemids' => array($header['objectid'])));
    } catch (Exception $e) {}
    if (!empty($itemlinks) && !empty($itemlinks[$header['objectid']])) {
        $url = $itemlinks[$header['objectid']]['url'];
        $header['objectlink'] = $itemlinks[$header['objectid']]['url'];
        $header['objecttitle'] = $itemlinks[$header['objectid']]['label'];
    } else {
        $url = xarModURL($modinfo['name'],'user','main');
    }
    /*if (empty($receipt['returnurl'])) {
        $receipt['returnurl'] = array('encoded' => rawurlencode($url),
                                      'decoded' => $url);
    }*/

    $package['settings'] = xarMod::apiFunc('comments','user','getoptions',$header);

    switch (strtolower($receipt['action'])) {
        case 'submit':
            if (empty($package['title'])) {
                $msg = xarML('Missing [#(1)] field on new #(2)','title','comment');
                throw new BadParameterException($msg);
            }

            if (empty($package['text'])) {
                $msg = xarML('Missing [#(1)] field on new #(2)','text','comment');
                throw new BadParameterException($msg);
            }
            // call transform input hooks
            // should we look at the title as well?
            $package['transform'] = array('text');

            if (empty($package['settings']['edittimelimit'])
               or (time() <= ($package['comments'][0]['xar_date'] + ($package['settings']['edittimelimit'] * 60)))
               or xarSecurityCheck('AdminComments')) {

            $package = xarModCallHooks('item', 'transform-input', 0, $package,
                                       'comments', 0);
            xarMod::apiFunc('comments','user','modify',
                                        array('id'      => $header['id'],
                                              'text'     => $package['text'],
                                              'title'    => $package['title'],
                                              'postanon' => $package['postanon'],
                                              'authorid' => $author_id));
            }

            if (isset($data['adminreturn']) && $data['adminreturn'] == 'yes') { // if we got here via the admin side
                xarResponse::redirect(xarModURL('comments','admin','view'));
            } else {
                xarResponse::redirect($comments[0]['objecturl'].'#'.$header['id']);
            }
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
            //Psspl:Added the field for post anonomous messages.
            $package['postanon']                    = $comments[0]['postanon'];
            $package['comments'][0]['id']  = $header['id'];
            $receipt['action']                  = 'modify';

            $data['header']                   = $header;
            $data['package']                  = $package;
            $data['receipt']                  = $receipt;

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

            $forwarded = xarServer::getVar('HTTP_X_FORWARDED_FOR');
            if (!empty($forwarded)) {
                $hostname = preg_replace('/,.*/', '', $forwarded);
            } else {
                $hostname = xarServer::getVar('REMOTE_ADDR');
            }

            $comments[0]['hostname'] = $hostname;
            $package['comments']         = $comments;
            $receipt['action']           = 'modify';

            break;

    }

    $hooks = xarMod::apiFunc('comments','user','formhooks');
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

    $data['hooks']              = $hooks;
    $data['header']             = $header;
    $data['package']            = $package;
    $data['package']['date']    = time();
    $data['package']['role_id']     = ((xarUserIsLoggedIn() && !$package['postanon']) ? xarUserGetVar('id') : 2);
    $data['package']['uname']   = ((xarUserIsLoggedIn() && !$package['postanon']) ? xarUserGetVar('uname') : 'anonymous');
    $data['package']['name']    = ((xarUserIsLoggedIn() && !$package['postanon']) ? xarUserGetVar('name') : 'Anonymous');
    $data['receipt']            = $receipt;
    return $data;

}
?>
