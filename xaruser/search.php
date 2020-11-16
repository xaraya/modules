<?php
/**
 * Keywords Module
 *
 * @package modules
 * @subpackage keywords module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/187.html
 * @author Alberto Cazzaniga (Janez)
 */

/**
 * Search for keywords
 * @return array retrieved keywords
 */
function keywords_user_search($args)
{
    if (!xarSecurity::check('ReadKeywords', 0)) {
        return '';
    }

    if (!xarVar::fetch('search', 'isset', $data['search'], null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('bool', 'isset', $bool, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('sort', 'isset', $sort, null, xarVar::DONT_SET)) {
        return;
    }

    $data['keys'] = array();
    if ($data['search'] == '') {
        return $data;
    }

    $data['keys'] = xarMod::apiFunc('keywords', 'user', 'search', array('search' => $data['search']));

    return $data;
}
