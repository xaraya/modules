<?php
/**
   View items
   
   @param $itemtype - type of item that is being viewed (required)
   @param $startnum - id of item to start the page with (optional)
   @returns data used in a template
*/
function helpdesk_admin_view()
{
    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('edithelpdesk')) return;

    // Get Vars
    xarVarFetch('itemtype', 'int', $itemtype,  10, XARVAR_NOT_REQUIRED);
    xarVarFetch('startnum', 'int', $data['startnum'],  NULL, XARVAR_NOT_REQUIRED);
    $data['itemsperpage'] = xarModGetVar('helpdesk','itemsperpage');
    $data['itemtype'] = $itemtype;
    $modid = xarModGetIDFromName('helpdesk');

    if (empty($data['itemtype'])){
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'item type', 'admin', 'view', 'helpdesk');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }    
        
    // The Generic helpdesk Menu
    //$data = xarModAPIFunc('helpdesk','admin','menu');

    // Get objects to build tabs    
    $objects = xarModAPIFunc('dynamicdata', 'user', 'getObjects');        
    $data['objects'] = array();
    foreach($objects as $object){
        if($object['moduleid'] == $modid){           
            $data['objects'][] = $object;
        }
    }
    
    // Return the template variables defined in this function
    return $data;
}
?>