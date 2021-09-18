<?php
/**
 * Wurfl Module
 *
 * @package modules
 * @subpackage wurfl module
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Call a test page
 *
 */
function wurfl_admin_test()
{
    if (!xarSecurity::check('ManageWurfl')) {
        return;
    }

    sys::import('modules.wurfl.wurfl_init');
    $wurflManager = wurfl_init();
    $data['wurflInfo'] = $wurflManager->getWURFLInfo();

    if (!xarVar::fetch('ua', 'str', $data['ua'], '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('mode', 'str', $data['mode'], 'performance', xarVar::NOT_REQUIRED)) {
        return;
    }
    $data['requestingDevice'] = xarMod::apiFunc('wurfl', 'user', 'get_device', ['ua' => $data['ua'], 'mode' => $data['mode']]);
    if (empty($data['ua'])) {
        $data['ua'] = $_SERVER['HTTP_USER_AGENT'];
    }
    return $data;
}
