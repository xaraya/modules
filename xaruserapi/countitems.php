<?php
/**
 * Utility function to count the number of items held by this module
 *
 * @package modules
 * @copyright (C) 2004-2009 2skies.com 
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xarigami.com/projects/xartinymce
 *
 * @subpackage xartinymce module
 * @author Jo Dalle Nogare <icedlava@2skies.com>
 */

/**
 * Utility function to count the number of items held by this module
 * 
 * @returns integer
 * @return number of items held by this module
 * @raise DATABASE_ERROR
 */
function tinymce_userapi_countitems($args)
{
    extract($args);

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    
    $tinymceTable = $xartable['tinymce'];

 $query = "SELECT COUNT(1)
            FROM $tinymceTable";

    $result = $dbconn->Execute($query,array());
    if (!$result) return;
    /* Obtain the number of items */
    list($numitems) = $result->fields;
    $result->Close();
    /* Return the number of items */
    return $numitems;
}
?>