<?php

/**
*
* This function builds options for an html select tag of all event categories.
* This function returns a string containing the category options
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

function julian_userapi_getcatoptions($args)
{
   extract($args);
   if (!xarVarFetch('cat_id','str',$cat_id,'')) return;
   //get all the categories and build html options
   $dbconn =& xarDBGetConn();
   //get db tables
   $xartable = xarDBGetTables();
   //set categories table
   $categories_table = $xartable['julian_categories'];
   $query = "SELECT cat_id,cat_name FROM " . $categories_table . " ORDER BY list_index,cat_name";
   $result = $dbconn->Execute($query);
   $options = '';
   while(!$result->EOF)
   {
      $obj = $result->FetchObject(false);
      $options .= '<option value="'.$obj->cat_id . '"';
     if(!strcmp($obj->cat_id,$cat_id))
         $options.=" SELECTED";
     $options.=">".$obj->cat_name."</option>";
      $result->MoveNext();  
    }
   return $options;
}
?>
