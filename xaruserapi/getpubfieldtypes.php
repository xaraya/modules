<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
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
    return [
        'title'    => 'string',
        'summary'  => 'text',
        'notes'    => 'text',
        'body'     => 'text',
        'owner' => 'integer',
        'pubdate'  => 'integer',
        'state'   => 'integer',
    ];
}
