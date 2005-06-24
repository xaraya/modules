<?php

/**
* File: $Id: jump.php,v 1.2 2005/01/26 08:45:25 michelv01 Exp $
*
* Forwards from the jump form to the page it's suppose to go to based on the jump to date.
*
* @package Xaraya eXtensible Management System
* @copyright (C) 2004 by Metrostat Technologies, Inc.
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.metrostat.net
*
* @subpackage julian
* initial template: Roger Raymond
* @author Jodie Razdrh/John Kevlin/David St.Clair
*/

  function julian_user_jump($args)
  { 
    //This takes a month,day,year, and location to jump to and forwards it on to the new location. 
    extract($args); unset($args);
    xarVarFetch('jump_to','str::',$jump_to);
    xarVarFetch('jump_month','str',$jump_month);
    xarVarFetch('jump_day','str',$jump_day);
    xarVarFetch('jump_year','str',$jump_year);
    xarResponseRedirect(xarModURL('julian', 'user', $jump_to, array('cal_date' => $jump_year . $jump_month . $jump_day)));
  }
?>
