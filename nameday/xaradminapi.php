<?php // File: $Id$
// ----------------------------------------------------------------------
// POST-NUKE Content Management System
// Copyright (C) 2001 by the PostNuke Development Team.
// http://www.postnuke.com/
// ----------------------------------------------------------------------
// Based on:
// PHP-NUKE Web Portal System - http://phpnuke.org/
// Thatware - http://thatware.org/
// ----------------------------------------------------------------------
// LICENSE
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License (GPL)
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// To read the license please visit http://www.gnu.org/copyleft/gpl.html
// ----------------------------------------------------------------------
// Original Author of file: Volodymyr Metenchuk (voll@yahoo.com, http://postnuke.solidno.ru)
// Purpose of file: 
// ----------------------------------------------------------------------

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

    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();

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

    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();

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

// delete nameday
function nameday_adminapi_delete($args)
{
    extract($args);

    if (!isset($ndid)) {
        pnSessionSetVar('errormsg', xarML('Error in nameday admin API arguments'));
        return false;
    }

    if (!pnSecAuthAction(0, 'nameday::', "$content::$ndid", ACCESS_DELETE)) {
        pnSessionSetVar('errormsg', xarML('Not Authorized to Access Admin API'));
        return false;
    }

    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();

    $namedaytable = $pntable['nameday'];
    $namedaycolumn = &$pntable['nameday_column'];

    $query = "DELETE FROM $namedaytable WHERE $namedaycolumn[ndid] = " . pnVarPrepForStore($ndid);
    $dbconn->Execute($query);

    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', xarML('Nameday API Update Failed.'));
        return false;
    }

    return true;
}

// return an array containing nameday data
function nameday_adminapi_display()
{
    // Security check
    if (!pnSecAuthAction(0, 'nameday::', '::', ACCESS_EDIT)) {
        pnSessionSetVar('errormsg', xarML('Not Authorized to Access Admin API'));
        return false;
    }

    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();

    $namedaytable = $pntable['nameday'];
    $namedaycolumn = &$pntable['nameday_column'];

    $query = "SELECT $namedaycolumn[ndid], $namedaycolumn[did], $namedaycolumn[mid],
    $namedaycolumn[content], $namedaycolumn[ndlanguage]
    FROM $namedaytable ORDER BY $namedaycolumn[ndid] DESC";

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

// return single array containing nameday data
function nameday_adminapi_edit($args)
{
    extract($args);

    // Argument check
    if (!isset($ndid)) {
        pnSessionSetVar('errormsg', xarML('Error in nameday admin API arguments'));
        return false;
    }    

    // Security check
    if (!pnSecAuthAction(0, 'nameday::', "$content::$ndid", ACCESS_EDIT)) {
        pnSessionSetVar('errormsg', xarML('Not Authorized to Access Admin API'));
        return false;
    }

    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();

    $namedaytable = $pntable['nameday'];
    $namedaycolumn = &$pntable['nameday_column'];

    $query = "SELECT $namedaycolumn[ndid], $namedaycolumn[did], $namedaycolumn[mid],
    $namedaycolumn[content], $namedaycolumn[ndlanguage]
    FROM $namedaytable WHERE $namedaycolumn[ndid] = ". pnVarPrepForStore($ndid);

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

    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();

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
    $ndfilepath = 'modules/'.pnVarPrepForOS($modinfo['directory']).'/pnlang/'.$clang.'/'.$clang.'.txt';

    if (!file_exists($ndfilepath)) {
        pnSessionSetVar('errormsg', xarML('No such file'));
        return false;
    }

    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();

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