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
 * Update an article
 * Usage : if (xarModAPIFunc('articles', 'admin', 'update', $article)) {...}
 *
 * @param id $args['id'] ID of the item (mandatory argument)
 * @param string $args['title'] name of the item (mandatory argument)
 * @param string $args['summary'] summary of the item
 * @param string $args['body'] body of the item
 * @param string $args['notes'] notes for the item
 * @param $args['status'] status of the item
 * @param int $args['ptid'] publication type ID for the item (*cough*)
 * @param int $args['pubdate'] publication date in unix time format
 * @param int $args['authorid'] ID of the new author (*cough*)
 * @param $args['language'] language of the item
 * @param $args['cids'] category IDs this item belongs to
 * @return bool true on success, false on failure
 */
function articles_adminapi_update($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (empty($id) || !is_numeric($id)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'article ID', 'admin', 'update',
                    'Articles');
        throw new BadParameterException(null,$msg);
    } elseif (empty($title)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'title', 'admin', 'update',
                    'Articles');
        throw new BadParameterException(null,$msg);
    }

// Note : this will take care of checking against the current article values
//        too if nothing is passed as arguments except id & title

    // Security check
    if (!xarModAPILoad('articles', 'user')) return;

    $args['mask'] = 'EditArticles';
    if (!xarModAPIFunc('articles','user','checksecurity',$args)) {
        $msg = xarML('Not authorized to update #(1) items',
                    'Article');
        throw new ForbiddenOperationException(null, $msg);
    }

    // Get database setup
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();
    $articlestable = $xartable['articles'];
    $bindvars = array();
    // Update the item
    $query = "UPDATE $articlestable
            SET title = ?";
    $bindvars[] = (string) $title;
// Note : we use isset() here because we *do* care whether it's set to ''
//        or if it's not set at all

    if (isset($summary)) {
        $query .= ", summary = ?";
        $bindvars[] = (string) $summary;
    }

    if (isset($body)) {
        $query .= ", body = ?";
        $bindvars[] = (string) $body;
    }

    if (isset($notes)) {
        $query .= ", notes = ?";
        $bindvars[] = (string) $notes;
    }

    if (isset($status) && is_numeric($status)) {
        $oldversion= xarModAPIFunc('articles','user','get',
                                    array('id'=>$id,
                                          'fields'=>array('status')));
        $args['oldstatus'] = $oldversion['status'];
        $query .= ", status = ?";
        $bindvars[] = (int) $status;
    }

    // not recommended
    if (isset($ptid) && is_numeric($ptid)) {
        $query .= ", pubtypeid = ?";
        $bindvars[] = (int) $ptid;
    }

    if (isset($pubdate) && is_numeric($pubdate)) {
        $query .= ", pubdate = ?";
        $bindvars[] = (int) $pubdate;
    }

    // not recommended
    if (isset($authorid) && is_numeric($authorid)) {
        $query .= ", authorid = ?";
        $bindvars[] = (int) $authorid;
    }

    if (isset($language)) {
        $query .= ", language = ?";
        $bindvars[] = (string) $language;
    }
    $query .= " WHERE id = ?";
    $bindvars[] =  (int) $id;
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;

    if (empty($cids)) {
        $cids = array();
    }

    // Call update hooks for categories etc.
    // We need to tell some hooks that we are coming from the update status screen
    // and not the update the actual article screen.  Right now, the keywords vanish
    // into thin air.  Bug 1960 and 3161
    if (xarVarIsCached('Hooks.all','noupdate')){
        $args['statusflag'] = true; // legacy support for old method - remove later on
    }

/* ---------------------------- TODO: Remove */
    sys::import('modules.dynamicdata.class.properties.master');
    $categories = DataPropertyMaster::getProperty(array('name' => 'categories'));
    $categories->checkInput('categories',$id);
/*------------------------------- */

    $args['module'] = 'articles';
    if (isset($ptid)) {
        $args['itemtype'] = $ptid;
    } elseif (isset($pubtypeid)) {
        $args['itemtype'] = $pubtypeid;
    }
    $args['cids'] = $cids;
    xarModCallHooks('item', 'update', $id, $args);

    return true;
}

?>
