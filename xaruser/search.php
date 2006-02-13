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
    if (!xarVarFetch('cdtitle',   'str:0:', $cdtitle  ,NULL,   XARVAR_DONT_SET)) return;
    if (!xarVarFetch('doccontent','str:0:', $doccontent, NULL,   XARVAR_DONT_SET)) return;
   if (!xarVarFetch('contributors','str:0:', $contributors, NULL,   XARVAR_DONT_SET)) return;
    if (!xarVarFetch('cdnum',     'int:0:',   $cdnum,    NULL,    XARVAR_DONT_SET)) return;
    if (!xarVarFetch('cdid',      'int:0:',   $cdid,      NULL,    XARVAR_DONT_SET)) return;
    if (!xarVarFetch('dochall',   'str:0:',   $dochall,  NULL,    XARVAR_DONT_SET)) return;
    if (!xarVarFetch('docstatus', 'int:0:',   $docstatus,  NULL,    XARVAR_DONT_SET)) return;
    //if (!xarVarFetch('uid',       'id',     $uid,      NULL, XARVAR_NOT_REQUIRED)) return;
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
        $data['authorsearch']=0;
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
    if (isset($cdnum)) {
        $search['cdnum'] = $q;
        $data['cdnum']=1;
    } else {
        $data['cdnum']=0;
        $cdnum=0;
    }
    if (isset($docstatus)) {
        $search['docstatus'] = $q;
        $data['docstatus']=1;
    } else {
        $data['docstatus']=0;
        $docstatus=0;
    }
    if (isset($cdtitle)) {
        $data['cdtitle']=1;
        $search['cdtitle'] = $q;
    } else {
        $data['cdtitle']=0;
        $cdtitle='';
    }

    if (isset($contributors) && isset($author)) {
        $data['contributors']=1;
        $search['contributors'] = $q;
    } else {
        $data['contributors']=0;
        $contributors='';
    }
    if (isset($doccontent)) {
         $search['doccontent'] = $q;
         $data['doccontent'] = 1;
    } else {
        $data['doccontent']=0;
        $doccontent='';
    }
    $halldata=xarModAPIFunc('legis','user','getsethall');
    $halls=$halldata['halls'];
    $data['halls']=$halls;
    if (isset($dochall)) {
      //check for a hall
         $data['dochall']=0;
         $dochall=0;
         foreach($halls as $k=>$v) {
             if(similar_text($v['name'],$q)){
                 $search['dochall'] = (int)$v['cid'];
                 $data['dochall'] = 1;
             }
         }
    } else {
        $data['dochall']=0;
        $dochall=0;
    }

    //Check to see if this doc hall exists or not

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