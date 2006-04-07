<?php
/**
 * Categories module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Categories Module
 * @link http://xaraya.com/index.php/release/147.html
 * @author Categories module development team
 */
/**
 * Handle <xar:categories-navigation ...> navigation tags
 * Format : <xar:categories-navigation layout="..." module="..." ... />
 *
 * @param $args array containing the requested layout, optional categories etc.
 * @return string with the PHP code needed to invoke shownavigation() in the BL template
 */
function categories_userapi_navigationTag($args)
{
    // FIXME: MrB Does the wrapping of xarModAPILoad have any consequences for this?
    $out = "xarModAPILoad('categories','user');
echo xarModAPIFunc('categories',
                   'user',
                   'navigation',
                   array(\n";
    foreach ($args as $key => $val) {
        if (is_numeric($val) || substr($val,0,1) == '$') {
            $out .= "                         '$key' => $val,\n";
        } else {
            $out .= "                         '$key' => '$val',\n";
        }
    }
    $out .= "                         ));";
    return $out;
}

?>
