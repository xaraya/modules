<?php
/**
 * File: $Id:
 * 
 * Get list of possible text states
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage bible
 * @author curtisdf 
 */
/**
 * generate list of states
 */
function bible_adminapi_statelist()
{ 
    $states = array(0 => xarML('Not Installed'),
                    1 => xarML('Inactive'),
                    2 => xarML('Active'),
                    3 => xarML('Updated'),
                    4 => xarML('Missing')); 

    return $states;
} 

?>
