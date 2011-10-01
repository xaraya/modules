<?php
/**
 * @package modules
 * @copyright (C) copyright-placeholder
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
class Shouter_ShoutBlockAdmin extends Shouter_ShoutBlock implements iBlock
{
    public function modify(Array $data=array())
    {
        $data = parent::modify($data);
        return $data;
    }


/**
 * update block settings
 * @return array
 * @todo set defaults for xarVarFetch with $vars
 */
    public function update(Array $data=array())
    {
        $data = parent::update($data);
        $vars = !empty($data['content']) ? $data['content'] : array();

        if (!xarVarFetch('numitems', 'int:1:', $vars['numitems'], 5, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('anonymouspost','checkbox', $vars['anonymouspost'], false,XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('shoutblockrefresh', 'int', $vars['shoutblockrefresh'], 0, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('allowsmilies','checkbox', $vars['allowsmilies'], false,XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('lightrow','str:1:', $vars['lightrow'], 'FFFFFF',XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('darkrow','str:1:', $vars['darkrow'], 'E0E0E0',XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('blockwidth','int:1:', $vars['blockwidth'], 180,XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('blockwrap','int:1:', $vars['blockwrap'], 19,XARVAR_NOT_REQUIRED)) return;

        // begin to turn off smilies--- TEST CODE ---
        if (!$vars['allowsmilies']) {
            xarModAPIFunc('modules', 'admin', 'disablehooks',
                    array('callerModName' => 'shouter', 'hookModName' => 'smilies'));
        } else {
            xarModAPIFunc('modules', 'admin', 'enablehooks',
                    array('callerModName' => 'shouter', 'hookModName' => 'smilies'));
        }
        $data['content'] = $vars;

        return $data;
    }
}
?>