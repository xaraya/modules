<?php
/**
 * File: $Id:
 * @copyright (C) 2004 by Jo Dalle Nogare
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.athomeandabout.com
 *
 * @subpackage xarcpshop
 * @author jojodee@xaraya.com
 */
/**
 * view removeproducts
 */
function xarcpshop_admin_removeproducts()
{

    if (!xarSecurityCheck('EditxarCPShop')) return;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    xarDBLoadTableMaintenanceAPI();
    $cptypestable = $xartable['cptypes'];

    $query = "DELETE FROM $cptypestable";

    if (empty($query)) return; // throw back
    $result = &$dbconn->Execute($query);
    if (!$result) return;
    xarResponseRedirect(xarModURL('xarcpshop', 'admin', 'prodtypes'));

return true;
}
?>
