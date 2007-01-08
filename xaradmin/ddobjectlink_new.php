<?php
/**
 * Subitems module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Subitems Module
 * @link http://xaraya.com/index.php/release/9356.html
 * @author Subitems Module Development Team
 */
function subitems_admin_ddobjectlink_new($args)
{
    extract($args);

    if(!xarVarFetch('confirm','str:1',$confirm,'')) return;
    if(!xarVarFetch('reload','str:1',$reloaded,'')) return;

    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('AdminSubitems')) return;

    if($confirm or $reloaded)    {
        $result_array = xarVarBatchFetch(
            array('objectid','int:1','objectid'),
            array('modid','str:1:','module'),
            array('itemtype','int:0:','itemtype'),
            array('template','str:0:','template')
        );

    } else {
        $result_array['no_errors'] = true;
        $result_array['module'] = array('value' => 182,'error' => '');
        $result_array['itemtype'] = array('value' => 0,'error' => '');
        $result_array['template'] = array('value' => '','error' => '');
        $result_array['objectid'] = array('value' => '','error' => '');
    }

    $modInfo = xarModGetInfo($result_array['module']['value']);
    $result_array['module_name'] = $modInfo['name'];

    // if(!xarVarFetch('objectid','int:1:',$objectid)) return;
    if(($result_array['no_errors'] == true) && !empty($confirm))    {
        if (!xarSecConfirmAuthKey()) return;

        if(!xarModAPIFunc('subitems','admin','ddobjectlink_create',array(
                "objectid" => $result_array['objectid']['value'],
                "module" => $modInfo['name'],
                "itemtype" => $result_array['itemtype']['value'],
                "template" => $result_array['template']['value']
            ))) return;

        xarResponseRedirect(xarModURL('subitems','admin','ddobjectlink_view'));
        return true;
    }

    $data = xarModAPIFunc('subitems','admin','menu');
    $data = array_merge($result_array,$data);
    return $data;
}

?>
