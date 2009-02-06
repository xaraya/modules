<?php
/**
 * Utility function to count the number of items held by this module
 *
 * @package Xaraya
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com
 *
 * @subpackage SiteContact Module
 * @copyright (C) 2004-2008 2skies.com
 * @link http://xarigami.com/project/sitecontact
 * @author Jo Dalle Nogare <icedlava@2skies.com>
 */

/**
 * Utility function to count the number of items held by this module
 * 
 * @author jojodee
 * @returns integer
 * @return number of items held by this module
 * @raise DATABASE_ERROR
 */
function sitecontact_userapi_countitems($args)
{
    extract($args);

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    
    $sitecontactTable = $xartable['sitecontact'];

 $query = "SELECT COUNT(1)
            FROM $sitecontactTable";

    $result = $dbconn->Execute($query,array());
    if (!$result) return;
    /* Obtain the number of items */
    list($numitems) = $result->fields;
    $result->Close();
    /* Return the number of items */
    return $numitems;
}
?>