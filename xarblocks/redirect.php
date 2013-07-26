<?php
/**
 * Redirect Block
 *
 * @package modules
 * @subpackage wurfl module
 * @category Third Party Xaraya Block
 * @version 1.0.0
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
sys::import('xaraya.structures.containers.blocks.basicblock');
class Wurfl_RedirectBlock extends BasicBlock
{
    protected $type                = 'redirect';
    protected $module              = 'wurfl';
    protected $text_type           = 'WURFL Redirect';
    protected $text_type_long      = 'WURFL Redirect';
    protected $xarversion          = '1.0.0';
    protected $show_preview        = true;
    protected $usershared          = true;
    protected $pageshared          = false;
    
    public $redirects              = false;
        
    public function init() 
    {
        parent::init();
    }

    
    public function upgrade($oldversion)
    {
        // grab existing content
        $data = $this->content;
        switch ($oldversion) {
            case '0.0.0':

            case '1.0.0':
                // upgrades from 1.0.0 go here...
            break;
        }
        // replace content with updated array 
        $this->content = $data;
        
        return true;
    }
}
?>