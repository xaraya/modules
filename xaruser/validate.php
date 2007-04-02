<?php

sys::import('modules.xen.xarclasses.xenquery');

function vendors_user_validate()
{
    if(!xarVarFetch('objectname',     'str:1', $objectname,      'vendors_vendors', XARVAR_DONT_SET)) {return;}
    $myobject = xarModApiFunc('dynamicdata','user','getobject', array('name' => $objectname));
    $isvalid = $myobject->checkInput();
    $properties = $myobject->getProperties();
    $q = new xenQuery('SELECT');

    $activestores = array();
    foreach ($properties as $property) {
        if ($property->getDisplayStatus() != DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE) continue;
        $value = $property->getValue();
        if (!empty($value)) $q->like($property->source,strtolower("%$value%"));
        $store = $property->getDataStore();
        $activestores[$store[0]] = $store[0];
    }
    if (1==1)               $q->eq("state",1);
    $q->setrowstodo(xarModVars::get('vendors','itemsperpage'));
    $q->setorder('name');

    $primaryindex = $myobject->properties[$myobject->primary]->source;
    foreach ($activestores as $activestore) {
        $q->addtable($activestore);
        $thisindex = $activestore . "." . $myobject->datastores[$activestore]->getPrimary();
        if ($thisindex != $primaryindex) $q->join($primaryindex,$thisindex);
    }

    if ($isvalid) {
        sys::import('modules.ledgerap.class.vendor');
        $vendor = new Vendor();
        $vendor->setcurrentquery($q);

        xarResponseRedirect(xarModURL('vendors', 'admin', 'view', array('objectname' => $objectname)));
    } else {
        $args = $myobject->getFieldValues();
        $args['objectname'] = $objectname;
        xarResponseRedirect(xarModURL('vendors', 'user', 'filter',$args));
    }
    return true;
}
?>