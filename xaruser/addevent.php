<?php
/**
 * Let user add an event
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 */

/**
 * 
 * Generates a form for adding an event.
 *
 * @copyright (C) 2004 by Metrostat Technologies, Inc.
 * @link http://www.metrostat.net
 *
 * @subpackage julian
 * initial template: Roger Raymond
 * @author Jodie Razdrh/John Kevlin/David St.Clair
 * @param $cal_date
 */

function julian_user_addevent($args)
{
    extract ($args);
    
    //This prevents users from viewing something they are not suppose to.
    if (!xarSecurityCheck('Editjulian')) return;
    
    if (!xarVarFetch('cal_date','int::',$cal_date)) return;

    // Build description for the item we want the hooks (i.e. category) for.
    $item = array();
    $item['module'] = 'julian';
    $item['multiple'] = false;
     
    // Get the hooks for this item.
    $hooks = xarModCallHooks('item', 'new', '', $item);
     
    // Deal with no-hook scenario (the template then must get an empty hook-array)
     if (empty($hooks)) {
        $bl_data['hooks'] = array();
    } else {
        $bl_data['hooks'] = $hooks;
    }
     
    $bl_data['todays_month'] = date("n",strtotime($cal_date));
    $bl_data['todays_year'] = date("Y",strtotime($cal_date));
    $bl_data['todays_day'] = date("d",strtotime($cal_date));  
    //building share options
    $bl_data['share_options'] = xarModAPIFunc('julian','user','getuseroptions',array('uids'=>''));  
    $bl_data['cal_date']=$cal_date;
    return $bl_data;
}
?>
