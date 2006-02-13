<?php
/**
 * Legis master document
 *
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
 * Update Master itemtype
 *
 * @param $args['mdid'] ID of the Document type
 * @param $args['mdname'] name of the document
 * @param $args['mdorder'] flexible or ordered and fixed.
 * @param $args['mddef'] definition
 * @returns bool
 * @return true on success, false on failure
 */
function legis_adminapi_updatemastertype($args)
{
    // Get arguments from argument array
    extract($args);

     $invalid = array();
    if (!isset($mdid) || !is_numeric($mdid) || $mdid < 1) {
        $invalid[] = 'Legis itemtype ID';
    }

    if (!isset($mdname) || !is_string($mdname) || empty($mdname)) {
        $invalid[] = 'mdname';
    }
   if (!isset($mddef)){// || !is_string($mddef) || empty($mddef)) {
        $invalid[] = 'mddef';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'admin', 'updatemastertype','Legis');
         xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }

    // Security check - we require ADMIN rights here
    if (!xarSecurityCheck('AdminLegis',1)) return;

    // Load user API to obtain item information function
    if (!xarModAPILoad('legis', 'user')) return;

    // Get current itemtypes
    $mastertypes = xarModAPIFunc('legis','user','getmastertypes');
    if (!isset($mastertypes[$mdid])) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'Legistype Itemtype ID', 'admin', 'updatemastertype',
                    'Legis');
         xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }

     // Get database setup
    $dbconn =& xarDBGetConn();
    $xarTables =& xarDBGetTables();
    $LegisMasterTable = $xarTables['legis_master'];
    $mdname=strtolower($mdname);
    // Update the publication type (don't allow updates on name)
    $query = "UPDATE $LegisMasterTable
            SET xar_mdname = ?,
                xar_mdorder= ?,
                xar_mddef = ?
            WHERE xar_mdid = ?";
    $bindvars = array($mdname, $mdorder,$mddef,$mdid);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;

    $item['module'] = 'legis';
    $item['mdname'] = $mdname;
    $item['itemtype'] = $mdid;
    xarModCallHooks('item', 'update', $mdid, $item);


    return true;
}

?>
