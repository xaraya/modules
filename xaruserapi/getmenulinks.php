<?php
/**
 * DOSSIER user getMenuLinks
 *
 * @package modules
 * @copyright (C) 2002-2007 Chad Kraeft
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dossier Module
 * @author Chad Kraeft <cdavidkraeft@miragelab.com>
 * Based on labDossier (on PostNuke) by Chad Kraeft <cdavidkraeft@miragelab.com>
 */

/**
 * builds an array of menulinks for display in a menu block
 *
 * @return array of menu links
 */
function dossier_userapi_getmenulinks()
{
    $menulinks = array();
    
    if (xarSecurityCheck('TeamDossierAccess',0)) {

        $menulinks[] = Array('url'   => xarModURL('dossier',
                                                   'user',
                                                   'main'),
                              'title' => xarML('Overview'),
                              'label' => xarML('Overview'));

        $menulinks[] = Array('url'   => xarModURL('roles',
                                                   'user',
                                                   'account',
                                                   array('moduleload' => 'dossier')),
                              'title' => xarML('My Contacts'),
                              'label' => xarML('My Contacts'));

        $menulinks[] = Array('url'   => xarModURL('dossier',
                                                   'user',
                                                   'view'),
                              'title' => xarML('Contact List'),
                              'label' => xarML('Contact List'));

        $menulinks[] = Array('url'   => xarModURL('dossier',
                                                   'user',
                                                   'callrotator'),
                              'title' => xarML('Call Rotator'),
                              'label' => xarML('Call Rotator'));

        $menulinks[] = Array('url'   => xarModURL('dossier',
                                                   'user',
                                                   'birthdays'),
                              'title' => xarML('Upcoming birthdays of contacts'),
                              'label' => xarML('Birthdays'));
    }
    
    return $menulinks;

}

?>
