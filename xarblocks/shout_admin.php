<?php
/**
 * Shouter Module
 *
 * @package modules
 * @subpackage shouter module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.zwiggybo.com
 * @link http://xaraya.com/index.php/release/236.html
 * @author Neil Whittaker
 */

/**
 * Modify block settings
 *
 * @param array $blockinfo The array with information for this block
 * @return array
 */
sys::import('modules.shouter.xarblocks.shout');

class Shouter_ShoutBlockAdmin extends Shouter_ShoutBlock implements iBlock
{
    public function modify()
    {
        $data = $this->getContent();

        $data['blockid'] = $this->block_id;
        return $data;
    }


/**
 * update block settings
 * @return array
 * @todo set defaults for xarVarFetch with $vars
 */
    public function update()
    {
        if (!xarVarFetch('numitems',          'int:1:',   $vars['numitems'],          $this->numitems, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('anonymouspost',     'checkbox', $vars['anonymouspost'],     false,XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('shoutblockrefresh', 'int',      $vars['shoutblockrefresh'], $this->shoutblockrefresh, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('allowsmilies',      'checkbox', $vars['allowsmilies'],      false,XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('lightrow',          'str:1:',   $vars['lightrow'],          $this->lightrow,XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('darkrow',           'str:1:',   $vars['darkrow'],           $this->darkrow,XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('blockwidth',        'int:1:',   $vars['blockwidth'],        $this->blockwidth,XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('blockwrap',         'int:1:',   $vars['blockwrap'],         $this->blockwrap,XARVAR_NOT_REQUIRED)) return;

        // begin to turn off smilies--- TEST CODE ---
        if (!$vars['allowsmilies']) {
            xarModAPIFunc('modules', 'admin', 'disablehooks',
                    array('callerModName' => 'shouter', 'hookModName' => 'smilies'));
        } else {
            xarModAPIFunc('modules', 'admin', 'enablehooks',
                    array('callerModName' => 'shouter', 'hookModName' => 'smilies'));
        }
        $this->setContent($vars);
        return true;
    }
}
?>
