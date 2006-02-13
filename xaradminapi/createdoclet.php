<?php
/**
 * Legis create doclet
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Legis Module
 * @link http://xaraya.com/index.php/release/593.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

function legis_adminapi_createdoclet($args)
{
    // Get arguments from argument array
    extract($args);


    $invalid = array();
    if (!isset($dname) || !is_string($dname) || empty($dname)) {
        $invalid[] = 'dname';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'admin', 'createdoclet','Legis');
         xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }


    $dname = strtolower($dname);
     // Security check - we require ADMIN rights here
    if (!xarSecurityCheck('AdminLegis')) return;

    if (!xarModAPILoad('legis', 'user')) return;

    if (!isset($ddef) || ($ddef=='')) {
        $ddef =' ';
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xarTables =& xarDBGetTables();
    $LegisDocletsTable = $xarTables['legis_doclets'];

    // Get next ID in table
    $nextId = $dbconn->GenId($LegisDocletsTable);

    // Insert the publication type
    $query = "INSERT INTO $LegisDocletsTable
              (xar_did, xar_dname, xar_dlabel, xar_dlabel2, xar_ddef)
            VALUES (?,?,?,?,?)";
    $bindvars = array($nextId, $dname, $dlabel, $dlabel2, $ddef);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;

    // Get ptid to return
    $did = $dbconn->PO_Insert_ID($LegisDocletsTable, 'xar_did');

     return $did;
}

?>
