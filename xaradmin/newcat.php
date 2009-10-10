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
    if (!xarSecurityCheck('AdminCrispBB') || !xarSecurityCheck('ManageCategories'))
        return xarTplModule('privileges','user','errors',array('layout' => 'no_privileges'));

    extract($args);

    $data = array();
    if (!xarVarFetch('phase', 'pre:trim:lower:str:1', $phase, 'form', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('return_url',  'isset',  $data['return_url'], NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('repeat','int:1:', $data['repeat'], 1, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('reassign', 'checkbox',  $reassign, false, XARVAR_NOT_REQUIRED)) return;

    sys::import('modules.dynamicdata.class.objects.master');
    for ($i=1;$i<=$data['repeat'];$i++) {
        $data['objects'][$i] = DataObjectMaster::getObject(array('name' =>          xarModVars::get('categories','categoriesobject'), 'fieldprefix' => $i));
    }

    if ($phase == 'update' && !$reassign) {
        for ($i=1;$i<=$data['repeat'];$i++) {
            if (!$data['objects'][$i]->checkInput()) {
                $invalid = true;
            }
        }
        if (empty($invalid)) {
            if (!xarSecConfirmAuthKey()) {
                return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
            }
            for ($i=1;$i<=$data['repeat'];$i++) {
                $data['objects'][$i]->createItem();
            }
            if (empty($data['return_url'])) {
                $data['return_url'] = xarModURL('crispbb', 'admin', 'categories');
            }
            xarResponse::Redirect($data['return_url']);
            return true;
        }
    }

    $data['menulinks'] = xarMod::apiFunc('crispbb', 'admin', 'getmenulinks',
        array(
            'current_module' => 'crispbb',
            'current_type' => 'admin',
            'current_func' => 'newcat',
        ));

    $basecats = xarMod::apiFunc('categories','user','getallcatbases',array('module' => 'crispbb'));
    $basecid = count($basecats) > 0 ? $basecats[0]['category_id'] : null;

    $data['basecid'] = $basecid;
    $data['basecatinfo'] = !empty($basecid) ? $basecats[0] : array();
    $data['authid'] = xarSecGenAuthKey();

    return $data;

}
?>