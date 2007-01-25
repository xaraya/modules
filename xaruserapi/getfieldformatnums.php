<?php
/**
 * Articles module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Articles Module
 * @link http://xaraya.com/index.php/release/151.html
 * @author mikespub
 */
/**
 * get array of field formats numbers for publication types
 * @TODO : move this to some common place in Xaraya (base module ?)
 * + replace with dynamic_propertytypes table
 * + extend with other pre-defined formats
 * @return array('static'  => 1,
                 'textbox' => 2,
                 ...);
 */
function articles_userapi_getfieldformatnums($args)
{
    $fieldnames= array(
        'static'          => 1,
        'textbox'         => 2,
        'textarea_small'  => 3,
        'textarea_medium' => 4,
        'textarea_large'  => 5,
        'dropdown'        => 6,
        'username'        => 7,
        'calendar'        => 8,
        'fileupload'      => 9,
        'status'          => 10,
        'url'             => 11,
        'image'           => 12,
        'webpage'         => 13,
        'imagelist'       => 35,
        'language'        => 36,
        'userlist'        => 37,
        'textupload'      => 38,
        'urltitle'        => 41,

    // TODO: add more property types after testing
    //other 'text' DD property types won't give significant performance hits
    );
    // Add  'text' dd properites that are dependent on module availability
    $fielditem=array();

    if (xarModIsAvailable('tinymce')) {
        $fielditems=array('tinymce' => 205);
        $fieldnames=array_merge($fieldnames,$fielditems);
    }


return $fieldnames;

}

?>
