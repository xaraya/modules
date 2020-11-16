<?php
/**
 * Translations Module
 *
 * @package modules
 * @subpackage translations module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/77.html
 * @author Marcel van der Boom <marcel@xaraya.com>
 */

/**
 * Pass individual menu items to the admin menu
 *
 * @return array containing the menulinks for the admin menu items.
 */
function translations_adminapi_getmenulinks()
{
    $menuLinks[] = array('url'   => xarModURL('translations', 'admin', 'start'),
                         'title' => xarML('Work on translations'),
                         'label' => xarML('Translate'));
    $menuLinks[] = array('url'   => xarModURL('translations', 'admin', 'bulk'),
                         'title' => xarML('Perform bulk operations'),
                         'label' => xarML('Bulk'));
    $menuLinks[] = array('url'   => xarModURL('translations', 'admin', 'show_status'),
                         'title' => xarML('Show the progress status of the locale currently being translated'),
                         'label' => xarML('Progress report'));
    $menuLinks[] = array('url'   => xarModURL('translations', 'admin', 'modifyconfig'),
                         'title' => xarML('Modify translation configuration Values'),
                         'label' => xarML('Modify Config'));

    return $menuLinks;
}
