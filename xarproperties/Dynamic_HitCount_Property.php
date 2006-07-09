<?php
/**
 * Hitcount
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Hitcount Module
 * @link http://xaraya.com/index.php/release/177.html
 * @author Hitcount Module Development Team
 */

/**
 * Handle the hitcount property
 * @author mikespub <mikespub@xaraya.com>
 *
 */
include_once "modules/dynamicdata/class/properties.php";

class Dynamic_HitCount_Property extends Dynamic_Property
{
    /**
     * Get the base information for this property.
     *
     *
     * @return array Base information for this property
     **/
     function getBasePropertyInfo()
     {
         $args = array();
         $baseInfo = array(
                            'id'         => 101,
                            'name'       => 'hitcount',
                            'label'      => 'Hit Count',
                            'format'     => '101',
                            'validation' => '',
                            'source'     => 'hook module',
                            'dependancies' => '',
                            'requiresmodule' => 'hitcount',
                            'aliases' => '',
                            'args'           => serialize($args),
                            // ...
                           );
        return $baseInfo;
     }

}

?>
