<?php
/**
 * Generate the common user menu
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
 * Generate the common menu configuration
 *
 * @author jojodee
 */
function legis_userapi_menu()
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
      $halllinks[$k]['view']=xarModURL('legis','user','view',
                               array('dochall'=>$v['cid'],
                                     'docstatus'=>2));
      $halllinks[$k]['pending']=xarModURL('legis','user','view',
                               array('dochall'=>$v['cid'],
                                     'docstatus'=>1));
      $halllinks[$k]['add']=xarModURL('legis','user','addlegis');
      $halllinks[$k]['sethall']=xarModURL('legis','user','main');
      $halllinks[$k]['viewdefault']=xarModURL('legis','user','view',
                               array('defaulthall'=>$v['cid'],
                                    'docstatus'=>2));
    }
    

    $menu['halllinks']=$halllinks;

     /* Return the array containing the menu configuration */
    return $menu;
}
?>
