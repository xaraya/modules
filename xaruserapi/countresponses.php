<?php
/**
 * Utility function to count the number of responses for a form
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage SiteContact Module
 * @link http://xaraya.com/index.php/release/890.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/**
 * Utility function to count the number of resonses for a form
 * 
 * @author jojodee
 * @returns integer
 * @return number of items held by this module
 * @raise DATABASE_ERROR
 */
function sitecontact_userapi_countresponses()
{
    if(!xarVarFetch('scid', 'int:0:', $scid, NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('responsetime',  'int:0:', $responsetime,  NULL, XARVAR_NOT_REQUIRED)) {return;}
   
    $bindvars=array();
    $where='';
    if (isset($scid)) {
        $where = 'WHERE xar_scid = ? ';
        $bindvars[]= $scid;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $sitecontactResponseTable = $xartable['sitecontact_response'];
    $query = "SELECT COUNT(1)
            FROM $sitecontactResponseTable
            $where";

    if (!empty($where)) {
        $result =& $dbconn->Execute($query,$bindvars);
    } else {
        $result =&$dbconn->Execute($query,array());
    }

    if (!$result) return;
    /* Obtain the number of items */
    list($numitems) = $result->fields;
    $result->Close();
    /* Return the number of items */
    return $numitems;
}
?>