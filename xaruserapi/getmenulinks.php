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
                                                   'callrotator'),
                              'title' => xarML('Call Rotator'),
                              'label' => xarML('Call Rotator'));

        $menulinks[] = Array('url'   => xarModURL('dossier',
                                                   'user',
                                                   'birthdays'),
                              'title' => xarML('Upcoming birthdays of contacts'),
                              'label' => xarML('Birthdays'));

        $menulinks[] = Array('url'   => xarModURL('dossier',
                                                   'admin',
                                                   'new'),
                              'title' => xarML('Add a new Contact'),
                              'label' => xarML('New Contact'));
    }

    if (xarSecurityCheck('TeamDossierAccess',0)) {

        $menulinks[] = Array('url'   => xarModURL('dossier',
                                                   'admin',
                                                   'view'),
                              'title' => xarML('View contact list entries'),
                              'label' => xarML('View Contact List'));
    }

    if (xarSecurityCheck('AdminDossier',0)) {

        $menulinks[] = Array('url'   => xarModURL('dossier',
                                                   'admin',
                                                   'modifyconfig'),
                              'title' => xarML('Modify module settings'),
                              'label' => xarML('Configuration'));
    }
    
    return $menulinks;

}

?>
