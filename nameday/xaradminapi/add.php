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

// add nameday to db
function nameday_adminapi_add($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($did)) || (!isset($mid)) || (!isset($content)) || (!isset($ndlanguage))) {
        pnSessionSetVar('errormsg', xarML('Error in nameday admin API arguments'));
        return false;
    }

    // Security check
    if (!pnSecAuthAction(0, 'nameday::', '::', ACCESS_ADD)) {
        pnSessionSetVar('errormsg', xarML('Not Authorized to Access Admin API'));
        return false;
    }

    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();

    $namedaytable = $pntable['nameday'];
    $namedaycolumn = &$pntable['nameday_column'];

    $nextId = $dbconn->GenId($namedaytable);

    $did = pnVarPrepForStore($did);
    $mid = pnVarPrepForStore($mid);
    $content = pnVarPrepForStore($content);
    $ndlanguage = pnVarPrepForStore($ndlanguage);
    
    $query = "INSERT INTO $namedaytable ($namedaycolumn[ndid], $namedaycolumn[did], $namedaycolumn[mid], $namedaycolumn[content], $namedaycolumn[ndlanguage])
				     VALUES ($nextId, '$did', '$mid', '$content', '$ndlanguage')";

    $result = $dbconn->Execute($query);

    return true;
}
?>