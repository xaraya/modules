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
 * Delete all links for a specific module, itemtype and list of cids (e.g. orphan links)
 * @param int $args['modid'] ID of the module
 * @param int $args['itemtype'] item type
 * @param array $args['cids'] array of category ids
 */
function categories_adminapi_unlinkcids($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((empty($modid)) || !is_numeric($modid) ||
        (empty($cids)) || !is_array($cids))
    {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (!isset($itemtype) || !is_numeric($itemtype)) {
        $itemtype = 0;
    }

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $categorieslinkagetable = $xartable['categories_linkage'];

    // Delete the link
    $bindvars = array();
    $query = "DELETE FROM $categorieslinkagetable
              WHERE xar_modid = ?
                AND xar_itemtype = ?
                AND xar_cid IN (?";
    $bindvars[] = (int) $modid;
    $bindvars[] = (int) $itemtype;
    $bindvars[] = (int) array_shift($cids);
    foreach ($cids as $cid) {
        $bindvars[] = (int) $cid;
        $query .= ',?';
    }
    $query .= ')';

    $result = $dbconn->Execute($query,$bindvars);
    if (!$result) return;

    return true;
}

?>
