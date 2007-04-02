<?php
/**
 * @package Commerce
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage Commerce
 * @author Marc Lutolf <mfl@netspan.ch>
*/


/**
 * Fancy pointer
 */
function commerce_admin_xmlhttp_proxy($args)
{
	extract($args);
	var_dump($args);
    $module = isset($module) ? $module : 'dynamicdata';
    $type = isset($type) ? $type : 'user';
    $func = isset($func) ? $func : 'display';
    $mode = isset($mode) ? $mode : '';
    echo xarModURL($module,$type,$func,$args);exit;
    xarResponseRedirect(xarModURL($module,$type,$func,$args));
    return true;
    if (empty($mode)) {
		return xarModFunc($module,$type,$func,$args);
    } else {
		return xarModAPIFunc($module,$type,$func,$args);
    }
}
?>