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
sys::import('modules.shouter.xarblocks.shout');

class Shouter_ShoutBlockAdmin extends Shouter_ShoutBlock implements iBlock
{
    public function modify(Array $data=array())
    {
        $data = parent::modify($data);

        // Defaults
        if (empty($data['numitems'])) $data['numitems'] = $this->numitems;
        if (empty($data['anonymouspost'])) $data['anonymouspost'] = $this->anonymouspost;
        if (empty($data['shoutblockrefresh'])) $data['shoutblockrefresh'] = $this->shoutblockrefresh;
        if (empty($data['allowsmilies'])) $data['allowsmilies'] = $this->allowsmilies;
        if (empty($data['lightrow'])) $data['lightrow'] = $this->lightrow;
        if (empty($data['darkrow'])) $data['darkrow'] = $this->darkrow;
        if (empty($data['blockwidth'])) $data['blockwidth'] = $this->blockwidth;
        if (empty($data['blockwrap'])) $data['blockwrap'] = $this->blockwrap;

        $data['blockid'] = $data['bid'];
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

        if (!xarVarFetch('numitems',          'int:1:',   $vars['numitems'],          $this->numitems, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('anonymouspost',     'checkbox', $vars['anonymouspost'],     $this->anonymouspost,XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('shoutblockrefresh', 'int',      $vars['shoutblockrefresh'], $this->shoutblockrefresh, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('allowsmilies',      'checkbox', $vars['allowsmilies'],      $this->allowsmilies,XARVAR_NOT_REQUIRED)) return;
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
        $data['content'] = $vars;

        return $data;
    }
}
?>