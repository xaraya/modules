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
 */
/**
 * Function to do delete a forum category
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * What this function does
 *
 * @return array
 */
function crispbb_admin_deletecat($args)
{
    if (!crispBB::userCan('admincrispbb')) {
         return xarTplModule('privileges','user','errors',array('layout' => 'no_privileges'));
    }
    extract($args);

    $data = array();
    if (!xarVarFetch('itemid', 'int:1', $data['itemid'], 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm', 'checkbox', $confirm, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('return_url',  'pre:trim:lower:str:1',  $data['return_url'], '', XARVAR_NOT_REQUIRED)) return;
    // secondary sec check on categories module
    if(!xarSecurityCheck('ManageCategories', 0, "All:$data[itemid]")) {
         return xarTplModule('privileges','user','errors',array('layout' => 'no_privileges'));
    }
    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(array('name' => xarModVars::get('categories','categoriesobject')));
    $data['object']->getItem(array('itemid' => $data['itemid']));

    if ($confirm) {
        if (!xarSecConfirmAuthKey()) {
            return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
        }
        $data['object']->deleteItem(array('itemid' => $data['itemid']));
        if (empty($data['return_url'])) {
            $data['return_url'] = xarModURL('crispbb', 'admin', 'categories');
        }
        xarResponse::Redirect($data['return_url']);
        return true;
    }

    $data['tabblocks'] = xarMod::apiFunc('crispbb', 'admin', 'getmenulinks',
        array('func' => 'deletecat', 'layout' => 'tabblocks'));

    $tabblock = array();
    $tabblock['categories'] = array(
        'url' => xarModURL('crispbb', 'admin', 'categories'),
        'label' => xarML('View Categories'),
        'title' => xarML('View/Manage Forum Categories'),
        'active' => true
    );
    $tabblock['newcat'] = array(
        'url' => xarModURL('crispbb', 'admin', 'newcat'),
        'label' => xarML('Add Category'),
        'title' => xarML('Add a new forum category'),
        'active' => false
    );
    $tabblock['mastercat'] = array(
        'url' => xarModURL('crispbb', 'admin', 'categories', array('sublink' => 'mastercat')),
        'label' => xarML('Base Category'),
        'title' => xarML('Set Base Category for crispBB Forums'),
        'active' => false
    );
    $data['tabblocks'][] = $tabblock;

    $data['layout'] = 'confirm';
    $basecats = xarMod::apiFunc('categories','user','getallcatbases',array('module' => 'crispbb'));
    $basecid = count($basecats) > 0 ? $basecats[0]['category_id'] : null;

    $data['basecid'] = $basecid;
    $data['basecatinfo'] = $basecats[0];
    return $data;

}
?>