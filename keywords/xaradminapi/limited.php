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

//gestire errore su inserted
function keywords_adminapi_limited($args)
{

extract($args);
if (!xarSecurityCheck('AdminKeywords')) return;
$invalid = array();
    if (!isset($moduleid) || !is_numeric($moduleid)) {
        $invalid[] = 'moduleid';
    } 
    if (!isset($keyword) || !is_string($keyword)) {
        $invalid[] = 'keyword';
    } 
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'update limited', 'Keywords');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    } 

 
 $key = xarModAPIFunc('keywords',
                         'admin',
                         'separekeywords',
                          array('keywords' => $keyword));
 
 foreach ($key as $keyres) {
   $keyres = trim($keyres);     
        
 $dbconn =& xarDBGetConn();
 $xartable =& xarDBGetTables(); 
 $keywordstable = $xartable['keywords_restr'];
 

 $nextId = $dbconn->GenId($keywordstable);
 $query = "INSERT INTO $keywordstable (
              xar_id,
              xar_keyword,
              xar_moduleid)
              VALUES (
              $nextId,
              '" . xarVarPrepForStore($keyres) . "',
              " . xarvarPrepForStore($moduleid) . ")";
    $result = &$dbconn->Execute($query); 
    if (!$result) return; 
}
 return;

}
?>