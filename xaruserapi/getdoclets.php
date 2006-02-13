<?php
/**
 * Legislation Doclets
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage legis Module
 * @link http://xaraya.com/index.php/release/593.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/**
 * get the name and description of all doclets
 * @returns array
 */
function legis_userapi_getdoclets($args)
{
    extract($args);

    $doclets = array();

    if (count($doclets) > 0) {
        return $doclets;
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xarTables =& xarDBGetTables();
    $LegisDocletsTable = $xarTables['legis_doclets'];

    // Get item
    $query = "SELECT xar_did,
                   xar_dname,
                   xar_dlabel,
                   xar_dlabel2,
                   xar_ddef
            FROM $LegisDocletsTable";
    $bindvars=array();
    if (isset($did) && is_numeric($did)) {
        $whereis = ' WHERE xar_did = ?';
        $bindvars[]=$did;
        $query .=$whereis;
    }

    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;

    if ($result->EOF) {
        return $doclets;
    }
    while (!$result->EOF) {
        list($did, $dname,$dlabel,$dlabel2,$ddef) = $result->fields;
        $doclets[$did] = array('did' => (int)$did,
                                  'dname' => $dname,
                                  'dlabel'=> $dlabel,
                                  'dlabel2'=> $dlabel2,
                                  'ddef'   => $ddef);
        $result->MoveNext();
    }

    return $doclets;
}

?>
