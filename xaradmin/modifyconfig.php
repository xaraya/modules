<?php
/**
 * Xaraya POP3 Gateway
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage pop3gateway Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author John Cox
 */
/**
 * Modify this module's configuration
 * 
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