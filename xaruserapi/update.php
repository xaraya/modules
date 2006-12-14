<?php
/**
 * Update an itsp item
 *
 * @package modules
 * @copyright (C) 2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ITSP Module
 * @link http://xaraya.com/index.php/release/572.html
 * @author ITSP Module Development Team
 */
/**
 * Update an itsp with a new status (and others maybe)
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @param array args An argument array, if called by other modules OPTIONAL
 * @param int itspid the ID of the item
 * @param int newstatus the new status of the item
 * @since 22 May 2006
 * @return bool true on success of update
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function itsp_userapi_update($args)
{
    extract($args);
    /* Argument check */
    $invalid = array();
    if (!isset($itspid) || !is_numeric($itspid)) {
        $invalid[] = 'item ID';
    }
    if (!isset($newstatus) || !is_numeric($newstatus)) {
        $invalid[] = 'new status ID';
    }

    // Date of approval
    if (!isset($dateappr)) {
        $dateappr = 0;
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'user', 'update', 'ITSP');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    /* The user API function is called. */
    $item = xarModAPIFunc('itsp',
        'user',
        'get',
        array('itspid' => $itspid));
    /*Check for exceptions */
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return false; // throw back

    /* Security check

    if (!xarSecurityCheck('EditITSP', 1, 'Item', "$item[name]:All:$exid")) {
        return;
    }
    if (!xarSecurityCheck('EditITSP', 1, 'Item', "$name:All:$exid")) {
        return;
    }
    */
    $datemodi = time();
    $modiby = xarUserGetVar('uid');
    $bindvars = array($newstatus, $modiby, $datemodi);

    switch($newstatus) {
        case 1:

        case 2:
        case 3:
        case 4:
        $dateappr = $item['dateappr'];
        case 5:
        //Approve
        if ($item['dateappr'] > 0) {
            $dateappr = $item['dateappr'];
            break;
        }
        // reformat the date to timestamp
        if (isset($dateappr) && !is_numeric($dateappr)) {
            $dateappr = strtotime($dateappr);
        } elseif (($item['dateappr'] < 1) && ($dateappr <1)) {
            $dateappr = 0;
        } else {
            $dateappr=$item['dateappr'];
        }

        break;
        case 6:
        $dateappr = $item['dateappr'];
        break;
    }
    $bindvars[] = $dateappr;
    $bindvars[] = $itspid;
    /* Get database setup
     */
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $itsptable = $xartable['itsp_itsp'];
    /* Update the item - the formatting here is not mandatory, but it does
     * make the SQL statement relatively easy to read. Also, separating
     * out the sql statement from the Execute() command allows for simpler
     * debug operation if it is ever needed
     */
    $query = "UPDATE $itsptable
            SET xar_itspstatus =?,
                xar_modiby =?,
                xar_datemodi =?,
                xar_dateappr =?
            WHERE xar_itspid = ?";

    $result = &$dbconn->Execute($query,$bindvars);
    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;
    /* Let any hooks know that we have updated an item. As this is an
     * update hook we're passing the updated $item array as the extra info
     */
    $item['module'] = 'itsp';
    $item['itemtype'] = 99999;
    $item['itemid'] = $itspid;
    $item['itspstatus'] = $newstatus;

    xarModCallHooks('item', 'update', $itspid, $item);

    /* Let the calling process know that we have finished successfully */
    return true;
}
?>