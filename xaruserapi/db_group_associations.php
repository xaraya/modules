<?php
/**
 * Uploads Module
 *
 * @package modules
 * @subpackage uploads module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright see the html/credits.html file in this Xaraya release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com/index.php/release/eid/666
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
    if (!xarSecurity::check('ViewUploads')) {
        return;
    }

    if (empty($fileId) || !is_numeric($fileId)) {
        $fileId = 0;
    }

    // Database information
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();
    $fileassoctable = $xartable['file_associations'];

    if ($dbconn->databaseType == 'sqlite') {

    // TODO: see if we can't do this some other way in SQLite

        $bindvars = [];
        // Get links
        $sql = "SELECT xar_modid, xar_itemtype, COUNT(*)
                FROM $fileassoctable";
        if (!empty($fileId)) {
            $sql .= " WHERE xar_fileEntry_id = ?";
            $bindvars[] = $fileId;
        }
        $sql .= " GROUP BY xar_modid, xar_itemtype";

        $result = $dbconn->Execute($sql, $bindvars);
        if (!$result) {
            return;
        }

        $modlist = [];
        while (!$result->EOF) {
            [$modid, $itemtype, $numlinks] = $result->fields;
            if (!isset($modlist[$modid])) {
                $modlist[$modid] = [];
            }
            $modlist[$modid][$itemtype] = ['items' => 0, 'files' => 0, 'links' => $numlinks];
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

        $result = $dbconn->Execute($sql, $bindvars);
        if (!$result) {
            return;
        }

        while (!$result->EOF) {
            [$modid, $itemtype, $numitems] = $result->fields;
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

        $result = $dbconn->Execute($sql, $bindvars);
        if (!$result) {
            return;
        }

        while (!$result->EOF) {
            [$modid, $itemtype, $numfiles] = $result->fields;
            $modlist[$modid][$itemtype]['files'] = $numfiles;
            $result->MoveNext();
        }
        $result->close();
    } else {
        $bindvars = [];
        // Get items
        $sql = "SELECT xar_modid, xar_itemtype, COUNT(*), COUNT(DISTINCT xar_objectid), COUNT(DISTINCT xar_fileEntry_id)
                FROM $fileassoctable";
        if (!empty($fileId)) {
            $sql .= " WHERE xar_fileEntry_id = ?";
            $bindvars[] = $fileId;
        }
        $sql .= " GROUP BY xar_modid, xar_itemtype";

        $result = $dbconn->Execute($sql, $bindvars);
        if (!$result) {
            return;
        }

        $modlist = [];
        while (!$result->EOF) {
            [$modid, $itemtype, $numlinks, $numitems, $numfiles] = $result->fields;
            if (!isset($modlist[$modid])) {
                $modlist[$modid] = [];
            }
            $modlist[$modid][$itemtype] = ['items' => $numitems, 'files' => $numfiles, 'links' => $numlinks];
            $result->MoveNext();
        }
        $result->close();
    }

    return $modlist;
}
