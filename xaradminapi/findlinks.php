<?php
/**
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sitetools
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/**
 * Find links in fields from different modules/itemtypes
 * @author mikespub
 * @param $args['fields'] array of [module][itemtype] = fieldlist
 * @param $args['skiplocal'] bool optional flag to skip local links (default false)
 * @returns array
 * @return number of links found per module/itemtype
 * @raise DATABASE_ERROR
*/
function sitetools_adminapi_findlinks($args)
{
    extract($args);

    if (!isset($skiplocal)) {
        $skiplocal = false;
    }

    // load APIs for table names etc.
    xarMod::apiLoad('roles', 'user');
    xarMod::apiLoad('dynamicdata', 'user');
    if (xarMod::isAvailable('articles')) {
        xarMod::apiLoad('articles', 'user');
    }

    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    $linkstable = $xartable['sitetools_links'];

    // remove old links from the database
    $query = "DELETE FROM $linkstable";
    $result =& $dbconn->Execute($query);
    if (!$result) {
        return;
    }

    $count = [];

    $server = xarServer::getHost();

    // find links for articles
    if (!empty($fields['articles']) && xarMod::isAvailable('articles')) {
        $modid = xarMod::getRegID('articles');
        $articlestable = $xartable['articles'];
        $pubtypes = xarMod::apiFunc('articles', 'user', 'getpubtypes');

        foreach ($fields['articles'] as $ptid => $fieldlist) {
            $descr = $pubtypes[$ptid]['descr'];
            $count[$descr] = 0;
            $articlefields = [];
            $dynamicfields = [];
            // get the list of defined columns for articles
            $columns = array_keys($pubtypes[$ptid]['config']);
            foreach ($fieldlist as $field) {
                if (in_array($field, $columns)) {
                    $articlefields[] = $field;
                } else {
                    $dynamicfields[] = $field;
                }
            }
            if (count($articlefields) > 0) {
                $where = [];
                foreach ($articlefields as $field) {
                    $where[] = $field . " ne ''";
                }
                $whereclause = join(' or ', $where);
                $getfields = $articlefields;
                if (!in_array('aid', $getfields)) {
                    $getfields[] = 'aid';
                }
                if (!in_array('title', $getfields)) {
                    $getfields[] = 'title';
                }
                $items = xarMod::apiFunc(
                    'articles',
                    'user',
                    'getall',
                    ['ptid' => $ptid,
                                             'fields' => $getfields,
                                             'where' => $whereclause, ]
                );
                $serialized = [];
                foreach ($articlefields as $field) {
                    if ($pubtypes[$ptid]['config'][$field]['format'] == 'urltitle') {
                        $serialized[$field] = 1;
                    }
                }
                foreach ($items as $item) {
                    $url = xarController::URL(
                        'articles',
                        'user',
                        'display',
                        ['aid' => $item['aid']]
                    );
                    foreach ($articlefields as $field) {
                        if (empty($item[$field])) {
                            continue;
                        }
                        if (!empty($serialized[$field])) {
                            $info = unserialize($item[$field]);
                            if (empty($info['link'])) {
                                continue;
                            }
                            $item[$field] = $info['link'];
                        }
                        if ($skiplocal &&
                            (!strstr($item[$field], '://') ||
                              preg_match("!://($server|localhost|127\.0\.0\.1)((:\d+)?/|$)!", $item[$field]))) {
                            continue;
                        }
                        $id = $dbconn->GenId($linkstable);
                        $query = "INSERT INTO $linkstable (xar_id, xar_link, xar_status, xar_moduleid, xar_itemtype, xar_itemid, xar_itemtitle, xar_itemlink)
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                        $bindvars = [$id, $item[$field], 0, $modid, $ptid, $item['aid'], $item['title'], $url];
                        $result =& $dbconn->Execute($query, $bindvars);
                        if (!$result) {
                            return;
                        }
                        $count[$descr]++;
                    }
                }
            }
            if (count($dynamicfields) > 0) {
                $where = [];
                foreach ($dynamicfields as $field) {
                    $where[] = $field . " ne ''";
                }
                $whereclause = join(' or ', $where);
                $object = new Dynamic_Object_List(['moduleid' => $modid,
                                                        'itemtype' => $ptid,
                                                        'fieldlist' => $fieldlist,
                                                        'where' => $whereclause, ]);
                $object->joinTable(['table' => $articlestable,
                                         'key' => 'aid',
                                         'fields' => ['aid','title'], ]);
                $items = $object->getItems();
                $serialized = [];
                foreach ($dynamicfields as $field) {
                    if ($object->properties[$field]->type == 41) { // urltitle
                        $serialized[$field] = 1;
                    }
                }
                foreach ($items as $item) {
                    $url = xarController::URL(
                        'articles',
                        'user',
                        'display',
                        ['aid' => $item['aid']]
                    );
                    foreach ($dynamicfields as $field) {
                        if (empty($item[$field])) {
                            continue;
                        }
                        if (!empty($serialized[$field])) {
                            $info = unserialize($item[$field]);
                            if (empty($info['link'])) {
                                continue;
                            }
                            $item[$field] = $info['link'];
                        }
                        if ($skiplocal &&
                            (!strstr($item[$field], '://') ||
                              preg_match("!://($server|localhost|127\.0\.0\.1)((:\d+)?/|$)!", $item[$field]))) {
                            continue;
                        }
                        $id = $dbconn->GenId($linkstable);
                        $query = "INSERT INTO $linkstable (xar_id, xar_link, xar_status, xar_moduleid, xar_itemtype, xar_itemid, xar_itemtitle, xar_itemlink)
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                        $bindvars = [$id, $item[$field], 0, $modid, $ptid, $item['aid'], $item['title'], $url];
                        $result =& $dbconn->Execute($query, $bindvars);
                        if (!$result) {
                            return;
                        }
                        $count[$descr]++;
                    }
                }
            }
        }
    }

    // find links for roles
    if (!empty($fields['roles'])) {
        $modid = xarMod::getRegID('roles');
        $rolestable = $xartable['roles'];
        // only 1 itemtype for now, but groups might have separate DD fields later on
        $descr = [0 => xarML('Users'),
                       1 => xarML('Groups'), ];
        foreach ($fields['roles'] as $itemtype => $fieldlist) {
            $where = [];
            foreach ($fieldlist as $field) {
                $where[] = $field . " ne ''";
            }
            $whereclause = join(' or ', $where);
            $object = new Dynamic_Object_List(['moduleid' => $modid,
                                                    'itemtype' => $itemtype,
                                                    'fieldlist' => $fieldlist,
                                                    'where' => $whereclause, ]);
            $object->joinTable(['table' => $rolestable,
                                     'key' => 'uid',
                                     'fields' => ['uid','name'], ]);
            $items = $object->getItems();
            $serialized = [];
            foreach ($fieldlist as $field) {
                if ($object->properties[$field]->type == 41) { // urltitle
                    $serialized[$field] = 1;
                }
            }
            $count[$descr[$itemtype]] = 0;
            foreach ($items as $itemid => $item) {
                $url = xarController::URL(
                    'roles',
                    'user',
                    'display',
                    ['uid' => $itemid]
                );
                foreach ($fieldlist as $field) {
                    if (empty($item[$field])) {
                        continue;
                    }
                    if (!empty($serialized[$field])) {
                        $info = unserialize($item[$field]);
                        if (empty($info['link'])) {
                            continue;
                        }
                        $item[$field] = $info['link'];
                    }
                    if ($skiplocal &&
                        (!strstr($item[$field], '://') ||
                          preg_match("!://($server|localhost|127\.0\.0\.1)((:\d+)?/|$)!", $item[$field]))) {
                        continue;
                    }
                    $id = $dbconn->GenId($linkstable);
                    $query = "INSERT INTO $linkstable (xar_id, xar_link, xar_status, xar_moduleid, xar_itemtype, xar_itemid, xar_itemtitle, xar_itemlink)
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                    $bindvars = [$id, $item[$field], 0, $modid, $itemtype, $itemid, $item['name'], $url];
                    $result =& $dbconn->Execute($query, $bindvars);
                    if (!$result) {
                        return;
                    }
                    $count[$descr[$itemtype]]++;
                }
            }
        }
    }
    // TODO: find links for ...
    return $count;
}
