<?php
/**
 * Get the default or set hall
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
 * Get the default or set hall
 *
 * Get the default hall which may be the user set hall of overall system default
  *
 * @author jojodee
 */
function legis_userapi_getsethall($args)
{
    extract($args);
  
    $halls=array();

    $hallsparent=xarModGetVar('legis','mastercids');
    $halls=xarModApiFunc('categories','user','getchildren',array('cid'=>$hallsparent));

    if (!isset($defaulthall) || empty($defaulthall)) {
            /* First check for user mod var that is set for logged in user */
           if (xarUserIsLoggedIn()) {
               $uid = xarUserGetVar('uid');
               $defaulthall = (int)xarModGetUserVar('legis','defaulthall');
               if (!isset($defaulthall) || empty($defaulthall))  {
                  // $defaulthall = (int)xarSessionGetVar('legishall');
                  $defaulthall=(int)xarModGetVar('legis','defaulthall');
               }
           }

           if (!xarUserIsLoggedIn()){
               /* try now for session */
               $defaulthall = (int)xarSessionGetVar('legishall');
               if (!isset($defaulthall) || empty($defaulthall))  {
                  $defaulthall=(int)xarModGetVar('legis','defaulthall');
                }
           }
            $defaulthalldata=$halls[$defaulthall];
   } else {
       $defaulthalldata=$halls[$defaulthall];
   }

   if (!isset($defaulthalldata)) {
           $usehall=xarModGetVar('legis','defaulthall');
           $defaulthalldata=$halls[$usehall];
   }

   $data['defaulthalldata']=$defaulthalldata;
   $data['halls']=$halls;
   $data['defaulthall']=$defaulthall;

    return $data;
}
?>