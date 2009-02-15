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
 * @return array Array containing the menulinks for the main menu items.
 */
function publications_userapi_getmenulinks()
{
    $menulinks = array();
    if (!xarSecurityCheck('ViewPublications',0)) return $menulinks;

// TODO: re-evaluate for browsing by category

    $menulinks[] = Array('url'   => xarModURL('publications',
                                              'user',
                                              'view'),
                         'title' => xarML('Highlighted Publications'),
                         'label' => xarML('Front Page'));

    $pubtypeobject = DataObjectMaster::getObjectList(array('name' => 'publications_types'));
    $items = $pubtypeobject->getItems(array('where' => 'state = 3'));

    foreach ($items as $item) {
        $menulinks[] = Array('url'   => xarModURL('publications','user','view',array('ptid' => $item['id'])),
                             'title' => xarML('Display #(1)',$item['description']),
                             'label' => $item['description']);

            if (!empty($settings['showarchives'])) {
                $menulinks[] = Array('url'   => xarModURL('publications',
                                                          'user',
                                                          'archive',
                                                          array('ptid' => $item['id'])),
                                     'title' => xarML('View #(1) Archive',$item['description']),
                                     'label' => '&#160;' . xarML('Archives'));
            }
    }

    $menulinks[] = Array('url'   => xarModURL('publications',
                                              'user',
                                              'viewmap'),
                         'title' => xarML('Displays a map of all published content'),
                         'label' => xarML('Publication Map'));
                             
    return $menulinks;
}

?>
