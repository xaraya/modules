<?php
/**
 * Reset the default hall
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage legis Module
 * @link http://xaraya.com/index.php/release/593.html
 * @author jojodee
 */

/**
 * The main user function
 *
 * @author jojodee
 */
function legis_userapi_sethall($args)
{
    extract($args);

  //  if (!xarSecurityCheck('ViewLegis')) return;
    if (!xarVarFetch('defaulthall', 'int:0:', $defaulthall, NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('reset', 'int:0:1', $reset, 0, XARVAR_DONT_SET)) {return;}

         //Get the category halls
    $hallsparent=xarModGetVar('legis','mastercids');
    $halls=xarModApiFunc('categories','user','getchildren',array('cid'=>$hallsparent));

    $data['halls']=$halls;

    if (isset($reset) && ($reset == 1)) { //this means we want to reset our default hall
         xarSessionDelVar('legishall');
         xarModDelUserVar('legis','defaulthall');
    }elseif (isset($defaulthall)&& !empty($defaulthall)) {
         if (xarUserIsLoggedIn()){
               $uid=xarUserGetVar('uid');
               xarModSetUserVar('legis','defaulthall',$defaulthall);
           }
           xarSessionSetVar('legishall',$defaulthall);

    }elseif (!isset($defaulthall) || empty($defaulthall)) {
            /* First check for user mod var that is set for logged in user */
           if (xarUserIsLoggedIn()) {
               $uid = xarUserGetVar('uid');
               $defaulthall = xarModGetUserVar('legis','defaulthall',$uid);
               if (!isset($defaulthall) || empty($defaulthall))  {
                   $defaulthall = xarSessionGetVar('legishall');
                   xarModSetUserVar('legis','defaulthall',$defaulthall);
               }
           }

           if (!xarUserIsLoggedIn()){
               /* try now for session */
               $defaulthall = xarSessionGetVar('legishall');
               if (!isset($defaulthall) || empty($defaulthall))  {
                  $defaulthall=xarModGetVar('legis','defaulthall');
                }
           }
        $defaulthalldata=$halls[$defaulthall];
        /* let's make sure we set the session var if the defaul hall  is set */
        if (isset($defaulthall) && !empty($defaulthall)) {
        xarSessionSetVar('legishall',$defaulthall);
    }

    }
    if (!isset($defaulthalldata)) {
       $usehall=xarModGetVar('legis','defaulthall');
       $defaulthalldata=$halls[$usehall];
    }
         //Get the types of legislation
        $legistypes=xarModAPIFunc('legis','user','getmastertypes');

        $data = xarModAPIFunc('legis', 'user', 'menu');
         $hallsparent=xarModGetVar('legis','mastercids');

    //create the hall links
    foreach ($halls as $k=>$hall) {
        if (isset($defaulthall) && $defaulthall!='') {
            $halls[$k]['link']=xarModURL('legis','user','view',
                          array('hall'=>$hall['cid']));
        }else{
            $halls[$k]['link']=xarModURL('legis','user','main',
                        array('defaulthall'=>$hall['cid']));
        }
    }
 
    $data['defaulthall']=$defaulthall;
    $data['defaulthalldata']=$defaulthalldata;
    $data['halls']=$halls;
    return $data;

}
?>