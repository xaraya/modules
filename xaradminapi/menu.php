<?php
/**
 * Generate admin menu
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
 * Generate admin menu
 * 
 * Standard function to generate a common admin menu configuration for the module
 *
 * @author jojodee
 */
function legis_adminapi_menu()
{ 
    $menu = array();
    $menu['menutitle'] = xarML('RIC Legislation');

    /* Specify the menu items to be used in your blocklayout template */
    $menu['menulabel_view'] = xarML('View legis items');
    $menu['menulink_view'] = xarModURL('legis', 'user', 'view');
    $hallsparent=xarModGetVar('legis','mastercids');
    $halls=xarModApiFunc('categories','user','getchildren',array('cid'=>$hallsparent));
    $data['halls']=$halls;
    $menu['halls']=$halls;
    $halllinks=array();
    foreach ($halls as $k=>$v) {
      $halllinks[$k]['view']=xarModURL('legis','admin','view',
                               array('dochall'=>$v['cid']));
      $halllinks[$k]['pending']=xarModURL('legis','admin','view',
                               array('dochall'=>$v['cid'],'docstatus'=>1));

      $halllinks[$k]['sethall']=xarModURL('legis','admin','main');
      if (xarSecurityCheck('AdminLegis',0)) {
        $halllinks[$k]['masterdocs']=xarModURL('legis','user','masters');
      }
    }


    $menu['halllinks']=$halllinks;

     /* Return the array containing the menu configuration */
    return $menu;
}

?>
