<?php
/**
 * getmenulinks
 * @package ckeditor
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com

 * @subpackage CKEditor Module
 * @link http://www.xaraya.com/index.php/release/eid/
 * @author Marc Lutolf <mfl@netspan.ch> and Ryan Walker <ryan@webcommunicate.net>
 */

function ckeditor_userapi_getmenulinks()
{
    $menulinks = array();

    if (xarSecurityCheck('ViewCKEditor',0)) {
        $menulinks[] = array('url'   => xarModURL('ckeditor',
                                                  'user',
                                                  'main'),
                              'title' => xarML(''),
                              'label' => xarML(''));
    }

    return $menulinks;
}

?>