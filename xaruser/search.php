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
    if (!xarVarFetch('cdtitle',   'checkbox', $cdtitle  , NULL,   XARVAR_DONT_SET)) return;
    if (!xarVarFetch('doccontent','checkbox', $doccontent,  NULL,   XARVAR_DONT_SET)) return;
   if (!xarVarFetch('contributors','checkbox', $contributors,  NULL,   XARVAR_DONT_SET)) return;
    if (!xarVarFetch('cdnum',     'checkbox',   $cdnum,     NULL,    XARVAR_DONT_SET)) return;
    if (!xarVarFetch('cdid',      'checkbox',   $cdid,      NULL,    XARVAR_DONT_SET)) return;
    if (!xarVarFetch('dochall',   'checkbox',   $dochall,   NULL,    XARVAR_DONT_SET)) return;
    if (!xarVarFetch('docstatus', 'checkbox',   $docstatus,  NULL,    XARVAR_DONT_SET)) return;
    //if (!xarVarFetch('uid',       'id',     $uid,      NULL, XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('author',     'isset',  $author,   NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('authorsearch','isset',  $authorsearch,   NULL, XARVAR_DONT_SET)) {return;}
    $data       = array();
    $search     = array();
     if (!isset($q) || strlen(trim($q)) <= 0) {
        if (isset($author) && (strlen(trim($author)) > 0) && $contributors) {
            $q = $author;
            $search['author']=$author;
            $data['authorsearch']=1;
        }
    } else {
        //$search['author']=null;
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
    if ($cdnum) {
        $search['cdnum'] = $q;
        $data['cdnum']=1;
    } else {
        $data['cdnum']=0;
        $cdnum=0;
    }
    if ($docstatus) {
        $search['docstatus'] = $q;
        $data['docstatus']=1;
    } else {
        $data['docstatus']=0;
        $docstatus=0;
    }
    if ($cdtitle) {
        $data['cdtitle']=1;
        $search['cdtitle'] = $q;
    } else {
        $data['cdtitle']=0;
        $cdtitle='';
    }

    if ($contributors)  {
        $data['contributors']=1;
        $search['contributors'] = $q;
    } else {
        $data['contributors']=0;
        $contributors='';
    }
    if ($doccontent) {
         $search['doccontent'] = $q;
         $data['doccontent'] = 1;
    } else {
        $data['doccontent']=0;
        $doccontent='';
    }
    $halldata=xarModAPIFunc('legis','user','getsethall');
    $halls=$halldata['halls'];
    $data['halls']=$halls;
    if ($dochall) {
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