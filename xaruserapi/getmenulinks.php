<?php
/**
 * Mailer Module
 *
 * @package modules
 * @subpackage mailer module
 * @copyright (C) 2010 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Return the options for the user menu
 *
 */

function mailer_userapi_getmenulinks()
{
    $menulinks = array();

    if (xarSecurityCheck('ViewMailer',0)) {
        $menulinks[] = array('url'   => xarModURL('mailer',
                                                  'user',
                                                  'main'),
                              'title' => xarML(''),
                              'label' => xarML(''));
        $menulinks[] = array('url'   => xarModURL('mailer',
                                                  'user',
                                                  'test'),
                              'title' => xarML('Send a test message'),
                              'label' => xarML('Test Message'));
    }

    return $menulinks;
}

?>
