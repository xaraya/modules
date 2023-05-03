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
    if (!xarSecurity::check('AdminCrispBB'))
        return xarTpl::module('privileges','user','errors',array('layout' => 'no_privileges'));

    extract($args);

    $data = array();
    if (!xarVar::fetch('itemid', 'int:1', $data['itemid'], 0, xarVar::NOT_REQUIRED)) return;
    if (!xarVar::fetch('confirm', 'checkbox', $confirm, false, xarVar::NOT_REQUIRED)) return;
    if (!xarVar::fetch('return_url',  'pre:trim:lower:str:1',  $data['return_url'], '', xarVar::NOT_REQUIRED)) return;
    // secondary sec check on categories module
    if(!xarSecurity::check('ManageCategories', 0, "All:$data[itemid]")) {
         return xarTpl::module('privileges','user','errors',array('layout' => 'no_privileges'));
    }
    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(array('name' => xarModVars::get('categories','categoriesobject')));
    $data['object']->getItem(array('itemid' => $data['itemid']));

    if ($confirm) {
        if (!xarSec::confirmAuthKey()) {
            return xarTpl::module('privileges','user','errors',array('layout' => 'bad_author'));
        }
        $data['object']->deleteItem(array('itemid' => $data['itemid']));
        if (empty($data['return_url'])) {
            $data['return_url'] = xarController::URL('crispbb', 'admin', 'categories');
        }
        xarController::redirect($data['return_url']);
        return true;
    }

    $data['menulinks'] = xarMod::apiFunc('crispbb', 'admin', 'getmenulinks',
        array(
            'current_module' => 'crispbb',
            'current_type' => 'admin',
            'current_func' => 'deletecat',
        ));

    $data['layout'] = 'confirm';
    $basecats = xarMod::apiFunc('crispbb','user','getcatbases');
    $basecid = count($basecats) > 0 ? $basecats[0] : 0;

    $data['basecid'] = $basecid;
    $data['basecatinfo'] = $basecats[0];
    return $data;

}
?>