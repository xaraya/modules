<?php

/*
 * Turn Security Caching on
 * @author Flavio Botelho <nuncanada@xaraya.com>
 */

function cachesecurity_adminapi_turnon()
{
    $filename = xarModAPIFunc('logconfig','admin','filename', array('part'=>'general'));

    if (!touch($filename)) return false;
  
     return true;
}

?>