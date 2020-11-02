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
    if (!xarVar::fetch('fid', 'id', $fid)) return;
    if (!xarVar::fetch('sublink', 'str:1:', $sublink, '', xarVar::NOT_REQUIRED)) return;
    if (!xarVar::fetch('phase', 'enum:form:update', $phase, 'form', xarVar::NOT_REQUIRED)) return;
    if (!xarVar::fetch('confirm', 'checkbox', $confirm, false, xarVar::NOT_REQUIRED)) return;
    // allow return url to be over-ridden
    if (!xarVar::fetch('return_url', 'str:1:', $data['return_url'], '', xarVar::NOT_REQUIRED)) return;

    $data = xarMod::apiFunc('crispbb', 'user', 'getforum', array('fid' => $fid, 'privcheck' => true));

    sys::import('modules.dynamicdata.class.objects.master');
    $data['forum'] = DataObjectMaster::getObject(array('name' => 'crispbb_forums'));
    //$data['forum']->joinCategories();

    // We only need some properties
    $fieldlist = array('fname','fdesc','fstatus','ftype','fprivileges','category','numtopics','numreplies');
    $data['forum']->setFieldlist($fieldlist);

    // Get the specific form and do a privilages check
    $data['forum']->userAction = 'deleteforum';
    $itemid = $data['forum']->getItem(array('itemid' => $fid));

    // CHECKME: remove this?
    if ($itemid != $fid)
        return xarTpl::module('privileges','user','errors',array('layout' => 'bad_author'));

    // CrispBB security
    if (empty($data['forum']->userLevel))
        return xarTpl::module('privileges','user','errors',array('layout' => 'no_privileges'));

    $userLevel = $data['forum']->userLevel;
    $secLevels = $data['forum']->fprivileges;
    $invalid = array();
    $now = time();

    $pageTitle = xarML('Delete #(1)', $data['forum']->properties['fname']->value);

    if ($phase == 'update') {
        if ($confirm) {
            if (!xarSec::confirmAuthKey()) {
                return xarTpl::module('privileges','user','errors',array('layout' => 'bad_author'));
            }
            $data['forum']->deleteItem(array('itemid' => $fid));
            if (empty($data['return_url'])) {
                $data['return_url'] = xarController::URL('crispbb', 'admin', 'view');
            }
            xarController::redirect($data['return_url']);
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
    xarTpl::setPageTitle(xarVar::prepForDisplay($pageTitle));

    return $data;
}
?>