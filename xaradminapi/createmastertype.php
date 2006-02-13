<?php
/**
 * Legis create master doc
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

function legis_adminapi_createmastertype($args)
{
    // Get arguments from argument array
    extract($args);

    $invalid = array();
    if (!isset($mdname) || !is_string($mdname) || empty($mdname)) {
        $invalid[] = 'mdname';
    }
   if (!isset($mddef)){// || !is_string($mddef) || empty($mddef)) {
        $invalid[] = 'mddef';
    }
    if (!isset($mdorder)) {$mdorder=0;}

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'admin', 'createmastertype','Legis');
         xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }

    // Integrated itemtype names *must* be lower-case for now
    $mdname = strtolower($mdname);
     // Security check - we require ADMIN rights here
    if (!xarSecurityCheck('AdminLegis')) return;

    if (!xarModAPILoad('legis', 'user')) return;



    // Get database setup
    $dbconn =& xarDBGetConn();
    $xarTables =& xarDBGetTables();
    $LegisMasterTable = $xarTables['legis_master'];

    // Get next ID in table
    $nextId = $dbconn->GenId($LegisMasterTable);

    // Insert the publication type
    $query = "INSERT INTO $LegisMasterTable (xar_mdid, xar_mdname,
            xar_mdorder,xar_mddef)
            VALUES (?,?,?,?)";
    $bindvars = array($nextId, $mdname, $mdorder, $mddef);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;

    // Get ptid to return
    $mdid = $dbconn->PO_Insert_ID($LegisMasterTable, 'xar_mdid');

    $args=array();
   // Let any hooks know that we have created a new masterdoc.
    $args['module'] = 'legis';
    $args['itemtype'] = $mdid ;
      // then call the create hooks
    $result = xarModCallHooks('item', 'create', $mdid, $args);


     return $mdid;
}

?>
