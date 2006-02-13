<?php
/**
 * Legis Master Document Management
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
 * Delete Master type
 *
 * @param $args['mdid'] ID of the itemtype
 * @returns bool
 * @return true on success, false on failure
 */
function legis_adminapi_deletemastertype($args)
{
    // Get arguments from argument array
    extract($args);

     if (!isset($mdid) || !is_numeric($mdid) || $mdid < 1) {
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
    $mastertypes = xarModAPIFunc('legis','user','getmastertypes');
    if (!isset($mastertypes[$mdid])) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'publication type ID', 'admin', 'deleteitype',
                    'Legis');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }

   //delete hooks
    $item['module'] = 'legis';
    $item['itemtype']=$mdid;
    xarModCallHooks('item', 'delete', $mdid, $item);


    // Get database setup
    $dbconn =& xarDBGetConn();
    $xarTables =& xarDBGetTables();
    $LegisMasterTable = $xarTables['legis_master'];

    // Delete the publication type
    $query = "DELETE FROM $LegisMasterTable
            WHERE xar_mdid = ?";
    $result =& $dbconn->Execute($query,array($mdid));
    if (!$result) return;

    $LegisMasterTable = $xarTables['legis_master'];

    // Delete all legis for this publication type
  /*
    $query = "DELETE FROM $legistable
            WHERE xar_mdid = ?";
    $result =& $dbconn->Execute($query,array($mdid));
    if (!$result) return;
  */
// TODO: call some kind of itemtype delete hooks here, once we have those
 //   xarModCallHooks('itemtype', 'delete', $mdid,
//                    array('module' => 'legis',
//                          'itemtype' =>$mdid));

    return true;
}

?>
