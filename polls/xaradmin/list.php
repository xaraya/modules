<?php

/**
 * list polls
 */
function polls_admin_list()
{
    $startnum = xarVarCleanFromInput('startnum');

    // Optional arguments.
    if (!isset($startnum)) {
        $startnum = 1;
    }

    if (!xarSecurityCheck('ListPolls')){
        return;
    }

    $hooked = xarVarCleanFromInput('hooked');
    if (empty($hooked)) {
        $modid = xarModGetIDFromName('polls');
    } else {
        $modid = null;
    }

    $polls = xarModAPIFunc('polls',
                            'user',
                            'getall',
                            array('modid' => $modid,
                                  'startnum' => $startnum,
                                  'numitems' => xarModGetVar('polls', 'itemsperpage')));
    $data = array();
    if (!$polls) {
        return $data;
    }
    $data['hooked'] = $hooked;
    $numrows = count($polls);
    $authid = xarSecGenAuthKey();
    $data['polls'] = array();

    foreach ($polls as $poll) {
        $row = array();
        $options = array();

        $row['title'] = $poll['title'];

        switch ($poll['type']) {
            case 'single':
                $row['type'] = xarML('Single');
                break;
            case 'multi':
                $row['type'] = xarML('Multiple');
                break;
        }

        if($poll['open'] == 0) {
                $row['open'] = xarML('Closed');
        }
        else {
                $row['open'] = xarML('Open');
        }

        $row['private'] = $poll['private'];
        $row['votes'] = $poll['votes'];
        $row['action_display'] = xarModURL('polls',
                                           'admin',
                                           'display',
                                           array('pid' => $poll['pid']));
        if ($poll['open']) {
            $row['action_close'] = xarModURL('polls',
                                               'admin',
                                               'close',
                                               array('pid' => $poll['pid'],
                                                     'authid' => $authid));
            $row['action_modify'] = xarModURL('polls',
                                               'admin',
                                               'modify',
                                               array('pid' => $poll['pid']));
        }
        if ($row['votes'] > 0 && $poll['open']) {
            $row['action_reset'] = xarModURL('polls',
                                               'admin',
                                               'reset',
                                               array('pid' => $poll['pid'],
                                                     'authid' => $authid));
            $row['action_modify'] = xarModURL('polls',
                                               'admin',
                                               'modify',
                                               array('pid' => $poll['pid']));
        }
        $row['action_delete'] = xarModURL('polls',
                                           'admin',
                                           'delete',
                                           array('pid' => $poll['pid'],
                                                     'authid' => $authid));
        $data['polls'][] = $row;
    }

    return $data;
}

?>
