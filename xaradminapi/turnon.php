<?php

/*
 * Turn Logging on
 * @author Flavio Botelho <nuncanada@xaraya.com>
 */

function logconfig_adminapi_turnon()
{
    if (!xarModAPIFunc('logconfig','admin','saveconfig')) return false;
    
    return true;
}

?>