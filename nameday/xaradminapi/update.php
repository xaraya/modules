<?php // File: $Id$
/**
 * File: $Id$
 *
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage nameday
 * @author Volodymyr Metenchuk (http://www.xaraya.ru)
 */

// update nameday
function nameday_adminapi_update($args)
{
    extract($args);

    if ((!isset($ndid)) || (!isset($did)) || (!isset($mid)) || 
        (!isset($content)) || (!isset($ndlanguage))) {
        pnSessionSetVar('errormsg', xarML('Error in nameday admin API arguments'));
        return false;
    }

    if (!pnSecAuthAction(0, 'nameday::', "$content::$ndid", ACCESS_EDIT)) {
        pnSessionSetVar('errormsg', xarML('Not Authorized to Access Admin API'));
        return false;
    }

    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();

    $namedaytable = $pntable['nameday'];
    $namedaycolumn = &$pntable['nameday_column'];

    $query = "UPDATE $namedaytable
              SET $namedaycolumn[mid] = '" . pnVarPrepForStore($mid) . "',
                  $namedaycolumn[did] = '" . pnVarPrepForStore($did) . "',
                  $namedaycolumn[content] = '" . pnVarPrepForStore($content) . "',
                  $namedaycolumn[ndlanguage] = '" . pnVarPrepForStore($ndlanguage) . "'
              WHERE $namedaycolumn[ndid] = $ndid";

    $dbconn->Execute($query);

    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', xarML('Nameday API Update Failed.'));
        return false;
    }
    return true;
}
?>