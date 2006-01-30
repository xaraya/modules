<?php
/**
 * Dynamic Categories Property
 *
 * @package modules
 * @copyright (C) 2002-2006 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage dynamicdata properties
 * @author mikespub <mikespub@xaraya.com>
*/
/**
 * handle static text property
 *
 * @package dynamicdata
 *
 */
include_once "modules/dynamicdata/class/properties.php";

class Dynamic_Categories_Property extends Dynamic_Property
{
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
                            'id'         => 100,
                            'name'       => 'categories',
                            'label'      => 'Categories',
                            'format'     => '100',
                            'validation' => '',
                            'source'     => 'hook module',
                            'dependancies' => '',
                            'requiresmodule' => 'categories',
                            'aliases' => '',
                            'args'           => serialize($args),
                            // ...
                           );
        return $baseInfo;
     }
}


?>
