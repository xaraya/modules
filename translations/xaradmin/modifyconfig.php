<?php

/**
 * File: $Id$
 *
 * Short description of purpose of file
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage translations
 * @author Marcel van der Boom <marcel@xaraya.com>
*/


function translations_admin_modifyconfig()
{

    $data['translationsBackend'] = xarConfigGetVar('Site.MLS.TranslationsBackend');
    $data['releaseBackend'] = xarModGetVar('translations', 'release_backend_type');
    $data['showcontext'] = xarModGetVar('translations', 'showcontext');

    $data['authid'] = xarSecGenAuthKey();
    return $data;

}

?>