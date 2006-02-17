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
 * Get the parent/left/right values for a single item.
 * Will include the virtual item '0' if necessary.
 * id: ID of the item.
 * tablename: name of table
 * idname: name of the ID column
 */

function surveys_treeapi_getitemranges($args) {
    // Expand the arguments.
    extract($args);

    // Database.
    $dbconn =& xarDBGetConn();

    if ($id <> 0) {
        // Insert point is a real item.
        $query = 'SELECT xar_parent, xar_left, xar_right'
            . ' FROM ' . $tablename
            . ' WHERE ' . $idname . ' = ?';
        $result = $dbconn->execute($query, array((int)$id));
        if (!$result->EOF) {
            list($parent, $left, $right) = $result->fields;
            $return = array('parent'=>(int)$parent, 'left'=>(int)$left, 'right'=>(int)$right);
        } else {
            // Item not found.
            // TODO: raise error.
            return;
        }
    } else {
        // Insert point is the virtual root.
        // This query should return EOF when the table is empty,
        // but it doesn't (on MySQL, at least - I'm sure a MAX() of
        // no rows returns no rows in Oracle).
        $query = 'SELECT 0, MIN(xar_left)-1 as xar_left, MAX(xar_right)+1 as xar_right'
            . ' FROM ' . $tablename;
        $result = $dbconn->execute($query);
        $parent = 0;
        if (!$result->EOF) {
            list($parent, $left, $right) = $result->fields;
            $return = array('parent'=>(int)$parent, 'left'=>(int)$left, 'right'=>(int)$right);
            // Hack for MySQL where EOF does not work on MIN/MAX group functions.
            if (!isset($left)) {
                $return = array('parent'=>0, 'left'=>1, 'right'=>2);
            }
        } else {
            $return = array('parent'=>0, 'left'=>1, 'right'=>2);
        }
    }

    return $return;
}

?>