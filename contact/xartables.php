<?php
// $Id: s.xartables.php 1.6 02/12/23 19:15:18-05:00 Scot.Gardner@ws75. $ $Name: <Not implemented> $
// ----------------------------------------------------------------------
// POST-NUKE Content Management System
// Copyright (C) 2002 by the PostNuke Development Team.
// http://www.postnuke.com/
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
// Original Author of file: Bjarne Varöystrand and Bjarne Varöystrand
// Modifications by: Richard Cave
// Purpose of file:  Table information for template module
// ----------------------------------------------------------------------

function contact_xartables()
{
    // Initialise table array
    $xartable = array();

    // Get the name for the example item table.  This is not necessary
    // but helps in the following statements and keeps them readable
    $xar_contact_departments = xarDBGetSiteTablePrefix() . '_contact_departments';

    // Set the table name
    $xartable['contact_departments'] = $xar_contact_departments;

    $xar_contact_department_members = xarDBGetSiteTablePrefix() . '_contact_department_members';

    $xartable['contact_department_members'] = $xar_contact_department_members;

    $xar_contact_titles = xarDBGetSiteTablePrefix() . '_contact_titles';

    $xartable['contact_titles'] = $xar_contact_titles;

    $xar_contact_city = xarDBGetSiteTablePrefix() . '_contact_city';

    $xartable['contact_city'] = $xar_contact_city;

    $xar_contact_country = xarDBGetSiteTablePrefix() . '_contact_country';

    $xartable['contact_country'] = $xar_contact_country;

    $xar_contact_infotype = xarDBGetSiteTablePrefix() . '_contact_infotype';

    $xartable['contact_infotype'] = $xar_contact_infotype;

    $xar_contact_persons = xarDBGetSiteTablePrefix() . '_contact_persons';

    $xartable['contact_persons'] = $xar_contact_persons;

    $xar_contact_attributes = xarDBGetSiteTablePrefix() . '_contact_attributes';

    $xartable['contact_attributes'] = $xar_contact_attributes;

    $xar_contact_company = xarDBGetSiteTablePrefix() . '_contact_company';

    $xartable['contact_company'] = $xar_contact_company;

    $xar_contact_langmembers = xarDBGetSiteTablePrefix() . '_contact_langmembers';

    $xartable['contact_langmembers'] = $xar_contact_langmembers;

    $xar_contact_countrymembers = xarDBGetSiteTablePrefix() . '_contact_countrymembers';

    $xartable['contact_countrymembers'] = $xar_contact_countrymembers;

    // Return the table information

    return $xartable;
}

?>