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
/**
 * Create a survey question group.
 *
 * It is added to the group hierarchy.
 *
 * @author     Jason Judge <jason.judge@academe.co.uk>
 * @author     Another Author <another@example.com>          [REQURIED]
 * @param string $arg1  the string used                      [OPTIONAL A REQURIED]
 * @param int    $arg2  an integer and use description
 *                      Identing long comments               [OPTIONAL A REQURIED]
 *  name: group name
 *  desc: group description
 *  insertpoint: ID of group inserting relative to
 *  offset: relationship to insertpoint ('after', 'before', 'firstchild', 'lastchild')
 * @return int  type and name returned                       [OPTIONAL A REQURIED]
 *
 * @throws      exceptionclass  [description]                [OPTIONAL A REQURIED]
 *
 * @access      public                                       [OPTIONAL A REQURIED]
 * @static                                                   [OPTIONAL]
 * @link       link to a reference                           [OPTIONAL]
 * @see        anothersample(), someotherlinke [reference to other function, class] [OPTIONAL]
 * @since      [Date of first inclusion long date format ]   [REQURIED]
 */

function surveys_adminapi_creategroup($args) {
    extract($args);

    // TODO: validate name (mand and unique)

    $xartable =& xarDBGetTables();
    $dbconn =& xarDBGetConn();

    $idname = 'xar_gid';
    $tablename = $xartable['surveys_groups'];

    // Open a space in the hierarchy.
    // Position in the hierarchy defined by args: insertpoint and offset
    $gap = xarModAPIfunc(
        'surveys', 'tree', 'insertprep',
        array_merge(
            $args,
            array('tablename' => $tablename, 'idname' => $idname)
        )
    );

    if (!empty($gap)) {
        // Insert the question group.
        $query = 'INSERT INTO ' . $tablename
            . ' (xar_gid, xar_parent, xar_left, xar_right, xar_name, xar_desc)'
            . ' VALUES(?, ?, ?, ?, ?, ?)';
        $nextID = $dbconn->GenId($xartable['surveys_groups']);
        $result = $dbconn->execute($query,
            array($nextID, (int)$gap['parent'], (int)$gap['left'], (int)$gap['right'], $name, isset($desc) ? $desc : NULL)
        );
        if (!$result) {return;}
        $gid = $dbconn->PO_Insert_ID($xartable['surveys_groups'], $idname);
    }

    // Create hooks
    // Get the itemtype of the question groups.
    $itemtype = xarModAPIfunc(
        'surveys', 'user', 'gettype',
        array('type' => 'G')
    );
    xarModCallHooks(
        'item', 'create', $gid,
        array(
            'itemtype' => $itemtype['tid'],
            'module' => 'surveys',
            'urlparam' => 'gid'
        )
    );

    return $gid;
}

?>