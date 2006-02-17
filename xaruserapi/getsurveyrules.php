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
/*
 * Get all rules for a survey.
 * sid: survey ID
 */

function surveys_userapi_getsurveyrules($args) {
    extract($args);

    // Survey ID is mandatory.
    if (!isset($sid)) {return;}

    // Database details.
    $xartable =& xarDBGetTables();
    $dbconn =& xarDBGetConn();

    // Query all rules for this survey.
    // Bring in the groups table so we can order the rules in group order.
    $query = 'SELECT grules.xar_rid, grules.xar_survey_id, grules.xar_group_id, grules.xar_logic, grules.xar_condition'
        . ' FROM ' . $xartable['surveys_group_rules'] . ' AS grules'
        . ' INNER JOIN ' . $xartable['surveys_groups'] . ' AS qgroups'
        . ' ON grules.xar_group_id = qgroups.xar_gid'
        . ' WHERE grules.xar_survey_id = ?'
        . ' ORDER BY qgroups.xar_left';
    $result = $dbconn->execute($query, array((int)$sid));
    if (!$result) {return;}

    $items = array();
    while (!$result->EOF) {
        // Get columns.
        list($rid, $sid, $gid, $logic, $condition) = $result->fields;

        // Expand the condition into arrays, commands and parameters.
        // Split separate conditions at semi-colons.
        $condition_array = preg_split('/(?<!\\\);/', $condition);
        foreach($condition_array as $key => $cond) {
            // Substitute escaped semi-colons.
            $cond = strtr($cond, array('\;' => ';'));
            // Split again at colons.
            $condition_array[$key] = split(':', $cond);
        }

        $items[(int)$rid] = array(
            'rid' => (int)$rid,
            'sid' => (int)$sid,
            'gid' => (int)$gid,
            'logic' => $logic,
            'condition' => $condition,
            'condition_array' => $condition_array
        );

        // Get next item.
        $result->MoveNext();
    }

    return $items;
}

?>