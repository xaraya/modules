<?php // $Id: s.user.php 1.2 02/12/01 14:47:45+00:00 miko@miko.homelinux.org $
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
// Purpose of file:  Language defines for pnuser.php
// ----------------------------------------------------------------------
define('_DP', 'Dynamic Planning Module');
define('_DPITEMFAILED', 'Failed to get any items');
define('_DPNAME', 'Track Name');
define('_DPTEXT', 'Track Description');
define('_DPVIEW', 'View Tracks');
define('_DPTITLE','Task Name');
define('_DPLEAD','Track Leader');
define('_DPDESC','Task Description');
define('_DPSTART','Start Date');
define('_DPEND','End Date');
define('_DPLAST','Last Date');
define('_DPPERC','Percent Complete');
define('_DPSTEPS','Task Steps');
define('_DPTEAM','Task Team');
define('_DPSTAT','Track Status');
define('_DPCAT','Track Category');
define('_DPNEWS','Most Recent News');

if (!defined('_DPNOAUTH')) {
	define('_DPNOAUTH','Not authorised to access  module');
}
?>