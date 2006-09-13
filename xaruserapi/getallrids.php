<?php
/*
 * Get all extensions  by specific sort
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release Module
 * @link http://xaraya.com/index.php/release/773.html
 */
/**
 * @author Jo Dalle Nogare
 * @description Get all extensions by specific sort or other criteria
 * @params $startnum, $numitems, $idtype (0 module, 1 theme ..), $sort (sort field)
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

    if (!isset($catid)) {
    $catid =NULL;
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
    $rolestable = $xartable['roles'];
    //Joins on Catids
    if(!empty($catid) )
    {
        $categoriesdef = xarModAPIFunc('categories', 'user', 'leftjoin', 
                              array('modid'    => 773,
                                    'itemtype' => 0, //has to stay this way til we can convert this
                                    'cids'     => array($catid),
                                    'andcids'  => 1));
    }

    $query = "SELECT $releasetable.xar_eid,
                     $releasetable.xar_rid,
                     $releasetable.xar_uid,
                     $releasetable.xar_regname,
                     $releasetable.xar_displname,
                     $releasetable.xar_desc,
                     $releasetable.xar_class,
                     $releasetable.xar_certified,
                     $releasetable.xar_approved,
                     $releasetable.xar_rstate,
                     $releasetable.xar_regtime,
                     $releasetable.xar_modified,
                     $releasetable.xar_members,
                     $releasetable.xar_scmlink,
                     $releasetable.xar_openproj,
                     $releasetable.xar_exttype,
                     $rolestable.xar_uname as xar_uname
            FROM $releasetable 
            LEFT JOIN $rolestable
            ON $releasetable.xar_uid = $rolestable.xar_uid";
    $bindvars = array();

    $from ='';
    $where = array();
    if (!empty($catid) && count(array($catid)) > 0) 
    {
        // add this for SQL compliance when there are multiple JOINs
        // Add the LEFT JOIN ... ON ... parts from categories
        $from .= ' LEFT JOIN ' . $categoriesdef['table'];
        $from .= ' ON ' . $categoriesdef['field'] . ' = ' . $releasetable.'.xar_eid';
        
        if (!empty($categoriesdef['more'])) 
        {
            //$from = ' ( ' . $from . ' ) ';
            $from .= $categoriesdef['more'];
        }
        
        $where[] = $categoriesdef['where'];
        $query .= $from;
    }
    /*
    switch ($idtypes) {
      case 3: // module
           $where[] = "xar_type = '0'";
           break;
       case 2: // theme
           $where[] = "xar_type = '1'";
          break;
    }
*/


    if (!empty($certified)) {
        $where[] = " xar_certified = ?";
        $bindvars[] = $certified;
    }

   if (!empty($openproj)) {
       $where[] = " xar_openproj = ?";
        $bindvars[] = (int)$openproj;
   }
   if (count($where) > 0) {
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
                $sortparts[] = ' xar_uname ' . (!empty($sortorder) ? $sortorder : 'ASC');
            } elseif ($criteria == 'name') {
                $sortparts[] = ' xar_regname ' . (!empty($sortorder) ? $sortorder : 'ASC');
            } elseif ($criteria == 'rstate') {
                $sortparts[] = ' xar_rstate ' . (!empty($sortorder) ? $sortorder : 'ASC');
            } elseif ($criteria == 'regtime') {
                $sortparts[] = ' xar_regtime ' . (!empty($sortorder) ? $sortorder : 'DESC');
            } else{
                 $sortparts[] = ' xar_eid ' . (!empty($sortorder) ? $sortorder : 'ASC');
            }
        }
        $query .= ' ORDER BY ' . join(', ',$sortparts);
    } else { // default is 'rid
        $query .= ' ORDER BY  xar_eid ASC';
    }

    //  $query .= " ORDER BY xar_rid";
    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1, $bindvars);
    if (!$result) return;

    // Put users into result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($eid,$rid, $uid, $regname, $displname, $desc,$class, $certified, $approved,$rstate,
             $regtime, $modified, $members, $scmlink,$openproj,$exttype, $uname) = $result->fields;
        if (xarSecurityCheck('OverviewRelease', 0)) {
            $releaseinfo[] = array('eid'        => $eid,
                                   'rid'        => $rid,
                                   'uid'        => $uid,
                                   'regname'    => $regname,
                                   'displname'  => $displname,
                                   'desc'       => $desc,
                                   'class'      => $class,
                                   'certified'  => $certified,
                                   'approved'   => $approved,
                                   'rstate'     => $rstate,
                                   'regtime'    => $regtime,
                                   'modified'   => $modified,
                                   'members'    => $members,
                                   'scmlink'    => $scmlink,
                                   'openproj'   => $openproj,
                                   'exttype'    => $exttype,
                                   'author'     => $uname);
        }
    }
    $result->Close();

    // Return the users
  return $releaseinfo;
}

?>