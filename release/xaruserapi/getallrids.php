<?php

/**
 * get all extensions
 * @returns array
 * @return array of extensions, or false on failure
 */
function release_userapi_getallrids($args)
{
    extract($args); 

    // Optional arguments.
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }
    if (!isset($idtypes)) {
        $idtypes = 1;
    }


    if (empty($sort)) {
        $sortlist = array('rids');
    } elseif (is_array($sort)) {
        $sortlist = $sort;
    } else {
        $sortlist = explode(',',$sort);
    }


    $releaseinfo = array();

    // Security Check
    if(!xarSecurityCheck('OverviewRelease')) return;

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $releasetable = $xartable['release_id'];

    //Joins on Catids
    if(!empty($catid))
    {
        $categoriesdef = xarModAPIFunc('categories', 'user', 'leftjoin', 
                              array('modid'    => 773,
                                    'itemtype' => 0,
                                    'cids'     => array($catid),
                                    'andcids'  => 1));
    }

    $query = "SELECT DISTINCT xar_rid,
                     xar_uid,
                     xar_regname,
                     xar_displname,
                     xar_desc,
                     xar_type,
                     xar_class,
                     xar_certified,
                     xar_approved,
                     xar_rstate
            FROM $releasetable ";

    $from ='';
    $where = array();
    if (!empty($catid) && count(array($catid)) > 0) 
    {
        // add this for SQL compliance when there are multiple JOINs
        // Add the LEFT JOIN ... ON ... parts from categories
        $from .= ' LEFT JOIN ' . $categoriesdef['table'];
        $from .= ' ON ' . $categoriesdef['field'] . ' = ' . $releasetable.'.xar_rid';
        
        if (!empty($categoriesdef['more'])) 
        {
            //$from = ' ( ' . $from . ' ) ';
            $from .= $categoriesdef['more'];
        }
        
        $where[] = $categoriesdef['where'];
        $query .= $from;
    }
    switch ($idtypes) {
    case 3: // module
        $where[] = "xar_type = '0'";
        break;
    case 2: // theme
        $where[] = "xar_type = '1'";
        break;
    }

    if (!empty($certified)) {
        $where[] = " xar_certified = '" . xarVarPrepForStore($certified). "'";
    }
   if (count($where) > 0)
    {
        $query .= ' WHERE ' . join(' AND ', $where);
    }
    if (count($sortlist) > 0) {
        $sortparts = array();
      foreach ($sortlist as $criteria) {
            // ignore empty sort criteria
            if (empty($criteria)) continue;
            // split off trailing ASC or DESC
            if (preg_match('/^(.+)\s+(ASC|DESC)\s*$/i',$criteria,$matches)) {
                $criteria = trim($matches[1]);
                $sortorder = strtoupper($matches[2]);
            } else {
                $sortorder = '';
            }
            if ($criteria == 'id') {
                $sortparts[] = ' xar_rid ' . (!empty($sortorder) ? $sortorder : 'ASC');
            } elseif ($criteria == 'author') {
                $sortparts[] = ' xar_uid ' . (!empty($sortorder) ? $sortorder : 'DESC');
            } elseif ($criteria == 'name') {
                $sortparts[] = ' xar_regname ' . (!empty($sortorder) ? $sortorder : 'ASC');
            } else {
                // ignore unknown sort fields
            }
        }

      $query .= ' ORDER BY ' . join(', ',$sortparts);
    } else { // default is 'rid
        $query .= ' ORDER BY  xar_rid ASC';
    }



  //  $query .= " ORDER BY xar_rid";

    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);
    if (!$result) return;

    // Put users into result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($rid, $uid, $regname, $displname, $desc, $type, $class, $certified, $approved,$rstate) = $result->fields;
        if (xarSecurityCheck('OverviewRelease', 0)) {
            $releaseinfo[] = array('rid'        => $rid,
                                   'uid'        => $uid,
                                   'regname'    => $regname,
                                   'displname'  => $displname,
                                   'desc'       => $desc,
                                   'type'       => $type,
                                   'class'      => $class,
                                   'certified'  => $certified,
                                   'approved'   => $approved,
                                   'rstate'     => $rstate);
        }
    }

    $result->Close();

    // Return the users
  return $releaseinfo;
}

?>
