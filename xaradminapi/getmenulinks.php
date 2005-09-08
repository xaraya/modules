<?php

/**
* File: $Id:$
*
* Administration menu links.
*
* @package Xaraya eXtensible Management System
* @copyright (C) 2005 by Metrostat Technologies, Inc.
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.metrostat.net
*
* @subpackage julian
* initial template: Roger Raymond
* @author Jodie Razdrh/John Kevlin/David St.Clair/ Michel V.
*/
/**
 * Retreive admin links
 *
 * standard function
 *
 * @author  Julian Development Team, MichelV. <michelv@xarayahosting.nl>
 * @access  private 
 * @param   
 * @return  array $menulinks
 * @todo    MichelV. <#> 
 */


function julian_adminapi_getmenulinks()
{

    $menulinks = '';
    /*
    $menulinks[] = Array('url'=>xarModURL('calendar','admin','modifyconfig'),
                         'title'=>xarML('Modify the configuration for Calendar'),
                         'label'=>xarML('Modify Config'));
    $menulinks[] = Array('url'=>xarModURL('calendar','admin','view'),
                         'title'=>xarML('View queued events'),
                         'label'=>xarML('View Queue'));
    */
    
    
    if (xarSecurityCheck('Adminjulian', 0)) {
        $menulinks[] = Array('url' => xarModURL('julian',
                'admin',
                'modifycategories'),
            'title' => xarML('Modify Categories'),
            'label' => xarML('Modify Categories'));
    }
    
    if (xarSecurityCheck('Adminjulian', 0)) {
        $menulinks[] = Array('url' => xarModURL('julian',
                'admin',
                'modifyconfig'),
            'title' => xarML('Modify Config'),
            'label' => xarML('Modify Config'));
    }
    
    return $menulinks;
}
?>
