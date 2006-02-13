<?php
/**
 * Legis doclet
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
 * Update doclet
 *
 * @param $args['did'] ID of the doclet
 * @param $args['dname'] name of the document
 * @param $args['ddef'] definition
 * @returns bool
 * @return true on success, false on failure
 */
function legis_adminapi_updatedoclet($args)
{

    // Get arguments from argument array
    extract($args);
     $invalid = array();
    if (!isset($did) || !is_numeric($did) || $did < 1) {
        $invalid[] = 'Legis itemtype ID';
    }

    if (!isset($dname) || !is_string($dname) || empty($dname)) {
        $invalid[] = 'dname';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'admin', 'updatedoclet','Legis');
         xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }

    // Security check - we require ADMIN rights here
    if (!xarSecurityCheck('AdminLegis',1)) return;

    // Load user API to obtain item information function
    if (!xarModAPILoad('legis', 'user')) return;

    // Get current doclets
    $doclets = xarModAPIFunc('legis','user','getdoclets',array('did'=>$did));
    if (!isset($doclets[$did])) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'Legistype Itemtype ID', 'admin', 'updatedoclet',
                    'Legis');
         xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }
   if (!isset($dlabel)) {
        $dlabel = $doclets['dlabel'];
    }
   if (!isset($dlabel2)) {
        $dlabel2 = $doclets['dlabel2'];
    }

     // Get database setup
    $dbconn =& xarDBGetConn();
    $xarTables =& xarDBGetTables();
    $LegisDocletsTable = $xarTables['legis_doclets'];
    $dname=strtolower($dname);
    // Update the publication type (don't allow updates on name)
    $query = "UPDATE $LegisDocletsTable
            SET xar_dname = ?,
                xar_dlabel=?,
                xar_dlabel2=?,
                xar_ddef = ?
            WHERE xar_did = ?";
    $bindvars = array($dname, $dlabel, $dlabel2, $ddef, $did);

    $result =& $dbconn->Execute($query,$bindvars);

    if (!$result) return;

    return true;
}

?>
