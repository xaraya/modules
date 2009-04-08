<?php

    function xarayatesting_scans_modules_to_upgrade()
    {
        $data['items'] = xarModAPIFunc('modules', 'admin', 'getlist', array('filter' => array('State' => XARMOD_STATE_UPGRADED)));
        return $data; 
    }

?>