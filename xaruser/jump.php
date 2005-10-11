<?php
/**
 * Jump to another date
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian
 * @author Julian module development team 
 */
 
/**
 *
 * Forwards from the jump form to the page it's suppose to go to based on the jump to date.
 *
 * @copyright (C) 2004 by Metrostat Technologies, Inc.
 * @link http://www.metrostat.net
 *
 * initial template: Roger Raymond
 * @author Jodie Razdrh/John Kevlin/David St.Clair
 * @param jump_to enum day, week, month, year
 * @param jump_month int
 * @param jump_day int
 * @param jump_year int
 */

function julian_user_jump($args)
{ 
    //This takes a month,day,year, and location to jump to and forwards it on to the new location. 
    extract($args); 
    unset($args);
    xarVarFetch('jump_to','str::',$jump_to);
    xarVarFetch('jump_month','int',$jump_month);
    xarVarFetch('jump_day','int',$jump_day);
    xarVarFetch('jump_year','int',$jump_year);
    xarResponseRedirect(xarModURL('julian', 'user', $jump_to, array('cal_date' => $jump_year . $jump_month . $jump_day)));
}
?>