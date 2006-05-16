<?php
/**
 * Add URL Form
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage window
 * @link http://xaraya.com/index.php/release/3002.html
 * @author Marc Lutolf
 * @author Yassen Yotov (CyberOto)
 */
 
/**
 * Add URL Form
 *
 * @return array $data template array values
 */ 
function window_admin_addurl()
{
    if (!xarSecurityCheck('AdminWindow')) return;

    $data = array();
    
    $data['authid'] = xarSecGenAuthKey();
    $data['action'] = xarModURL('window', 'admin', 'newurl');
    $data['window_status'] = 'add';
    $data['urls'] = xarModAPIFunc('window','admin','geturls');
    $data['id'] = '';
    $data['host'] = 'http://';
    $data['alias'] = '';
    $data['reg_user_only'] = xarModGetVar('window', 'reg_user_only');
    $data['open_direct'] = xarModGetVar('window', 'open_direct');
    $data['use_fixed_title'] = xarModGetVar('window', 'use_fixed_title');
    $data['auto_resize'] = xarModGetVar('window', 'auto_resize');
    $data['vsize'] = xarModGetVar('window', 'vsize');
    $data['hsize'] = xarModGetVar('window', 'hsize');
    $data['lang_action'] = xarML('Add');

    return xarTplModule('window','admin','url',$data);
}
?>