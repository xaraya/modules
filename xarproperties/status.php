<?php
/**
 * Article Status Property
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
 * handle the status property
 *
 * @package dynamicdata
 */
class StatusProperty extends SelectProperty
{
    public $id         = 10;
    public $name       = 'status';
    public $desc       = 'Article Status';
    public $reqmodules = array('articles');

    function __construct(ObjectDescriptor $descriptor)
    {
        parent::__construct($descriptor);
        $this->filepath   = 'modules/articles/xarproperties';
        if (count($this->options) == 0) {
            $this->options = array(
                 array('id' => 0, 'name' => xarML('Submitted')),
                 array('id' => 1, 'name' => xarML('Rejected')),
                 array('id' => 2, 'name' => xarML('Approved')),
                 array('id' => 3, 'name' => xarML('Front Page')),
             );
        }
    }

    /**
     * Get the base information for this property.
     *
     *
     * @return array base information for this property
     **/
     function getBasePropertyInfo()
     {
         $args = array();
         $baseInfo = array(
                              'id'         => 10,
                              'name'       => 'status',
                              'label'      => 'Status',
                              'format'     => '10',
                              'validation' => '',
                            'source'     => '',
                            'dependancies' => '',
                            'requiresmodule' => 'articles',
                            'aliases'        => '',
                            'args'           => serialize($args)
                            // ...
                           );
        return $baseInfo;
     }

}

?>