<?php

    function xarayatesting_scans_modules_to_remove()
    {
        $data['items'] = xarMod::apiFunc('modules', 'admin', 'getlist', array('filter' => array('State' => XARMOD_STATE_MISSING_FROM_ACTIVE)));
        return $data; 
    }

?>