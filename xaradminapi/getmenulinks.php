<?php
/**
 * getmenulinks
 * @package modules
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com

 * @subpackage CKEditor Module
 * @link http://www.xaraya.com/index.php/release/eid/1166
 * @author Marc Lutolf <mfl@netspan.ch> and Ryan Walker <ryan@webcommunicate.net>
 */

function ckeditor_adminapi_getmenulinks()
{
    $menulinks = [];

    if (xarSecurity::check('AdminCKEditor', 0)) {
        $menulinks[] = ['url'   => xarController::URL(
            'ckeditor',
            'admin',
            'modifyconfig'
        ),
                              'title' => xarML('Modify Configuration'),
                              'label' => xarML('Modify Configuration'), ];
    }

    if (xarSecurity::check('AdminCKEditor', 0)) {
        $menulinks[] = ['url'   => xarController::URL(
            'ckeditor',
            'admin',
            'overview'
        ),
                              'title' => xarML('Module Overview'),
                              'label' => xarML('Overview'),
                              'active' => ['main'], ];
    }

    return $menulinks;
}
