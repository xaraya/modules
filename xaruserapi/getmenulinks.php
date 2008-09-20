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
function messages_userapi_getmenulinks ( $args )
{

    $menulinks = ARRAY();
    if (xarSecurityCheck('ReadMessages', 0)) {
        $menulinks[] = array(
            'url'      => xarModURL('messages', 'user', 'view'),
            'title'    => 'Look at the Messages',
            'label'    => 'View Messages' );
    }

    if (xarSecurityCheck('AddMessages', 0)) {
        $menulinks[] = array(
            'url'      => xarModURL('messages', 'user', 'new'),
            'title'    => 'Send a message to someone',
            'label'    => 'New Message' );
    } 

    return $menulinks;
}

?>
