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
 * @author Marc Lutolf <mfl@netspan.ch>
 */
sys::import('xaraya.structures.containers.blocks.basicblock');

class Keywords_SearchBlock extends BasicBlock
{
    // File Information, supplied by developer, never changes during a versions lifetime, required
    protected $type             = 'search';
    protected $module           = 'keywords'; // module block type belongs to, if any
    protected $text_type        = 'Keywords Search';  // Block type display name
    protected $text_type_long   = 'Seach keywords'; // Block type description
    // Additional info, supplied by developer, optional
    protected $type_category    = 'block'; // options [(block)|group]
    protected $author           = '';
    protected $contact          = '';
    protected $credits          = '';
    protected $license          = '';

    // blocks subsystem flags
    protected $show_preview = true;  // let the subsystem know if it's ok to show a preview
    protected $show_help    = false; // let the subsystem know if this block type has a help() method

    public $module_id           = 27;
    public $itemtype            = 1;

    /*    function display()
        {
            $vars = $this->getContent();
            $vars['tags'] = array();
            switch ($vars['cloudtype']) {
                case 1:
                break;
                case 2:
                case 3:
                    $vars['tags'] = xarMod::apiFunc('keywords','user','getkeywordhits',array('cloudtype' => $vars['cloudtype']));
                break;
            }
            return $vars;
        }*/
}
