<?php
/**
 * Headlines - Generates a list of feeds
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage headlines module
 * @link http://www.xaraya.com/index.php/release/777.html
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