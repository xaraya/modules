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
// Purpose of file:  Images administration display functions
// ----------------------------------------------------------------------

/**
 * the main administration function
 */
function images_admin_main()
{
    // Create output object
    $output = new pnHTML();

    // Security check
    if (!pnSecAuthAction(0, 'Images::Category', '::', ACCESS_DELETE)) {
        $output->Text(_IMAGESNOAUTH);
        return $output->GetOutput();
    }

    // Add menu to output
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->Text(images_adminmenu());
    $output->SetInputMode(_PNH_PARSEINPUT);

    // Return output
    return $output->GetOutput();
}

/**
 * add new image
 */
function images_admin_new()
{
    // Create output object
    $output = new pnHTML();

    // Security check
    if (!pnSecAuthAction(0, 'Images::Item', '::', ACCESS_ADD)) {
        $output->Text(_IMAGESNOAUTH);
        return $output->GetOutput();
    }

    // Image formats
    $formatinfo = array(array('name' => 'GIF',
                              'id' => 'gif'),
                        array('name' => 'JPEG',
                              'id' => 'jpeg'),
                        array('name' => 'PNG',
                              'id' => 'png'));

    // Add menu to output
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->Text(images_adminmenu());
    $output->SetInputMode(_PNH_PARSEINPUT);

    // Title
    $output->Title(_IMAGESADD);

    // Start form
    $output->UploadMode();
    $output->FormStart(pnModURL('images', 'admin', 'create'));
    $output->FormHidden('authid', pnSecGenAuthKey());

    $output->TableStart('', array(), 0, '80%');

    // Image title
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(_IMAGESTITLE). ' ');
    $row[] = $output->FormTextArea('title', '', 1, 40);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $output->Linebreak(2);

    // Image description
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(_IMAGESDESCRIPTION). ' ');
    $row[] = $output->FormTextArea('description', '', 4, 40);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $output->Linebreak(2);

    // Image format
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(_IMAGESFORMAT). ' ');
    $row[] = $output->FormSelectMultiple('format', $formatinfo);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $output->Linebreak(2);

    // Image file
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(_IMAGESFILE). ' ');
    $row[] = $output->FormFile('file');
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $output->Linebreak(2);

    // End form
    $output->TableEnd();
    $output->Linebreak(2);
    $output->FormSubmit(_IMAGESADD);
    $output->FormEnd();


    // Return output
    return $output->GetOutput();
}

/**
 * create item from images_admin_new()
 */
function images_admin_create()
{
    // Get parameters
    list($title,
         $description,
         $format,
         $file) = pnVarCleanFromInput('title',
                                      'description',
                                      'format',
                                      'file');

    // Confirm authorisation code
    if (!pnSecConfirmAuthKey()) {
        pnSessionSetVar('errormsg', _BADAUTHKEY);
        pnRedirect(pnModURL('images', 'admin', 'view'));
        return true;
    }

    // Load API
    pnModAPILoad('images', 'admin');
    // Pass to API
    $iid = pnModAPIFunc('images',
                        'admin',
                        'create',
                        array('title' => $title,
                              'description' => $description,
                              'format' => $format,
                              'file' => $file));

    if ($iid != false) {
        // Success
        pnSessionSetVar('statusmsg', _IMAGESCREATED);
    }

    pnRedirect(pnModURL('images', 'admin', 'view'));

    return true;
}

/**
 * Modify configuration
 */
function images_admin_modifyconfig()
{
    // Create output object
    $output = new pnHTML();

    // Security check
    if (!pnSecAuthAction(0, 'Images::Category', '::', ACCESS_ADMIN)) {
        $output->Text(_IMAGESNOAUTH);
        return $output->GetOutput();
    }

    // Add menu to output
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->Text(images_adminmenu());
    $output->SetInputMode(_PNH_PARSEINPUT);

    // Title
    $output->Title(_IMAGESMODIFYCONFIG);

    // Start form
    $output->FormStart(pnModURL('images', 'admin', 'updateconfig'));
    $output->FormHidden('authid', pnSecGenAuthKey());

    $output->TableStart();

    // Display style
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(_IMAGESDISPLAYNEWS));
    $row[] = $output->FormCheckbox('displaynews', pnModGetVar('images', 'displaynews'));
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $output->Linebreak(2);

    // End form
    $output->TableEnd();
    $output->Linebreak(2);
    $output->FormSubmit(_IMAGESUPDATECONFIG);
    $output->FormEnd();
    
    return $output->GetOutput();
}

