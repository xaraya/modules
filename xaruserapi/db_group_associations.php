<?php
/**
 * Purpose of File
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Uploads Module
 * @link http://xaraya.com/index.php/release/666.html
 * @author Uploads Module Development Team
 */
/**
 * get the list of modules and itemtypes we're associating files with
 *
 * @return array
 * @return $array[$modid][$itemtype] = array('items' => $numitems,'files' => $numfiles,'links' => $numlinks);
 */
function uploads_userapi_db_group_associations($args)
{
    // Get arguments from argument array
    extract($args);

    // Security check
    if (!xarSecurityCheck('ViewUploads')) return;

    if (empty($fileId) || !is_numeric($fileId)) {
        $fileId = 0;
    }

    // Database information
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();
    $fileassoctable = $xartable['file_associations'];

    if($dbconn->databaseType == 'sqlite') {

    // TODO: see if we can't do this some other way in SQLite

        $bindvars = array();
        // Get links
        $sql = "SELECT xar_modid, xar_itemtype, COUNT(*)
                FROM $fileassoctable";
        if (!empty($fileId)) {
            $sql .= " WHERE xar_fileEntry_id = ?";
            $bindvars[] = $fileId;
        }
        $sql .= " GROUP BY xar_modid, xar_itemtype";

        $result = $dbconn->Execute($sql,$bindvars);
        if (!$result) return;

        $modlist = array();
        while (!$result->EOF) {
            list($modid,$itemtype,$numlinks) = $result->fields;
            if (!isset($modlist[$modid])) {
                $modlist[$modid] = array();
            }
            $modlist[$modid][$itemtype] = array('items' => 0, 'files' => 0, 'links' => $numlinks);
            $result->MoveNext();
        }
        $result->close();

        // Get items
        $sql = "SELECT xar_modid, xar_itemtype, COUNT(*)
                FROM (SELECT DISTINCT xar_objectid, xar_modid, xar_itemtype
                      FROM $fileassoctable";
        if (!empty($fileId)) {
            $sql .= " WHERE xar_fileEntry_id = ?";
            $bindvars[] = $fileId;
        }
        $sql .= ") GROUP BY xar_modid, xar_itemtype";

        $result = $dbconn->Execute($sql,$bindvars);
        if (!$result) return;

        while (!$result->EOF) {
            list($modid,$itemtype,$numitems) = $result->fields;
            $modlist[$modid][$itemtype]['items'] = $numitems;
            $result->MoveNext();
        }
        $result->close();

        // Get files
        $sql = "SELECT xar_modid, xar_itemtype, COUNT(*)
                FROM (SELECT DISTINCT xar_fileEntry_id, xar_modid, xar_itemtype
                      FROM $fileassoctable";
        if (!empty($fileId)) {
            $sql .= " WHERE xar_fileEntry_id = ?";
            $bindvars[] = $fileId;
        }
        $sql .= ") GROUP BY xar_modid, xar_itemtype";

        $result = $dbconn->Execute($sql,$bindvars);
        if (!$result) return;

        while (!$result->EOF) {
            list($modid,$itemtype,$numfiles) = $result->fields;
            $modlist[$modid][$itemtype]['files'] = $numfiles;
            $result->MoveNext();
        }
        $result->close();

    } else {
        $bindvars = array();
        // Get items
        $sql = "SELECT xar_modid, xar_itemtype, COUNT(*), COUNT(DISTINCT xar_objectid), COUNT(DISTINCT xar_fileEntry_id)
                FROM $fileassoctable";
        if (!empty($fileId)) {
            $sql .= " WHERE xar_fileEntry_id = ?";
            $bindvars[] = $fileId;
        }
        $sql .= " GROUP BY xar_modid, xar_itemtype";

        $result = $dbconn->Execute($sql,$bindvars);
        if (!$result) return;

        $modlist = array();
        while (!$result->EOF) {
            list($modid,$itemtype,$numlinks,$numitems,$numfiles) = $result->fields;
            if (!isset($modlist[$modid])) {
                $modlist[$modid] = array();
            }
            $modlist[$modid][$itemtype] = array('items' => $numitems, 'files' => $numfiles, 'links' => $numlinks);
            $result->MoveNext();
        }
        $result->close();
    }

    return $modlist;
}

?>
