<?php
/**
 * Function calling compilebody API to compile html page types body contents.
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 * @subpackage Xarpages Module
 * @link link-placeholder
 * @author Param Software Services <paramsoft@eth.net>
 * @returns true.
 */
function xarpages_funcapi_html($args)
{
    if ((isset($args['current_page']['dd']['body_type']) && $args['current_page']['dd']['body_type'] == 2)) {

        $data['body'] = $args['current_page']['dd']['body'];
        $data['data'] = $args;

        $args['current_page']['dd']['body'] = xarMod::apiFunc('xarpages', 'user', 'compilebody',$data);
    }
    return true;
}
?>