<?php // $Id$
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
// Purpose of file:  Images user API
// ----------------------------------------------------------------------

/**
 * get a specific image
 * @param $args['iid'] id of image to get
 * @returns array
 * @return image, or false on failure
 */
function images_userapi_get($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($iid)) {
        pnSessionSetVar('errormsg', xarML('Bad arguments for API function'));
        return false;
    }

    // Get datbase setup
    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();
    $imagestable = $pntable['images'];
    $imagescolumn = &$pntable['images_column'];

    // Get item
    $sql = "SELECT $imagescolumn[title],
                   $imagescolumn[format],
                   $imagescolumn[file]
            FROM $imagestable
            WHERE $imagescolumn[iid] = " . pnVarPrepForStore($iid);
    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        return false;
    }

    if ($result->EOF) {
        return false;
    }

    list($title, $format, $file) = $result->fields;

    // Security check
    if (!pnSecAuthAction(0, 'Images::Item', "$title::$iid", ACCESS_READ)) {
        return false;
    }

    $image = '';
    $fh = fopen($file, 'r');
    while ($imagebit = fread($fh, 16384)) {
        $image .= $imagebit;
    }
    fclose($fh);
    return array($image, $format);
}

?>