<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
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
    if (!xarSecurity::check('ViewPublications',0)) return $menulinks;

    $menulinks[] = Array('url'   => xarController::URL('publications',
                                              'user',
                                              'main'),
                         'title' => xarML('Highlighted Publications'),
                         'label' => xarML('Front Page'));

    $items = xarMod::apiFunc('publications', 'user', 'get_menu_pages');
    foreach ($items as $item) {
        $menulinks[] = Array('url'   => xarController::URL('publications','user','display',array('itemid' => $item['id'])),
                             'title' => xarML('Display #(1)',$item['description']),
                             'label' => $item['title']);

    }

    $menulinks[] = Array('url'   => xarController::URL('publications',
                                              'user',
                                              'viewmap'),
                         'title' => xarML('Displays a map of all published content'),
                         'label' => xarML('Publication Map'));
                             
    return $menulinks;
}

?>