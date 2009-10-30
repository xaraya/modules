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
sys::import('modules.authsystem.xarblocks.login');
class rLoginBlock extends LoginBlock implements iBlock
{
    public $no_cache            = 1;

    public $name                = 'rLoginBlock';
    public $module              = 'registration';
    public $text_type           = 'Login';
    public $text_type_long      = 'Registration and Login';
    public $pageshared          = 1;

    public $showlogout          = 0;
    public $logouttitle         = '';

}
?>