<?php 
// File: $Id: s.xartables.php 1.6 02/11/28 20:00:49-05:00 John.Cox@mcnabb. $ 
// ---------------------------------------------------------------------- 
// Xaraya eXtensible Management System 
// Copyright (C) 2002 by the Xaraya Development Team. 
// http://www.xaraya.org 
// ---------------------------------------------------------------------- 
// Original Author of file: Jim McDonald 
// Purpose of file:  Table information for example module 
// ---------------------------------------------------------------------- 
 
/** 
 * This function is called internally by the core whenever the module is 
 * loaded.  It adds in the information 
 */ 
function uploads_xartables() 
{ 
    // Initialise table array 
    $xartable = array(); 
 
    // Get the name for the uploads item table.  This is not necessary 
    // but helps in the following statements and keeps them readable 
    $uploads = xarDBGetSiteTablePrefix() . '_uploads'; 
		$blobs = xarDBGetSiteTablePrefix() . '_uploadblobs'; 
 
    // Set the table name 
    $xartable['uploads'] = $uploads; 
		$xartable['blobs'] = $blobs; 
 
    // Return the table information 
    return $xartable; 
} 
 
?>