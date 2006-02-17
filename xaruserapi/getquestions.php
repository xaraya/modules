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
 * Get a list of questions.
 * TODO: provide some restrictions, so we can select questions used
 * in a particular survey, questions of a specific type, etc.
 */

function surveys_userapi_getquestions($args) {
    // Expand arguments.
    extract($args);

    // Database details.
    $xartable =& xarDBGetTables();
    $dbconn =& xarDBGetConn();

    $where = array();
    $bind = array();

    if (!empty($qid)) {
        $where[] = 'questions.xar_qid = ?';
        $bind[] = (int)$qid;
    }

    if (!empty($name)) {
        $where[] = 'questions.xar_name = ?';
        $bind[] = $name;
    }

    // Query all rules for this survey.
    // Bring in the groups table so we can order the rules in group order.
    $query = 'SELECT questions.xar_qid, questions.xar_type_id, questions.xar_name, questions.xar_desc,'
        . ' questions.xar_mandatory, questions.xar_default'
        . ' FROM ' . $xartable['surveys_questions'] . ' AS questions'
        . ' INNER JOIN ' . $xartable['surveys_types'] . ' AS qtypes'
        . ' ON qtypes.xar_tid = questions.xar_type_id'
        . (!empty($where) ? ' WHERE ' . implode(' AND ', $where) : '')
        ;
    $result = $dbconn->execute($query, $bind);
    if (!$result) {return;}

    $items = array();
    while (!$result->EOF) {
        // Get columns.
        list($qid, $qtid, $name, $desc, $mandatory, $default) = $result->fields;

        // TODO: get DD columns

        if (empty($column)) {
            // Return all columns.
            $items[$qid] = array(
                'qid' => $qid,
                'qtid' => $qtid,
                'name' => $name,
                'desc' => $desc,
                'mandatory' => ($mandatory == 'Y' ? true : false),
                'default' => $default
            );
        } else {
            // Return a single column.
            if ($column == 'digest') {
                // Special format for drop-down lists.
                $items[$qid] = $name;
                if (!empty($desc)) {
                    $items[$qid] .= ': '
                        . ' (' . substr($desc,0,30)
                        . (strlen($desc)>30 ? '...' : '') . ')';
                }
            } else {
                $items[$qid] = $$column;
            }
        }

        // Get next item.
        $result->MoveNext();
    }

    return $items;
}

?>