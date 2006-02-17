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
 * Get item type records.
 *
 * Long Description [OPTIONAL one or more lines]
 *
 * @author     Jason Judge <jason.judge@academe.co.uk>
 * @author     Another Author <another@example.com>          [REQURIED]
 * @param string $name
 * @param int    $type
 * @param string $object_name
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


function surveys_userapi_gettypes($args) {
    // Expand arguments.
    extract($args);

    // Database stuff.
    $xartable =& xarDBGetTables();
    $dbconn =& xarDBGetConn();

    // Choices are: 'S'urvey, 'G'roup, 'Q'uestion, 'R'esponse, s'T'atus.
    if (isset($type)) {
        $type = strtoupper(substr($type, 0, 1));

        $bind = array($type);
        $where = array('types1.xar_type = ?');
    }

    // The name is optional.
    if (isset($name)) {
        $bind[] = $name;
        $where[] = 'types1.xar_name = ?';
    }

    // Can fetch a question type by its object name.
    if (isset($object_name)) {
        $bind[] = $object_name;
        $where[] = 'types1.xar_object_name = ?';
    }

    // Formulate the query.
    // Outer join to the response type, if this is a question.
    // (There isn't actually anything to select on the response type yet.)
    $query = 'SELECT types1.xar_tid, types1.xar_type, types1.xar_name,'
        . ' types1.xar_response_type_id, types1.xar_object_name'
        . ' FROM ' . $xartable['surveys_types'] . ' AS types1'
        . ' LEFT OUTER JOIN ' . $xartable['surveys_types'] . ' AS types2'
        . ' ON types2.xar_tid = types1.xar_response_type_id'
        . (!empty($where) ? ' WHERE ' . implode(' AND ', $where) : '');

    $result = $dbconn->execute($query, $bind);
    if (!$result) {return;}

    $types = array();
    while (!$result->EOF) {
        list($tid, $typecode, $name, $rtid, $object) = $result->fields;
        $tid = (int)$tid;
        $rtid = (int)$rtid;

        $type = array(
            'tid' => $tid,
            'type' => $typecode,
            'name' => $name,
            'rtid' => $rtid,
            'object' => $object
        );

        if ($typecode == 'Q') {$type['qtid'] = $tid;}
        if ($typecode == 'G') {$type['gtid'] = $tid;}

        $types[$tid] = $type;

        // Get next type.
        $result->MoveNext();
    }

    return $types;
}

?>