<?php
// ----------------------------------------------------------------------
// eNvolution Content Management System
// Copyright (C) 2002 by the eNvolution Development Team.
// http://www.envolution.com/
// ----------------------------------------------------------------------
// Based on:
// Postnuke Content Management System - www.postnuke.com
// PHP-NUKE Web Portal System - http://phxaruke.org/
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
//
/********************************************************/
/* Dimensionquest Help Desk                             */
/*  Development by:                                     */
/*     Burke Azbill - burke@dimensionquest.net          */
/*                                                      */
/* This program is opensource so you can do whatever    */
/* you want with it.                                    */
/*                                                      */
/* http://www.dimensionquest.net               		    */
/********************************************************/
//////////////// Common ///////////////////////////
function helpdesk_adminapi_new_id($args)
{
    extract($args);
    if (!isset($table) || !isset($field)) {
        xarSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $db_table = $xartable['helpdesk_'.$table];
    $db_column = &$xartable['helpdesk_'.$table.'_column'];
    $sql = "Select max(".$db_column[$field].") from ".$db_table;
    $newID = $dbconn->Execute($sql);
    return (($newID->fields[0])+1);
}
?>
