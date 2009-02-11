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
 * utility function to pass individual item links to whoever
 *
 * @param $args['itemtype'] item type (optional)
 * @param $args['itemids'] array of item ids to get
 * @param $args['field'] field to return as label in the list (default 'title')
 * @return array Array containing the itemlink(s) for the item(s).
 */
function publications_userapi_getitemlinks($args)
{
    extract($args);

    $itemlinks = array();
    if (empty($itemtype)) {
        $itemtype = null;
    }
    // get cids for security check in getall
    $fields = array('id','title','pubtype_id','cids');

    // make sure we have the title field we want here
    if (empty($field)) {
        $field = 'title';
    } elseif (!in_array($field,$fields)) {
        $fields[] = $field;
    }
    if (empty($sort)) {
        $sort = null;
    }

    if (xarSecurityCheck('AdminPublications',0)) {
        // get all publications for admins (not editors)
        $state = null;
    } else {
// CHECKME: make sure we don't need other statuses somewhere
        // get approved and frontpage publications only
        $state = array(2, 3);
    }
    $publications = xarModAPIFunc('publications','user','getall',
                             array('ids' => $itemids,
                                   'ptid' => $itemtype,
                                   'fields' => $fields,
                                   'state' => $state,
                                   'sort' => $sort,
                                  )
                            );
    if (!isset($publications) || !is_array($publications) || count($publications) == 0) {
       return $itemlinks;
    }

    // if we didn't have a list of itemids, return all the publications we found
    if (empty($itemids)) {
        foreach ($publications as $article) {
            $itemid = $article['id'];
            if (!isset($article[$field])) continue;
            $itemlinks[$itemid] = array('url'   => xarModURL('publications', 'user', 'display',
                                                             array('ptid' => $article['pubtype_id'],
                                                                   'id' => $article['id'])),
                                        'title' => xarML('Display Publication'),
                                        'label' => xarVarPrepForDisplay($article[$field]));
        }
        return $itemlinks;
    }

    // if we had a list of itemids, return only those publications
    $itemid2key = array();
    foreach ($publications as $key => $article) {
        $itemid2key[$article['id']] = $key;
    }
    foreach ($itemids as $itemid) {
        if (!isset($itemid2key[$itemid])) continue;
        $article = $publications[$itemid2key[$itemid]];
        if (!isset($article[$field])) continue;
        $itemlinks[$itemid] = array('url'   => xarModURL('publications', 'user', 'display',
                                                                 array('ptid' => $article['pubtype_id'],
                                                                       'id' => $article['id'])),
                                            'title' => xarML('Display Publication'),
                                            'label' => xarVarPrepForDisplay($article[$field]));
    }
    return $itemlinks;
}

?>
