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
 * Delete a question group
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
 * @param $args['gid'] the ID of the category
 * @return bool true on success, false on failure
 */
function surveys_adminapi_deletegroup($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (empty($gid)) {
        $msg = xarML('Invalid #(1)', 'gid');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return false;
    }

    // Obtain current information on the reference group
    $args = array(
        'gid' => $gid,
        'getparents' => false,
        'getchildren' => true,
        'return_itself' => true
    );
    $groups = xarModAPIFunc('surveys', 'user', 'getgroups', $args);
    if (empty($groups)) {
        $msg = xarML('Question group does not exist.');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    $group = reset($groups['items']);

    // These are set to be used later on
    $right = $group['xar_right'];
    $left = $group['xar_left'];
    $deslocation_inside = $right - $left + 1;
    //$categories = xarModAPIFunc('categories', 'user', 'getcat', $args);
    //if ($categories == false || count($categories) == 0) {
    //    $msg = xarML('Category does not exist. Invalid #(1) for #(2) function #(3)() in module #(4)',
    //                 'category', 'admin', 'deletecat', 'categories');
    //    xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
    //    return;
    //}
    // Useful Variables set...

    // Security check
    // Don?t check by name anything! That?s evil... Unique ID is the way to go.
    //if (!xarSecurityCheck('DeleteCategories',1,'category',"All:$cid")) return;

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Deleting a group

    //There are two possibilities when deleting a set:
    //1 - Destroy every child inside it
    //2 - Destroy the parent, and make the parent?s parent inherity the children
    //As this model has the moving feature, i think the best option is '1'

    // This part was mostly taken from Joe Celko?s article SQL for Smarties on DBMS, April 1996

    // So deleting all the subtree


    // TODO: Hooks

    // TODO: remove questions linkage

    // TODO: handle survey responses attached to a group - cannot delete groups while anything still
    // links to them.

    // Remove the category and its sub-tree
    $table = $xartable['surveys_groups'];

    $query = 'DELETE FROM ' . $table . ' WHERE xar_left BETWEEN ? AND ?';

    $result = $dbconn->Execute($query, array($left, $right));
    if (!$result) return;

    // Now close up the the gap
    $query = 'UPDATE ' . $table
        . ' SET xar_left ='
        . ' CASE WHEN xar_left > ' . $left
        . '    THEN xar_left - ' . $deslocation_inside
        . '    ELSE xar_left'
        . ' END,'
        . ' xar_right ='
        . ' CASE WHEN xar_right > ' . $left
        . '    THEN xar_right - ' . $deslocation_inside
        . '    ELSE xar_right'
        . ' END';
    $result = $dbconn->Execute($query);
    if (!$result) return;

    // Call delete hooks
    $itemtype = xarModAPIfunc(
        'surveys', 'user', 'gettype',
        array('type' => 'G')
    );
    $args['module'] = 'categories';
    $args['itemtype'] = $itemtype['tid'];
    $args['itemid'] = $gid;
    xarModCallHooks('item', 'delete', $gid, $args);
    return true;
}
?>