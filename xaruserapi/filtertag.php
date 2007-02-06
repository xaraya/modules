<?php
/**
 * Categories module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Categories Module
 * @link http://xaraya.com/index.php/release/147.html
 * @author Categories module development team
 */
/**
 * Handle <xar:categories-filter ...> filter tags
 * Format : <xar:categories-filter basecids="..." layout="..." module="..." ... />
 *
 * @param $args array containing the requested layout, optional categories etc.
 * @return string with the PHP code needed to invoke showfilter() in the BL template
 */
function categories_userapi_filtertag($args)
{
    // FIXME: MrB Does the wrapping of xarModAPILoad have any consequences for this?
    $out = "xarModAPILoad('categories','user');
echo xarModAPIFunc('categories',
                   'user',
                   'showfilter',
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
