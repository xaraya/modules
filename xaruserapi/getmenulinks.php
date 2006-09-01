<?php
/**
 * Pass individual menu items to the user menu
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage window
 * @link http://xaraya.com/index.php/release/3002.html
 * @author Marc Lutolf
 */
/**
 * Pass individual menu items to the user menu
 *
 * @return array containing the menulinks for the main menu items.
 */
function window_userapi_getmenulinks()
{
    $urls = xarModAPIFunc('window','user','getall',array('status' => 1));
    foreach($urls as $url) {
        $url_parts = parse_url($url['name']);
        $menulinks[] = array('url'   => xarModURL('window',
                                                  'user',
                                                  'display',array('page' => $url['name'])),
                              'title' => $url['description'],
                              'label' => $url['label']);
    }
    if (xarSecurityCheck('ReadWindow',0)) {
    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}
?>
