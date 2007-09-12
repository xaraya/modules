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
 * Link items to categories
 * @param array $args['cids'] Array of IDs of the category
 * @param array $args['iids'] Array of IDs of the items
 * @param int $args['modid'] ID of the module
 * @param int $args['itemtype'] item type

 * Links each cid in cids to each iid in iids

 * @param bool $args['clean_first'] If is set to true then any link of the item IDs
 *                             at iids will be removed before inserting the
 *                             new ones
 * @return bool true on success
 */
function categories_adminapi_linkcat($args)
{
    // Argument check
    if (isset($args['clean_first']) && $args['clean_first'] == true)
    {
        $clean_first = true;
    } else {
        $clean_first = false;
    }

    if (!isset($args['cids']) || !isset($args['iids']) || !isset($args['modid']))
    {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (isset($args['itemtype']) && is_numeric($args['itemtype'])) {
        $itemtype = $args['itemtype'];
    } else {
        $itemtype = 0;
    }

    if (!empty($itemtype)) {
        $modtype = $itemtype;
    } else {
        $modtype = 'All';
    }

    foreach ($args['cids'] as $cid) {
        $cat = xarModAPIFunc(
            'categories', 'user', 'getcatinfo',
            array('cid' => $cid)
        );
        if ($cat == false) {
            $msg = xarML('Unknown Category');
            xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
            return;
        }
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $categorieslinkagetable = $xartable['categories_linkage'];

    if ($clean_first)
    {
        // Get current links
        $childiids = xarModAPIFunc(
            'categories', 'user', 'getlinks',
            array(
                'iids' => $args['iids'],
                'itemtype' => $itemtype,
                'modid' => $args['modid'],
                'reverse' => 0
            )
        );

        if (count($childiids) > 0) {
            // Security check
            foreach ($args['iids'] as $iid)
            {
                foreach (array_keys($childiids) as $cid)
                {
                    if (!xarSecurityCheck('EditCategoryLink', 1, 'Link', "$args[modid]:$modtype:$iid:$cid")) return;
                }
            }

            // Delete old links
            $sql = 'DELETE FROM ' . $categorieslinkagetable
                . ' WHERE xar_modid = ? AND xar_itemtype = ?'
                . ' AND xar_iid IN (?' . str_repeat(',?', count($args['iids'])-1) . ')';
            $result = $dbconn->Execute($sql, array_merge(array((int)$args['modid'], (int)$itemtype), $args['iids']));
            if (!$result) return;
        } else {
            // Security check
            foreach ($args['iids'] as $iid)
            {
                if (!xarSecurityCheck('EditCategoryLink', 1, 'Link', "$args[modid]:$modtype:$iid:All")) return;
            }
        }
    }

    foreach ($args['iids'] as $iid)
    {
        foreach ($args['cids'] as $cid)
        {
            // Security check
            if (!xarSecurityCheck('SubmitCategoryLink', 1, 'Link', "$args[modid]:$modtype:$iid:$cid")) continue;

            // Insert the link
            $bindvars = array((int)$args['modid'], (int)$itemtype, (int)$iid, (int)$cid);

            // Make sure the linkage does not exist first.
            $sql = 'SELECT 1 FROM ' . $categorieslinkagetable
                . ' WHERE xar_modid = ? AND xar_itemtype = ? AND xar_iid = ? AND xar_cid = ?';

            $result =& $dbconn->Execute($sql, $bindvars);

            if ($result->EOF) {
                $sql = 'INSERT INTO ' . $categorieslinkagetable
                    . ' (xar_modid, xar_itemtype, xar_iid, xar_cid)'
                    . ' VALUES(?,?,?,?)';
                $result =& $dbconn->Execute($sql, $bindvars);
                if (!$result) return;
            }
        }
    }

    return true;
}

?>