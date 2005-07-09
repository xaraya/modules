<?php
// $Id: s.xaradminapi.php 1.3 02/12/01 14:27:28+01:00 marcel@hsdev.com $
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
// but WIthOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// To read the license please visit http://www.gnu.org/copyleft/gpl.html
// ----------------------------------------------------------------------
// Original Author of file: Jim McDonald
// Purpose of file:  Images administration API
// ----------------------------------------------------------------------

/**
 * create a new images item
 * @param $args['title'] name of the item
 * @param $args['description'] description for this item
 * @param $args['format'] file format for this item
 * @param $args['file'] file for this item
 * @returns int
 * @return images item ID on success, false on failure
 */
function images_adminapi_create($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($title)) ||
        (!isset($description)) ||
        (!isset($format)) ||
        (!isset($file))) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    // Security check
    if (!authorised(0, 'Images::Item', "$title::", ACCESS_ADD)) {
        pnSessionSetVar('errormsg', xarML('Not authorised to carry out that operation'));
        return false;
    }

    // Get datbase setup
    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();
    $imagestable = $pntable['images'];
    $imagescolumn = &$pntable['images_column'];

    // Get next ID in table
    $nextId = $dbconn->GenId($imagestable);

    // Add item
    $sql = "INSERT INTO $imagestable (
              $imagescolumn[iid],
              $imagescolumn[title],
              $imagescolumn[description],
              $imagescolumn[format])
            VALUES (
              " . pnVarPrepForStore($nextId) . ",
              '" . pnVarPrepForStore($title) . "',
              '" . pnvarPrepForStore($description) . "',
              '" . pnvarPrepForStore($format) . "')";
    $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', _CREATEFAILED);
        return false;
    }

    // Get iid
    $iid = $dbconn->PO_Insert_ID($imagestable, $imagescolumn['iid']);

    // TODO: make this optional and/or allow admin to place them elsewhere !!

    // Store image on filesystem
    $outfile = 'modules/images/pnimages/' . pnVarPrepForOS($iid);
    // Move image data into file
    $infh = fopen($file, 'r');
    $outfh = fopen($outfile, 'w');
    while ($imagebit = fread($infh, 16384)) {
        fwrite($outfh, $imagebit);
    }
    fclose($infh);
    fclose($outfh);

    // Update DB with item information
    $sql = "UPDATE $imagestable
            SET $imagescolumn[file] = '" . pnVarPrepForStore($outfile) . "'
            WHERE $imagescolumn[iid] = " . pnVarPrepForStore($iid);
    $dbconn->Execute($sql);
    // TODO - error checking and recovery

    // Call creation hooks
    pnModCallHooks('item', 'create', $iid, 'iid');

    return $iid;
}

/**
 * delete an image
 * @param $args['iid'] ID of the image
 * @returns bool
 * @return true on success, false on failure
 */
function images_adminapi_delete($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($iid)) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    // Security check
    if (!authorised(0, 'Images::Item', "::$iid", ACCESS_DELETE)) {
        pnSessionSetVar('errormsg', xarML('Not authorised to carry out that operation'));
        return false;
    }

    // Get datbase setup
    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();
    $imagestable = $pntable['images'];
    $imagescolumn = &$pntable['images_column'];

    // TODO - delete linkage

    // Call deletion hooks
    pnModCallHooks('item', 'delete', $aid, 'aid');

    // Delete item
    $sql = "DELETE FROM $imagestable
            WHERE $imagescolumn[aid] = " . pnVarPrepForStore($aid);
    $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', _DELETEFAILED);
        return false;
    }

    return true;
}

/**
 * create images item
 * @param $args['aid'] ID of the item
 * @param $args['title'] name of the item
 * @param $args['title'] summary of the item
 * @param $args['bodytype'] type of input for this item
 * @param $args['bodytext'] direct input text for this item
 * @param $args['bodyfile'] file input text for this item
 * @param $args['langauge'] language of the item
 */
function images_adminapi_update($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($aid)) ||
        (!isset($title)) ||
        (!isset($summary)) ||
        (!isset($bodytype)) ||
        ((!isset($bodytext)) && (!isset($bodyfile))) ||
        (!isset($language))) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    // Security check
    if (!authorised(0, 'Images::Item', "$title::$aid", ACCESS_EDIT)) {
        pnSessionSetVar('errormsg', xarML('Not authorised to carry out that operation'));
        return false;
    }

    // Get relevant text
    if ($bodytype == 'file') {
        $body = join('', @file($bodyfile));
    } else {
        $body = $bodytext;
    }

    // Get datbase setup
    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();
    $imagestable = $pntable['images'];
    $imagescolumn = &$pntable['images_column'];

    // Update the item
    $sql = "UPDATE $imagestable
            SET $imagescolumn[title] = '" . pnVarPrepForStore($title) . "',
                $imagescolumn[summary] = '" . pnVarPrepForStore($summary) . "',
                $imagescolumn[body] = '" . pnVarPrepForStore($body) . "',
                $imagescolumn[language] = '" . pnVarPrepForStore($language) . "'
            WHERE $imagescolumn[aid] = " . pnVarPrepForStore($aid);
    $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', _UPDATEFAILED);
        return false;
    }

    return true;
}
?>