<?php
/**
 * crispBB Forum Module
 *
 * @package modules
 * @copyright (C) 2008-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage crispBB Forum Module
 * @link http://xaraya.com/index.php/release/970.html
 * @author crisp <crisp@crispcreations.co.uk>
 *//**
 * Standard function to delete a forum
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @return array
 */
function crispbb_admin_delete($args)
{
    extract($args);
    if (!xarVarFetch('fid', 'id', $fid)) return;
    if (!xarVarFetch('sublink', 'str:1:', $sublink, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('phase', 'enum:form:update', $phase, 'form', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm', 'checkbox', $confirm, false, XARVAR_NOT_REQUIRED)) return;
    // allow return url to be over-ridden
    if (!xarVarFetch('return_url', 'str:1:', $data['return_url'], '', XARVAR_NOT_REQUIRED)) return;

    $data = xarMod::apiFunc('crispbb', 'user', 'getforum', array('fid' => $fid, 'privcheck' => true));

    sys::import('modules.dynamicdata.class.objects.master');
    $data['forum'] = DataObjectMaster::getObject(array('name' => 'crispbb_forums'));
    //$data['forum']->joinCategories();
    $fieldlist = array('fname','fdesc','fstatus','ftype','category','numtopics','numreplies');
    $data['forum']->setFieldlist($fieldlist);
    $data['forum']->userAction = 'deleteforum';
    $itemid = $data['forum']->getItem(array('itemid' => $fid));

    if ($itemid != $fid)
        return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));

    if (empty($data['forum']->userLevel))
        return xarTplModule('privileges','user','errors',array('layout' => 'no_privileges'));

    $userLevel = $data['forum']->userLevel;
    $secLevels = $data['forum']->fprivileges;
    $invalid = array();
    $now = time();
    $tracking = xarMod::apiFunc('crispbb', 'user', 'tracking', array('now' => $now));
    // End Tracking
    if (!empty($tracking)) {
        xarVarSetCached('Blocks.crispbb', 'tracking', $tracking);
        xarModUserVars::set('crispbb', 'tracking', serialize($tracking));
    }
    $pageTitle = xarML('Delete #(1)', $data['forum']->properties['fname']->value);

    if ($phase == 'update') {
        if ($confirm) {
            if (!xarSecConfirmAuthKey()) {
                return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
            }
            $data['forum']->deleteItem(array('itemid' => $fid));
            if (empty($data['return_url'])) {
                $data['return_url'] = xarModURL('crispbb', 'admin', 'view');
            }
            xarResponse::Redirect($data['return_url']);
            return true;
        }
    }
    $data['pageTitle'] = $pageTitle;
    // populate the menulinks for this function
    $data['menulinks'] = xarMod::apiFunc('crispbb', 'admin', 'getmenulinks',
        array(
            'current_module' => 'crispbb',
            'current_type' => 'admin',
            'current_func' => 'delete',
            'current_sublink' => $sublink,
            'fid' => $fid,
            'catid' => $data['catid'],
            'secLevels' => $secLevels
        ));
    // set page title
    xarTPLSetPageTitle(xarVarPrepForDisplay($pageTitle));

    return $data;
}
?>