<?php
/**
 * Sitemapper Module
 *
 * @package modules
 * @subpackage sitemapper module
 * @category Third Party Xaraya Module
 * @copyright (C) 2011 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Return the options for the admin menu
 *
 */

    function sitemapper_adminapi_getmenulinks()
    {
        return xarMod::apiFunc('base','admin','menuarray',array('module' => 'sitemapper'));
    }

?>