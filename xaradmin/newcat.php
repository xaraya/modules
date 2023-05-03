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
 * Function to do add a new forum category
 *
 * @author crisp <crisp@crispcreations.co.uk>
 *
 * @param array $args
 * @param string $phase current display phase, default form
 * @param string $return_url url to return to on successful update
 * @param integer $repeat number of new categories to show inputs for, default 1
 * @param bool $reassign flag to indicate multiple new categories
 * @return mixed array on form display or invalid input, true on successful category creation
 */
function crispbb_admin_newcat($args)
{
    if (!xarSecurity::check('AdminCrispBB') || !xarSecurity::check('ManageCategories'))
        return xarTpl::module('privileges','user','errors',array('layout' => 'no_privileges'));

    extract($args);

    $data = array();
    if (!xarVar::fetch('phase', 'pre:trim:lower:str:1', $phase, 'form', xarVar::NOT_REQUIRED)) return;
    if (!xarVar::fetch('return_url',  'isset',  $data['return_url'], NULL, xarVar::DONT_SET)) {return;}
    if (!xarVar::fetch('repeat','int:1:', $data['repeat'], 1, xarVar::NOT_REQUIRED)) {return;}
    if (!xarVar::fetch('reassign', 'checkbox',  $reassign, false, xarVar::NOT_REQUIRED)) return;

    sys::import('modules.dynamicdata.class.objects.master');
    for ($i=1;$i<=$data['repeat'];$i++) {
        $data['objects'][$i] = DataObjectMaster::getObject(array('name' => xarModVars::get('categories','categoriesobject'), 'fieldprefix' => $i));
    }

    if ($phase == 'update' && !$reassign) {
        for ($i=1;$i<=$data['repeat'];$i++) {
            if (!$data['objects'][$i]->checkInput()) {
                $invalid = true;
            }
        }
        if (empty($invalid)) {
            if (!xarSec::confirmAuthKey()) {
                return xarTpl::module('privileges','user','errors',array('layout' => 'bad_author'));
            }
            for ($i=1;$i<=$data['repeat'];$i++) {
                $data['objects'][$i]->createItem();
            }
            if (empty($data['return_url'])) {
                $data['return_url'] = xarController::URL('crispbb', 'admin', 'categories');
            }
            xarController::redirect($data['return_url']);
            return true;
        }
    }

    $data['menulinks'] = xarMod::apiFunc('crispbb', 'admin', 'getmenulinks',
        array(
            'current_module' => 'crispbb',
            'current_type' => 'admin',
            'current_func' => 'newcat',
        ));

    $basecats = xarMod::apiFunc('crispbb','user','getcatbases');
    $basecid = count($basecats) > 0 ? $basecats[0] : 0;

    $data['basecid'] = $basecid;
    $data['basecatinfo'] = !empty($basecid) ? $basecats[0] : array();
    $data['authid'] = xarSec::genAuthKey();

    return $data;

}
?>