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
 * Overview function that displays the standard Overview page
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @return array
 */
function crispbb_admin_overview()
{
    // Admin only function
    if (!xarSecurity::check('AdminCrispBB')) {
        return;
    }

    $data = [];
    $data['menulinks'] = xarMod::apiFunc(
        'crispbb',
        'admin',
        'getmenulinks',
        [
            'current_module' => 'crispbb',
            'current_type' => 'admin',
            'current_func' => 'overview',
        ]
    );
    $modid = xarMod::getRegID('crispbb');
    $modinfo = xarMod::getInfo($modid);
    $data['version'] = $modinfo['version'];
    $pageTitle = xarML('Module Overview');
    $now = time();

    // store function name for use by admin-main as an entry point
    xarSession::setVar('crispbb_adminstartpage', 'overview');
    xarTpl::setPageTitle(xarVar::prepForDisplay($pageTitle));
    return $data;
}
