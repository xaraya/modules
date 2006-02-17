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
 * update a question group
 *
 *  -- INPUT --
 * @param $args['cid'] the ID of the category
 * @param $args['name'] the modified name of the category
 * @param $args['desc'] the modified description of the category
 * @param $args['moving'] = 1 means the category can move around
 *
 * If $args['moving'] != 1 then these shouldn?t be set:
 *
 *    @param $args['refcid'] the ID of the reference category
 *
 *    These two parameters are set in relationship with the reference category:
 *
 *       @param $args['inorout'] Where the new category should be: IN or OUT
 *       @param $args['rightorleft'] Where the new category should be: RIGHT or LEFT
 *
 *  -- OUTPUT --
 * @return true on success, false on failure

 */
function surveys_adminapi_updategroup($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($gid) || !isset($name) || !isset($desc)
        || ($moving == 1 && (!isset($insertpoint) || !isset($offset)))
    ) {
        $msg = xarML('Bad Parameters');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // Obtain current information on the group
    $group = xarModAPIfunc('surveys', 'user', 'getgroups', array('gid' => $gid));

    if (empty($group)) {
        $msg = xarML('The group does not exist');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    // Only want the first group.
    $group = reset($group['items']);

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $table = $xartable['surveys_groups'];

    // Get inside if the category is supposed to move
    // TODO: move this section to the tree API, since it is generic tree manipulation.
    if ($moving == 1) {
        // Obtain current information on the reference category
        $refgroup = xarModAPIFunc('surveys', 'user', 'getgroups', array('gid' => $insertpoint));

        if (empty($refgroup)) {
            xarExceptionSet(XAR_USER_EXCEPTION, xarML('The group does not exist'), new DefaultUserException($msg));
            return;
        }
        // Only want the first group.
        $refgroup = reset($refgroup['items']);
        //var_dump($refgroup); die;

        // Checking if the reference ID is of a child or itself
        if ($refgroup['xar_left'] >= $group['xar_left']
            && $refgroup['xar_left'] <= $group['xar_right']
        ) {
            $msg = xarML('Group references siblings');
            xarExceptionSet(XAR_USER_EXCEPTION, $msg, new DefaultUserException($msg));
            return;
        }

        // Security check
        //if (!xarSecurityCheck('EditCategories',1,'All',"All:$cid")) return;

        // Find the point of insertion.
        switch (strtolower($offset)) {
            case 'lastchild': // last child
                $insertion_point = $refgroup['xar_right'];
                break;
            case 'after': // after, same level
                $insertion_point = $refgroup['xar_right'] + 1;
                break;
            case 'firstchild': // first child
                $insertion_point = $refgroup['xar_left'] + 1;
                break;
            case 'before': // before, same level
                $insertion_point = $refgroup['xar_left'];
                break;
            default:
                $msg = xarML('Offset not set correctly');
                xarExceptionSet(XAR_USER_EXCEPTION, $msg, new DefaultUserException($msg));
                return;
        };

        $size = $group['xar_right'] - $group['xar_left'] + 1;
        $distance = $insertion_point - $group['xar_left'];
        //echo " insertion_point=$insertion_point size=$size distance=$distance "; die;

        // If necessary to move then evaluate
        if ($distance != 0) {
            if ($distance > 0)
            { // moving forward
                $distance = $insertion_point - $group['xar_right'] - 1;
                $deslocation_outside = -$size;
                $between_string = ($group['xar_right'] + 1) . " AND " . ($insertion_point - 1);
            } else { // $distance < 0 (moving backward)
                $deslocation_outside = $size;
                $between_string = $insertion_point . " AND " . ($group['xar_left'] - 1);
            }

            // This seems SQL-92 standard... Its a good test to see if
            // the databases we are supporting are complying with it. This can be
            // broken down in 3 simple UPDATES which shouldnt be a problem with any database.
            $query = 'UPDATE ' . $table
                . ' SET xar_left = CASE'
                . '    WHEN xar_left BETWEEN ' . $group['xar_left'] . ' AND ' . $group['xar_right']
                . '    THEN xar_left + (' . $distance . ')'
                . '    WHEN xar_left BETWEEN ' . $between_string
                . '    THEN xar_left + (' . $deslocation_outside . ')'
                . '    ELSE xar_left'
                . ' END,'
                . ' xar_right = CASE'
                . '    WHEN xar_right BETWEEN ' . $group['xar_left'] . ' AND ' . $group['xar_right']
                . '    THEN xar_right + (' . $distance . ')'
                . '    WHEN xar_right BETWEEN ' . $between_string
                . '    THEN xar_right + (' . $deslocation_outside . ')'
                . '    ELSE xar_right'
                . ' END';
            //echo "<pre>$query</pre>"; die;

            $result = $dbconn->Execute($query);
            if (!$result) return;

            // Find the right parent for this category.
            if (strtolower($offset) == 'lastchild' || strtolower($offset) == 'firstchild') {
                $parent_id = $insertpoint;
            } else {
                $parent_id = $refgroup['parent'];
            }

            // Update parent id
            $query = 'UPDATE ' . $table
                . ' SET xar_parent = ?'
                . ' WHERE xar_gid = ?';

            $result = $dbconn->Execute($query, array((int)$parent_id, (int)$gid));
            if (!$result) return;
        } // else (distace == 0) not necessary to move
    }


    // Update name and description
    $query = 'UPDATE ' . $table
        . ' SET xar_name = ?, xar_desc = ?'
        . ' WHERE xar_gid = ?';
    $result = $dbconn->execute($query, array($name, $desc, (int)$gid));
    if (!$result) return;

    // Call update hooks

    // Get the itemtype of the question groups.
    $itemtype = xarModAPIfunc(
        'surveys', 'user', 'gettype',
        array('type' => 'G')
    );
    $args['module'] = 'surveys';
    $args['itemtype'] = (isset($itemtype['tid']) ? (int)$itemtype['tid'] : 0);
    $args['itemid'] = $gid;
    xarModCallHooks('item', 'update', $gid, $args);

    return true;
}

?>