<?php // File: $Id$
// ----------------------------------------------------------------------
// POSTNUKE Content Management System
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
// Original Author of file: Francisco Burzi
// Purpose of file:
// ----------------------------------------------------------------------

function nameday_namedayblock_init()
{
    // Security
    pnSecAddSchema('nameday:namedayblock:', 'Block title::');
}

function nameday_namedayblock_info()
{
    return array('text_type' => 'nameday',
    'module' => 'nameday',
    'text_type_long' => 'nameday',
    'allow_multiple' => false,
    'form_content' => false,
    'form_refresh' => false,
    'show_preview' => true);
}

function nameday_namedayblock_display($blockinfo)
{
    // Database information
    pnModDBInfoLoad('nameday');
    $dbconn =& xarDBGetConn();;

    $pntable =& xarDBGetTables();
    $namedaytable = $pntable['nameday'];
    $namedaycolumn = &$pntable['nameday_column'];

    if (!pnSecAuthAction(0, 'nameday:namedayblock:', "$blockinfo[title]::", ACCESS_READ)) {
        return;
    }

    $currentlang = pnUserGetLang();
    if (pnConfigGetVar('multilingual') == 1) {
        $querylang = "AND ($namedaycolumn[ndlanguage]='".pnVarPrepForStore($currentlang)."' OR $namedaycolumn[ndlanguage]='')";
    } else {
        $querylang = "";
    }
    $today = getdate();
    $nd_day = $today['mday'];
    $nd_month = $today['mon'];
    $result = $dbconn->Execute("SELECT $namedaycolumn[content]
                              FROM $namedaytable
                              WHERE $namedaycolumn[did]='".pnVarPrepForStore($nd_day)."' AND $namedaycolumn[mid]='".pnVarPrepForStore($nd_month)."' $querylang");
    $output = new pnHTML();
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->Text('<span class="pn-normal"><b>'.xarML('Today is the nameday of').'</b></span>');
    $output->LineBreak(2);

    while(list($content) = $result->fields) {
        $result->MoveNext();
        $output->Text(''.nl2br($content).'');
    }
    $output->SetInputMode(_PNH_PARSEINPUT);

    if (empty($blockinfo['title'])) {
        $blockinfo['title'] = xarML('Nameday');
    }
    $blockinfo['content'] = $output->GetOutput();
    return themesideblock($blockinfo);
}
?>