/**
 * Update configuration
 */
function images_admin_updateconfig()
{
    // Get parameters
    $displaynews = pnVarCleanFromInput('displaynews');

    // Confirm authorisation code
    if (!pnSecConfirmAuthKey()) {
        pnSessionSetVar('errormsg', _BADAUTHKEY);
        pnRedirect(pnModURL('images', 'admin', 'view'));
        return true;
    }

    // Update module variables
    if (!isset($displaynews)) {
        $displaynews = 0;
    }
    pnModSetVar('images', 'displaynews', $displaynews);

    pnRedirect(pnModURL('images', 'admin', 'view'));

    return true;
}
/**
 * Main administration menu
 */
function images_adminmenu()
{
    // Create output object
    $output = new pnHTML();

    // Display status message if any
    $output->Text(pnGetStatusMsg());
    $output->Linebreak(2);

    // Start options menu
    $output->TableStart(_IMAGES);

    // Menu options
    $columns = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $columns[] = $output->URL(pnModURL('images',
                                        'admin',
                                        'new'),
                              _IMAGESNEW); 
//    $columns[] = $output->URL(pnModURL('images',
//                                        'admin',
//                                        'view'),
//                              _IMAGESVIEW); 
//    $columns[] = $output->URL(pnModURL('images',
//                                        'admin',
//                                        'modifyconfig'),
//                              _IMAGESMODIFYCONFIG); 
    $output->SetOutputMode(_PNH_KEEPOUTPUT);

    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddRow($columns);
    $output->SetInputMode(_PNH_PARSEINPUT);

    $columns = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $columns[] = $output->URL(pnModURL('images',
                                        'admin',
                                        'newcat'),
                              _IMAGESNEWCAT); 
    $columns[] = $output->URL(pnModURL('images',
                                        'admin',
                                        'viewcats'),
                              _IMAGESVIEWCAT); 
    $columns[] = $output->URL(pnModURL('images',
                                        'admin',
                                        'viewcattree'),
                              _IMAGESVIEWCATTREE); 
    $output->SetOutputMode(_PNH_KEEPOUTPUT);

    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddRow($columns);
    $output->SetInputMode(_PNH_PARSEINPUT);

    $output->TableEnd();

    // Return output
    return $output->GetOutput();
}

/*
 * Category functions below
 * We just use the standard category functions so just
 * defer directly to them
 */

function images_admin_newcat()
{
    pnModLoad('categories', 'admin');
    return pnModFunc('categories', 'admin', 'newcat');
}

function images_admin_createcat()
{
    pnModLoad('categories', 'admin');
    return pnModFunc('categories', 'admin', 'createcat');
}

function images_admin_viewcats()
{
    pnModLoad('categories', 'admin');
    return pnModFunc('categories', 'admin', 'viewcats');
}

function images_admin_deletecat()
{
    pnModLoad('categories', 'admin');
    return pnModFunc('categories', 'admin', 'deletecat');
}

function images_admin_modifycat()
{
    pnModLoad('categories', 'admin');
    return pnModFunc('categories', 'admin', 'modifycat');
}

function images_admin_treeparentcat()
{
    pnModLoad('categories', 'admin');
    return pnModFunc('categories', 'admin', 'treeparentcat');
}

function images_admin_parentcat()
{
    pnModLoad('categories', 'admin');
    return pnModFunc('categories', 'admin', 'parentcat');
}

function images_admin_treeorphancat()
{
    pnModLoad('categories', 'admin');
    return pnModFunc('categories', 'admin', 'treeorphancat');
}

function images_admin_orphancat()
{
    pnModLoad('categories', 'admin');
    return pnModFunc('categories', 'admin', 'orphancat');
}

function images_admin_updatecat()
{
    pnModLoad('categories', 'admin');
    return pnModFunc('categories', 'admin', 'updatecat');
}

function images_admin_viewcattree()
{
    pnModLoad('categories', 'admin');
    return pnModFunc('categories', 'admin', 'viewcattree');
}

function images_admin_modifyfromcattree()
{
    pnModLoad('categories', 'admin');
    return pnModFunc('categories', 'admin', 'modifyfromcattree');
}

function images_admin_updatefromcattree()
{
    pnModLoad('categories', 'admin');
    return pnModFunc('categories', 'admin', 'updatefromcattree');
}

?>
