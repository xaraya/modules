<?php

    function xarayatesting_user_view()
    {
        if (!xarSecurity::check('ReadXarayatesting')) {
            return;
        }
        if (!xarVar::fetch('name', 'str:1', $name, 'xarayatesting_tests', xarVar::NOT_REQUIRED)) {
            return;
        }
        $myobject = DataObjectMaster::getObject(['name' => $name]);
        $return_url = xarServer::getCurrentURL();
        return ['return_url'=>$return_url, 'object'=>$myobject];
    }
