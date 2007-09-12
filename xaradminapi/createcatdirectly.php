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
function categories_adminapi_createcatdirectly($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($name))        ||
        (!isset($description)) ||
        (!isset($point_of_insertion)))
    {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (!isset($image)) {
        $image = '';
    }
    if (!isset($parent)) {
        $parent = 0;
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $categoriestable = $xartable['categories'];
    $bindvars = array();
    $bindvars[1] = array();
    $bindvars[2] = array();
    $bindvars[3] = array();

    // Get next ID in table
    $nextId = $dbconn->GenId($categoriestable);

    /* Opening space for the new node */
    $SQLquery[1] = "UPDATE $categoriestable
                    SET xar_right = xar_right + 2
                    WHERE xar_right >= ?";
    $bindvars[1][] = $point_of_insertion;

    $SQLquery[2] = "UPDATE $categoriestable
                    SET xar_left = xar_left + 2
                    WHERE xar_left >= ?";
    $bindvars[2][] = $point_of_insertion;
    // Both can be transformed into just one SQL-statement, but i dont know if every database is SQL-92 compliant(?)

    $nextID = $dbconn->GenId($categoriestable);

    $SQLquery[3] = "INSERT INTO $categoriestable (
                                xar_cid,
                                xar_name,
                                xar_description,
                                xar_image,
                                xar_parent,
                                xar_left,
                                xar_right)
                         VALUES (?,?,?,?,?,?,?)";
    $bindvars[3] = array($nextID, $name, $description, $image, $parent, $point_of_insertion, $point_of_insertion + 1);

    for ($i=1;$i<4;$i++)
    {
        $result = $dbconn->Execute($SQLquery[$i],$bindvars[$i]);
        if (!$result) return;
    }


    // Call create hooks for categories, hitcount etc.
    $cid = $dbconn->PO_Insert_ID($categoriestable, 'xar_cid');

    //Hopefully Hooks will work-out better these args in the near future
    $args['module'] = 'categories';
    $args['itemtype'] = 0;
    $args['itemid'] = $cid;
    xarModCallHooks('item', 'create', $cid, $args);

    // Get cid to return
    return $cid;
}

?>
