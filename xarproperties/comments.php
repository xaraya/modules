<?php
/**
 * Comments module - Allows users to post comments on items
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Comments Module
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
/**
 * handle static text property
 *
 * @package dynamicdata
 *
 */
sys::import('modules.dynamicdata.class.properties.base');

class CommentsProperty extends DataProperty
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
                            'id'         => 103,
                            'name'       => 'comments',
                            'label'      => 'Comments',
                            'format'     => '103',
                            'validation' => '',
                            'source'     => 'hook module',
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
