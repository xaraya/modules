<?php
/*
 * File: $Id: $
 *
 * Newsletter 
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003-2004 by the Xaraya Development Team
 * @link http://www.xaraya.com
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
*/


/**
 * Get the contents of a single publication
 * 
 * @private
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['issueId'] issue id
 * @returns array
 * @return $publication
 */
function newsletter_userapi_getpublicationfordisplay($args)
{
    // Extract args
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($publicationId) || !is_numeric($publicationId)) {
        $invalid[] = 'publication id';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'userapi', 'getpublicationfordisplay', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // initialize array
    $publication = array();

    // Get the publication
    $publication = xarModAPIFunc('newsletter',
                                 'user',
                                 'getpublication',
                                 array('id' => $publicationId));

    // Check for exceptions
    if (!isset($publication) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    // Get disclaimer
    $publication['disclaimer'] = "";
    if ($publication['disclaimerId'] != 0) {
        $disclaimer = xarModAPIFunc('newsletter',
                                    'user',
                                    'getdisclaimer',
                                    array('id' => $publication['disclaimerId']));

        if ($disclaimer) {
            $publication['disclaimer'] = $disclaimer['disclaimer'];
        }
    }

    // Check that we have a real category id
    if ($publication['cid'] != 0) {

        // Get category
        $category = xarModAPIFunc('categories',
                                  'user',
                                  'getcatinfo', // may need to change to getcat
                                  Array('cid' => $publication['cid']));
                                        //'return_itself' => true,
                                        //'getparents' => false,
                                        //'getchildren' => false));

        // Check for exceptions
        if (!isset($category) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
            return; // throw back

        // Set category name
        $publication['categoryName'] = $category['name'];
    }

    // Don't do this - logo should be "http://my.site.com/logo.gif"
    //if (!empty($publication['logo'])) {
    //    $sourceFileName = xarModAPIFunc('newsletter',
    //                             'user',
    //                             'gettemplatefile',
    //                             array('filename' => $publication['logo']));
    //    if (!$logoFile)
    //        $publication['logo'] = '';
    //    else
    //       $publication['logo'] = $logoFile;
    //}

    return $publication;
}

?>
