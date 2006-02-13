<?php
/**
 * Legis Doclet Management
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
/**
 * Delete Doclet
 *
 * @param $args['did'] ID of the itemtype
 * @returns bool
 * @return true on success, false on failure
 */
function legis_adminapi_deletedoclet($args)
{
    // Get arguments from argument array
    extract($args);

     if (!isset($did) || !is_numeric($did) || $did < 1) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'Item type ID', 'admin', 'deleteitype',
                    'Legis');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }

    // Security check - we require ADMIN rights here
    if (!xarSecurityCheck('AdminLegis')) return;

    // Load user API to obtain item information function
    if (!xarModAPILoad('legis', 'user')) return;

    // Get current publication types
    $doclets = xarModAPIFunc('legis','user','getdoclets');
    if (!isset($doclets[$did])) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'publication type ID', 'admin', 'deletedoclet',
                    'Legis');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xarTables =& xarDBGetTables();
    $LegisDocletsTable = $xarTables['legis_doclets'];

    // Delete the publication type
    $query = "DELETE FROM $LegisDocletsTable
            WHERE xar_did = ?";
    $result =& $dbconn->Execute($query,array($did));
    if (!$result) return;

    $LegisDocletsTable = $xarTables['legis_doclets'];

    // Delete all legis for this publication type
  /*
    $query = "DELETE FROM $legistable
            WHERE xar_did = ?";
    $result =& $dbconn->Execute($query,array($did));
    if (!$result) return;
  */
// TODO: call some kind of itemtype delete hooks here, once we have those
 //   xarModCallHooks('itemtype', 'delete', $did,
//                    array('module' => 'legis',
//                          'itemtype' =>$did));

    return true;
}

?>
