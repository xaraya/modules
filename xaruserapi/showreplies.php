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
 *//**
 * Standard function to do something
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @return array
 */
function crispbb_userapi_showreplies($args)
{
    extract($args);

    if (empty($tid) || !is_numeric($tid)) {
        return;
    }

    // Allow the template to the over-ridden.
    // This allows different post display formats in different places.
    if (!empty($template)) {
        $template_override = $template;
    }

    $data = xarMod::apiFunc('crispbb', 'user', 'gettopic', ['tid' => $tid]);


    if (!isset($numitems)) {
        $numitems = $data['postsperpage'];
    }
    if (!isset($sort)) {
        $sort = 'ptime';
    }
    if (!isset($order)) {
        $order = $data['postsortorder'];
    }
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($starttime)) {
        $starttime = null;
    }
    if (!isset($endtime)) {
        $endtime = null;
    }
    $item = [];
    $item['module'] = 'crispbb';
    $item['itemtype'] = $data['topicstype'];
    $item['itemid'] = $tid;
    $item['tid'] = $tid;
    $item['returnurl'] = xarController::URL('crispbb', 'user', 'display', ['tid' => $tid, 'startnum' => $startnum]);
    xarVar::setCached('Hooks.hitcount', 'save', true);
    $hooks = xarModHooks::call('item', 'display', $tid, $item);

    $data['hookoutput'] = !empty($hooks) && is_array($hooks) ? $hooks : [];
    $posts = xarMod::apiFunc(
        'crispbb',
        'user',
        'getposts',
        [
            'tid' => $tid,
            'sort' => $sort,
            'order' => $order,
            'startnum' => $startnum,
            'numitems' => $numitems,
            'starttime' => $starttime,
            'endtime' => $endtime,
            'pstatus' => [0,1],
       ]
    );


    if (!empty($data['iconfolder'])) {
        $iconlist = xarMod::apiFunc(
            'crispbb',
            'user',
            'gettopicicons',
            ['iconfolder' => $data['iconfolder']]
        );
        $data['iconlist'] = $iconlist;
    } else {
        $data['iconlist'] = [];
    }
    $seenposters = [];
    foreach ($posts as $pid => $post) {
        $item = $post;
        if (!empty($post['towner'])) {
            $seenposters[$post['towner']] = 1;
        }
        if (!empty($post['powner'])) {
            $seenposters[$post['powner']] = 1;
        }
        if ($post['firstpid'] == $pid) {
            if (!empty($data['topicicon']) && isset($iconlist[$data['topicicon']])) {
                $item['topicicon'] = $iconlist[$data['topicicon']]['imagepath'];
            } else {
                $item['topicicon'] = '';
            }
            $item['hookoutput'] = $data['hookoutput'];
        } else {
            if (!empty($post['topicicon']) && isset($iconlist[$post['topicicon']])) {
                $item['topicicon'] = $iconlist[$post['topicicon']]['imagepath'];
            } else {
                $item['topicicon'] = '';
            }
            $hookitem = [];
            $hookitem['module'] = 'crispbb';
            $hookitem['itemtype'] = $post['poststype'];
            $hookitem['itemid'] = $post['pid'];
            $hookitem['pid'] = $post['pid'];
            $hookitem['returnurl'] = xarController::URL('crispbb', 'user', 'display', ['tid' => $tid, 'startnum' => $startnum]);
            $posthooks = xarModHooks::call('item', 'display', $post['pid'], $hookitem);
            $item['hookoutput'] = !empty($posthooks) && is_array($posthooks) ? $posthooks : [];
            unset($posthooks);
        }
        if ($data['fstatus'] == 0) { // open forum
            //$item['reporturl'] = xarController::URL('crispbb', 'user', 'reportpost', array('pid' => $post['pid']));
        }
        $posts[$pid] = $item;
    }

    $uidlist = !empty($seenposters) ? array_keys($seenposters) : [];
    $posterlist = xarMod::apiFunc('crispbb', 'user', 'getposters', ['uidlist' => $uidlist, 'showstatus' => true]);

    $data['posts'] = $posts;
    $data['uidlist'] = $uidlist;
    $data['posterlist'] = $posterlist;
    $data['startnum'] = $startnum;

    // Specify the module where the templates are located
    if (empty($tplmodule)) {
        $tplmodule = 'crispbb';
    }
    // Do template override.
    if (!empty($template_override)) {
        $template = $template_override;
    }
    if (empty($template)) {
        $template = null;
    }

    return xarTpl::module($tplmodule, 'user', 'showreplies', $data, $template);
}
