<?php
/**
 * File: $Id: $
 *
 * Categories System
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 * @subpackage category module
 * @author Michel Dalle <mikespub@xaraya.com>
 */

/**
 * get category by name
 *
 * @param $args['name'] name of the category to retrieve
 * @param $args['return_itself'] =Boolean= return the cid itself (default true)
 * @param $args['getchildren'] =Boolean= get children of category (default false)
 * @param $args['getparents'] =Boolean= get parents of category (default false)
 * @returns array
 * @return array of category info arrays, false on failure
 */
function categories_userapi_getcatbyname($args) 
{
    // Extract arguments
    extract($args);

    // Argument validation
    if (!isset($name) && !is_string($name)) {
        $msg = xarML('Invalid name for #(1) function #(2)() in module #(3)',
                     'userapi', 'getcatbyname', 'category');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                        new SystemException($msg));
       return false;
    }
    
    // Check for optional arguments
    if (!isset($return_itself)) {
        $return_itself = true;
    }
    if (!isset($getchildren)) {
        $getchildren = false;
    }
    if (!isset($getparents)) {
        $getparents = false;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $categoriestable = $xartable['categories'];

    $SQLquery = "SELECT xar_cid
                 FROM $categoriestable
                 WHERE xar_name = '". $name ."'";
    $result = $dbconn->Execute($SQLquery);
    if (!$result) return;

    // Check for no rows found
    if ($result->EOF) {
        $result->Close();
        //$msg = xarML('This category does not exist');
        //xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
        //              new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    // Obtain the owner information from the result set
    list($cid) = $result->fields;
                                                                                
    // Close result set
    $result->Close();

    // Get category information
    $category = xarModAPIFunc('categories',
                              'user',
                              'getcat',
                              Array('cid' => $cid,
                                    'return_itself' => $return_itself,
                                    'getparents' => $getparents,
                                    'getchildren' => $getchildren));

    return $category;
}

?>
