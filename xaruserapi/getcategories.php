<?php
/**
 * Get all calendar categorie's id and its info
 *
 * @package modules
 * @copyright (C) 2004 by Metrostat Technologies, Inc.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.metrostat.net
 *
 * @subpackage Julian Module
 * initial template: Roger Raymond
 * @author Jodie Razdrh/John Kevlin/David St.Clair
 *
 * @return array array(cid => info_array)
 */
/**
 * get the categories stored in Julian
 * @todo MichelV <1> Move category properties to module variables
 */
function julian_userapi_getcategories()
{
    // establish a db connection
    $dbconn =& xarDBGetConn();
    //get db tables
    $xartable =& xarDBGetTables();
    $category_properties_table = $xartable['julian_category_properties'];

    $categories = array();

    //get all the calendar categories for display
    $query = "SELECT  cid , color  FROM  $category_properties_table ";
    $result = $dbconn->Execute($query);
    while(!$result->EOF) {
        $cid = $result->fields[0];

        $catinfo = xarModAPIFunc('categories', 'user', 'getcatinfo', array('cid'=>$cid));
        if (!empty($catinfo)) {
            // Store names/colors.
            $categories[$cid]['cid']   = $cid;
            $categories[$cid]['name']  = $catinfo['name'];
            $categories[$cid]['color'] = $result->fields[1];
        } else {
            // This category does no longer exist; delete the corresponding properties.
            // It would be nicer to do this when the category was deleted, but unfortunately,
            // we cannot know when that happens (we cannot hook that event).
            $dbconn->Execute("DELETE FROM  ".$category_properties_table."  WHERE  cid ='".$result->fields[0]."'");
        }
        $result->MoveNext();
    }

    return $categories;
}
?>