<?php

function polls_admin_display()
{
    $pid = xarVarCleanFromInput('pid');

    // Check arguments
    if (empty($pid)) {
        $msg = xarML('No poll selected');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    $data = array();

    // Get info on this poll
    $poll = xarModAPIFunc('polls',
                           'user',
                           'get',
                           array('pid' => $pid));

    if (!$poll) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'UNKNOWN');
        return;
    }

    // Security check
    if(!xarSecurityCheck('EditPolls',1,'All',"$poll[title]:All:$pid")){
        return;
    }

    $authid = xarSecGenAuthKey();

    $data['polltitle'] = $poll['title'];
    $data['type'] = $poll['type'];
    $data['authid'] = $authid;
    $data['uplabel'] = xarML('Up');
    $data['downlabel'] = xarML('Down');
    $data['newurl'] = xarModURL('polls',
                                'admin',
                                'newopt',
                                array('pid' => $pid));

    $data['options'] = array();

    $rownum = 1;
    foreach ($poll['options'] as $opt => $optinfo) {
        $row = array();
        $row['down'] = NULL;
        $row['up'] = NULL;

        $upurl = xarModURL('polls',
                                'admin',
                                'incopt',
                                array('pid' => $pid,
                                      'opt' => $opt,
                                      'authid' => $authid));
        $downurl = xarModURL('polls',
                                  'admin',
                                  'decopt',
                                  array('pid' => $pid,
                                        'opt' => $opt,
                                        'authid' => $authid));

        if (count($poll['options']) > 1) {
            switch($rownum) {
                case 1:
                    $row['down'] = $downurl;
                    break;
                case count($poll['options']):
                    $row['up'] = $upurl;
                    break;
                default:
                    $row['down'] = $downurl;
                    $row['up'] = $upurl;
            }
        }

        $row['name'] = $optinfo['name'];

        $row['votes'] = $optinfo['votes'];

        if (xarSecurityCheck('EditPolls',0,'All',"$poll[title]:All:$pid")) {
            $row['modify'] = xarModURL('polls',
                                               'admin',
                                               'modifyopt',
                                               array('pid' => $pid,
                                                     'opt' => $opt));
        }
        if (xarSecurityCheck('EditPolls',0,'All',"$poll[title]:All:$pid")) {
            $row['delete'] = xarModURL('polls',
                                               'admin',
                                               'deleteopt',
                                               array('pid' => $pid,
                                                     'opt' => $opt));
        }
        $data['options'][] = $row;
        $rownum++;
        $row = NULL;
    }

    if ($poll['open']) {
        $data['newopturl'] = xarModURL('polls',
                                'admin',
                                'newopt',
                                array('pid' => $pid));
    }

    return $data;
}

?>