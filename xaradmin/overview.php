<?php
/**
 * Overview displays standard Overview page
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage AddressBook Module
 * @link http://xaraya.com/index.php/release/66417.html
 * @author Garrett Hunter <garrett@blacktower.com>
 * Based on pnAddressBook by Thomas Smiatek <thomas@smiatek.com>
 */
/**
 * Overview function that displays the standard Overview page
 *
 * This function shows the overview template, currently admin-main.xd.
 * The template contains overview and help texts
 *
 * @author the AddressBook module development team
 * @return array xarTplModule with $data containing template data
 * @since 4 March 2006
 */
function addressbook_admin_overview()
{
   /* Security Check */
    if (!xarSecurityCheck('AdminAddressBook',0)) return;

    $data=array();

    /* if there is a separate overview function return data to it
     * else just call the main function that displays the overview
     */

    return xarTplModule('addressbook', 'admin', 'main', $data,'main');
}

?>
