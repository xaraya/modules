<?php
/**
 * Translations module
 *
 * @package modules
 * @subpackage translations module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author M. Lutolf (mfl@netspan.ch)
 */

function translations_userapi_getmenulinks($args)
{
    if (xarSecurity::check('ReadTranslations', 0) == true) {
        $menulinks[] = array(
            'url'   => xarController::URL('translations', 'user', 'show_status', array('action' => 'post')),
            'title' => xarML('Show the progress status of the locale currently being translated'),
            'label' => xarML('Progress report'));
    } else {
        $menulinks = '';
    }

    return $menulinks;
}
