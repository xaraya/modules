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

// add nameday list to db from form
function nameday_adminapi_addlist($args)
{
    // Security check
    if (!pnSecAuthAction(0, 'nameday::', '::', ACCESS_ADD)) {
        pnSessionSetVar('errormsg', xarML('Not Authorized to Access Admin API'));
        return false;
    }

    $clang=pnUserGetLang();
    $modinfo = pnModGetInfo(pnModGetIDFromName('nameday'));
    $ndfilepath = 'modules/'.pnVarPrepForOS($modinfo['directory']).'/data/'.$clang.'/'.$clang.'.txt';

    if (!file_exists($ndfilepath)) {
        pnSessionSetVar('errormsg', xarML('No such file'));
        return false;
    }

    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();

    $namedaytable = $pntable['nameday'];
    $namedaycolumn = &$pntable['nameday_column'];

    $filecontent = file($ndfilepath);
    foreach ($filecontent as $line) {
        $line1 = preg_split("/\|/",$line);
        $nextId = $dbconn->GenId($namedaytable);
        $mid = pnVarPrepForStore(ltrim($line1[0]));
        $did = pnVarPrepForStore(ltrim($line1[1]));
        $content = pnVarPrepForStore(rtrim($line1[2]));
        $ndlanguage = pnVarPrepForStore($clang);
        $query = "INSERT INTO $namedaytable ($namedaycolumn[ndid], $namedaycolumn[did], $namedaycolumn[mid], $namedaycolumn[content], $namedaycolumn[ndlanguage])
                  VALUES ($nextId, '$did', '$mid', '$content', '$ndlanguage')";
        $result = $dbconn->Execute($query);
    }

    return true;
}
?>