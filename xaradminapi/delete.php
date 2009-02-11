<?php
/**
 * Publications module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Publications Module
 
 * @author mikespub
 */
/**
 * Delete an article
 * Usage : if (xarModAPIFunc('publications', 'admin', 'delete', $article)) {...}
 *
 * @param $args['id'] ID of the article
 * @param $args['ptid'] publication type ID for the item (*cough*)
 * @return bool true on success, false on failure
 */
function publications_adminapi_delete($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($id)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'article ID', 'admin', 'delete',
                    'Publications');
        throw new BadParameterException(null,$msg);
    }

    // Security check
    if (!xarModAPILoad('publications', 'user')) return;

    $args['mask'] = 'ManagePublications';
    if (!xarModAPIFunc('publications','user','checksecurity',$args)) {
        $msg = xarML('Not authorized to delete #(1) items',
                    'Publication');
        throw new BadParameterException(null,$msg);
    }

    // Call delete hooks for categories, hitcount etc.
    $args['module'] = 'publications';
    $args['itemid'] = $id;
    if (isset($ptid)) {
        $args['itemtype'] = $ptid;
    } elseif (isset($pubtype_id)) {
        $args['itemtype'] = $pubtype_id;
    }
    xarModCallHooks('item', 'delete', $id, $args);

    // Get database setup
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();
    $publicationstable = $xartable['publications'];

    // Delete item
    $query = "DELETE FROM $publicationstable
            WHERE id = ?";
    $result =& $dbconn->Execute($query,array($id));
    if (!$result) return;

    return true;
}

?>
