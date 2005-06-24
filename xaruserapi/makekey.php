<?php

/**
*
* This function creates a key based on the categories available.
*
* @package Xaraya eXtensible Management System
* @copyright (C) 2004 by Metrostat Technologies, Inc.
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.metrostat.net
*
* @subpackage julian
* initial template: Roger Raymond
* @author Jodie Razdrh/John Kevlin
*/

function julian_userapi_makekey()
{
   // Not longer neccecary... just handled in template
   
   $categories = xarModAPIFunc('julian','user','getcategories');
   // TODO do this in template
   $key = '';
   //Building html for popup content
   foreach ($categories as $cid => $info)
   {
     $key.= "<font color=" .$info['color']. ">&bull;</font>&nbsp;&nbsp;" . addslashes($info['name']) . "<br />";
   }
   
   return $key;
   
   /*
   //establish db connection
   $dbconn =& xarDBGetConn();
   //get db tables
   $xartable = xarDBGetTables();
   //set categories table
   $categories_table = $xartable['julian_categories'];
   //get all categories
   $sql = "SELECT cat_name, color FROM " . $categories_table . " WHERE 1;";
   $rs = $dbconn->Execute($sql);
   $key = '';
   //Building html for popup content
   while(!$rs->EOF)
   {
     $catObj = $rs->FetchObject(false);
     $key.= "<font color=" .$catObj->color. ">&bull;</font>&nbsp;&nbsp;" . addslashes($catObj->cat_name) . "<br />";
     $rs->MoveNext();
   }
   return $key;
   */
}
?>
