<?php
/**
 * Security - Provides unix style privileges to xaraya items.
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Security Module
 * @author Brian McGilligan <brian@mcgilligan.us>
 */
/**
 * Wrapper for setting module hook settings.
 *
 * @deprecated  use Security::save instead
 */
function security_adminapi_set_hook_settings($args)
{
    extract($args);

    //if( empty($modid) ){ return false; }
    if( empty($settings) ){ return false; }

    $var_name = "settings";
    if( !empty($modid) ){ $var_name .= ".$modid"; }
    if( !empty($itemtype) ){ $var_name .= ".$itemtype"; }

    return xarModSetVar('security', $var_name, serialize($settings));
}
?>