<?php
function twitter_hooks_moduleupdateconfig($args)
{
    extract($args);

    if (!isset($extrainfo) || !is_array($extrainfo)) {
        $extrainfo = array();
    }
    
    return $extrainfo;
}
?>