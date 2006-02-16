<?php
/**
 * Articles module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Articles Module
 * @link http://xaraya.com/index.php/release/151.html
 * @author mikespub
 */
/**
 * get the name and description of all publication types
 *
 * @return array(id => array('name' => name, 'descr' => descr)), or false on
 *         failure
 */
function articles_userapi_getpubtypes($args)
{
    static $pubtypes = array();

    if (count($pubtypes) > 0) {
        return $pubtypes;
    }

    if (isset($args['sort'])) {
        $sort = $args['sort'];
    } else {
        $sort = xarModGetVar('articles','sortpubtypes');
    }
    if (empty($sort)) {
        $sort = 'id';
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pubtypestable = $xartable['publication_types'];

    // Get item
    $query = "SELECT xar_pubtypeid,
                   xar_pubtypename,
                   xar_pubtypedescr,
                   xar_pubtypeconfig
            FROM $pubtypestable";
    switch ($sort) {
        case 'name':
            $query .= " ORDER BY xar_pubtypename ASC";
            break;
        case 'descr':
            $query .= " ORDER BY xar_pubtypedescr ASC";
            break;
        case 'id':
        default:
            $query .= " ORDER BY xar_pubtypeid ASC";
            break;
    }
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    if ($result->EOF) {
        return $pubtypes;
    }
    while (!$result->EOF) {
        list($id, $name, $descr, $config) = $result->fields;
        $pubtypes[$id] = array('name' => $name,
                               'descr' => $descr,
                               'config' => unserialize($config));
        $result->MoveNext();
    }

    return $pubtypes;
}

?>
