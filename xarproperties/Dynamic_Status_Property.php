<?php
/**
 * File: $Id$
 *
 * Dynamic Data Status Property
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage dynamicdata properties
 * @author mikespub <mikespub@xaraya.com>
*/

/**
 * Include the base class
 *
 */
include_once "includes/properties/Dynamic_Select_Property.php";

/**
 * handle the status property
 *
 * @package dynamicdata
 */
class Dynamic_Status_Property extends Dynamic_Select_Property
{
    function Dynamic_Status_Property($args)
    {
        $this->Dynamic_Select_Property($args);
        if (count($this->options) == 0) {
            $this->options = array(
                                 array('id' => 0, 'name' => xarML('Submitted')),
                                 array('id' => 1, 'name' => xarML('Rejected')),
                                 array('id' => 2, 'name' => xarML('Approved')),
                                 array('id' => 3, 'name' => xarML('Front Page')),
                             );
        }
    }

    // default showInput() from Dynamic_Select_Property

    // default showOutput() from Dynamic_Select_Property


    /**
     * Get the base information for this property.
     *
     * @returns array
     * @return base information for this property
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
                            'requiresmodule' => '',
                            'aliases'        => '',
                            'args'           => serialize($args)
                            // ...
                           );
        return $baseInfo;
     }

}

?>
