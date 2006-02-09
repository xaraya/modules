<?php
/**
 * Subitems module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Subitems Module
 * @link http://xaraya.com/index.php/release/9356.html
 * @author Subitems Module Development Team
 */
function subitems_admin_ddobjectlink_edit($args)
{
    extract($args);

    // The subobject which is linked to a parent
    if(!xarVarFetch('objectid','int:1:',$subobjectid)) return;

    if(!xarVarFetch('confirm','str:0',$confirm,'',XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('reload','str:0',$reloaded,'',XARVAR_NOT_REQUIRED)) return;

    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('AdminSubitems')) return;

    if(!$ddobjectlink = xarModAPIFunc('subitems','user','ddobjectlink_get',array('objectid' => $subobjectid))) return;
    // nothing to see here
    if (empty($ddobjectlink)) return xarML('This item does not exist');

    $subobjectlink = $ddobjectlink[0];
    if($confirm or $reloaded)    {
        $result_array = xarVarBatchFetch(
                                         array('modid','str:1:','module'),
                                         array('itemtype','int:0:','itemtype'),
                                         array('template','str:0:','template'),
                                         array('sortby','str:0:','sortby'),
                                         array('sortmode','str:0:','sortmode')
                                        );
        if (empty($result_array['sortby']['value'])) {
            $result_array['sort'] = array('value' => array(),
                                          'error' => '');
        } else {
            $result_array['sort'] = array('value' =>
                                            array($result_array['sortby']['value'] => $result_array['sortmode']['value']),
                                          'error' => '');
        }
    }  else  {
        $result_array['no_errors'] = true;
        $result_array['module'] = array('value' => xarModGetIDFromName($subobjectlink['module']),'error' => '');
        $result_array['itemtype'] = array('value' => $subobjectlink['itemtype'],'error' => '');
        $result_array['template'] = array('value' => $subobjectlink['template'],'error' => '');
        $result_array['sort'] = array('value' => $subobjectlink['sort'],'error' => '');
    }

    $modInfo = xarModGetInfo($result_array['module']['value']);
    $result_array['module_name'] = $modInfo['name'];

    if(($result_array['no_errors'] == true) && !empty($confirm))    {
        if (!xarSecConfirmAuthKey()) return;


        if(!xarModAPIFunc('subitems','admin','ddobjectlink_update',array(
                "objectid" => $subobjectid,
                "module" => $modInfo['name'],
                "itemtype" => $result_array['itemtype']['value'],
                "template" => $result_array['template']['value'],
                "sort" => $result_array['sort']['value']
            ))) return;

        xarResponseRedirect(xarModURL('subitems','admin','ddobjectlink_view'));
        return true;
    }

    // get the Dynamic Object defined for this module (and itemtype, if relevant)
    $subobject = xarModAPIFunc('dynamicdata','user','getobject',
                             array('objectid' => $subobjectid,
                                     'status' => 1));
    if (!isset($subobject)) return;

    $data = xarModAPIFunc('subitems','admin','menu');
    $data = array_merge($result_array,$data);
    $data['properties'] =  $subobject->getProperties();
    $subobjectinfo = xarModAPIFunc('dynamicdata','user','getobjectinfo',
                                array('objectid' => $subobjectid));

    $data['label'] = xarML('Unknown');
    if (!empty($subobjectinfo)) $data['label'] = $subobjectinfo['label'];

    $data['objectid'] = $subobjectid;

    return $data;
}

?>
