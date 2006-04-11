<?php
/**
 * Update a forum topic
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/
function xarbb_user_locktopic()
{
    if (!xarVarFetch('tid', 'int:1:', $tid)) return;
    if (!xarVarFetch('tstatus', 'int', $tstatus)) return;
    if (!xarVarFetch('options', 'int', $options, NULL, XARVAR_NOT_REQUIRED)) return;

    // Need to handle locked topics
    $data = xarModAPIFunc('xarbb', 'user', 'gettopic', array('tid' => $tid));

    // The user API function is called.
    $forum = xarModAPIFunc('xarbb', 'user', 'getforum', array('fid' => $data['fid']));

    if (!xarSecurityCheck('ModxarBB', 1, 'Forum', $forum['catid'] . ':' . $forum['fid'])) return;

    // We need to handle the stickies and announcements as well.
    if (!empty($options)){
        // First lets not kill any other options:
        if (!empty($data['toptions'])){
            $topicoptions = unserialize($data['toptions']);
            $topicoptions['lock'] = array();
            array_push($topicoptions['lock'], true);
            $mergedarray = serialize($topicoptions);
        } else {
            $topicoptions['lock'] = array(true);
            $mergedarray = serialize($topicoptions);
        }
        // And then we update the topic options
        if (!xarModAPIFunc('xarbb', 'user', 'updatetopic',
            array(
                'tid'      => $tid,
                'toptions' => $mergedarray,
                'time'     => $data['ttime'])
            )
        ) return;
    } else {
        // Let's check to see if this is a dual status
        if ($tstatus == 0){
            if (!empty($data['toptions'])){
                $topicoptions = unserialize($data['toptions']);
                // OK, question of the day, is there anything in the $shadow var?
                if (!empty($topicoptions['lock'])) {
                    //Welp, its dual status, need to change that for consistancy sake.
                    $topicoptions['lock'] = NULL;
                    $mergedarray = serialize($topicoptions);
                }
            }
        }
        // Check to see if we have a mergedarray value
        if (!empty($mergedarray)){
            // All that, and its just a regular old topic
            if (!xarModAPIFunc('xarbb', 'user', 'updatetopic',
            array(
                'tid'      => $tid,
                'toptions' => $mergedarray,
                'time'     => $data['ttime'])
                )
            ) return;
        } else {
            // All that, and its just a regular old topic
            if (!xarModAPIFunc('xarbb', 'user', 'updatetopic',
                array(
                    'tid'      => $tid,
                    'tstatus'  => $tstatus,
                    'time'     => $data['ttime'])
                )
            ) return;
        }
    }

    xarResponseRedirect(xarModURL('xarbb', 'user', 'viewtopic', array('tid' => $tid)));
    return;
}
?>