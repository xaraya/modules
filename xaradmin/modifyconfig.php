<?php
/**
 * Purpose of file
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Weather Module
 * @link http://xaraya.com/index.php/release/662.html
 * @author Weather Module Development Team
 */

/**
 * @author Marc Lutolf
 */

    function weather_admin_modifyconfig()
    {
        // Security Check
        if (!xarSecurityCheck('AdminWeather')) return;
        if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
        if (!xarVarFetch('tab', 'str:1:100', $data['tab'], 'general', XARVAR_NOT_REQUIRED)) return;
        switch (strtolower($phase)) {
            case 'modify':
            default:
                switch ($data['tab']) {
                    case 'general':
                        if (!xarVarFetch('default_location', 'str', $data['default_location'],  xarModVars::get('weather', 'default_location'), XARVAR_NOT_REQUIRED)) return;
                        break;
                    default:
                        break;
                }

                break;

            case 'update':
                break;

        }
        $data['authid'] = xarSecGenAuthKey();
        return $data;
    }
?>
