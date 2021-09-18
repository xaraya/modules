<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * retrieve the settings of a publication type
 *
 * @param $args array containing the publication type
 * @return array of setting keys and values
 */

function publications_userapi_getsetting($data)
{
    $settings = xarMod::apiFunc('publications', 'user', 'getsettings', $data);

    if (isset($settings[$data['setting']])) {
        return $settings[$data['setting']];
    }
    return null;
}
