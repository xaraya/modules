<?php
/*
 *
 * Polls Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage polls
 * @author Jim McDonalds, dracos, mikespub et al.
 */

/**
 * list polls
 */
function polls_admin_list()
{

    //extract($args);

    if (!xarVarFetch('status', 'int:0:2', $status, 1, XARVAR_NOT_REQUIRED)) return;

    if (!xarSecurityCheck('ListPolls')){
        return;
    }


    if ($status == 2) {
        $stat = null;
    } else {
     $stat = $status;
    }

    $data = array();
    $data['status'] = $status;
    $authid = xarSecGenAuthKey();

    $polls = xarModAPIFunc('polls',
                            'user',
                            'getall',
                            array('status' => $stat));

    if (!$polls) {
        return $data;
    }

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

        $modinfo = xarModGetInfo($poll['modid']);

        $mytypes = xarModAPIFunc($modinfo['name'],'user','getitemtypes',array(), 0);
                if ($poll['itemtype'] == 0) {
                $moditem['modname'] = ucwords($modinfo['displayname']). ' - ' . $poll['itemid'];
                } else {
                if (isset($mytypes) && !empty($mytypes[$poll['itemtype']])) {
                $moditem['modname'] = ucwords($modinfo['displayname']) . ' - ' . $mytypes[$poll['itemtype']]['label'] . ' - ' . $poll['itemid'];
                                } else {
               $moditem['modname'] = ucwords($modinfo['displayname']) . ' ' . $poll['itemtype']. ' - ' . $poll['itemid'];
               }
            }
       //todo: add link to itemid
        if ($modinfo['displayname'] == 'Polls') {
            $row['hook'] = null;
        } else {
           $row['hook'] = $moditem['modname'];
        }

        //$row['hooked']=

        $row['action_display'] = xarModURL('polls',
                                           'admin',
                                           'display',
                                           array('pid' => $poll['pid']));
        if ($poll['open']) {
            $row['action_close'] = xarModURL('polls',
                                               'admin',
                                               'close',
                                               array('pid' => $poll['pid'],
                                                     'authid' => $authid,
                                                     'status' => $status));
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
                                                     'authid' => $authid,
                                                     'status' => $status));
        $data['polls'][] = $row;
    }

    return $data;
}

?>