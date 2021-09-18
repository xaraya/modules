<?php
/**
 * Comments Module
 *
 * @package modules
 * @subpackage comments
 * @category Third Party Xaraya Module
 * @version 2.4.0
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
/**
 * Sets up any formaction / formdisplay hooks
 *
 */
function comments_userapi_formhooks()
{
    $hooks = [];
    $hooks['formaction']              = xarModHooks::call('item', 'formaction', '', [], 'comments');
    $hooks['formdisplay']             = xarModHooks::call('item', 'formdisplay', '', [], 'comments');

    if (empty($hooks['formaction'])) {
        $hooks['formaction'] = '';
    } elseif (is_array($hooks['formaction'])) {
        $hooks['formaction'] = join('', $hooks['formaction']);
    }

    if (empty($hooks['formdisplay'])) {
        $hooks['formdisplay'] = '';
    } elseif (is_array($hooks['formdisplay'])) {
        $hooks['formdisplay'] = join('', $hooks['formdisplay']);
    }

    return $hooks;
}
