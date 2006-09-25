<?php
/**
 *  Common UI function to wrap common info objects rendering
 *
 * @param string objectname The object which contains the definition (and data)
 * @author Marcel van der Boom <marcel@xaraya.com>
 *
 */
function commerce_admin_commoninfo_object($args = array() )
{
    /*
        These three are from the older pages, we probably want to change these

        For the common info pages, we probably want to be able to inject the following
        for better access:
        - itemid   : which itemid should be selected/active/visible etc.
        - startnum : where should the listing start? [DONE]
    */
    if(!xarVarFetch('action', 'str',  $action, NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('page',   'int',  $page, 1, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('cID',    'int',  $cID, NULL, XARVAR_DONT_SET)) {return;}
    extract($args);

    // Do we still need this here?
    $localeinfo = xarLocaleGetInfo(xarMLSGetSiteLocale());
    $data['language'] = $localeinfo['lang'] . "_" . $localeinfo['country'];
    $data['cInfo'] = isset($cInfo) ? get_object_vars($cInfo) : '';
    $data['page'] = $page;
    $data['action'] = $action;

    // Retrieve the object info from DD
    $objectInfo = xarModApiFunc('dynamicdata','user','getobjectinfo', array('name' => $objectname));
    if(!$objectInfo) {
        // NOT FOUND, (obviously this must be less dramatic than this eventually)
        die('ICE object not found!!!');
    }
    $data['fieldlist'] = isset($fieldlist) ? $fieldlist : '';
    $data['moduleid'] = $objectInfo['moduleid'];
    $data['itemtype'] = $objectInfo['itemtype'];
    $data['objectlabel'] = xarML($objectInfo['label']); // What sort of effect does this have?
    // TODO: get the first item, not itemid 1, that might not even exist.
    $data['itemid'] = isset($cId) ? $cId : 0;
    $data['tplmodule'] = isset($tplmodule) ? $tplmodule : 'dynamicdata';


    $data['itemsperpage'] = xarModVars::get('commerce', 'itemsperpage');
    $data['use_grouping'] = isset($use_grouping) ? $use_grouping : false;
    $data['group_field'] = isset($group_field) ? $group_field : null;
    $data['group_value'] = isset($group_value) ? $group_value : null;
    return $data;
}
?>
