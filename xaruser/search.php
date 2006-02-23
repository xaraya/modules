<?php
/**
 * Release Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005-2006 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage Release
 * @original author Jim McDonalds, dracos, mikespub et al.
 */
/**
 * Search for a release
 * @author Michel V.
 * @author jojodee
 * @since 2005
 * @param q
 * @param bool
 * @param sort
 * @param string regname
 * @param string displname
 * @param string desc
 * @return array
 */
function release_user_search()
{
    if (!xarVarFetch('q',         'isset',  $q,        NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('bool',      'isset',  $bool,     NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('sort',      'isset',  $sort,     NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('regname',   'str:0:', $regname,  '',   XARVAR_DONT_SET)) return;
    if (!xarVarFetch('displname', 'str:0:', $displname,'',   XARVAR_DONT_SET)) return;
    if (!xarVarFetch('desc',      'str:0:', $desc,     '',   XARVAR_DONT_SET)) return;
    if (!xarVarFetch('rid',       'id',     $rid,      0,    XARVAR_DONT_SET)) return;
    if (!xarVarFetch('uid',       'id',     $uid,      NULL, XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('author',     'isset',  $author,   NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('authorsearch','isset',  $authorsearch,   NULL, XARVAR_DONT_SET)) {return;}
    $data       = array();
    $search     = array();

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

     if(trim($q) == ''){
        return $data;
    }
    // Default parameters
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = 10;
    }
    if (isset($rid)) {
        $search['rid'] = $q;
        $data['rid']=1;
    } else {
        $data['rid']=0;
        $rid=0;
    }
    if (isset($regname)) {
        $search['regname'] = $q;
        $data['regname']=1;
    } else {
        $data['regname']=0;
        $regname='';
    }
    if (isset($displname)) {
        $data['displname']=1;
        $search['displname'] = $q;
    } else {
        $data['displname']=0;
        $displayname='';
    }
    if (isset($desc)) {
         $search['desc'] = $q;
         $data['desc'] = 1;
    } else {
        $data['desc']=0;
        $desc='';
    }


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
    $search['q']=$q;
    $seach['modid']= xarModGetIDFromName('release');
    /* Search for release information */
    $data['release'] = xarModAPIFunc('release','user','search',$search);

    if (empty($data['release'])){
        $data['status'] = xarML('No extension found that matches your search');
    }

    return $data;
}
?>