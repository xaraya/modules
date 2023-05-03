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
 * @author crisp <crisp@crispcreations.co.uk>
 * display waiting content as a hook
 */
function crispbb_admin_waitingcontent()
{
    $forums = xarMod::apiFunc('crispbb', 'user', 'getforums');
    $subtopics = array();
    foreach ($forums as $fid => $forum) {
        $subtopics = xarMod::apiFunc('crispbb', 'user', 'counttopics',
            array('fid' => $fid,'tstatus' => 2));
        if (empty($subtopics) || empty($forum['privs']['approvetopics'])) {
            unset($forums[$fid]);
            continue;
        }
        $forums[$fid]['subtopics'] = $subtopics;
        $forums[$fid]['modforumurl'] = xarController::URL('crispbb', 'user', 'moderate', array('fid' => $fid, 'component' => 'topics', 'tstatus' => 2));
        unset($subtopics);
    }
    $topics = xarMod::apiFunc('crispbb', 'user', 'gettopics',
        array('numsubs' => true, 'submitted' => true));

    foreach ($topics as $tid => $topic) {
        if (empty($topic['privs']['approvereplies'])) {
            unset($topics[$tid]);
            continue;
        }
        $topics[$tid]['modtopicurl'] = xarController::URL('crispbb', 'user', 'moderate', array('tid' => $tid,
            'component' => 'posts', 'pstatus' => 2));
    }

    $newversion = xarModVars::get('crispbb', 'latestversion');
    $modid = xarMod::getRegID('crispbb');
    $modinfo = xarMod::getInfo($modid);
    $oldversion = $modinfo['version'];
    $isupdated = false;
    if (!empty($newversion)) {
        list($maj, $min, $mic) = explode('.', $newversion);
        list($omaj, $omin, $omic) = explode('.', $oldversion);
        if ($maj > $omaj) { // new major version
            $isupdated = $newversion;
        } elseif ($maj == $omaj) { // same major version
            if ($min > $omin) { // new minor version
                $isupdated = $newversion;
            } elseif ($min == $omin) { // same minor version
                if ($mic > $omic) { // new micro version
                    $isupdated = $newversion;
                }
            }
        }
    }

    if (empty($topics) && empty($forums) && empty($isupdated)) return '';

    $data = array();
    $data['topics'] = $topics;
    $data['forums'] = $forums;
    $data['isupdated'] = $isupdated;
    return $data;
}
?>