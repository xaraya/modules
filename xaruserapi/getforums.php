<?php
/**
 * crispBB Forum Module
 *
 * @package modules
 * @copyright (C) 2008-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage crispBB Forum Module
 * @link http://xaraya.com/index.php/release/970.html
 * @author crisp <crisp@crispcreations.co.uk>
 */
/**
* Get forums
*
* Standard function of a module to retrieve forums
*
* @author crisp <crisp@crispcreations.co.uk>
* @return mixed  item array, or false on failure
* @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
*/
function crispbb_userapi_getforums($args)
{
    extract($args);
    $startnum ??= 1;
    $numitems ??= -1;
    if (empty($cids) && !empty($catid)) {
        $cids = [$catid];
    }
    if (empty($cids)) {
        $cids = [];
    }

    $bycat = isset($bycat) ? true : false;
    $unkeyed = isset($unkeyed) ? true : false;
    /* TODO
    if (!isset($systheme)) {
        if (!xarVar::fetch('theme', 'enum:RSS:rss:atom:xml:json', $systheme, '', xarVar::NOT_REQUIRED)) return;
    }
    */

    $xartable =& xarDB::getTables();

    /*  Replace current code with this?
        sys::import('xaraya.structures.query');
        $q = new Query('SELECT');

        // Get the forms
        $q->addtable($xartable['crispbb_forums'], 'forums');
        $q->addfields('forums.id AS id, fname, fdesc, fstatus, ftype, fowner, forder, lasttid, fsettings, fprivileges, numtopics, forums.numreplies AS numreplies');

        // Join on forum category
        $q->addtable($xartable['categories_linkage'], 'linkage');
        $q->leftjoin('forums.id','linkage.item_id');
        $q->eq('linkage.module_id', xarMod::getRegID('crispbb'));
        $q->addfield('linkage.category_id');

        // Join on itemtype
        $q->addtable($xartable['crispbb_itemtypes'], 'itemtypes');
        $q->leftjoin('forums.id', 'itemtypes.fid');
        $q->eq('itemtypes.component', 'forum');
        $q->addfield('itemtypes.id AS itemtype_id');

        // Join on topic
        $q->addtable($xartable['crispbb_topics'], 'topics');
        $q->leftjoin('forums.lasttid','topics.id');
        $q->addfields('ttitle, towner, tstatus, tsettings, topicstype, lastpid');

        // Join on post
        $q->addtable($xartable['crispbb_posts'], 'posts');
        $q->leftjoin('topics.lastpid','posts.id');
        $q->addfields('ptime, powner, poststype');

        // Join on first post
        $q->addtable($xartable['crispbb_posts'], 'firstpost');
        $q->leftjoin('topics.firstpid','firstpost.id');
        $q->addfields('firstpost.ptime AS ttime, firstpost.pdesc AS tdesc');

        // Add constraints
        // Forum ID
        if (!empty($fid)) {
            // Make sure the constraint is an array
            if (!is_array($fid)) $fid = array($fid);
            // Remove duplicate values
            $fid = array_unique($fid);
            // Force numeric values
            foreach ($fid as $k => $v) {
                if (!is_numeric($v)) {
                    unset($fid[$k]);
                } else {
                    $fid[$k] = (int)$fid[$k];
                }
            }
            // Create the constraint
            if (count($fid) == 1) {
                $q->eq('forums.id', reset($fid));
            } else {
                $q->in('forums.id', $fid);
            }
        }

        // Forum status
        if (isset($fstatus)) {
            // See comments above
            if (!is_array($fid)) $fid = array($fid);
            $fid = array_unique($fid);
            foreach ($fid as $k => $v) {
                if (!is_numeric($v)) {
                    unset($fid[$k]);
                } else {
                    $fid[$k] = (int)$fid[$k];
                }
            }
            if (count($fid) == 1) {
                $q->eq('forums.fstatus', reset($fid));
            } else {
                $q->in('forums.fstatus', $fid);
            }
        }

        // Forum type
        if (isset($ftype)) {
            // See comments above
            if (!is_array($fid)) $fid = array($fid);
            $fid = array_unique($fid);
            foreach ($fid as $k => $v) {
                if (!is_numeric($v)) {
                    unset($fid[$k]);
                } else {
                    $fid[$k] = (int)$fid[$k];
                }
            }
            if (count($fid) == 1) {
                $q->eq('forums.ftype', reset($fid));
            } else {
                $q->in('forums.ftype', $fid);
            }
        }

        // Grouping and order
        $q->setgroup('forums.id');
        $q->setorder('forums.forder', 'ASC');

        $q->qecho();
        echo "<br/><br/>";
    //    $q->run();
    //    var_dump($q->output());exit;
    */
    $dbconn = xarDB::getConn();
    $forumstable = $xartable['crispbb_forums'];
    $fields = ['id', 'fname', 'fdesc', 'fstatus', 'ftype', 'fowner', 'forder', 'lasttid', 'fsettings', 'fprivileges', 'numtopics', 'numreplies'];
    $select = [];
    $where = [];
    foreach ($fields as $k => $fieldname) {
        $select[] = $forumstable . '.' . $fieldname;
    }
    $from = $forumstable;
    $addme =0;

    // join on forum category
    $categoriesdef = xarMod::apiFunc('categories', 'user', 'leftjoin', ['cids' => $cids, 'modid' => xarMod::getRegID('crispbb')]);

    if (!empty($categoriesdef)) {
        $fields[] = 'catid';
        $select[] = $categoriesdef['category_id'];
        if ($dbconn->databaseType != 'sqlite') {
            $from = '(' . $from . ')';
        }
        $from .= ' LEFT JOIN ' . $categoriesdef['table'];
        $from .= ' ON ' . $categoriesdef['field'] . ' = ' . $forumstable . '.id';
        if (!empty($categoriesdef['more'])) {
            if ($dbconn->databaseType != 'sqlite') {
                $from = '(' . $from . ')';
            }
            $from .= $categoriesdef['more'];
        }
        if (!empty($categoriesdef['where'])) {
            $where[] = $categoriesdef['where'];
        }
    }
    $typefields = ['id'];
    $itemtypestable = $xartable['crispbb_itemtypes'];
    foreach ($typefields as $k => $fieldname) {
        $select[] = $itemtypestable . '.' . $fieldname;
        $fields[] = 'itemtype';
    }
    if ($addme && ($dbconn->databaseType != 'sqlite')) {
        $from = '(' . $from . ')';
    }
    $from .= ' LEFT JOIN ' . $itemtypestable;
    $from .= ' ON ' . $itemtypestable . '.fid' . ' = ' . $forumstable . '.id';
    $where[] = $itemtypestable . '.component' . ' = "forum"';

    $topicstable = $xartable['crispbb_topics'];
    $topicsfields = ['ttitle', 'towner', 'tstatus', 'tsettings', 'topicstype', 'lastpid'];
    foreach ($topicsfields as $k => $fieldname) {
        $select[] = $topicstable . '.' . $fieldname;
        $fields[] = $fieldname;
    }
    if ($addme && ($dbconn->databaseType != 'sqlite')) {
        $from = '(' . $from . ')';
    }
    $from .= ' LEFT JOIN ' . $topicstable;
    $from .= ' ON ' . $topicstable . '.id' . ' = ' . $forumstable . '.lasttid';
    // $where[] = $itemtypestable . '.component' . ' = "forum"';
    $addme = 1;

    $poststable = $xartable['crispbb_posts'];

    $postsfields = ['ptime', 'powner', 'poststype'];
    /* TODO
    if (!empty($systheme)) {
        $postsfields[] = 'ptext';
        $postsfields[] = 'pdesc';
        $postsfields[] = 'psettings';
    }
    */
    foreach ($postsfields as $k => $fieldname) {
        $select[] = $poststable . '.' . $fieldname;
        $fields[] = $fieldname;
    }
    if ($addme && ($dbconn->databaseType != 'sqlite')) {
        $from = '(' . $from . ')';
    }
    $from .= ' LEFT JOIN ' . $poststable;
    $from .= ' ON ' . $poststable . '.id' . ' = ' . $topicstable . '.lastpid';
    // $where[] = $itemtypestable . '.component' . ' = "forum"';
    $addme = 1;

    if ($addme && ($dbconn->databaseType != 'sqlite')) {
        $from = '(' . $from . ')';
    }
    $from .= ' LEFT JOIN ' . $poststable . ' AS firstpost';
    $from .= ' ON firstpost.id = ' . $topicstable . '.firstpid';
    // $where[] = $itemtypestable . '.component' . ' = "forum"';
    $addme = 1;
    $select[] = 'firstpost.pdesc AS tdesc';
    $select[] = 'firstpost.ptime AS ttime';
    $fields[] = 'tdesc';
    $fields[] = 'ttime';

    $bindvars = [];
    if (!empty($itemtype) && is_numeric($itemtype)) {
        $where[] = $itemtypestable.".id = ?";
        $bindvars[] = $itemtype;
    }
    if (!empty($fid)) {
        if (is_numeric($fid)) {
            $where[] = $forumstable . '.id = ' . $fid;
        } elseif (is_array($fid) && count($fid) > 0) {
            $seenfid = [];
            foreach ($fid as $id) {
                if (empty($id) || !is_numeric($id)) {
                    continue;
                }
                $seenfid[$id] = 1;
            }
            if (count($seenfid) == 1) {
                $fids = array_keys($seenfid);
                $where[] = $forumstable . '.id = ' . $fids[0];
            } elseif (count($seenfid) > 1) {
                $fids = join(', ', array_keys($seenfid));
                $where[] = $forumstable . '.id IN (' . $fids . ')';
            }
        }
    }
    if (isset($fstatus)) {
        if (is_numeric($fstatus)) {
            $where[] = $forumstable . '.fstatus = ' . $fstatus;
        } elseif (is_array($fstatus) && count($fstatus) > 0) {
            $seenfstatus = [];
            foreach ($fstatus as $id) {
                if (empty($id) || !is_numeric($id)) {
                    continue;
                }
                $seenfstatus[$id] = 1;
            }
            if (count($seenfstatus) == 1) {
                $fstatuses = array_keys($seenfstatus);
                $where[] = $forumstable . '.fstatus = ' . $fstatuses[0];
            } elseif (count($seenfstatus) > 1) {
                $fstatuses = join(', ', array_keys($seenfstatus));
                $where[] = $forumstable . '.fstatus IN (' . $fstatuses . ')';
            }
        }
    }
    if (isset($ftype)) {
        if (is_numeric($ftype)) {
            $where[] = $forumstable . '.ftype = ' . $ftype;
        } elseif (is_array($ftype) && count($ftype) > 0) {
            $seenftype = [];
            foreach ($ftype as $id) {
                if (empty($id) || !is_numeric($id)) {
                    continue;
                }
                $seenftype[$id] = 1;
            }
            if (count($seenftype) == 1) {
                $ftypes = array_keys($seenftype);
                $where[] = $forumstable . '.ftype = ' . $ftypes[0];
            } elseif (count($seenftype) > 1) {
                $ftypes = join(', ', array_keys($seenftype));
                $where[] = $forumstable . '.ftype IN (' . $ftypes . ')';
            }
        }
    }

    $query = 'SELECT ' . join(', ', $select);
    $query .= ' FROM ' . $from;
    if (!empty($where)) {
        $query .= ' WHERE ' . join(' AND ', $where);
    }
    $query .= ' GROUP BY ' . $forumstable . '.id';
    if (!empty($sort) && $sort == 'totals') {
        $query .= " ORDER BY SUM(" . $forumstable . ".numtopics+" . $forumstable . ".numreplies) DESC";
    } else {
        $query .= " ORDER BY " . $forumstable . '.forder';
    }
    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1, $bindvars);
    if (!$result) {
        return;
    }
    $forums = [];
    // module defaults
    $presets = xarMod::apiFunc(
        'crispbb',
        'user',
        'getpresets',
        ['preset' => 'fsettings,fprivileges,ftransfields,ttransfields,ptransfields']
    );
    $loggedin = xarUser::isLoggedIn();
    $checkfailed = false;
    for (; !$result->EOF; $result->MoveNext()) {
        $data = $result->fields;
        $forum = [];
        foreach ($fields as $key => $field) {
            $value = array_shift($data);
            if ($field == 'fsettings') {
                // forum settings
                $fsettings = !empty($value) && is_string($value) ? unserialize($value) : [];
                // add in any new presets from defaults
                foreach ($presets['fsettings'] as $p => $pv) {
                    if (!isset($fsettings[$p])) {
                        $fsettings[$p] = $pv;
                    }
                }
                foreach ($fsettings as $k => $v) {
                    // remove any settings not in defaults
                    if (!isset($presets['fsettings'][$k])) {
                        unset($fsettings[$k]);
                        continue;
                    }
                    $forum[$k] = $v;
                }
                $forum[$field] = $fsettings;
                unset($fsettings);
            } elseif ($field == 'fprivileges') {
                // forum privileges
                $fprivileges = !empty($value) && is_string($value) ? unserialize($value) : [];
                // add in any new presets from defaults
                foreach ($presets['fprivileges'] as $level => $actions) {
                    foreach ($actions as $action => $value) {
                        if (!isset($fprivileges[$level][$action])) {
                            $fprivileges[$level][$action] = $value;
                        }
                    }
                }
                // remove any settings not in defaults
                foreach ($fprivileges as $level => $actions) {
                    foreach ($actions as $action => $value) {
                        if (!isset($presets['fprivileges'][$level][$action])) {
                            unset($fprivileges[$level][$action]);
                        }
                    }
                }
                $forum[$field] = $fprivileges;
                unset($fprivileges);
            } elseif ($field == 'tsettings' || $field == 'psettings') {
                $value = !empty($value) && is_string($value) ? unserialize($value) : [];
                $forum[$field] = $value;
            } else {
                if ($field == 'id') {
                    $field = 'fid';
                } elseif ($field == 'category_id') {
                    $field = 'catid';
                }
                $forum[$field] = $value;
            }
        }
        if (!$secLevel = xarMod::apiFunc(
            'crispbb',
            'user',
            'checkseclevel',
            ['check' => $forum, 'priv' => 'viewforum']
        )) {
            $checkfailed = true;
            continue;
        }

        $forum['totalitems'] = $forum['numreplies'] + $forum['numtopics'];
        $forum['forumLevel'] = $secLevel;
        // add privs for current user level in this forum
        $forum['privs'] = $forum['fprivileges'][$secLevel];
        if (empty($nolinks)) { // allow turn off links (shorturls throws a loop without it)
            // forum viewers
            if ($forum['ftype'] != 1) {
                $forum['viewforumurl'] = xarController::URL('crispbb', 'user', 'view', ['fid' => $forum['fid']]);
                // TODO: deprecate this, use viewforumurl instead
                $forum['forumviewurl'] = xarController::URL('crispbb', 'user', 'view', ['fid' => $forum['fid']]);
            } else {
                $redirecturl = !empty($forum['redirected']) ? $forum['redirected'] : '';
                if (!empty($redirecturl)) {
                    $forum['viewforumurl'] = $redirecturl;
                    $forum['forumviewurl'] = $redirecturl;
                }
            }
            if (xarMod::apiFunc('crispbb', 'user', 'checkseclevel', ['check' => $forum, 'priv' => 'readforum'])) {
                // forum readers
                if ($forum['ftype'] != 1) { // forum not redirected
                    // first we check the tstatus for topics user can't see
                    if (!empty($forum['lasttid'])) {
                        $reset = false;
                        // this forum has a last topic
                        if ($forum['tstatus'] == 3) {
                            // we don't want to show a moved topic here
                            $reset = true;
                        } elseif ($forum['tstatus'] == 4 && !xarMod::apiFunc('crispbb', 'user', 'checkseclevel', ['check' => $forum, 'priv' => 'locktopics'])) {
                            // need locktopics priv to see locked topics
                            $reset = true;
                        } elseif ($forum['tstatus'] == 2 && !xarMod::apiFunc('crispbb', 'user', 'checkseclevel', ['check' => $forum, 'priv' => 'approvetopics'])) {
                            // need approvetopics priv to see submitted topics
                            $reset = true;
                        }
                        if ($reset) {
                            $tstatuses = [0,1];
                            if (xarMod::apiFunc('crispbb', 'user', 'checkseclevel', ['check' => $forum, 'priv' => 'approvetopics'])) {
                                $tstatuses[] = 2;
                            }
                            if (xarMod::apiFunc('crispbb', 'user', 'checkseclevel', ['check' => $forum, 'priv' => 'locktopics'])) {
                                $tstatuses[] = 4;
                            }
                            // get a topic this user can see
                            $lasttopic = xarMod::apiFunc('crispbb', 'user', 'gettopics', ['fid' => $forum['fid'], 'tstatus' => $tstatuses, 'sort' => 'ptime', 'order' => 'desc', 'numitems' => 1]);
                            $lasttopic = !empty($lasttopic) ? reset($lasttopic) : [];
                            if (!empty($lasttopic)) {
                                // replace the last topic
                                $forum['lasttid'] = $lasttopic['tid'];
                                foreach ($topicsfields as $tfield) {
                                    $forum[$tfield] = $lasttopic[$tfield];
                                }
                                foreach ($postsfields as $pfield) {
                                    $forum[$pfield] = $lasttopic[$pfield];
                                }
                                $forum['numtopics'] = xarMod::apiFunc(
                                    'crispbb',
                                    'user',
                                    'counttopics',
                                    ['fid' => $forum['fid'], 'tstatus' => $tstatuses]
                                );
                                $forum['numreplies'] = xarMod::apiFunc('crispbb', 'user', 'countposts', ['fid' => $forum['fid'], 'tstatus' => $tstatuses]);
                            } else {
                                // no topic to display
                                $forum['lasttid'] = '';
                                foreach ($topicsfields as $tfield) {
                                    $forum[$tfield] = '';
                                }
                                foreach ($postsfields as $pfield) {
                                    $forum[$pfield] = '';
                                }
                                $forum['numtopics'] = 0;
                                $forum['numreplies'] = 0;
                            }
                            unset($lasttopic);
                        }
                    }
                    // we have a last topic
                    if (!empty($forum['lasttid'])) {
                        // add in the topic urls
                        $forum['lasttopicurl'] = xarController::URL(
                            'crispbb',
                            'user',
                            'display',
                            ['tid' => $forum['lasttid']]
                        );
                        $forum['lastreplyurl'] = xarController::URL(
                            'crispbb',
                            'user',
                            'display',
                            ['tid' => $forum['lasttid'], 'action' => 'lastreply']
                        );
                        $forum['townerurl'] = xarController::URL(
                            'roles',
                            'user',
                            'display',
                            ['uid' => $forum['towner']]
                        );
                        $forum['pownerurl'] = xarController::URL(
                            'roles',
                            'user',
                            'display',
                            ['uid' => $forum['powner']]
                        );
                        if ($loggedin) {
                            $forum['lastunreadurl'] = xarController::URL(
                                'crispbb',
                                'user',
                                'display',
                                ['tid' => $forum['lasttid'], 'action' => 'unread']
                            );
                        }
                        $forum['townerurl'] = xarController::URL(
                            'roles',
                            'user',
                            'display',
                            ['uid' => $forum['towner']]
                        );
                        $forum['pownerurl'] = xarController::URL(
                            'roles',
                            'user',
                            'display',
                            ['uid' => $forum['powner']]
                        );
                    }
                }
                if ($loggedin) {
                    if ($forum['ftype'] != 1) {
                        // logged in users
                        $forum['readforumurl'] = xarController::URL(
                            'crispbb',
                            'user',
                            'view',
                            ['fid' => $forum['fid'], 'action' => 'read']
                        );
                        // TODO: deprecate this, use readforumurl instead
                        $forum['forumreadurl'] = xarController::URL(
                            'crispbb',
                            'user',
                            'view',
                            ['fid' => $forum['fid'], 'action' => 'read']
                        );
                        // forum posters
                        if (xarMod::apiFunc(
                            'crispbb',
                            'user',
                            'checkseclevel',
                            ['check' => $forum, 'priv' => 'newtopic']
                        )) {
                            $forum['newtopicurl'] = xarController::URL(
                                'crispbb',
                                'user',
                                'newtopic',
                                ['fid' => $forum['fid']]
                            );
                        }
                        // forum moderators
                        if (xarMod::apiFunc(
                            'crispbb',
                            'user',
                            'checkseclevel',
                            ['check' => $forum, 'priv' => 'ismoderator']
                        )) {
                            $forum['modforumurl'] = xarController::URL(
                                'crispbb',
                                'user',
                                'moderate',
                                ['component' => 'topics', 'fid' => $forum['fid']]
                            );
                            // TODO: deprecate this, use modforumurl instead
                            $forum['admintopicsurl'] = xarController::URL(
                                'crispbb',
                                'admin',
                                'topics',
                                ['fid' => $forum['fid']]
                            );
                        }
                    }
                    // forum owners
                    if (xarMod::apiFunc(
                        'crispbb',
                        'user',
                        'checkseclevel',
                        ['check' => $forum, 'priv' => 'addforum']
                    )) {
                        $forum['addforumurl'] = xarController::URL(
                            'crispbb',
                            'admin',
                            'modify',
                            ['fid' => $forum['fid']]
                        );
                    }
                    // forum config editors
                    if (xarMod::apiFunc(
                        'crispbb',
                        'user',
                        'checkseclevel',
                        ['check' => $forum, 'priv' => 'editforum']
                    )) {
                        $forum['editforumurl'] = xarController::URL(
                            'crispbb',
                            'admin',
                            'modify',
                            ['fid' => $forum['fid'], 'sublink' => 'edit']
                        );
                        if ($forum['ftype'] != 1) {
                            $forum['hooksforumurl'] = xarController::URL(
                                'crispbb',
                                'admin',
                                'modify',
                                ['fid' => $forum['fid'], 'sublink' => 'forumhooks']
                            );
                            $forum['hookstopicsurl'] = xarController::URL(
                                'crispbb',
                                'admin',
                                'modify',
                                ['fid' => $forum['fid'], 'sublink' => 'topichooks']
                            );
                            $forum['hookspostsurl'] = xarController::URL(
                                'crispbb',
                                'admin',
                                'modify',
                                ['fid' => $forum['fid'], 'sublink' => 'posthooks']
                            );
                            $forum['privsforumurl'] = xarController::URL(
                                'crispbb',
                                'admin',
                                'modify',
                                ['fid' => $forum['fid'], 'sublink' => 'privileges']
                            );
                        }
                    }
                    // forum delete
                    if (xarMod::apiFunc(
                        'crispbb',
                        'user',
                        'checkseclevel',
                        ['check' => $forum, 'priv' => 'deleteforum']
                    )) {
                        $forum['deleteforumurl'] = xarController::URL(
                            'crispbb',
                            'admin',
                            'delete',
                            ['fid' => $forum['fid']]
                        );
                    }
                }
                // end forum readers
            }
            // we have a last topic
            if (!empty($forum['lasttid'])) {
                $forum['townerurl'] = xarController::URL(
                    'roles',
                    'user',
                    'display',
                    ['uid' => $forum['towner']]
                );
                $forum['pownerurl'] = xarController::URL(
                    'roles',
                    'user',
                    'display',
                    ['uid' => $forum['powner']]
                );
            }
            // end links
        }
        foreach ($presets['ftransfields'] as $field => $option) {
            if (!isset($forum['ftransforms'][$field])) {
                $forum['ftransforms'][$field] = [];
            }
        }
        foreach ($presets['ttransfields'] as $field => $option) {
            if (!isset($forum['ttransforms'][$field])) {
                $forum['ttransforms'][$field] = [];
            }
        }
        foreach ($presets['ptransfields'] as $field => $option) {
            if (!isset($forum['ptransforms'][$field])) {
                $forum['ptransforms'][$field] = [];
            }
        }
        $transargs = [];
        $transargs['itemtype'] = $forum['itemtype'];
        $transargs['transforms'] = $forum['ftransforms'];
        $transargs['fname'] = $forum['fname'];
        $transargs['fdesc'] = $forum['fdesc'];
        $transformed = xarMod::apiFunc('crispbb', 'user', 'dotransforms', $transargs);
        $forum['transformed_fname'] = $transformed['fname'];
        $forum['transformed_fdesc'] = $transformed['fdesc'];
        unset($transformed);
        if (!empty($forum['ttitle'])) {
            $transargs = [];
            $transargs['itemtype'] = $forum['topicstype'];
            $transargs['transforms'] = $forum['ttransforms'];
            $transargs['ttitle'] = $forum['ttitle'];
            $transargs['tdesc'] = $forum['tdesc'];
            $ignore = [];
            if (!empty($forum['tsettings']['htmldeny'])) {
                $ignore['html'] = 1;
            }
            if (!empty($forum['tsettings']['bbcodedeny'])) {
                $ignore['bbcode'] = 1;
            }
            if (!empty($forum['tsettings']['smiliesdeny'])) {
                $ignore['smilies'] = 1;
            }
            $transargs['ignore'] = $ignore;
            $transformed = xarMod::apiFunc('crispbb', 'user', 'dotransforms', $transargs);
            $forum['transformed_ttitle'] = $transformed['ttitle'];
            $forum['transformed_tdesc'] = $transformed['tdesc'];
        }
        /* TODO
        if (!empty($systheme)) {
            $transargs = array();
            $transargs['itemtype'] = $forum['poststype'];
            $transargs['transforms'] = $forum['ptransforms'];
            $transargs['pdesc'] = $forum['pdesc'];
            $transargs['ptext'] = $forum['ptext'];
            $ignore = array();
            if (!empty($forum['psettings']['htmldeny'])) $ignore['html'] = 1;
            if (!empty($forum['psettings']['bbcodedeny'])) $ignore['bbcode'] = 1;
            if (!empty($forum['psettings']['smiliesdeny'])) $ignore['smilies'] = 1;
            $transargs['ignore'] = $ignore;
            $ptransformed = xarMod::apiFunc('crispbb', 'user', 'dotransforms', $transargs);
            $forum['transformed_pdesc'] = $ptransformed['pdesc'];
            $forum['transformed_ptext'] = $ptransformed['ptext'];
        }
        */
        // TODO: get dd?
        if ($bycat) {
            $forums[$forum['catid']][$forum['fid']] = $forum;
        } elseif ($unkeyed) {
            $forums[] = $forum;
        } else {
            $forums[$forum['fid']] = $forum;
        }
    }
    $result->Close();

    // we didn't find any forums
    if ((empty($forums)) || (!empty($catid) && $bycat && empty($forums[$catid]))) {
        // check if forums were found and subsequently removed due to privs
        // if no forums exist the problem wasn't a lack of privs
        // this will either mean no forums have been created
        // or params didn't match existing forums
        // either way, this message only gets returned when privs was an issue
        if (!empty($privcheck) && $checkfailed) {
            $forums['error'] = 'NO_PRIVILEGES';
        } elseif (!empty($privcheck)) {
            $forums['error'] = 'BAD_DATA';
        }
    }

    return $forums;
}
