<?php
// ----------------------------------------------------------------------
// Xaraya Applications Framework
// Ported as a Xaraya module by Marc Lutolf.
// http://www.xaraya.com/
// ----------------------------------------------------------------------
// Based on: POST-NUKE Content Management System
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
// Original Author of file: Yassen Yotov
// ----------------------------------------------------------------------

/**
 * This function is called internally by the core whenever the module is
 * loaded.  It adds in the information
 */
function window_xartables()
{
    $xartable = array();

    $window = xarDBGetSiteTablePrefix() . '_window';

/*    // Set the column names.  Note that the array has been formatted
    // on-screen to be very easy to read by a user.
    $xartable['postwrap_url_column'] = array('id'                => $urltable . '.id',
                                            'name'              => $urltable . '.name',
                                            'alias'             => $urltable . '.alias',
                                            'reg_user_only'     => $urltable . '.reg_user_only',
                                            'open_direct'       => $urltable . '.open_direct',
                                            'use_fixed_title'   => $urltable . '.use_fixed_title',
                                            'auto_resize'       => $urltable . '.auto_resize',
                                            'vsize'             => $urltable . '.vsize',
                                            'hsize'             => $urltable . '.hsize');

*/
// Set the table name
    $xartable['window'] = $window;
    // Return the table information
    return $xartable;
}

?>