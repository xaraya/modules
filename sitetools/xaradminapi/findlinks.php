<?php
/*
 * File: $Id: $
 *
 * SiteTools Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by jojodee
 * @link http://xaraya.athomeandabout.com
 *
 * @subpackage SiteTools module
 * @author Jo Dalle Nogare <http://xaraya.athomeandabout.com  contact:jojodee@xaraya.com>
*/

/**
 * Find links in fields from different modules/itemtypes
 *
 * @param $args['fields'] array of [module][itemtype] = fieldlist
 * @param $args['skiplocal'] bool optional flag to skip local links (default false)
 * @returns array
 * @return number of links found per module/itemtype
 * @raise DATABASE_ERROR
*/
function sitetools_adminapi_findlinks($args)
{ 
    extract($args);

    if (!isset($skiplocal)) $skiplocal = false;

    // load APIs for table names etc.
    xarModAPILoad('roles','user');

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $linkstable = $xartable['sitetools_links'];

    // remove old links from the database
    $query = "DELETE FROM $linkstable";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $count = array();

    $server = xarServerGetHost();

    // find links for articles
    if (!empty($fields['articles']) && xarModIsAvailable('articles')) {
        $modid = xarModGetIDFromName('articles');
        $pubtypes = xarModAPIFunc('articles','user','getpubtypes');

        foreach ($fields['articles'] as $ptid => $fieldlist) {
            $where = array();
            foreach ($fieldlist as $field) {
                $where[] = $field . " ne ''";
            }
            $whereclause = join(' or ',$where);
            $getfields = $fieldlist;
            if (!in_array('aid',$getfields)) $getfields[] = 'aid';
            if (!in_array('title',$getfields)) $getfields[] = 'title';
            $items = xarModAPIFunc('articles','user','getall',
                                   array('ptid' => $ptid,
                                         'fields' => $getfields,
                                         'where' => $whereclause));
            $serialized = array();
            foreach ($fieldlist as $field) {
                if ($pubtypes[$ptid]['config'][$field]['format'] == 'urltitle') {
                    $serialized[$field] = 1;
                }
            }
            $descr = $pubtypes[$ptid]['descr'];
            $count[$descr] = 0;
            foreach ($items as $item) {
                $url = xarModURL('articles','user','display',
                                 array('aid' => $item['aid']));
                foreach ($fieldlist as $field) {
                    if (empty($item[$field])) continue;
                    if (!empty($serialized[$field])) {
                        $info = unserialize($item[$field]);
                        if (empty($info['link'])) continue;
                        $item[$field] = $info['link'];
                    }
                    if ($skiplocal &&
                        (!strstr($item[$field],'://') ||
                          preg_match("!://($server|localhost|127\.0\.0\.1)((:\d+)?/|$)!",$item[$field])) ) {
                        continue;
                    }
                    $id = $dbconn->GenId($linkstable);
                    $query = "INSERT INTO $linkstable (xar_id, xar_link, xar_status, xar_moduleid, xar_itemtype, xar_itemid, xar_itemtitle, xar_itemlink)
                              VALUES ($id, '" . xarVarPrepForStore($item[$field]) . "', 0, $modid, $ptid, $item[aid], '" . xarVarPrepForStore($item['title']) . "', '" . xarVarPrepForStore($url) . "')";
                    $result =& $dbconn->Execute($query);
                    if (!$result) return;
                    $count[$descr]++;
                }
            }
        }
    }

    // find links for roles
    if (!empty($fields['roles'])) {
        $modid = xarModGetIDFromName('roles');
        $rolestable = $xartable['roles'];
        // only 1 itemtype for now, but groups might have separate DD fields later on
        $rolesobject = xarModAPIFunc('dynamicdata','user','getobject',
                                     array('module' => 'roles'));
        $descr = array(0 => xarML('Users'),
                       1 => xarML('Groups'));

        foreach ($fields['roles'] as $itemtype => $fieldlist) {
            $where = array();
            foreach ($fieldlist as $field) {
                $where[] = $field . " ne ''";
            }
            $whereclause = join(' or ',$where);
            $getfields = $fieldlist;
            //if (!in_array('uid',$getfields)) $getfields[] = 'uid';
            if (!in_array('name',$getfields)) $getfields[] = 'name';
            $items = xarModAPIFunc('dynamicdata','user','getitems',
                                   array('module' => 'roles',
                                         'itemtype' => $itemtype,
                                     // join with the roles table for user name
                                         'join' => $rolestable,
                                         'fieldlist' => $getfields,
                                         'where' => $whereclause));
            $serialized = array();
            foreach ($fieldlist as $field) {
                if ($rolesobject->properties[$field]->type == 41) { // urltitle
                    $serialized[$field] = 1;
                }
            }
            $count[$descr[$itemtype]] = 0;
            foreach ($items as $itemid => $item) {
                $url = xarModURL('roles','user','display',
                                 array('uid' => $itemid));
                foreach ($fieldlist as $field) {
                    if (empty($item[$field])) continue;
                    if (!empty($serialized[$field])) {
                        $info = unserialize($item[$field]);
                        if (empty($info['link'])) continue;
                        $item[$field] = $info['link'];
                    }
                    if ($skiplocal &&
                        (!strstr($item[$field],'://') ||
                          preg_match("!://($server|localhost|127\.0\.0\.1)((:\d+)?/|$)!",$item[$field])) ) {
                        continue;
                    }
                    $id = $dbconn->GenId($linkstable);
                    $query = "INSERT INTO $linkstable (xar_id, xar_link, xar_status, xar_moduleid, xar_itemtype, xar_itemid, xar_itemtitle, xar_itemlink)
                              VALUES ($id, '" . xarVarPrepForStore($item[$field]) . "', 0, $modid, $itemtype, $itemid, '" . xarVarPrepForStore($item['name']) . "', '" . xarVarPrepForStore($url) . "')";
                    $result =& $dbconn->Execute($query);
                    if (!$result) return;
                    $count[$descr[$itemtype]]++;
                }
            }
        }
    }

    // TODO: find links for ...

    return $count;
}

?>
