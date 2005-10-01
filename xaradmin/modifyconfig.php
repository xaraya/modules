<?php
/**
 * File: $Id$
 *
 * Xaraya POP3 Gateway
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage pop3gateway
 * @author John Cox
*/
function pop3gateway_admin_modifyconfig()
{
    if(!xarSecurityCheck('AdminPOP3Gateway')) return;
    $hooks = xarModCallHooks('module', 'modifyconfig', 'pop3gateway', array('module' => 'pop3gateway'));
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }
    $data['authid'] = xarSecGenAuthKey();
    $data['pubtypes'] = xarModAPIFunc('articles','user','getpubtypes');
    $data['importpubtype'] = xarModGetVar('pop3gateway','importpubtype');
    if (empty($data['importpubtype'])) {
        $data['importpubtype'] = xarModGetVar('articles','defaultpubtype');
        if (empty($data['importpubtype'])) {
            $data['importpubtype'] = 1;
        }
    }
    return $data;
}
?>