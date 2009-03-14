<?php
/**
 * Keywords Module
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Keywords Module
 * @link http://xaraya.com/index.php/release/187.html
 * @author mikespub
*/
/**
 * utility function pass individual menu items to the Admin menu
 *
 * @author mikespub
 * @return array containing the menulinks
 */
function keywords_adminapi_getmenulinks()
{
    static $menulinks = array();
    if (isset($menulinks[0])) {
        return $menulinks;
    }
        /* Removing the view function due to usuability.  Seems over complicated, since most of the editing is done via the original item.
        $menulinks[] = Array('url'   => xarModURL('keywords',
                                                  'admin',
                                                  'view'),
                              'title' => xarML('Overview of the keyword assignments'),
                              'label' => xarML('View Keywords'));
        */
    if (xarSecurityCheck('AdminKeywords', 0)) {
        $menulinks[] = array( 'url'    => xarModURL('keywords','admin','modifyconfig')
                             ,'title'  => xarML('Modify the keywords configuration')
                             ,'label'  => xarML('Modify Config')
                             ,'active' => array('modifyconfig')
        );
        $menulinks[] = array( 'url'    => xarModURL('keywords','admin','overview')
                             ,'title'  => xarML('Introduction on handling this module')
                             ,'label'  => xarML('Overview')
                             ,'active' => array('overview')
        );
    }

    return $menulinks;
}
?>