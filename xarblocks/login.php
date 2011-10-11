<?php
/**
 * Login via a block.
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
 * Login via a block.
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 * @author Jim McDonald
 * initialise block
 * @return array
 */
sys::import('xaraya.structures.containers.blocks.basicblock');
class Registration_LoginBlock extends BasicBlock implements iBlock
{
    protected $type                = 'login';
    protected $module              = 'registration';
    protected $text_type           = 'Login';
    protected $text_type_long      = 'User Login';

    public $showlogout          = 0;
    public $logouttitle         = '';
    
}
?>