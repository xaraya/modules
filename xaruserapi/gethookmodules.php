<?php
/**
 * crispBB Forum Module
 *
 * @package modules
 * @copyright (C) 2008-2009 The Digital Development Foundation
 * @license GPL {@link http//www.gnu.org/licenses/gpl.html}
 * @link http//www.xaraya.com
 *
 * @subpackage crispBB Forum Module
 * @link http//xaraya.com/index.php/release/970.html
 * @author crisp <crisp@crispcreations.co.uk>
 */
/**
 * get the list of modules and itemtypes for which we have associated topic
 *
 * @return array $array[$modid][$itemtype] = array('numitems' => $numitems,'numtopics' => $numcats,'numlinks' => $numlinks);
 */
function crispbb_userapi_gethookmodules($args)
{
    // Get arguments from argument array
    extract($args);

    // Database information
    $dbconn =& xarDB::getConn();
    $tables =& xarDB::getTables();
    $hookstable = $tables['crispbb_hooks'];

    $query = "SELECT moduleid,
                    itemtype,
                    COUNT(*),
                    COUNT(DISTINCT itemid),
                    COUNT(DISTINCT tid)
                    FROM $hookstable";

    $query .= " GROUP BY moduleid, itemtype";

    $result = $dbconn->Execute($query,array());
    if (!$result) return;

    $modlist = array();
    while (!$result->EOF) {
        list($modid,$itemtype,$numlinks,$numitems,$numtopics) = $result->fields;
        if (!isset($modlist[$modid])) {
            $modlist[$modid] = array();
        }
        $modlist[$modid][$itemtype] = array('numitems' => $numitems, 'numtopics' => $numtopics, 'numlinks' => $numlinks);
        $result->MoveNext();
    }
    $result->close();

    return $modlist;
}

?>
