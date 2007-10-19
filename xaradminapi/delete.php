<?php
/**
 * Articles module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Articles Module
 * @link http://xaraya.com/index.php/release/151.html
 * @author mikespub
 */
/**
 * Delete an article
 * Usage : if (xarModAPIFunc('articles', 'admin', 'delete', $article)) {...}
 *
 * @param $args['id'] ID of the article
 * @param $args['ptid'] publication type ID for the item (*cough*)
 * @return bool true on success, false on failure
 */
function articles_adminapi_delete($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($id)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'article ID', 'admin', 'delete',
                    'Articles');
        throw new BadParameterException(null,$msg);
    }

    // Security check
    if (!xarModAPILoad('articles', 'user')) return;

    $args['mask'] = 'DeleteArticles';
    if (!xarModAPIFunc('articles','user','checksecurity',$args)) {
        $msg = xarML('Not authorized to delete #(1) items',
                    'Article');
        throw new BadParameterException(null,$msg);
    }

    // Call delete hooks for categories, hitcount etc.
    $args['module'] = 'articles';
    $args['itemid'] = $id;
    if (isset($ptid)) {
        $args['itemtype'] = $ptid;
    } elseif (isset($pubtypeid)) {
        $args['itemtype'] = $pubtypeid;
    }
    xarModCallHooks('item', 'delete', $id, $args);

    // Get database setup
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();
    $articlestable = $xartable['articles'];

    // Delete item
    $query = "DELETE FROM $articlestable
            WHERE id = ?";
    $result =& $dbconn->Execute($query,array($id));
    if (!$result) return;

    return true;
}

?>
