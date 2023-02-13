<?php
/**
 * Mime Module
 *
 * @package modules
 * @subpackage mime module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright see the html/credits.html file in this Xaraya release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com/index.php/release/eid/999
 * @author Marc Lutolf <mfl@netspan.ch>
 */

/**
 * Return the options for the admin menu
 *
 */

    function mime_adminapi_getmenulinks()
    {
        return xarMod::apiFunc('base','admin','menuarray',array('module' => 'mime'));
    }

?>