<?
function gmaps_admin_create()
{
    if (!xarVarFetch('itemid',      'isset', $itemid,     0,    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('return_url',  'isset', $return_url, NULL, XARVAR_DONT_SET)) {return;}

// FIXME: can't use this as long as we don't know what the current module is
//    if (!xarSecConfirmAuthKey()) return;

    $objinfo = xarModAPIFunc('dynamicdata','user','getobjectinfo',array('name' => 'gmaps_locations'));
    $myobject = & Dynamic_Object_Master::getObject(array('moduleid' => $objinfo['moduleid'],
                                         				 'itemtype' => $objinfo['itemtype'],
                                         				 'itemid' => $itemid,
                                         				 'fieldlist' => array('longitude','latitude','longitude2','latitude2')));
    $isvalid = $myobject->checkInput();

    $itemid = $myobject->createItem();
    if (empty($itemid)) return;

    if (!empty($return_url)) {
        xarResponseRedirect($return_url);
    } else {
        xarResponseRedirect(xarModURL('gmaps', 'user', 'manage'));
    }
    return true;
}
?>