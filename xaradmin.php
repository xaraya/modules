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
 * formhooks - should be moved elsewhere...
 *
 * I'll handle it as an API call once I have my seperate integration module built;) JC.
 */
function articles_user_formhooks($ptid = '')
{

    $hooks = array();
    // call the right hooks, i.e. not the ones for the comments module :)
    $hooks['formaction']              = xarModCallHooks('item', 'formaction', '', array(), 'articles', $ptid);
    $hooks['formdisplay']             = xarModCallHooks('item', 'formdisplay', '', array(), 'articles', $ptid);

    // Note: this was used by the bbcode module to insert id="post" in the form tag - ignored
    if (empty($hooks['formaction'])){
        $hooks['formaction'] = '';
    } elseif (is_array($hooks['formaction'])) {
        $hooks['formaction'] = join('',$hooks['formaction']);
    }

    // Note: this is used by the bbcode module to insert bbcode input buttons etc. in forms
    if (empty($hooks['formdisplay'])){
        $hooks['formdisplay'] = '';
    } elseif (is_array($hooks['formdisplay'])) {
        $hooks['formdisplay'] = join('',$hooks['formdisplay']);
    }

    return $hooks;
}

?>
