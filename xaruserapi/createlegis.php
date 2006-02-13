<?php
/**
 * Add Legislation
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Legis Module
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */
function legis_userapi_createlegis($args)
{
    // Get arguments
    extract($args);

    // Argument check
    if ((!isset($mdid)) ||
        (!isset($cdtitle))||
        (!isset($doccontent))
        ) {

        $msg = xarML('Wrong arguments to legis_userapi_createlegis.');
        xarErrorSet(XAR_SYSTEM_EXCEPTION,
                        'BAD_PARAM',
                        new SystemException($msg));
        return false;
    }

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xarTables =& xarDBGetTables();

    $LegisCompiledTable = $xarTables['legis_compiled'];


    // Get next ID in table
    $nextId = $dbconn->GenId($LegisCompiledTable);
    $time = time();
    $query = "INSERT INTO $LegisCompiledTable (
                     xar_cdid,
                     xar_mdid,
                     xar_cdnum,
                     xar_cdtitle,
                     xar_docstatus,
                     xar_submitdate,
                     xar_contributors,
                     xar_doccontent,
                     xar_pubnotes,
                     xar_dochall
              )
            VALUES (?,?,?,?,?,?,?,?,?,?)";

    $bindvars = array($nextId,$mdid,$cdnum,$cdtitle,$docstatus,$submitdate,$contributors, $doccontent,
                      $pubnotes, $dochall);
    $result =&$dbconn->Execute($query,$bindvars);
    if (!$result) return;

    // Get the ID of the item that we inserted
    $cdid = $dbconn->PO_Insert_ID($LegisCompiledTable, 'xar_cdid');

    // Let any hooks know that we have created a new user.
    xarModCallHooks('item', 'create', $cdid, 'cdid');

    // Return the id of the newly created user to the calling process
    return $cdid;

}

?>
