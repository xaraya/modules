<?php
/**
 * Gallery
 *
 * @package   Xaraya eXtensible Management System
 * @copyright (C) 2006 by Brian McGilligan
 * @license   New BSD License <http://www.abrasiontechnology.com/index.php/page/7>
 * @link      http://www.abrasiontechnology.com/
 *
 * @subpackage Gallery module
 * @author     Brian McGilligan
 */
/*
    Makes sure that the gallery name is unique so that we don't have two gallerys with
    the same name. As that would cause storage problems.
*/
function gallery_userapi_make_unique_album_name($args)
{
    extract($args);

    if( empty($name) )
        return false;

    $dbconn   =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $album_table = $xartable['gallery_albums'];

    $i = 0;
    $new_name = $name;
    do
    {
        $i++;

        $sql = "
            SELECT *
            FROM $album_table
            WHERE name = ?
        ";
        $bindvars = array($new_name);
        $rs = $dbconn->Execute($sql, $bindvars);

        if( !$rs->EOF )
        {
            /*
                Modify name to try to make unique
            */
            $new_name = $name . '_' .$i;
        }

    }while( !$rs->EOF );

    $rs->Close();

    return $new_name;
}
?>