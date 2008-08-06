<?php
/**
 * Messages Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Messages Module
 * @link http://xaraya.com/index.php/release/6.html
 * @author XarayaGeek
 */
    function messages_adminapi_getmenulinks()
    {
        $menulinks = array();

        if (xarSecurityCheck('ManageCategories',0)) {

            $menulinks[] = Array('url'   => xarModURL('messages',
                                                       'admin',
                                                       'overview'),
                                  'title' => xarML('The overview page for this module'),
                                  'label' => xarML('Overview'));
            $menulinks[] = Array('url'   => xarModURL('messages',
                                                       'admin',
                                                       'modifyconfig'),
                                  'title' => xarML('Modify the configuration for the module'),
                                  'label' => xarML('Modify Config'));
        }

        return $menulinks;
    }
?>
