<?php
/**
 * Mime Module
 *
 * @package modules
 * @subpackage mime module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright see the html/credits.html file in this Xaraya release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com/index.php/release/eid/999
 * @author Carl Corliss <rabbitt@xaraya.com>
 */

/**
 *  Get the name of a mime type
 *
 *  @author Carl P. Corliss
 *  @access public
 *  @param  integer    extensionId        the ID of the extension to lookup   (optional)
 *  @param  integer    extensionName     the Name of the extension to lookup (optional)
 *  returns array      An array of (subtypeId, extension) or an empty array
 */

function mime_userapi_get_extension($args)
{
    extract($args);

    if (!isset($extensionId) && !isset($extensionName)) {
        $msg = xarML('No (usable) parameter to work with (#(1)::#(2)::#(3))', 'mime', 'userapi', 'get_extension');
        throw new Exception($msg);
    }

    // Get database setup
    $dbconn = xarDB::getConn();
    $xartable     =& xarDB::getTables();

    $where = ' WHERE ';

    if (isset($extensionId)) {
        $where .= ' id = ' . $extensionId;
    } else {
        $where .= " name = '".strtolower($extensionName)."'";
    }

    // table and column definitions
    $extension_table =& $xartable['mime_extension'];

    $sql = "SELECT subtype_id,
                   id,
                   name
              FROM $extension_table
            $where";

    $result = $dbconn->Execute($sql);

    if (!$result || $result->EOF) {
        return [];
    }

    $row = $result->GetRowAssoc(false);

    return ['subtypeId'     => $row['subtype_id'],
                 'extensionId'   => $row['id'],
                 'extensionName' => $row['name'], ];
}
