<?php

/**
 * utility function to count the number of items held by this module
 *
 * @author the Example module development team
 * @returns integer
 * @return number of items held by this module
 * @raise DATABASE_ERROR
 */
function xproject_groupsapi_countitems()
{
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $groupstable = $xartable['groups'];

    $sql = "SELECT COUNT(1)
            FROM $groupstable";
    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('Database error for #(1) function #(2)() in module #(3)',
                    'user', 'countitems', 'groups');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return false;
    }

    list($numitems) = $result->fields;

    $result->Close();

    return $numitems;
}

/*
 * addGroup - add a group
 * @param $args['gname'] group name to add
 * @return true on success, false if group exists
 */
?>