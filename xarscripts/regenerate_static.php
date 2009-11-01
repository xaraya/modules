<?php

include 'includes/xarCache.php';
if (xarCache_init()) {
    define('XARCACHE_IS_ENABLED',1);
}
include 'includes/xarCore.php';
xarCoreInit(XARCORE_SYSTEM_ALL);
xarMod::apiFunc( 'xarcachemanager', 'admin', 'regenstatic');

?>
