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
 * Standard function to update a current item
 *
 * This function is called with the results of the
 * form supplied by xarModFunc('Legis','admin','modify') to update a current item
 * 
 * @author jojodee
 * @param  $ 'cdid' the id of the item to be updated
 */
function legis_admin_update($args)
{
    extract($args);
    $doccontent=array();
    $currentdoclets=xarSessionGetVar('Legisdoclets');

    foreach ($currentdoclets as $k=>$doc) {
        for ($i = 1; $i <= $doc['clauseno']; $i++) {
            if (!xarVarFetch("clause{$k}_{$i}", 'str:1:',
                ${'clause'.$k.'_'.$i},'', XARVAR_NOT_REQUIRED )) {return;};
                    $doccontent[$k][$i]=${'clause'.$k.'_'.$i};
        }
    }

    xarSessionDelVar('Legisdoclets',$currentdoclets);

    if (!xarVarFetch('cdid',     'int:0:',     $cdid,     $cdid,     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('mdid',     'int:0:',     $mdid,     $mdid,     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'int:0:',     $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid',  'array',  $invalid,  $invalid,        XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('defaulthall', 'int:1:', $defaulthall, null, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('contributorno', 'int:0:', $contributorno, null, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('authortype', 'int:0:', $authortype, $authortype, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('legistype', 'int:0:', $legistype, $legistype, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('contributordata', 'array', $contributordata, null, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('dochall', 'int:0:', $dochall, $dochall, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('docstatus', 'int:1:', $docstatus, $docstatus, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('votestatus', 'int:0:', $votestatus, $votestatus, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('vetostatus', 'int:0:', $vetostatus, $vetostatus, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('cdtitle', 'str:1:', $cdtitle,$cdtitle, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('pubnotes', 'str:1:', $pubnotes,$pubnotes, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('authordata', 'array', $authordata,$authordata, XARVAR_NOT_REQUIRED)) {return;}
          if (!xarVarFetch('authorhallname', 'array', $authorhallname,$authorhallname, XARVAR_NOT_REQUIRED)) {return;}
    if (!empty($objectid)) {
        $cdid = $objectid;
    }

   if (!xarSecConfirmAuthKey()) return;

    $invalid = array();
    if (empty($cdid) || !is_numeric($cdid)) {
        $invalid['cdid'] = 1;
        $cdid = '';
    }
   if (empty($cdtitle) || !is_string($cdtitle)) {
        $invalid['cdtitle'] = 1;
        $cdtitle = '';
    }
    /* check if we have any errors */
    if (count($invalid) > 0) {
        /* call the admin_new function and return the template vars
         * (you need to copy admin-new.xd to admin-create.xd here)
         */
        return xarModFunc('legis', 'admin', 'modify',
                          array('cdid'     => $cdid,
                                'invalid'  => $invalid));
    }

         if (is_array($authordata)) {
              foreach ($authordata as $k=>$v) {
              $contributors[$k]=array('authorname'=>$contributordata[$k],
                                    'authordata'=>$authordata[$k],
                                    'authorhallname'=>$authorhallname[$k]);
              }
          }
          $contributorcompiled=serialize($contributors);

          $doccompiled=serialize($doccontent);

    // cdnum - set when the document doc status is set to Valid and not changed after that ie > 1
    //         not from UI

    // cdtitle - editable until Veto status is set ie > 0
    // doccontent - editable until Veto status set  to  > 0
    // docstatus  - editable to set - not editable (due to doc number allocation)
    // votestatus - editable to set - then not editable (cept by top admin and until veto status set)
    // vetostatus - editable to set - then fixed (cept by top admin)
    // all status dates set at time the status is set
    // pubnotes - editable
    // dochall - not editable once the document has doc status changed (as cdnum is set then)
    //archivedate - when archived
    // archiveswitch - turn on for achive state

    if (!xarModAPIFunc('legis','admin','update',
                       array('cdid'        => (int)$cdid,
                             'mdid'        => (int)$mdid,
                             'cdtitle'     => $cdtitle,  // editable until final veto status
                             'docstatus'   => $docstatus, //limited editing
                             'votestatus'  => $votestatus, //limited editing
                             'vetostatus'  => $vetostatus, //limited editing
                             'contributors' => $contributorcompiled,
                             'doccontent'    => $doccompiled, // editable until final veto status
                             'pubnotes'      => $pubnotes, // editable all the time
                             'dochall'       => (int)$dochall))) { //limited editing
        return;
    }

    xarSessionSetVar('statusmsg', xarML('Legislation document was successfully updated!'));
    xarResponseRedirect(xarModURL('legis', 'admin', 'modify',array('cdid'=>$cdid)));
    /* Return */
    return true;
}
?>
