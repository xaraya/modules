<?php
/**
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage headlines module
 * @author John Cox
*/
function headlines_admin_new()
{
    
    // Security Check
    if(!xarSecurityCheck('AddHeadlines')) return;
    $item = array();

    $item['module'] = 'headlines';
    $item['itemtype'] = NULL; // forum
    $hooks = xarModCallHooks('item','new','',$item);

    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }
    
    $data['submitlabel'] = xarML('Submit');
    $data['authid'] = xarSecGenAuthKey();

    // Return the output
    return $data;
}
?>
