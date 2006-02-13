<?php
/**
 * Get a specific item
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Legis Module
 * @link http://xaraya.com/index.php/release/593.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/*
 * @author jojodee
 * Search for legislation
 *
 */
function legis_user_search()
{
    if (!xarVarFetch('q',         'isset',  $q,        NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('bool',      'isset',  $bool,     NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('sort',      'isset',  $sort,     NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('cdtitle',   'str:0:', $cdtitle  ,'',   XARVAR_DONT_SET)) return;
    if (!xarVarFetch('doccontent','str:0:', $doccontent, '',   XARVAR_DONT_SET)) return;
    if (!xarVarFetch('cdnum',     'int:0:',   $cdnum,    0,    XARVAR_DONT_SET)) return;
    if (!xarVarFetch('cdid',      'int:0:',   $cdid,      0,    XARVAR_DONT_SET)) return;
    if (!xarVarFetch('dochall',   'int:0:',   $dochall,   0,    XARVAR_DONT_SET)) return;
    //if (!xarVarFetch('uid',       'id',     $uid,      NULL, XARVAR_NOT_REQUIRED)) return;
    //if(!xarVarFetch('author',     'isset',  $author,   NULL, XARVAR_DONT_SET)) {return;}
    //if(!xarVarFetch('authorsearch','isset',  $authorsearch,   NULL, XARVAR_DONT_SET)) {return;}
    $data       = array();
    $search     = array();
/*
  if (!isset($q) || strlen(trim($q)) <= 0) {
        if (isset($author) && strlen(trim($author)) > 0) {
            $q = $author;
            $search['author']=$author;
            $data['authorsearch']=1;
        }
    } else {
        $search['author']='';
        $data['authorsearch']=1;
    }
*/
     if($q == ''){
        return $data;
    }
    // Default parameters
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = 10;
    }
    if (isset($cdid)) {
        $search['cdnum'] = $q;
        $data['cdnum']=1;
    } else {
        $data['cdnum']=0;
        $cdnum=0;
    }
    if (isset($cdtitle)) {
        $data['cdtitle']=1;
        $search['cdtitle'] = $q;
    } else {
        $data['cdtitl']=0;
        $cdtitl='';
    }
    if (isset($doccontent)) {
         $search['doccontent'] = $q;
         $data['doccontent'] = 1;
    } else {
        $data['doccontent']=0;
        $doccontent='';
    }
    if (isset($dochall)) {
         $search['dochall'] = $q;
         $data['dochall'] = 1;
    } else {
        $data['dochall']=0;
        $dochall=0;
    }

/*
     if (isset($author)) {
        // Check user id is real (can't use roles api here - throws a fit - need a utility function)
        $dbconn =& xarDBGetConn();
        $xartable =& xarDBGetTables();

        // Get user information
        $rolestable = $xartable['roles'];
        $query = "SELECT xar_uid
                  FROM $rolestable
                  WHERE xar_uname = ? or xar_name = ?";
        $result =& $dbconn->Execute($query,array($search['author'],$search['author']));
        if (!$result) return;
        // if we found the uid add it to the search list,
        // otherwise we won't bother searching for it
        if (!$result->EOF) {
            $uids = $result->fields;
            $search['uid'] = $uids[0];
        }
        $result->Close();
    } else {
        $search['author']='';
    }
    */
    $search['q']=$q;
    $seach['modid']= xarModGetIDFromName('legis');
    /* Search for legislation information */
    $data['legis'] = xarModAPIFunc('legis','user','search',$search);

    if (empty($data['legis'])){
        $data['status'] = xarML('No Legislation Found that matches your search');
    }

    return $data;

}

?>