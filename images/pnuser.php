<?php
// $Id$
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
// Purpose of file:  Images user display functions
// ----------------------------------------------------------------------

/**
 * the main user function
 */
function images_user_main($args)
{
    return images_user_display($args);
}

/**
 * display item
 */
function images_user_display($args)
{
    // Get parameters from user
    $iid = pnVarCleanFromInput('iid');

    // Override if needed from argument array
    extract($args);

    // Create output object
    $output = new pnHTML();

    // Load API
    if (!pnModAPILoad('images', 'user')) {
        $output->Text(_LOADFAILED);
        return $output->GetOutput();
    }

    // Get article
    $imageinfo = pnModAPIFunc('images',
                              'user',
                              'get',
                              array('iid' => $iid));

    if (!is_array($imageinfo)) {
        $output->Text(_IMAGESITEMFAILED);
    }

    list($image, $format) = $imageinfo;

    header('Content-type: image/' . $format);
    header('Content-length: ' . strlen($image));
    echo $image;
    exit;
}

?>
