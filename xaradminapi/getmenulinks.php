<?php
/**
 * Purpose of file
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Weather Module
 * @link http://xaraya.com/index.php/release/662.html
 * @author Weather Module Development Team
 */

/**
 * @author Roger Raymond
 */
function &weather_adminapi_getmenulinks()
{

    $menulinks = '';
    
    $menulinks[] = Array(
        'url'=>xarModURL('weather','admin','modifyconfig'),
        'title'=>xarML('Modify the configuration for weather'),
        'label'=>xarML('Modify Config')
        );
    
    return $menulinks;
}
?>
