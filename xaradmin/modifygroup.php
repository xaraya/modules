<?php
/**
 * Surveys table definitions function
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Surveys
 * @author Surveys module development team
 */
/*
 * Short Description [REQUIRED one line description]
 *
 * Long Description [OPTIONAL one or more lines]
 *
 * @author     Jason Judge <jason.judge@academe.co.uk>
 * @author     Another Author <another@example.com>          [REQURIED]
 * @param string $arg1  the string used                      [OPTIONAL A REQURIED]
 * @param int    $arg2  an integer and use description
 *                      Identing long comments               [OPTIONAL A REQURIED]
 *
 * @return int  type and name returned                       [OPTIONAL A REQURIED]
 *
 * @throws      exceptionclass  [description]                [OPTIONAL A REQURIED]
 *
 * @access      public                                       [OPTIONAL A REQURIED]
 * @static                                                   [OPTIONAL]
 * @link       link to a reference                           [OPTIONAL]
 * @see        anothersample(), someotherlinke [reference to other function, class] [OPTIONAL]
 * @since      [Date of first inclusion long date format ]   [REQURIED]
 * @deprecated Deprecated [release version here]             [AS REQUIRED]
 */
/**
 * Update question group.
 */
function surveys_admin_modifygroup()
{
    if (!xarVarFetch('creating', 'bool', $creating, true, XARVAR_NOT_REQUIRED)) {return;}

    if (!xarVarFetch('gid','int::', $gid, NULL, XARVAR_DONT_SET)) {return;}
    if (empty($gid)) {
        if(!xarVarFetch('repeat','int:1:', $repeat, 1, XARVAR_NOT_REQUIRED)) {return;}
    } else {
        $repeat = 1;
    }

    if (!xarSecurityCheck('EditSurvey', 1, 'Survey', 'All')) {
        // No privilege for editing survey structures.
        return false;
    }

    $data = array();

    $data['repeat'] = $repeat;
    $data['addlabel'] = xarML('Add');
    $data['modifylabel'] = xarML('Modify');
    $data['reassignlabel'] = xarML('Reassign');

    // Item type for question groups.
    $itemtype = xarModAPIfunc('surveys', 'user', 'gettype', array('type'=>'G'));
    if (!empty($itemtype)) {
        $itemtype = $itemtype['tid'];
    } else {
        $itemtype = 0;
    }

    if (!empty($gid)) {
        // Editing an existing group

        // Setting up necessary data.
        // Get all groups except the current group.
        $data['gid'] = $gid;
        $data['group'] = xarModAPIFunc(
            'surveys', 'user', 'getgroups',
            array('gid' => $gid)
        );
        $data['group'] = reset($data['group']['items']);

        $groups = xarModAPIFunc(
            'surveys', 'user', 'getgroups',
            array(
                'gid' => 0,
                'eid' => $gid
            )
        );
        //var_dump($groups);

        $data['func'] = 'modify';

        $groupinfo = $data['group'];
        $groupinfo['module'] = 'surveys';
        $groupinfo['itemtype'] = $itemtype;
        $groupinfo['itemid'] = $gid;
        $hooks = xarModCallHooks('item', 'modify', $gid, $groupinfo);
        if (empty($hooks)) {
            $data['hooks'] = '';
        } elseif (is_array($hooks)) {
            $data['hooks'] = join('', $hooks);
        }
    } else {
        // Adding a new group

        // Setting up necessary data.
        // Get all groups.
        $groups = xarModAPIFunc(
            'surveys', 'user', 'getgroups',
            array('gid' => 0)
        );

        $groupinfo = array();
        $groupinfo['module'] = 'surveys';
        $groupinfo['itemtype'] = $itemtype;
        $groupinfo['itemid'] = '';
        $hooks = xarModCallHooks('item', 'new', '', $groupinfo);
        if (empty($hooks)) {
            $data['hooks'] = '';
        } elseif (is_array($hooks)) {
            $data['hooks'] = join('', $hooks);
        }

        $data['group'] = array('xar_left'=>0, 'xar_right'=>0, 'group_name'=>'', 'group_desc'=>'');
        $data['func'] = 'create';
        $data['gid'] = NULL;
    }

    $group_stack = array();

    // The first group item is a virtual group - we don't want it.
    array_shift($groups['items']);

    foreach ($groups['items'] as $key => $group) {
        $groups['items'][$key]['slash_separated'] = '';

        while ((count($group_stack) > 0) && ($group_stack[count($group_stack)-1]['level'] >= $group['level'])) {
           array_pop($group_stack);
        }

        foreach ($group_stack as $stack_group) {
            $groups['items'][$key]['slash_separated'] .= $stack_group['group_name'] . '/';
        }

        array_push($group_stack, $group);
        $groups['items'][$key]['slash_separated'] .= $group['group_name'];
    }

    $data['groups'] = $groups['items'];

    // Return output
    return xarTplModule('surveys', 'admin', 'editgroup', $data);
}

?>