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
function crispbb_admin_categories($args)
{
    if (!xarSecurity::check('AdminCrispBB') || !xarSecurity::check('ManageCategories')) {
        return xarTpl::module('privileges', 'user', 'errors', ['layout' => 'no_privileges']);
    }

    extract($args);
    if (!xarVar::fetch('sublink', 'pre:trim:lower:str:1', $sublink, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('phase', 'pre:trim:lower:str:1', $phase, 'form', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('confirm', 'checkbox', $confirm, false, xarVar::NOT_REQUIRED)) {
        return;
    }

    $data = [];
    $basecats = xarMod::apiFunc('crispbb', 'user', 'getcatbases');
    $basecid = count($basecats) > 0 ? $basecats[0] : 0;
    $data['options'] = [];
    if (!empty($basecid)) {
        $data['options'][] = ['id' => $basecid];
    }
    $data['sublink'] = $sublink;

    if ($sublink == 'mastercat') {
        sys::import('modules.dynamicdata.class.properties.master');
        $picker = DataPropertyMaster::getProperty(['name' => 'categorypicker']);
        $picker_basecat_config = serialize(
            [
                                        'initialization_include_no_cat' => 1,
                                        'initialization_include_all_cats' => 0,
                                        'initialization_basecategories' => [
                                                [
                                                    0 => 'Forum Dropdown',
                                                    1 => unserialize(xarModVars::get('crispbb', 'base_categories')),
                                                    2 => true,
                                                    3 => 1,
                                                ],
                                        ],
                                    ]
        );
        $data['column_configuration'] = serialize([
                            ["Forum Name",2,"New Forum",""],
                            ["Base Category",100,0,$picker_basecat_config],
                            ["Include Self",14,0,""],
                            ["Select Type",6,0,'a:3:{s:12:"display_rows";s:1:"0";s:14:"display_layout";s:7:"default";s:22:"initialization_options";s:62:"1,Single Dropdown;2,Multiple - One Box;3,Multiple - Two Boxes;";}'],
                            ]);
        if ($phase == 'update') {
            if (!$confirm) {
                // @TODO:
                //$data['sublink'] = 'confirm';
                //return $data;
            }
            // Confirm authorisation code.
            if (!xarSec::confirmAuthKey()) {
                return xarTpl::module('privileges', 'user', 'errors', ['layout' => 'bad_author']);
            }
            $isvalid = $picker->checkInput('basecid');
            if ($isvalid) {
                //$picker->createValue();
            }
            xarController::redirect(xarController::URL('crispbb', 'admin', 'categories', ['sublink' => 'mastercat']));
        }
        $data['authid'] = xarSec::genAuthKey();
    }
    $data['menulinks'] = xarMod::apiFunc(
        'crispbb',
        'admin',
        'getmenulinks',
        [
            'current_module' => 'crispbb',
            'current_type' => 'admin',
            'current_func' => 'categories',
            'current_sublink' => $sublink,
        ]
    );
    return $data;
}
