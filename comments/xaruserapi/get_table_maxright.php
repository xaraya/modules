<?php

/**
 * Grab the highest 'right' value for the whole comments table
 *
 * @author   Carl P. Corliss (aka rabbitt)
 * @access   private
 * @returns   integer   the highest 'right' value for the table or zero if it couldn't find one
 */
function comments_userapi_get_table_maxright(/* VOID */) 
{

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $ctable = &$xartable['comments_column'];


    // grab the root node's id, left and right values
    // based on the objectid/modid pair
    $sql = "SELECT  MAX($ctable[right]) as max_right
              FROM  $xartable[comments]";

    $result =& $dbconn->Execute($sql);

    if (!$result)
        return;

    if (!$result->EOF) {
        $node = $result->GetRowAssoc(false);
    } else {
        $node['max_right'] = 0;
    }
    $result->Close();

    return $node['max_right'];
}

?>