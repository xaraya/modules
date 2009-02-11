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
 * get a specific article by id, or by a combination of other fields
 *
 * @param id $args['id'] id of article to get, or
 * @param id $args['pubtype_id'] pubtype id of article to get, and optional
 * @param string $args['title'] title of article to get, and optional
 * @param string $args['summary'] summary of article to get, and optional
 * @param string $args['body'] body of article to get, and optional
 * @param int $args['owner'] id of the author of article to get, and optional
 * @param $args['pubdate'] pubdate of article to get, and optional
 * @param string $args['notes'] notes of article to get, and optional
 * @param int $args['state'] status of article to get, and optional
 * @param string $args['locale'] language of article to get
 * @param bool $args['withcids'] (optional) if we want the cids too (default false)
 * @param array $args['fields'] array with all the fields to return per article
 *                        Default list is : 'id','title','summary','owner',
 *                        'pubdate','pubtype_id','notes','state','body'
 *                        Optional fields : 'cids','author','counter','rating','dynamicdata'
 * @param array $args['extra'] array with extra fields to return per article (in addition
 *                       to the default list). So you can EITHER specify *all* the
 *                       fields you want with 'fields', OR take all the default
 *                       ones and add some optional fields with 'extra'
 * @param id $args['ptid'] same as 'pubtype_id'
 * @return array article array, or false on failure
 */
function publications_userapi_get($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (isset($id) && (!is_numeric($id) || $id < 1)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'article ID', 'user', 'get',
                    'Publications');
        throw new BadParameterException(null,$msg);
    }

    // allow ptid instead of pubtype_id, like getall and other api's (if both specified, ptid wins)
    if (!empty($ptid))
        $pubtype_id = $ptid;

    // bypass this function, call getall instead?
    if (isset($fields) || isset($extra)) {
        if (!empty($id))
            $args['ids'] = array($id);
        if (!empty($pubtype_id))
            $args['ptid'] = $pubtype_id;
        $wheres = array();
        if (!empty($title))
            $wheres[] = "title eq '$title'";
        if (!empty($summary))
            $wheres[] = "summary eq '$summary'";
        if (!empty($body))
            $wheres[] = "body eq '$body'";
        if (!empty($notes))
            $wheres[] = "notes eq '$notes'";
        if (!empty($withcids))
            $fields[] = "cids";
        foreach ($wheres as $w) {
            if (isset($where))
                $where .= " || $w";
            else
                $where = $w;
        }
        if (isset($where))
            $args['where'] = $where;
        $arts = xarModApiFunc('publications','user','getall', $args );
        if (!empty($arts))
            return current($arts);
        else
            return false;
    }

// TODO: put all this in dynamic data and retrieve everything via there (including hooked stuff)

    $bindvars = array();
    if (!empty($id)) {
        $where = "WHERE id = ?";
        $bindvars[] = $id;
    } else {
        $wherelist = array();
        $fieldlist = array('title','summary','owner','pubdate','pubtype_id',
                           'notes','state','body','locale');
        foreach ($fieldlist as $field) {
            if (isset($$field)) {
                $wherelist[] = "$field = ?";
                $bindvars[] = $$field;
            }
        }
        if (count($wherelist) > 0) {
            $where = "WHERE " . join(' AND ',$wherelist);
        } else {
            $where = '';
        }
    }

    // Get database setup
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();
    $publicationstable = $xartable['publications'];

    // Get item
    $query = "SELECT id,
                   title,
                   summary,
                   body,
                   owner,
                   pubdate,
                   pubtype_id,
                   notes,
                   state,
                   locale
            FROM $publicationstable
            $where";
    if (!empty($id)) {
        $result =& $dbconn->Execute($query,$bindvars);
    } else {
        $result =& $dbconn->SelectLimit($query,1,0,$bindvars);
    }
    if (!$result) return;

    if ($result->EOF) {
        return false;
    }

    list($id, $title, $summary, $body, $owner, $pubdate, $pubtype_id, $notes,
         $state, $locale) = $result->fields;

    $article = array('id' => $id,
                     'title' => $title,
                     'summary' => $summary,
                     'body' => $body,
                     'owner' => $owner,
                     'pubdate' => $pubdate,
                     'pubtype_id' => $pubtype_id,
                     'notes' => $notes,
                     'state' => $state,
                     'locale' => $locale);

    if (!empty($withcids)) {
        $article['cids'] = array();
        if (!xarModAPILoad('categories', 'user')) return;

        $info = xarMod::getBaseInfo('publications');
        $sysid = $info['systemid'];
        $articlecids = xarModAPIFunc('categories',
                                    'user',
                                    'getlinks',
                                    array('iids' => Array($id),
                                          'itemtype' => $pubtype_id,
                                          'modid' => $sysid,
                                          'reverse' => 0
                                         )
                                   );
        if (is_array($articlecids) && count($articlecids) > 0) {
            $article['cids'] = array_keys($articlecids);
        }
    }

    // Security check
    if (isset($article['cids']) && count($article['cids']) > 0) {
// TODO: do we want all-or-nothing access here, or is one access enough ?
        foreach ($article['cids'] as $cid) {
            if (!xarSecurityCheck('ReadPublications',0,'Publication',"$pubtype_id:$cid:$owner:$id")) return;
        // TODO: combine with ViewCategoryLink check when we can combine module-specific
        // security checks with "parent" security checks transparently ?
            if (!xarSecurityCheck('ReadCategories',0,'Category',"All:$cid")) return;
        }
    } else {
        if (!xarSecurityCheck('ReadPublications',0,'Publication',"$pubtype_id:All:$owner:$id")) return;
    }

/*
    if (xarModIsHooked('dynamicdata','publications')) {
        $values = xarModAPIFunc('dynamicdata','user','getitem',
                                 array('module'   => 'publications',
                                       'itemtype' => $pubtype_id,
                                       'itemid'   => $id));
        if (!empty($values) && count($values) > 0) {
        // TODO: compare with looping over $name => $value pairs
            $article = array_merge($article,$values);
        }
    }
*/
    return $article;
}

?>