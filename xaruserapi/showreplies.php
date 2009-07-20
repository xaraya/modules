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

    if (empty($tid) || !is_numeric($tid)) return;

    // Allow the template to the over-ridden.
    // This allows different post display formats in different places.
    if (!empty($template)) {
        $template_override = $template;
    }

    $data = xarModAPIFunc('crispbb', 'user', 'gettopic', array('tid' => $tid));


    if (!isset($numitems)) $numitems = $data['postsperpage'];
    if (!isset($sort)) $sort = 'ptime';
    if (!isset($order)) $order = $data['postsortorder'];
    if (!isset($startnum)) $startnum = 1;
    if (!isset($starttime)) $starttime = NULL;
    if (!isset($endtime)) $endtime = NULL;
    $item = array();
    $item['module'] = 'crispbb';
    $item['itemtype'] = $data['topicstype'];
    $item['itemid'] = $tid;
    $item['tid'] = $tid;
    $item['return_url'] = xarModURL('crispbb', 'user', 'display', array('tid' => $tid, 'startnum' => $startnum));
    xarVarSetCached('Hooks.hitcount','save', true);
    $hooks = xarModCallHooks('item', 'display', $tid, $item);

    $data['hookoutput'] = !empty($hooks) && is_array($hooks) ? $hooks : array();
    $posts = xarModAPIFunc('crispbb', 'user', 'getposts',
        array(
            'tid' => $tid,
            'sort' => $sort,
            'order' => $order,
            'startnum' => $startnum,
            'numitems' => $numitems,
            'starttime' => $starttime,
            'endtime' => $endtime,
            'pstatus' => array(0,1)
       ));


    if (!empty($data['iconfolder'])) {
        $iconlist = array();
        //$iconlist['none'] = array('id' => 'none', 'name' => xarML('None'));
        $topicicons = xarModAPIFunc('crispbb', 'user', 'browse_files', array('module' => 'crispbb', 'basedir' => 'xarimages/'.$data['iconfolder'], 'match_re' => '/(gif|png|jpg)$/'));
        if (!empty($topicicons)) {
            foreach ($topicicons as $ticon) {
                $tname =  preg_replace( "/\.\w+$/U", "", $ticon );
                $imagepath = $data['iconfolder'] . '/' . $ticon;
                $iconlist[$ticon] = array('id' => $ticon, 'name' => $tname, 'imagepath' => $imagepath);
            }
        }
        $data['iconlist'] = $iconlist;
    } else {
        $data['iconlist'] = array();
    }
    $seenposters = array();
    foreach ($posts as $pid => $post) {
        $item = $post;
        if (!empty($post['towner'])) $seenposters[$post['towner']] = 1;
        if (!empty($post['powner'])) $seenposters[$post['powner']] = 1;
        if ($post['firstpid'] == $pid) {
            if (!empty($data['topicicon'])) {
                $item['topicicon'] = $data['topicicon'];
            } else {
                $item['topicicon'] = '';
            }
            $item['hookoutput'] = $data['hookoutput'];
        }   else {
            if (!empty($post['topicicon']) && isset($iconlist[$post['topicicon']])) {
                $item['topicicon'] = $iconlist[$post['topicicon']]['imagepath'];
            } else {
                $item['topicicon'] = '';
            }
            $hookitem = array();
            $hookitem['module'] = 'crispbb';
            $hookitem['itemtype'] = $post['poststype'];
            $hookitem['itemid'] = $post['pid'];
            $hookitem['pid'] = $post['pid'];
            $hookitem['return_url'] = xarModURL('crispbb', 'user', 'display', array('tid' => $tid, 'startnum' => $startnum));
            $posthooks = xarModCallHooks('item', 'display', $post['pid'], $hookitem);
            $item['hookoutput'] = !empty($posthooks) && is_array($posthooks) ? $posthooks : array();
            unset($posthooks);
        }
        if ($data['fstatus'] == 0) { // open forum
            //$item['reporturl'] = xarModURL('crispbb', 'user', 'reportpost', array('pid' => $post['pid']));
        }
        $posts[$pid] = $item;
    }

    $uidlist = !empty($seenposters) ? array_keys($seenposters) : array();
    $posterlist = xarModAPIFunc('roles', 'user', 'getall', array('uidlist' => $uidlist));

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
    if (empty($template)) $template = NULL;

    return xarTplModule($tplmodule, 'user', 'showreplies', $data, $template);

}
?>