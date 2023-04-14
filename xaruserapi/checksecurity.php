<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author mikespub
 */
/**
 * check security for a particular article
 *
 * @param $args['mask'] the requested security mask
 *
 * @param $args['article'] the article array (if already retrieved)
 * @param $args['id'] the article ID (if known, and article array not
                       already retrieved)
 * @param $args['owner'] the user ID of the author (if not already included)
 * @param $args['ptid'] the publication type ID (if not already included)
 * @param $args['cids'] array of additional required category checks
 *
 * @return bool true if OK, false if not OK
 */
function publications_userapi_checksecurity($args)
{
    // Get arguments from argument array
    extract($args);

    // Compatibility mode with old API params - remove later
    if (isset($access) && !isset($mask)) {
        switch ($access) {
            case ACCESS_OVERVIEW:
                $mask = 'ViewPublications';
                break;
            case ACCESS_READ:
                $mask = 'ReadPublications';
                break;
            case ACCESS_COMMENT:
                $mask = 'SubmitPublications';
                break;
            case ACCESS_EDIT:
                $mask = 'EditPublications';
                break;
            case ACCESS_DELETE:
                $mask = 'ManagePublications';
                break;
            case ACCESS_ADMIN:
                $mask = 'AdminPublications';
                break;
            default:
                $mask = '';
        }
    }

    if (empty($mask)) {
        return false;
    }
    // Get article information
    if (!isset($publication) && !empty($id) && $mask != 'SubmitPublications') {
        $publication = xarMod::apiFunc('publications',
                                'user',
                                'get',
                                array('id' => $id,
                                      'withcids' => true));
        if ($publication == false) {
            return false;
        }
    }
    if (empty($id) && isset($publication['id'])) {
        $id = $publication['id'];
    }
    if (!isset($id)) {
        $id = '';
    }

    // Get author ID
    if (isset($publication['owner']) && empty($owner)) {
        $owner = $publication['owner'];
    }

    // Get state
    if (isset($publication['state']) && !isset($state)) {
        $state = $publication['state'];
    }
    if (empty($state)) {
        $state = 0;
    }
    // reject reading access to unapproved publications
    if ($state < 2 && ($mask == 'ViewPublications' || $mask == 'ReadPublications')) {
        return false;
    }

    // Get publication type ID
    if (isset($publication['pubtype_id'])) {
        if (!isset($ptid)) {
            $ptid = $publication['pubtype_id'];
        } elseif ($ptid != $publication['pubtype_id'] && $mask != 'EditPublications') {
            return false;
        }
    }

    // Get root categories for this publication type
    if (!empty($ptid)) {
        $rootcats = xarMod::apiFunc('categories','user','getallcatbases',array('module' => 'publications', 'itemtype' => $ptid));
    } else {
        $ptid = null;
    }
    if (!isset($rootcids)) {
    // TODO: handle cross-pubtype views better
        $rootcats = xarMod::apiFunc('categories','user','getallcatbases',array('module' => 'publications'));
    }

    // Get category information for this article
    if (!isset($publication['cids']) && !empty($id)) {
        if (!xarMod::apiLoad('categories', 'user')) return;
        $info = xarMod::getBaseInfo('publications');
        $sysid = $info['systemid'];
        $publicationcids = xarMod::apiFunc('categories',
                                    'user',
                                    'getlinks',
                                    array('iids' => Array($id),
                                          'itemtype' => $ptid,
                                          'modid' => $sysid,
                                          'reverse' => 0
                                         )
                                   );
        if (is_array($publicationcids) && count($publicationcids) > 0) {
            $publication['cids'] = array_keys($publicationcids);
        }
    }
    if (!isset($publication['cids'])) {
        $publication['cids'] = array();
    }

    if (!isset($cids)) {
        $cids = array();
    }

    $jointcids = array();
/* TODO: forget about parent/root cids for now
    foreach ($rootcids as $cid) {
        $jointcids[$cid] = 1;
    }
*/
    foreach ($publication['cids'] as $cid) {
        $jointcids[$cid] = 1;
    }
    // FIXME: the line within the foreach is known to give an illegal offset error, not sure how to properly
    // fix it. Only seen on using xmlrpc and bloggerapi.
    foreach ($cids as $cid) {
        if (empty($cid) || !is_numeric($cid)) continue;
        $jointcids[$cid] = 1;
    }

// TODO 1: find a way to combine checking over several categories
// TODO 2: find a way to check parent categories for privileges too

// TODO 3: find a way to specify current user in privileges too
// TODO 4: find a way to check parent groups of authors for privileges too ??

    if (empty($ptid)) {
        $ptid = 'All';
    }
    if (count($jointcids) == 0) {
        $jointcids['All'] = 1;
    }
// TODO: check for anonymous publications
    if (!isset($owner)) {
        $owner = 'All';
    }
    if (empty($id)) {
        $id = 'All';
    }

    // Loop over all categories and check the different combinations
    $result = false;
    foreach (array_keys($jointcids) as $cid) {
// TODO: do we want all-or-nothing access here, or is one access enough ?
        if (xarSecurity::check($mask,0,'Publication',"$ptid:$cid:$owner:$id")) {
            $result = true;
        }
    }
    return $result;
}

?>