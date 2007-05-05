<?php

function categories_adminapi_updatecelkolinks($args)
{
    extract($args);

    // Argument check
    if (!isset($cid)) {
        $msg = xarML('Invalid Parameter Count');
        throw new BadParameterExcepton(null,$msg);
    }

    $cat = xarModAPIFunc('categories', 'user', 'getcatinfo', array('cid'=>$cid));
    $catparent = xarModAPIFunc('categories', 'user', 'getcatinfo', array('cid'=>$cat['parent']));

    $point_of_insertion = $catparent['right'];

    // Get database setup
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();
    $categoriestable = $xartable['categories'];
    $bindvars = array();
    $bindvars[1] = array();
    $bindvars[2] = array();
    $bindvars[3] = array();

    /* Opening space for the new node */
    $SQLquery[1] = "UPDATE $categoriestable
                    SET right_id = right_id + 2
                    WHERE right_id >= ?";
    $bindvars[1][] = $point_of_insertion;

    $SQLquery[2] = "UPDATE $categoriestable
                    SET left_id = left_id + 2
                    WHERE left_id >= ?";
    $bindvars[2][] = $point_of_insertion;

    $SQLquery[3] = "UPDATE $categoriestable
                                SET left_id = ?,
                                right_id = ?
                    WHERE id >= ?";
    $bindvars[3] = array($point_of_insertion, $point_of_insertion + 1,$cid);

    for ($i=1;$i<4;$i++)
    {
        $result = $dbconn->Execute($SQLquery[$i],$bindvars[$i]);
        if (!$result) return;
    }
    return true;
}

?>
