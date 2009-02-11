<?php
/**
 * Publication Status Property
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage dynamicdata properties
 * @author mikespub <mikespub@xaraya.com>
 */
/**
 * Include the base class
 *
 */
sys::import('modules.base.xarproperties.dropdown');

/**
 * handle the state property
 *
 * @package dynamicdata
 */
class StatusProperty extends SelectProperty
{
    public $id         = 10;
    public $name       = 'state';
    public $desc       = 'Publication Status';
    public $reqmodules = array('publications');

    function __construct(ObjectDescriptor $descriptor)
    {
        parent::__construct($descriptor);
        $this->filepath   = 'modules/publications/xarproperties';
    }

    function getOptions()
    {
        $options = $this->getFirstline();
        if (count($this->options) > 0) {
            if (!empty($firstline)) $this->options = array_merge($options,$this->options);
            return $this->options;
        }
        
        $options[] = array('id' => 0, 'name' => xarML('Submitted'));
        $options[] = array('id' => 1, 'name' => xarML('Rejected'));
        $options[] = array('id' => 2, 'name' => xarML('Approved'));
        $options[] = array('id' => 3, 'name' => xarML('Front Page'));
        return $options;
    }
}

?>
