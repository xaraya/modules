<?php
/**
 * Categories module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Categories Module
 * @link http://xaraya.com/index.php/release/147.html
 * @author Categories module development team
 */

/**
 * get category by name
 * @author Michel Dalle <mikespub@xaraya.com>
 *
 * @param $args['name'] name of the category to retrieve
 * @param $args['return_itself'] =Boolean= return the cid itself (default true)
 * @param $args['getchildren'] =Boolean= get children of category (default false)
 * @param $args['getparents'] =Boolean= get parents of category (default false)
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
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
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
                 WHERE xar_name = ?";
    $bindvars = array($name);
    $result = $dbconn->Execute($SQLquery,$bindvars);
    if (!$result) return;

    // Check for no rows found
    if ($result->EOF) {
        $result->Close();
        //$msg = xarML('This category does not exist');
        //xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
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
