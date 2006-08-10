<?php
/**
 * Sitecontact itemtypes
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage SiteContact Module
 * @link http://xaraya.com/index.php/release/890.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/**
 * get the name and description of all Sitecontact page scid types
 * @returns array
 */
function sitecontact_userapi_getcontacttypes($args)
{
    extract($args);
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }
    if (isset($args['sort'])) {
        $sort = $args['sort'];
    } else {
        $sort = xarModGetVar('sitecontact','defaultsort');
    }
    if (empty($sort)) {
        $sort = 'scid';
    }
    $where ='';
    $bindvars=array();
    if (isset($scid)) {
      $where = 'WHERE xar_scid = ?';
      $bindvars[]=$scid;
    }
   if (isset($sctypename)) {
       if (isset($scid)) {
         $where .= ' AND xar_sctypename = ?';
       } else {
          $where = 'WHERE xar_sctypename = ?';
       }
       $bindvars[]=$sctypename;
    }
    if (!isset($scid) && !isset($sctypename)) {
     $where ='';
    }
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $sitecontactTable = $xartable['sitecontact'];

    // Get item
    $query = "SELECT xar_scid,
              xar_sctypename,
              xar_sctypedesc,
              xar_customtext,
              xar_customtitle,
              xar_optiontext,
              xar_webconfirmtext,
              xar_notetouser,
              xar_allowcopy,
              xar_usehtmlemail,
              xar_scdefaultemail,
              xar_scdefaultname,
              xar_scactive,
              xar_savedata,
              xar_permissioncheck,
              xar_termslink,
              xar_soptions
            FROM $sitecontactTable ";
    if (!empty($where)) {
        $query .= " $where";
    }

    switch ($sort) {
        case 'name':
            $query .= " ORDER BY xar_sctypename ASC";
            break;
        case 'desc':
            $query .= " ORDER BY xar_sctypedesc ASC";
            break;
        case 'scid':
        default:
            $query .= " ORDER BY xar_scid ASC";
            break;
    }
    
    $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1,$bindvars );

    if (!$result) return;
    $sctypes=array();
    
    while (!$result->EOF) {
        list($scid, $sctypename, $sctypedesc, $customtext, $customtitle, $optiontext,
             $webconfirmtext, $notetouser, $allowcopy, $usehtmlemail, $scdefaultemail, $scdefaultname, $scactive,
             $savedata,$permissioncheck,$termslink,$soptions) = $result->fields;
            $sctypes[] =     array('scid'     => (int)$scid,
                                   'sctypename'     => $sctypename,
                                   'sctypedesc'     => $sctypedesc,
                                   'customtext'     => $customtext,
                                   'customtitle'    => $customtitle,
                                   'optiontext'     => $optiontext,
                                   'webconfirmtext' => $webconfirmtext,
                                   'notetouser'     => $notetouser,
                                   'allowcopy'      => $allowcopy,
                                   'usehtmlemail'  => $usehtmlemail,
                                   'scdefaultemail' => $scdefaultemail,
                                   'scdefaultname'  => $scdefaultname,
                                   'scactive'       => (int)$scactive,
                                   'savedata'       => (int)$savedata,
                                   'permissioncheck'=> (int)$permissioncheck,
                                   'termslink'      => $termslink,
                                   'soptions'       => $soptions);
        $result->MoveNext();
    }
  
    return $sctypes;
}
?>
