<?php

function opentracker_userapi_set_instanceid($args) {
    extract($args);
    if ((!isset($instanceid)) && (is_array($args) && isset($args[0])))
        $instanceid = $args[0];
    if (isset($instanceid))
        xarVarSetCached('opentracker', 'xarinstanceid', $instanceid);
}
    

?>