<?php
/**
 * Categories module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Categories Module
 * @link http://xaraya.com/index.php/release/147.html
 * @author Categories module development team
 */
/**
 * creates a category using the parent model
 *
 *  -- INPUT --
 * @param $args['name'] the name of the category
 * @param $args['description'] the description of the category
 * @param $args['image'] the (optional) image for the category
 * @param $args['parent_id'] Parent Category ID (0 if root)
 *
 *  -- OUTPUT --
 * @return mixed category ID on success, false on failure
 */
function categories_adminapi_create ($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($name))        ||
        (!isset($description)) ||
        (!isset($parent_id))   ||
        (!is_numeric($parent_id))
       )
    {
        $msg = xarML('Invalid Parameter Count in  categories_adminapi_create');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', $msg);
        return;
    }

    if (!isset($image)) {
        $image = '';
    }

    // Security check
    // Has to be redone later

    if(!xarSecurityCheck('AddCategories')) return;

    if ($parent_id != 0)
    {
       $cat = xarModAPIFunc('categories', 'user', 'getcatinfo', Array('cid'=>$parent_id));

       if ($cat == false)
       {
          xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
          new SystemException(__FILE__.'('.__LINE__.'): Unable to load the categories module?s user API'));
          return false;
       }
//       $point_of_insertion = $cat['left'] + 1;
        $point_of_insertion = $cat['right'];
    } else {
        $dbconn =& xarDBGetConn();
        $xartable =& xarDBGetTables();
        $categoriestable = $xartable['categories'];
        $query = "SELECT MAX(xar_right) FROM " . $categoriestable;
        $result = $dbconn->Execute($query);
        if (!$result) return;

        if (!$result->EOF) {
            list($max) = $result->fields;
            $point_of_insertion = $max + 1;
        } else {
            $point_of_insertion = 1;
        }
    }
    return xarModAPIFunc('categories','admin','createcatdirectly',Array(
                    'point_of_insertion' => $point_of_insertion,
                    'name' => $name,
                    'description' => $description,
                    'image' => $image,
                    'parent' => $parent_id
                )
            );
}

?>