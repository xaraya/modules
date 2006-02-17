<?php
/**
 * get the color for a category
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian development Team
 */
/**
 *
 * This function gets the color for a category and returns it as a hex number.
 *
 * This module:
 * @copyright (C) 2004 by Metrostat Technologies, Inc.
 * @link http://www.metrostat.net
 *
 * initial template: Roger Raymond
 * @author Jodie Razdrh/John Kevlin/David St.Clair
 * @param int category
 * @return string color
 * @todo MichelV: move all this to modvars
 */
function julian_userapi_getcolor($args)
{
  extract($args);
  if (xarVarFetch('category','isset',$category, $category, XARVAR_DONT_SET)) return;

  if(empty($category)) {
      return;
  }
  //Setup DB connection
  $dbconn =& xarDBGetConn();
  $xartable =& xarDBGetTables();
  //set events table
  $categories_table = $xartable['julian_category_properties'];
  //Get the color for the category
  $query = "SELECT color FROM " . $categories_table . " WHERE cid = '".$category."';";
  $rs = $dbconn->Execute($query);
  $color_obj = $rs->FetchObject(false);
  return $color_obj->color;
}
?>
