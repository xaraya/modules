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
 * Function to do something
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * What this function does
 *
 * @return array
 */
function crispbb_admin_modifycat($args)
{
    if (!xarSecurityCheck('AdminCrispBB') || !xarSecurityCheck('ManageCategories'))
        return xarTplModule('privileges','user','errors',array('layout' => 'no_privileges'));

    extract($args);

    $data = array();
    if (!xarVarFetch('phase', 'pre:trim:lower:str:1', $phase, 'form', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('return_url',  'pre:trim:lower:str:1',  $data['return_url'], '', XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('itemid', 'int:1', $data['itemid'], 0, XARVAR_NOT_REQUIRED)) return;

    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(array('name' => xarModVars::get('categories','categoriesobject')));
    $data['object']->getItem(array('itemid' => $data['itemid']));

    if ($phase == 'update') {
        $isvalid = $data['object']->checkInput();
        if ($isvalid) {
            if (!xarSecConfirmAuthKey()) {
                return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
            }
            $itemid = $data['object']->updateItem(array('itemid' => $data['itemid']));
            if (empty($data['return_url'])) {
                $data['return_url'] = xarModURL('crispbb', 'admin', 'categories');
            }
            xarResponse::Redirect($data['return_url']);
            return true;
        }
    }

    $data['category'] = $data['object']->getFieldValues();
    $data['menulinks'] = xarMod::apiFunc('crispbb', 'admin', 'getmenulinks',
        array(
            'current_module' => 'crispbb',
            'current_type' => 'admin',
            'current_func' => 'modifycat',
        ));

    return $data;

}
?>