<?php

function labAccounting_admin_view()
{
    if(!xarVarFetch('active', 'isset', $active,  NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('accttype',   'str', $accttype,   '', XARVAR_NOT_REQUIRED)) return;
    
    $data = xarModAPIFunc('labAccounting','admin','menu');
    
    if (!xarSecurityCheck('AdminAccounting')) return;
    
    $where = "";
    if(!empty($active)) {
        $where .= "active eq 1";
    }
    if(!empty($accttype)) {
        $where .= "ledgertype eq '".$accttype."'";
    }
    
    if(empty($where)) $where = NULL;
    
    $object = xarModAPIFunc('dynamicdata','user','getitems',
                          array('module'    => 'labaccounting',
                                'itemtype'  => 5,
                                'numitems'  => -1,
                                'where'     => $where,
                                'startnum'  => 0,
                                'sort'    => 'accountnumstart',
                                'getobject' => 1));
                                
    if(count($object->items) == 0) {
        xarResponseRedirect(xarModURL('labaccounting','admin','populatechart'));
        return true;
    }

    $data['active'] = $active;                
    $data['object'] = &$object; 
    
    return $data;
}

?>