<?php
/**
 * @package modules
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage content
 * @link http://xaraya.com/index.php/release/1015.html
 */
/**
 * utility function to retrieve the list of item types of this module (if any)
 * *** Not sure if this works ***
 */
function content_userapi_getitemtypes($args)
{
    return xarMod::apiFunc('dynamicdata','user','getmoduleitemtypes',array('moduleid' => 1015, 'native' =>false));
}
?>
