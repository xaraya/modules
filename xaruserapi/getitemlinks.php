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
 * Utility function to pass individual item links to whoever
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @return array
 */
function crispbb_userapi_getitemlinks($args)
{
    $itemlinks = array();

    $secLevel = xarModAPIFunc('crispbb', 'user', 'getseclevel');
    if (empty($secLevel)) return $itemlinks;

    if (isset($args['itemtype'])) {
        $itemtypes = xarModAPIFunc('crispbb', 'user', 'getitemtypes',
            array('itemtype' => $args['itemtype']));
        if (!empty($itemtypes)) {
            $thistype = reset($itemtypes);
            $fid = $thistype['fid'];
            $component = $thistype['component'];
            $itemtype = $thistype['itemtype'];
        }
    }

    if (empty($component)) {
        $component = 'forum';
    }

    if (empty($args['itemids']) || !is_array($args['itemids'])) {
        $args['itemids'] = array();
    }

    switch ($component) {
        case 'forum':
            default:
            $bycat = empty($args['itemids']) ? true : NULL;
            $forums = xarModAPIFunc('crispbb', 'user', 'getforums', array('fid' => $args['itemids'], 'bycat' => $bycat));
            if (!empty($forums)) {
                if (empty($bycat)) {
                    foreach($forums as $foundfid => $forum) {
                        if (empty($forum['forumviewurl'])) continue;
                        $itemlinks[$foundfid] = array(
                            'url' => $forum['forumviewurl'],
                            'title' => $forum['fdesc'],
                            'label' => xarVarPrepForDisplay($forum['fname']),
                            'id' => $foundfid,
                            'name' => $forum['fname']
                            );
                    }
                } else {
                    // get forum categories
                    $mastertype = xarModAPIFunc('crispbb', 'user', 'getitemtype',
                        array('fid' => 0, 'component' => 'forum'));
                    $mastercids = xarModGetVar('crispbb', 'mastercids.'.$mastertype);
                    $parentcat = array_shift(explode(';', $mastercids));
                    $categories = xarModAPIFunc('categories', 'user', 'getchildren',
                        array('cid' => $parentcat));
                    foreach ($categories as $cid => $cat) {
                        $secLevel = xarModAPIFunc('crispbb', 'user', 'getseclevel',
                            array('catid' => $cid));
                        if (empty($secLevel)) continue;
                        if (!empty($forums[$cid])) {
                        foreach($forums[$cid] as $foundfid => $forum) {
                            if (empty($forum['forumviewurl'])) continue;
                            $itemlinks[$foundfid] = array(
                                'url' => $forum['forumviewurl'],
                                'title' => $forum['fdesc'],
                                'label' => xarVarPrepForDisplay($forum['fname']),
                                'id' => $foundfid,
                                'name' => $forum['fname']
                            );
                        }
                        }
                    }
                }
            }
            break;
        case 'topics':
            $topics = xarModAPIFunc('crispbb', 'user', 'gettopics', array('tid' => $args['itemids']));
            if (!empty($topics)) {
                foreach($topics as $foundtid => $topic) {
                    if (empty($topic['viewtopicurl'])) {
                        $url = '';
                    } else {
                        $url = $topic['viewtopicurl'];
                    }
                    $itemlinks[$foundtid] = array(
                        'url' => $url,
                        'title' => $topic['ttitle'],
                        'label' => xarVarPrepForDisplay($topic['ttitle']),
                        'id' => $foundtid,
                        'name' => $topic['ttitle']
                        );
                }
            }
            break;
        case 'posts':
            $posts = xarModAPIFunc('crispbb', 'user', 'getposts', array('pid' => $args['itemids']));
            if (!empty($posts)) {
                foreach($posts as $foundpid => $post) {
                    if (empty($post['viewreplyurl'])) {
                        $url = '';
                    } else {
                        $url = $post['viewreplyurl'];
                    }
                    $itemlinks[$foundpid] = array(
                        'url' => $url,
                        'title' => $post['ttitle'],
                        'label' => xarVarPrepForDisplay($post['ttitle']),
                        'id' => $post['tid'],
                        'name' => $topic['ttitle']
                    );
                }
            }
            break;
    }
    return $itemlinks;
}
?>