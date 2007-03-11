<?php
/**
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.zwiggybo.com
 *
 * @subpackage shouter
 * @link http://xaraya.com/index.php/release/236.html
 * @author Neil Whittaker
 */

/**
 * Modify block settings
 *
 * @param array $blockinfo The array with information for this block
 * @return array
 */
function shouter_shoutblockblock_modify($blockinfo)
{
    /* Get current content */
    if (!is_array($blockinfo['content'])) {
        $vars = unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    /* Defaults */
    //if (empty($vars['numitems'])) {
    //    $vars['numitems'] = 5;
    //}

    /* Send content to template */
    $data = $vars;
    $data['blockid'] = $blockinfo['bid'];
    return $data;
}

/**
 * update block settings
 * @return array
 * @todo set defaults for xarVarFetch with $vars
 */
function shouter_shoutblockblock_update($blockinfo)
{
    $vars = array();
    if (!xarVarFetch('numitems', 'int:1:', $vars['numitems'], 5, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('anonymouspost','checkbox', $vars['anonymouspost'], false,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('shoutblockrefresh', 'int', $vars['shoutblockrefresh'], 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('allowsmilies','checkbox', $vars['allowsmilies'], false,XARVAR_NOT_REQUIRED)) return;
//    if (!xarVarFetch('lightrow','str:1:', $vars['lightrow'], 'FFFFFF',XARVAR_NOT_REQUIRED)) return;
//    if (!xarVarFetch('darkrow','str:1:', $vars['darkrow'], 'E0E0E0',XARVAR_NOT_REQUIRED)) return;
//    if (!xarVarFetch('blockwidth','int:1:', $vars['blockwidth'], 180,XARVAR_NOT_REQUIRED)) return;
//    if (!xarVarFetch('blockwrap','int:1:', $vars['blockwrap'], 19,XARVAR_NOT_REQUIRED)) return;

    // begin to turn off smilies--- TEST CODE ---
    if (!$vars['allowsmilies']) {
        xarModAPIFunc('modules', 'admin', 'disablehooks',
                array('callerModName' => 'shouter', 'hookModName' => 'smilies'));
    } else {
        xarModAPIFunc('modules', 'admin', 'enablehooks',
                array('callerModName' => 'shouter', 'hookModName' => 'smilies'));
    }

    $blockinfo['content'] = $vars;

    return $blockinfo;
}
?>
