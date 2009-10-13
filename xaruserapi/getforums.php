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
    $startnum = isset($startnum) ? $startnum : 1;
    $numitems = isset($numitems) ? $numitems : -1;
    if (empty($cids) && !empty($catid)) {
        $cids = array($catid);
    }
    if (empty($cids)) $cids = array();

    $bycat = isset($bycat) ? true : false;
    $unkeyed = isset($unkeyed) ? true : false;
    /* TODO
    if (!isset($systheme)) {
        if (!xarVarFetch('theme', 'enum:RSS:rss:atom:xml:json', $systheme, '', XARVAR_NOT_REQUIRED)) return;
    }
    */

    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();
    $forumstable = $xartable['crispbb_forums'];
    $fields = array('id', 'fname', 'fdesc', 'fstatus', 'ftype', 'fowner', 'forder', 'lasttid', 'fsettings', 'fprivileges', 'numtopics', 'numreplies');
    $select = array();
    $where = array();
    foreach ($fields as $k => $fieldname) {
        $select[] = $forumstable . '.' . $fieldname;
    }
    $from = $forumstable;
    $addme =0;

    // join on forum category
    $categoriesdef = xarMod::apiFunc('categories','user','leftjoin',array('cids' => $cids, 'modid' => xarMod::getRegID('crispbb')));
    if (!empty($categoriesdef)) {
        $fields[] = 'catid';
        $select[] = $categoriesdef['category_id'];
        if ($dbconn->databaseType != 'sqlite') $from = '(' . $from . ')';
        $from .= ' LEFT JOIN ' . $categoriesdef['table'];
        $from .= ' ON ' . $categoriesdef['field'] . ' = ' . $forumstable . '.id';
        if (!empty($categoriesdef['more'])) {
            if ($dbconn->databaseType != 'sqlite') $from = '(' . $from . ')';
            $from .= $categoriesdef['more'];
        }
        if (!empty($categoriesdef['where'])) $where[] = $categoriesdef['where'];
    }
    $typefields = array('id');
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
    $topicsfields = array('ttitle', 'towner', 'tstatus', 'tsettings', 'topicstype', 'lastpid');
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

    $postsfields = array('ptime', 'powner', 'poststype');
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

    $bindvars = array();
    if (!empty($itemtype) && is_numeric($itemtype)) {
        $where[] = $itemtypestable.".id = ?";
        $bindvars[] = $itemtype;
    }
    if (!empty($fid)) {
        if (is_numeric($fid)) {
            $where[] = $forumstable . '.id = ' . $fid;
        } elseif (is_array($fid) && count($fid) > 0) {
            $seenfid = array();
            foreach ($fid as $id) {
                if (empty($id) || !is_numeric($id)) continue;
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
            $seenfstatus = array();
            foreach ($fstatus as $id) {
                if (empty($id) || !is_numeric($id)) continue;
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
            $seenftype = array();
            foreach ($ftype as $id) {
                if (empty($id) || !is_numeric($id)) continue;
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
    $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1, $bindvars);
    if (!$result) return;
    $forums = array();
    // module defaults
    $presets = xarMod::apiFunc('crispbb', 'user', 'getpresets',
        array('preset' => 'fsettings,fprivileges,ftransfields,ttransfields,ptransfields'));
    $loggedin = xarUserIsLoggedIn();
    $checkfailed = false;
    for (; !$result->EOF; $result->MoveNext()) {
        $data = $result->fields;
        $forum = array();
        foreach ($fields as $key => $field) {
            $value = array_shift($data);
            if ($field == 'fsettings') {
                // forum settings
                $fsettings = !empty($value) && is_string($value) ? unserialize($value) : array();
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
                $fprivileges = !empty($value) && is_string($value) ? unserialize($value) : array();
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
                $value = !empty($value) && is_string($value) ? unserialize($value) : array();
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
        if (!$secLevel = xarMod::apiFunc('crispbb', 'user', 'checkseclevel',
            array('check' => $forum, 'priv' => 'viewforum'))) {
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
                $forum['viewforumurl'] = xarModURL('crispbb', 'user', 'view', array('fid' => $forum['fid']));
                // TODO: deprecate this, use viewforumurl instead
                $forum['forumviewurl'] = xarModURL('crispbb', 'user', 'view', array('fid' => $forum['fid']));
            } else {
                $redirecturl = !empty($forum['redirected']['redirecturl']) ? $forum['redirected']['redirecturl'] : '';
                if (!empty($redirecturl)) {
                    $forum['viewforumurl'] = $redirecturl;
                    $forum['forumviewurl'] = $redirecturl;
                }
            }
            if (xarMod::apiFunc('crispbb', 'user', 'checkseclevel', array('check' => $forum, 'priv' => 'readforum'))) {
                // forum readers
                if ($forum['ftype'] != 1) { // forum not redirected
                    // first we check the tstatus for topics user can't see
                    if (!empty($forum['lasttid'])) {
                        $reset = false;
                        // this forum has a last topic
                        if ($forum['tstatus'] == 3) {
                            // we don't want to show a moved topic here
                            $reset = true;
                        } elseif ($forum['tstatus'] == 4 && !xarMod::apiFunc('crispbb', 'user', 'checkseclevel', array('check' => $forum, 'priv' => 'locktopics'))) {
                            // need locktopics priv to see locked topics
                            $reset = true;
                        } elseif ($forum['tstatus'] == 2 && !xarMod::apiFunc('crispbb', 'user', 'checkseclevel', array('check' => $forum, 'priv' => 'approvetopics'))) {
                            // need approvetopics priv to see submitted topics
                            $reset = true;
                        }
                        if ($reset) {
                            $tstatuses = array(0,1);
                            if (xarMod::apiFunc('crispbb', 'user', 'checkseclevel', array('check' => $forum, 'priv' => 'approvetopics'))) {
                                $tstatuses[] = 2;
                            }
                            if (xarMod::apiFunc('crispbb', 'user', 'checkseclevel', array('check' => $forum, 'priv' => 'locktopics'))) {
                                $tstatuses[] = 4;
                            }
                            // get a topic this user can see
                            $lasttopic = xarMod::apiFunc('crispbb', 'user', 'gettopics', array('fid' => $forum['fid'], 'tstatus' => $tstatuses, 'sort' => 'ptime', 'order' => 'desc', 'numitems' => 1));
                            $lasttopic = !empty($lasttopic) ? reset($lasttopic) : array();
                            if (!empty($lasttopic)) {
                                // replace the last topic
                                $forum['lasttid'] = $lasttopic['tid'];
                                foreach ($topicsfields as $tfield) {
                                    $forum[$tfield] = $lasttopic[$tfield];
                                }
                                foreach ($postsfields as $pfield) {
                                    $forum[$pfield] = $lasttopic[$pfield];
                                }
                                $forum['numtopics'] = xarMod::apiFunc('crispbb', 'user', 'counttopics',
                                    array('fid' => $forum['fid'], 'tstatus' => $tstatuses));
                                $forum['numreplies'] = xarMod::apiFunc('crispbb', 'user', 'countposts', array('fid' => $forum['fid'], 'tstatus' => $tstatuses));
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
                        $forum['lasttopicurl'] = xarModURL('crispbb', 'user', 'display',
                            array('tid' => $forum['lasttid']));
                        $forum['lastreplyurl'] = xarModURL('crispbb', 'user', 'display',
                            array('tid' => $forum['lasttid'], 'action' => 'lastreply'));
                        $forum['townerurl'] = xarModURL('roles', 'user', 'display',
                            array('uid' => $forum['towner']));
                        $forum['pownerurl'] = xarModURL('roles', 'user', 'display',
                            array('uid' => $forum['powner']));
                        if ($loggedin) {
                            $forum['lastunreadurl'] = xarModURL('crispbb', 'user', 'display',
                                array('tid' => $forum['lasttid'], 'action' => 'unread'));
                        }
                        $forum['townerurl'] = xarModURL('roles', 'user', 'display',
                            array('uid' => $forum['towner']));
                        $forum['pownerurl'] = xarModURL('roles', 'user', 'display',
                            array('uid' => $forum['powner']));
                    }
                }
                if ($loggedin) {
                    if ($forum['ftype'] != 1) {
                        // logged in users
                        $forum['readforumurl'] = xarModURL('crispbb', 'user', 'view',
                            array('fid' => $forum['fid'], 'action' => 'read'));
                        // TODO: deprecate this, use readforumurl instead
                        $forum['forumreadurl'] = xarModURL('crispbb', 'user', 'view',
                            array('fid' => $forum['fid'], 'action' => 'read'));
                        // forum posters
                        if (xarMod::apiFunc('crispbb', 'user', 'checkseclevel',
                            array('check' => $forum, 'priv' => 'newtopic'))) {
                            $forum['newtopicurl'] = xarModURL('crispbb', 'user', 'newtopic',
                                array('fid' => $forum['fid']));
                        }
                        // forum moderators
                        if (xarMod::apiFunc('crispbb', 'user', 'checkseclevel',
                            array('check' => $forum, 'priv' => 'ismoderator'))) {
                            $forum['modforumurl'] = xarModURL('crispbb', 'user', 'moderate',
                                array('component' => 'topics', 'fid' => $forum['fid']));
                            // TODO: deprecate this, use modforumurl instead
                            $forum['admintopicsurl'] = xarModURL('crispbb', 'admin', 'topics',
                                array('fid' => $forum['fid']));
                        }
                    }
                    // forum owners
                    if (xarMod::apiFunc('crispbb', 'user', 'checkseclevel',
                        array('check' => $forum, 'priv' => 'addforum'))) {
                        $forum['addforumurl'] = xarModURL('crispbb', 'admin', 'modify',
                            array('fid' => $forum['fid']));
                    }
                    // forum config editors
                    if (xarMod::apiFunc('crispbb', 'user', 'checkseclevel',
                        array('check' => $forum, 'priv' => 'editforum'))) {
                        $forum['editforumurl'] = xarModURL('crispbb', 'admin', 'modify',
                            array('fid' => $forum['fid'], 'sublink' => 'edit'));
                        if ($forum['ftype'] != 1) {
                            $forum['hooksforumurl'] = xarModURL('crispbb', 'admin', 'modify',
                                array('fid' => $forum['fid'], 'sublink' => 'forumhooks'));
                            $forum['hookstopicsurl'] = xarModURL('crispbb', 'admin', 'modify',
                                array('fid' => $forum['fid'], 'sublink' => 'topichooks'));
                            $forum['hookspostsurl'] = xarModURL('crispbb', 'admin', 'modify',
                                array('fid' => $forum['fid'], 'sublink' => 'posthooks'));
                            $forum['privsforumurl'] = xarModURL('crispbb', 'admin', 'modify',
                                array('fid' => $forum['fid'], 'sublink' => 'privileges'));
                        }
                    }
                    // forum delete
                    if (xarMod::apiFunc('crispbb', 'user', 'checkseclevel',
                        array('check' => $forum, 'priv' => 'deleteforum'))) {
                        $forum['deleteforumurl'] = xarModURL('crispbb', 'admin', 'delete',
                            array('fid' => $forum['fid']));
                    }
                }
                // end forum readers
            }
            // we have a last topic
            if (!empty($forum['lasttid'])) {
                $forum['townerurl'] = xarModURL('roles', 'user', 'display',
                    array('uid' => $forum['towner']));
                $forum['pownerurl'] = xarModURL('roles', 'user', 'display',
                    array('uid' => $forum['powner']));
            }
            // end links
        }
        foreach ($presets['ftransfields'] as $field => $option) {
            if (!isset($forum['ftransforms'][$field]))
                $forum['ftransforms'][$field] = array();
        }
        foreach ($presets['ttransfields'] as $field => $option) {
            if (!isset($forum['ttransforms'][$field]))
                $forum['ttransforms'][$field] = array();
        }
        foreach ($presets['ptransfields'] as $field => $option) {
            if (!isset($forum['ptransforms'][$field]))
                $forum['ptransforms'][$field] = array();
        }
        $transargs = array();
        $transargs['itemtype'] = $forum['itemtype'];
        $transargs['transforms'] = $forum['ftransforms'];
        $transargs['fname'] = $forum['fname'];
        $transargs['fdesc'] = $forum['fdesc'];
        $transformed = xarMod::apiFunc('crispbb', 'user', 'dotransforms', $transargs);
        $forum['transformed_fname'] = $transformed['fname'];
        $forum['transformed_fdesc'] = $transformed['fdesc'];
        unset($transformed);
        if (!empty($forum['ttitle'])) {
            $transargs = array();
            $transargs['itemtype'] = $forum['topicstype'];
            $transargs['transforms'] = $forum['ttransforms'];
            $transargs['ttitle'] = $forum['ttitle'];
            $transargs['tdesc'] = $forum['tdesc'];
            $ignore = array();
            if (!empty($forum['tsettings']['htmldeny'])) $ignore['html'] = 1;
            if (!empty($forum['tsettings']['bbcodedeny'])) $ignore['bbcode'] = 1;
            if (!empty($forum['tsettings']['smiliesdeny'])) $ignore['smilies'] = 1;
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
?>