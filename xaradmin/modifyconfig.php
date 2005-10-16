<?php
/**
 * xarLinkMe configuration
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarlinkme
 * @link http://xaraya.com/index.php/release/889.html
 * @author jojodee <jojodee@xaraya.com>
 */
/**
 * This is a standard function to modify the configuration parameters of the
 * module
 */
function xarlinkme_admin_modifyconfig()
{
     // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('AdminxarLinkMe')) return;
    if (!xarVarFetch('tab', 'str:1:100', $data['tab'], 'basicconfig', XARVAR_NOT_REQUIRED)) return;

    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();

    $data['itemsvalue']       = xarModGetVar('xarlinkme','itemsperpage');
    $data['shorturlschecked'] = xarModGetVar('xarlinkme', 'SupportShortURLs') ? true : false;
    $data['imagedir']         = xarModGetVar('xarlinkme','imagedir');
    $data['pagetitle']        = xarModGetVar('xarlinkme','pagetitle');
    $data['instructions']     = xarModGetVar('xarlinkme','instructions');
    $data['instructions2']    = xarModGetVar('xarlinkme','instructions2');
    $data['txtintro']         = xarModGetVar('xarlinkme','txtintro');
    $data['txtadlead']        = xarModGetVar('xarlinkme','txtadlead');
    $data['allowlinks']       = xarModGetVar('xarlinkme','allowlinking') ? true : false;
    $data['sitename']         = xarModGetVar('themes','sitename');
    $data['useAliasName']     = xarModGetVar('xarlinkme', 'useModuleAlias');
    $data['aliasname ']       = xarModGetVar('xarlinkme','aliasname');
    $data['usebanners']       = xarModGetVar('xarlinkme','activebanners') ? true : false;


    $exludedips = xarModGetVar('xarlinkme','excludedips');
    if (empty($exludedips)) {
          $ip = serialize('10.0.0.1');
          xarModSetVar('xarlinkme','excludedips', $ip);
    }
    $data['excludedips']       = unserialize(xarModGetVar('xarlinkme','excludedips'));
    $hooks = xarModCallHooks('module', 'modifyconfig', 'xarlinkme',
                       array('module' => 'xarlinkme'));
    if (empty($hooks)) {
        $data['hooks'] = array('categories' => xarML('You can assign base categories by enabling the categories hooks for xarLinkMe module'));
    } else {
        $data['hooks'] = $hooks;
    }
    
    if (!isset($data['tab'])) {
        $data['tab']='basicconfig';
    }
    // Return the template variables defined in this function
    return $data;
}

?>