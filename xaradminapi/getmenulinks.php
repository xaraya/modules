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
 * Return the options for the admin menu
 *
 */

    function mailer_adminapi_getmenulinks()
    {
        return xarModAPIFunc('base','admin','menuarray',array('module' => 'mailer'));
    }

?>