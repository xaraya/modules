<?php
function hookbridge_debug ($modname, $modid, $extrainfo)
{
    ob_start();
    echo $modname."\n";
    echo $modid."\n";
    print_r($extrainfo);
    $arg_dump = ob_get_contents();
    ob_end_clean();
    
    mail( xarModGetVar('mail', 'adminmail'), "HookBridgeDebug", $arg_dump);
    
    return $extrainfo;
}
?>