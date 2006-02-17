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
 * Create a survey 'type' record.
 * It is added to the group hierarchy.
 */

function surveys_adminapi_createtype($args) {
    extract($args);

    // TODO: validate arguments
    if (!isset($response_type_id)) {$response_type_id = NULL;}
    if (!isset($object_name)) {$object_name = NULL;}

    $xartable =& xarDBGetTables();
    $dbconn =& xarDBGetConn();

    $idname = 'xar_tid';
    $tablename = $xartable['surveys_types'];

    // Insert the question group.
    $query = 'INSERT INTO ' . $tablename
        . ' (xar_tid, xar_type, xar_name, xar_response_type_id, xar_object_name)'
        . ' VALUES(?, ?, ?, ?, ?)';
    $nextID = $dbconn->GenId($tablename);
    $result = $dbconn->execute($query,
        array($nextID, $type, $name, (int)$response_type_id, $object_name)
    );
    if (!$result) {return;}
    $tid = (int)$dbconn->PO_Insert_ID($tablename, $idname);

    // TODO: hooks for the creation of a type?

    return $tid;
}

?>