<?php
/**
 * Create a new item of the foo object
 *
 */
    function foo_admin_new()
    {
        if (!xarSecurityCheck('AddFoo')) return;

        if (!xarVarFetch('name',       'str',    $name,   'foo_foo', XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('confirm',    'isset',  $confirm, null, XARVAR_DONT_SET)) return;

        $data['object'] = DataObjectMaster::getObject(array('name' => $name));
        $data['tplmodule'] = 'foo';
        $data['authid'] = xarSecGenAuthKey('dynamicdata');

        if (isset($confirm)) {
            $isvalid = $data['object']->checkInput();
            if (!$isvalid) {
                return xarTplModule('foo','user','new', $data);        
            } else {
                $item = $data['object']->createItem();
                xarResponseRedirect(xarModURL('foo','admin','view'));
            }
        }
        return $data;
    }
?>