<?php
/**
*
* This function gets the color for a category and returns it as a hex number.
*
* @package Xaraya eXtensible Management System
* @copyright (C) 2004 by Metrostat Technologies, Inc.
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.metrostat.net
*
* @subpackage julian
* initial template: Roger Raymond
* @author Jodie Razdrh/John Kevlin/David St.Clair
*/
function julian_userapi_getcolor($args)
{
  extract($args);
  if (!xarVarFetch('category','isset',$category)) return;

  //Setup DB connection
  $dbconn =& xarDBGetConn();
  //get db tables
  $xartable = xarDBGetTables();
  //set events table
  $categories_table = $xartable['julian_category_properties'];
  //Get the color for the category
  $query = "SELECT color FROM " . $categories_table . " WHERE cid = '".$category."';";
  $rs = $dbconn->Execute($query);
  $color_obj = $rs->FetchObject(false);
  return $color_obj->color;
}
?>
