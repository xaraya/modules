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

// return array containing nameday data for single day
function nameday_adminapi_editday($args)
{
    extract($args);

    // Argument check

    if ((!isset($did)) || (!isset($mid))) {
        pnSessionSetVar('errormsg', xarML('Error in nameday admin API arguments'));
        return false;
    }

    // Security check
    if (!pnSecAuthAction(0, 'nameday::', "::", ACCESS_EDIT)) {
        pnSessionSetVar('errormsg', xarML('Not Authorized to Access Admin API'));
        return false;
    }

    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();

    $namedaytable = $pntable['nameday'];
    $namedaycolumn = &$pntable['nameday_column'];

    $query = "SELECT $namedaycolumn[ndid], $namedaycolumn[did], $namedaycolumn[mid],
    $namedaycolumn[content], $namedaycolumn[ndlanguage]
    FROM $namedaytable WHERE $namedaycolumn[did] = ". pnVarPrepForStore($did) .
    " AND $namedaycolumn[mid] = ". pnVarPrepForStore($mid);

    $result = $dbconn->Execute($query);

    if($result->EOF) {
	return false;
    }

    $resarray = array();

    while(list($ndid, $did, $mid, $content, $ndlanguage) = $result->fields) {
	$result->MoveNext();

	$resarray[] = array('ndid' => $ndid,
			    'did' => $did,
			    'mid' => $mid,
			    'content' => $content,
			    'ndlanguage' => $ndlanguage);
    }
    $result->Close();

    return $resarray;
}
?>