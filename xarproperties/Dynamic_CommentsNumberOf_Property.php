<?php
/**
 * File: $Id$
 *
 * Dynamic Number of Comments Property
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
 * handle static text property
 *
 * @package dynamicdata
 *
 */
include_once "modules/dynamicdata/class/properties.php"; 
class Dynamic_CommentsNumberOf_Property extends Dynamic_Property
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
                            'id'         => 104,
                            'name'       => 'numcomments',
                            'label'      => '# of Comments',
                            'format'     => '104',
                            'validation' => 'comments_userapi_get_count',
                            'source'     => 'user function',
                            'dependancies' => '',
                            'requiresmodule' => 'comments',
                            'aliases' => '',
                            'args'           => serialize($args),
                            // ...
                           );
        return $baseInfo;
     }
}

?>
