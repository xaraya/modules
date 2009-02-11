<?php
/**
 * Publications module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Publications Module
 
 * @author mikespub
 */
/**
 * Handle <xar:publications-field ...> form field tags
 * Format : <xar:publications-field definition="$definition" /> with $definition an array
 *                                             containing the type, name, value, ...
 *       or <xar:publications-field name="thisname" type="thattype" value="$val" ... />
 *
 * @param $args array containing the form field definition or the type, name, value, ...
 * @return string The PHP code needed to invoke showfield() in the BL template
 * @TODO : move this to some common place in Xaraya (base module ?)
 */
function publications_userapi_handlefieldtag($args)
{
    $out = "xarModAPILoad('publications','user');
echo xarModAPIFunc('publications',
                   'user',
                   'showfield',\n";
    if (isset($args['definition'])) {
        $out .= '                   '.$args['definition']."\n";
        $out .= '                  );';
    } else {
        $out .= "                   array(\n";
        foreach ($args as $key => $val) {
            if (is_numeric($val) || substr($val,0,1) == '$') {
                $out .= "                         '$key' => $val,\n";
            } else {
                $out .= "                         '$key' => '$val',\n";
            }
        }
        $out .= "                         ));";
    }
    return $out;
}

?>
