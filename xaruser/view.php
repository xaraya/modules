<?php

    function xarayatesting_user_view()
    {
        if (!xarSecurityCheck('ReadXarayatesting')) return;
        if(!xarVarFetch('name', 'str:1', $name, 'xarayatesting_tests', XARVAR_NOT_REQUIRED)) {return;}
        $myobject = DataObjectMaster::getObject(array('name' => $name));
        $return_url = xarServerGetCurrentURL();
        return array('return_url'=>$return_url, 'object'=>$myobject);
    }

?>