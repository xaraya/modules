<?php
/**
 * Get a specific item
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
 * Get a specific item
 * 
 * Standard function oto retrieve a specific document
 *
 * @author jojodee
 * @param  $args ['exid'] id of legis item to get
 * @returns array
 * @return item array, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function legis_userapi_get($args)
{
    extract($args);
   if (!isset($cdid) || !is_numeric($cdid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'user', 'get', 'legis');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $LegisCompiledTable = $xartable['legis_compiled'];
  $query = "SELECT xar_cdid,
                     xar_mdid,
                     xar_cdnum,
                     xar_cdtitle,
                     xar_docstatus,
                     xar_votestatus,
                     xar_vetostatus,
                     xar_submitdate,
                     xar_submitter,
                     xar_reviewdate,
                     xar_passdate,
                     xar_vetodate,
                     xar_archivedate,
                     xar_archswitch,
                     xar_contributors,
                     xar_doccontent,
                     xar_pubnotes,
                     xar_dochall
              FROM $LegisCompiledTable
              WHERE xar_cdid = ?";
    $result = &$dbconn->Execute($query,array($cdid));
    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;
    /* Check for no rows found, and if so, close the result set and return an exception */
    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This item does not exist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
            new SystemException(__FILE__ . '(' . __LINE__ . '): ' . $msg));
        return;
    }
    /* Obtain the item information from the result set */
       list($cdid,$mdid,$cdnum,$cdtitle,$docstatus,$votestatus,$vetostatus,$submitdate,
            $submitter,$reviewdate,$passdate,$vetodate,$archivedate,$archswitch,
            $contributors, $doccontent,$pubnotes,$dochall) = $result->fields;
    $result->Close();
    if (!xarSecurityCheck('ReadLegis', 1, 'Item', "$cdtitle:All:$cdid")) {
        return;
    }
    /* Create the item array */
         $item =array('cdid'         => $cdid,
                      'mdid'         => $mdid,
                      'cdnum'        => $cdnum,
                      'cdtitle'      => $cdtitle,
                      'docstatus'    => $docstatus,
                      'votestatus'   => $votestatus,
                      'vetostatus'   => $vetostatus,
                      'submitdate'   => $submitdate,
                      'submitter'    => $submitter,
                      'reviewdate'   => $reviewdate,
                      'passdate'     => $passdate,
                      'vetodate'     => $vetodate,
                      'archivedate'  => $archivedate,
                      'archswitch'   => $archswitch,
                      'contributors' => $contributors,
                      'doccontent'   => $doccontent,
                      'pubnotes'     => $pubnotes,
                      'dochall'      => $dochall);
    /* Return the item array */
    return $item;
}
?>
