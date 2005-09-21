<?php
/**
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage headlines module
 * @author John Cox
*/
function headlines_admin_modifyconfig()
{
    if(!xarSecurityCheck('AdminHeadlines')) return;
    $hooks = xarModCallHooks('module', 'modifyconfig', 'headlines', array('module' => 'headlines'));
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }
    $data['shorturlslabel']     = xarML('Enable short URLs?');
    $data['shorturlschecked']   = xarModGetVar('headlines', 'SupportShortURLs') ?   true : false;
    $data['magpiechecked']      = xarModGetVar('headlines', 'magpie') ?   true : false;
    $data['authid']             = xarSecGenAuthKey();
    $data['pubtypes']           = xarModAPIFunc('articles','user','getpubtypes');
    $data['importpubtype']      = xarModGetVar('headlines','importpubtype');
    return $data;
}
?>