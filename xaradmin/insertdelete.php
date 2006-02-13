<?php
/**
 * Standard function to update a current item
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Legis Module
 * @link http://xaraya.com/index.php/release/593.html
 * @author jojodee
 */
/**
 * Standard function to insert  a clause
 *
 * This function is called with the parameters
 * supplied by xarModFunc('legis','admin','modify')
 *
 * @author jojodee
 * @param  $ 'cdid' the id of the item to be updated
 * @param  $ 'clausetype' the type of the clause 0 = contributor, 1, 2 is equal to did
 * @param $ 'clauseno' the number of the clause
 * @param $ 'action' the action 1- insert 2 -delete
 */
function legis_admin_insertdelete()
{
// 1 - insert after current clause
// 2 - delete the current clause

    if (!xarSecurityCheck('EditLegis', 0)) {
      return;
    }
    if (!xarVarFetch('cdid',     'int:0:',  $cdid,      $cdid,     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('clausetype','int:0:',$clausetype,$clausetype,     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'int:0:',  $objectid,  $objectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid',  'array',   $invalid,   $invalid,        XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('clauseno', 'int:0:',  $clauseno,  $clauseno, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('action',  'int:1:2',  $action,  $action, XARVAR_NOT_REQUIRED)) {return;}


     if (!empty($objectid)) {
        $cdid = $objectid;
    }

    $invalid = array();
    if (empty($cdid) || !is_numeric($cdid)) {
        $invalid['cdid'] = 1;
        $cdid = '';
    }
   if (!isset($clausetype) ) {
        $invalid['clausetype'] = 1;
        $clausetype = '';
    }
   if (!isset($clauseno) ) {
        $invalid['clauseno'] = 1;
        $clauseno = '';
    }
   if (empty($action) || !is_numeric($action)) {
        $invalid['action'] = 1;
        $action= '';
    }
    /* check if we have any errors */
    if (count($invalid) > 0) {
        return xarModFunc('legis', 'admin', 'modify',
                          array('cdid'     => $cdid,
                                'invalid'  => $invalid));
    }
    $item = xarModAPIFunc('legis','user','get',array('cdid' => (int)$cdid));
   /* Check for exceptions */
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

    $docdata=array();
    $docdata=unserialize($item['doccontent']);
    $contribdata=unserialize($item['contributors']);
    $docdef=xarModAPIFunc('legis','user','getmaster',array('mdid'=>$item['mdid']));
    $clauses=unserialize($docdef['mddef']);
    $dummy=array(0); // for fiddling array keys

    if ($clausetype ==0 ) { //contributor
        $currentcontributors = $contribdata;
        $newauthordata=1;
        $newhallname=(int)xarModGetVar('legis','defaulthall');
        $newcontributor[]=array('authorname'=>xarML('Name Me'),
                              'authordata'=>1,
                              'authorhallname'=>$newhallname);

        if ($action == 1) {
           $clauseno=$clauseno-1;
            $contribdata=insertafter($currentcontributors,$newcontributor,$clauseno);
            xarSessionSetVar('statusmsg', xarML('Clause was successfully inserted!'));
        }else {
            //erg fix this later
            unset($currentcontributors[$clauseno]);
            $currentcontributors=array_merge($dummy,$currentcontributors);
            unset ($currentcontributors[0]);
            $contribdata=$currentcontributors;
             xarSessionSetVar('statusmsg', xarML('Clause was successfully deleted!'));
        }

     }else{ //some other clause
         $newclause=array();
         $newclause[]=xarML('New clause - edit me!');
         $currentdata = $docdata[$clausetype];
        if ($action == 1) {
            $clauseno=$clauseno-1;
            $docdata[$clausetype]=insertafter($currentdata,$newclause,$clauseno);
            xarSessionSetVar('statusmsg', xarML('Legislation clause was successfully inserted!'));
        } else {
            unset($currentdata[$clauseno]);
            $currentdata=array_merge($dummy,$currentdata);
            unset($currentdata[0]);
            $docdata[$clausetype]=$currentdata;
           xarSessionSetVar('statusmsg', xarML('Clause was successfully deleted!'));
        }

    }
        foreach ($clauses as $docs =>$v) {
            $doctype=(int)$v;
            $newdata[$doctype]=$docdata[$doctype];
        }

    $doccontent=serialize($newdata);
    $contributorserialized=serialize($contribdata);

    if (!xarModAPIFunc('legis','admin','update',
                       array('cdid'        => (int)$cdid,
                             'doccontent'  => $doccontent,
                             'contributors'=>$contributorserialized))) {
        return;
    }

    xarResponseRedirect(xarModURL('legis', 'admin', 'modify', array('cdid'=>$cdid)));

    return true;

}
  function insertafter($currentdata,$insertarray,$clauseno){
    $clauseposition=(int)$clauseno;
    if(is_int($clauseposition)){
        $temparray=array_merge(array_slice($currentdata,0,$clauseposition+1), $insertarray, array_slice($currentdata,$clauseposition+1));
    }else{
        foreach($currentdata as $k=>$v){
            $temparray[$k]=$v;
            if($k==$clauseposition)$temparray=array_merge($temparray,$newarray);
        }
    }
    //fix this later - here for now due to numeric array resequencing and need to start from 1
    $dummy=array(0);
    $temparray=array_merge($dummy,$temparray);
    unset ($temparray[0]);

    return $temparray;
  }

?>
