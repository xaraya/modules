<?php
/**
 * Surveys Get Groups
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
 * Get Groups from db
 *
 * Long Description [OPTIONAL one or more lines]
 *
 * @author     Jason Judge <jason.judge@academe.co.uk>
 * @author     Another Author <another@example.com>          [REQURIED]
 * @param string $arg1  the string used                      [OPTIONAL A REQURIED]
 * @param int    $gid
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
/*
 * Get a question group tree, starting at a group tree root.
 */

function surveys_userapi_getgroups($args) {
    // Expand arguments.
    extract($args);

    if (!isset($gid)) {
        $survey = xarModAPIfunc('surveys', 'user', 'getsurvey', $args);
        if (empty($survey)) {return;}
        $gid = $survey['gid'];
    }


    // Get the itemtype for groups
    $itemtype = xarModAPIfunc('surveys', 'user', 'gettype', array('type'=>'G'));
    if (empty($itemtype)) {return;}




    // Get the group tree, complete with DD fields if hooked.
    $groups = xarModAPIfunc(
        'surveys', 'tree', 'getdescendants',
        array(
            'tablename' => xarDBGetSiteTablePrefix() . '_surveys_groups',
            'idname' => 'xar_gid',
            'id' => $gid,
            'columns' => array(
                'xar_name' => 'group_name',
                'xar_desc' => 'group_desc',
                'xar_gid' => 'gid'
            ),
            'module' => 'surveys',
            'itemtype' => $itemtype['tid'],
            'group_key' => (isset($group_key) ? $group_key : NULL),
            'eid' => (isset($eid) ? $eid : NULL),
            'lang_suffix' => (isset($lang_suffix) ? $lang_suffix : '')
        )
    );

    return $groups;
}

?>