<?php
/**
 * Surveys Get a tree of items from an hierarchical table.
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
 * Get a tree of items from an hierarchical table.
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
 * id: start id
 * tablename: name of the table to fetch from
 * idname: name of the ID column ('index' or 'id')
 * module: module name (if DD columns needed)
 * itemtype: item type (if DD columns needed)
 * columns: additional columns to fetch from the database, in array(dbname,arrayname) pairs
 * eid: exluding IDs
 *
 * @todo MichelV: 1: error when there is no result from query.
 */

function surveys_treeapi_getdescendants($args) {

    extract($args);

    if (!isset($group_key)) {$group_key = 'id';}
    if (!isset($eid)) {$eid = array();}

    // FIXME: is_string()?
    if (is_numeric($eid)) {
        $eid = explode(',', $eid);
    }

    // TODO: validate args: tablename, $id

    $xartable =& xarDBGetTables();
    $dbconn =& xarDBGetConn();

    if (!isset($idname)) {$idname = 'xar_id';}

    // Get the left/right values for the root item.
    $startitem = xarModAPIfunc(
        'surveys', 'tree', 'getitemranges',
        array(
            'tablename' => $tablename,
            'idname' => $idname,
            'id' => $id
        )
    );
    if (!$startitem) {return;}

    // Build extra columns to select from.
    // Columns are an array of 'dbcolumn'=>'returnname' pairs.
    // TODO: need some method to cast some of these columns to a their
    // source datatype. It is not enough just to return strings.
    $columnstring = '';
    if (is_array($columns)) {
        foreach($columns as $colname => $returnname)
        {
            $columnstring .= ', ' . $colname . ' AS ' . $returnname;
        }
    }

    $query = 'SELECT ' . $idname . ', xar_parent as parent, xar_left, xar_right' . $columnstring
        . ' FROM ' . $tablename
        . ' WHERE xar_left BETWEEN ? AND ?'
        . (!empty($eid) ? ' AND '.$idname.' NOT IN ('.implode(',',$eid).')' : '')
        . ' ORDER BY xar_left ASC';
    $result = $dbconn->execute($query, array((int)$startitem['left'], (int)$startitem['right']));
    if (!$result) {return;}

    $items = array();
    $stack = array();
    $index = 0;
    while (!$result->EOF) {
        // If we are fetching all lists, then create the virtual root node
        // on the first iteration, rather than fetching from the database.
        if ($id == 0 && empty($items)) {
            $row = array(
                $idname=>0,
                'parent' => 0,
                'xar_left'=>(int)$startitem['left'],
                'xar_right'=>(int)$startitem['right']
            );
        } else {
            $row = $result->GetRowAssoc(0);
            // Assumption is that IDs will be numeric.
            $row[$idname] = (int)$row[$idname];
            $row['parent'] = (int)$row['parent'];
            $row['xar_left'] = (int)$row['xar_left'];
            $row['xar_right'] = (int)$row['xar_right'];
        }

        // Calculate the nesting level. Top level (root node) is zero.
        if (!empty($stack)) {
            while (!empty($stack) && end($stack) < $row['xar_right']) {
                array_pop($stack);
            }
        }
        $row['level'] = count($stack);
        $stack[] = $row['xar_right'];

        // Store the item with the appropriate key.
        if ($group_key == 'id') {
            $index = $row[$idname];
        }
        $items[$index] = $row;

        // Keep a note of the details for DD later.
        $dd_ids[$row[$idname]] = $index;

        if ($group_key == 'index') {
            $index += 1;
        }

        // Get next node.
        $result->MoveNext();
    }

    // Fetch and merge optional DD columns into a element named 'dd'.
    // Fetch them all in one go to be more efficient.
    if (!empty($module) && !empty($itemtype) && xarModIsHooked('dynamicdata', $module, $itemtype)) {
        $dd_data = xarModAPIfunc(
            'dynamicdata', 'user', 'getitems',
            array('module' => $module, 'itemtype' => $itemtype, 'itemids' => array_keys($dd_ids))
        );
        //var_dump($dd_data);
        //$dummy = array_keys($dd_ids); var_dump($dummy);
        //$dummy = array_keys($dd_data); var_dump($dummy);

        if (!empty($lang_suffix)) {
            $lang_len = strlen($lang_suffix);
        }
        foreach($dd_ids as $itemid => $index) {
            if (isset($dd_data[$itemid])) {
                foreach($dd_data[$itemid] as $dd_name => $dd_value) {
                    if (!isset($items[$index][$dd_name])) {
                        $items[$index][$dd_name] = $dd_value;
                    }
                    //echo " ".substr($dd_name, -1*$lang_len);

                    // Do optional language stuff: copy a suffix-marked DD column
                    // to the non-suffixed column if it exists.
                    if (!empty($lang_suffix) && substr($dd_name, -1*$lang_len) == $lang_suffix) {
                        $left = substr($dd_name, 0, -1*$lang_len);
                        if (!empty($items[$index][$left]) && !empty($items[$index][$dd_name])) {
                            $items[$index][$left] =& $items[$index][$dd_name];
                        }
                    }
                }
                //$items[$index]['dd'] = $dd_data[$itemid];
            }
        }
    }


    // Now create a separate children list, so the tree can be walked recursively if required.
    $children = array();
    if (is_array($items)) {
        // TODO: other types of loop more efficient?
        foreach($items as $id => $item) {
            // Add an entry to the children array of lists.
            // Create a new 'parent' list if it does not exist.
            if (!isset($children[$item['parent']])) {
                $children[$item['parent']] = array();
            }
            // Don't allow item 0 to loop back onto itself.
            if ($id != 0 || $item['parent'] != 0) {
                $children[$item['parent']][$id] = $id;
            }
        }
    }

    return array(
        'items' => $items,
        'children' => $children
    );
}

?>
