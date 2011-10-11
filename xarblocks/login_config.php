<?php
/**
 * Modify Function to the Blocks Admin
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Registration module
 * @link http://xaraya.com/index.php/release/30205.html
 */
/**
 * Modify Function to the Blocks Admin
 * @author Jo Dalle Nogare
 * @author Jim McDonald
 * @param $blockinfo array containing title,content
 * @return array $args array
 */
sys::import('modules.registration.xarblocks.login');
class Registration_LoginBlockConfig extends Registration_LoginBlock implements iBlock
{

    public function configmodify()
    {
        $data = $this->getContent();
        return $data;
    }

/**
 * Updates the Block config from the Blocks Admin
 * @param $data array containing title,content
 */
    public function configupdate()
    {
        if (!xarVarFetch('showlogout', 'checkbox',
            $showlogout, false, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('logouttitle', 'pre:trim:str:1:254',
            $logouttitle, '', XARVAR_NOT_REQUIRED)) return;
        
        $this->showlogout = $showlogout;        
        $this->logouttitle = $logouttitle;
        return true;
    }
}
?>