<?php
/**
 * Xaraya Google Search
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Google Search Module
 * @link http://xaraya.com/index.php/release/809.html
 * @author John Cox
 */
/**
 * modify configuration
 */
function googlesearch_admin_modifyconfig()
{
    // Security Check
  if(!xarSecurityCheck('Admingooglesearch')) return;
    $hooks = xarModCallHooks('module', 'modifyconfig', 'googlesearch', array('module' => 'googlesearch'));
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }
    $data['createlabel'] = xarML('Submit');
    $data['authid'] = xarSecGenAuthKey();
    return $data;
}
?>