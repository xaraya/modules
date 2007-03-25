<?php

sys::import('modules.xen.xarclasses.xenquery');

function vendors_user_validate()
{
    if(!xarVarFetch('objectname',     'str:1', $objectname,      'vendors_vendors', XARVAR_DONT_SET)) {return;}
    $myobject = xarModApiFunc('dynamicdata','user','getobject', array('name' => $objectname));
    $isvalid = $myobject->checkInput();
    $properties = $myobject->getProperties();
    $q = new xenQuery('SELECT');
    foreach ($properties as $property) {
        if ($property->getDisplayStatus() != DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE) continue;
        $value = $property->getValue();
        if (!empty($value)) $q->like($property->source,strtolower("%$value%"));
    }
    if (1==1)               $q->eq("state",1);
    $q->setrowstodo(xarModVars::get('vendors','itemsperpage'));
    $q->setorder('name');
//    $q->qecho();

    if ($isvalid) {
        sys::import('modules.vendors.class.vendor');
        $vendor = new Vendor();
        $vendor->setcurrentquery($q);

        xarResponseRedirect(xarModURL('vendors', 'user', 'view', array('objectname' => $objectname)));
    } else {
        $args = $myobject->getFieldValues();
        $args['objectname'] = $objectname;
        xarResponseRedirect(xarModURL('vendors', 'user', 'filter',$args));
    }
    return true;
}
?>