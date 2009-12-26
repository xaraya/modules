<?php
/**
 * Articles module
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Articles Module
 * @link http://xaraya.com/index.php/release/151.html
 * @author mikespub
 */

/**
 *
 * formhooks
 *
 */
function articles_userapi_formhooks($args)
{

    extract($args);

    $hooks = array();
    // call the right hooks, i.e. not the ones for the comments module :)
    $hooks['formaction']              = xarModCallHooks('item', 'formaction', '', array(), 'articles', $ptid);
    $hooks['formdisplay']             = xarModCallHooks('item', 'formdisplay', '', array(), 'articles', $ptid);

    if (empty($hooks['formaction'])){
        $hooks['formaction'] = '';
    } elseif (is_array($hooks['formaction'])) {
        $hooks['formaction'] = join('',$hooks['formaction']);
    }

    if (empty($hooks['formdisplay'])){
        $hooks['formdisplay'] = '';
    } elseif (is_array($hooks['formdisplay'])) {
        $hooks['formdisplay'] = join('',$hooks['formdisplay']);
    }

    return $hooks;
}

?>
