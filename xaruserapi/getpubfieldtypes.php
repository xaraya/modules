<?php
/**
 * Publications module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Publications Module
 
 * @author mikespub
 */
/**
 * get array of (fixed) field types for publication types
 * @TODO : add dynamic fields here for .81+
 * @return array('title'   => 'string',
                 'summary' => 'text',
                 ...);
 */
function publications_userapi_getpubfieldtypes($args)
{
    return array(
        'title'    => 'string',
        'summary'  => 'text',
        'notes'    => 'text',
        'body'     => 'text',
        'owner' => 'integer',
        'pubdate'  => 'integer',
        'state'   => 'integer',
    );
}

?>
