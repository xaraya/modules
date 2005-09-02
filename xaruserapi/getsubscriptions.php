<?php
/*
 * Get the configuration of an user which categories the user wants to 
 * recieve alerts for.
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by Metrostat Technologies, Inc.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.metrostat.net
 *
 * @subpackage julian
 * initial template: Roger Raymond
 * @author Jodie Razdrh/John Kevlin/David St.Clair
 *
 * @return array catid's where subscripted to
 */
function julian_userapi_getsubscriptions($uid = NULL)
{
    $useralerts = xarModGetUserVar('julian','alerts',$uid);
    
    if (!empty($useralerts)) {
        $useralerts = unserialize($useralerts);
    }else {
        $useralerts = array();
    }
    
    return $useralerts;
}
?>