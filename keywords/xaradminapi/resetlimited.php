<?php
/*
 *
 * Keywords Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @author mikespub
*/

//inserire controllo sicurezza
function keywords_adminapi_resetlimited($args)
{
   if (!xarSecurityCheck('AdminKeywords')) return;
    
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    
    $keywordstable = $xartable['keywords_restr'];

    $query = "DELETE FROM $keywordstable";

    $result =& $dbconn->Execute($query);
    
    if (!$result) {
    return ;
}
return true;
}

?>