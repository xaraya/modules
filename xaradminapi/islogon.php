<?php

/**
 * Is log currently on? 
 */
function logconfig_adminapi_islogon()
{
    return xarLogConfigReadable();    
}

?>