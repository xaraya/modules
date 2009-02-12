<?php
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
// Original Author of file: Chad Kraeft <cdavidkraeft@miragelab.com>
//
// Copyright (c)2004 Chad Kraeft
// ----------------------------------------------------------------------

function labaccounting_ledgersapi_getshortchart()
{

    $options = array();
    $options[] = array('id' => "nonstandard", 'name' => "Non-Standard Accounts");
    $options[] = array('id' => "all", 'name' => "All Accounts");
    $options[] = array('id' => "assets", 'name' => "Asset Accounts");
    $options[] = array('id' => 1000, 'name' => " --Current Assets--");
    $options[] = array('id' => 1500, 'name' => " --Fixed Assets--");
    $options[] = array('id' => 1900, 'name' => " --Other Assets--");   
    $options[] = array('id' => "liabilities", 'name' => "Liability Accounts");  
    $options[] = array('id' => 2000, 'name' => " --Current Liabilities--");   
    $options[] = array('id' => 2700, 'name' => " --Long-term Liabilities--");
    $options[] = array('id' => '', 'name' => "Other Accounts");  
    $options[] = array('id' => 3000, 'name' => " --Equity Accounts--");  
    $options[] = array('id' => 4000, 'name' => " --Revenue Accounts--");  
    $options[] = array('id' => 5000, 'name' => " --Cost of Goods Sold--");  
    $options[] = array('id' => 6000, 'name' => " --Expenses--");
    
    return $options;
}

?>