<?php // $Id: userapi.php,v 1.2 2002/03/12 10:32:34 voll Exp $
// ----------------------------------------------------------------------
// POST-NUKE Content Management System
// Copyright (C) 2001 by the PostNuke Development Team.
// http://www.postnuke.com/
// ----------------------------------------------------------------------
// Based on:
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
// but WIthOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// To read the license please visit http://www.gnu.org/copyleft/gpl.html
// ----------------------------------------------------------------------
// Original Author of file: Jim McDonald
// Purpose of file:  Language defines for xaruserapi.php
// ----------------------------------------------------------------------
define('_NOTASKS', 'No (sub)tasks available');
define('_GETFAILED', 'Tasks load failed');
if (!defined('_XPROJECTNOAUTH')) {
	define('_XPROJECTNOAUTH','Not authorised to access Tasks module');
}
?>