<?php
/**
 * Publications module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Publications Module
 
 * @author mikespub
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Publications module development team
 * @return array containing the menulinks for the main menu items.
 */
function publications_adminapi_getmenulinks()
{
    $menulinks = array();

// Security Check
    if (xarSecurityCheck('EditPublications',0)) {

        $menulinks[] = Array('url'   => xarModURL('publications',
                                                   'admin',
                                                   'view'),
                              'title' => xarML('View and edit all publications'),
                              'label' => xarML('View Publications'));
    }

// Security Check
    if (xarSecurityCheck('SubmitPublications',0)) {

        $menulinks[] = Array('url'   => xarModURL('publications',
                                                   'admin',
                                                   'new'),
                              'title' => xarML('Add a new publication'),
                              'label' => xarML('Add Publication'));
    }
    if (xarSecurityCheck('ManagePublications',0)) {

        $menulinks[] = Array('url'   => xarModURL('publications',
                                                   'admin',
                                                   'stats'),
                              'title' => xarML('Utility functions'),
                              'label' => xarML('Utlities'));
    }
    if (xarSecurityCheck('AdminPublications',0)) {
        $menulinks[] = Array('url'   => xarModURL('publications',
                                                   'admin',
                                                   'pubtypes'),
                              'title' => xarML('View and edit publication types'),
                              'label' => xarML('Publication Types'));

    // TODO: differentiate security check according to pubtype ?
        $menulinks[] = Array('url'   => xarModURL('publications',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Modify the publications module configuration'),
                              'label' => xarML('Modify Config'));
    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}

?>