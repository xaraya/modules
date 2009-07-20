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
 * Do something
 *
 * Utility function to redirect user to some function or other depending on params
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @return array
 * @throws none
 */
function crispbb_user_redirect($args)
{
    extract($args);
    if (!xarVarFetch('forumjump', 'int', $forumjump, NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('topicjump', 'str:1', $topicjump, NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('catid', 'id', $current_catid, NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('fid', 'id', $current_fid, NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('tid', 'id', $current_tid, NULL, XARVAR_DONT_SET)) return;

    $return_func = 'main'; // default return action
    $return_args = array();

    if (isset($forumjump)) {
        if (!empty($forumjump)) { // return to target forum
            $return_args['fid'] = $forumjump;
        } elseif (!empty($current_fid)) { // form passed a current fid and no target selected
            $return_args['fid'] = $current_fid;
        }
        if (!empty($return_args)) {
            $return_func = 'view';
        }
    }
    $now = time();
    if (isset($topicjump)) {
        if (!empty($topicjump)) {
            $return_func = 'search';
            if (preg_match("/([0-9])/", $topicjump, $matches)) {
                $jumpfid = $matches[1];
                $topicjump = str_replace($jumpfid, '', $topicjump);
                $return_args['crispbb_fids'][$jumpfid] = 1;
            }
            switch ($topicjump) {
                case 'lastvisit':
                    $tracking = xarModAPIFunc('crispbb', 'user', 'tracking', array('now' => $now));
                    if (!empty($tracking[0]['lastvisit'])) {
                    $lastvisit = $tracking[0]['lastvisit'];
                    $return_args['start'] = $lastvisit;
                    }
                break;
                case 'latest':
                    default:
                    $return_args['start'] = 1;
                break;
                case 'unanswered':
                    $return_args['noreplies'] = 1;
                break;
                case 'unread':

                break;
                case 'towner':
                    $return_args['towner'] = xarUserGetVar('uid');
                break;
            }
        }
    }

    $return_url = xarModURL('crispbb', 'user', $return_func, $return_args);

    return xarResponseRedirect($return_url);
}
?>