<?php
/**
 * Count the number of docs
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage release
 * @link http://xaraya.com/index.php/release/773.html
 */
/**
 * count the number of docs per release
 *
 * @param int $rid ID
 * @return int number of docs for rid
 */
function release_userapi_countdocs($args)
{
    extract($args);

    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();

    $releasetable = $xartable['release_docs'];

    $query = "SELECT COUNT(1)
            FROM $releasetable
            WHERE xar_eid = ?";
    $result =&$dbconn->Execute($query, [$eid]);
    if (!$result) {
        return;
    }

    [$numitems] = $result->fields;

    $result->Close();

    return $numitems;
}
