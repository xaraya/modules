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
 * Create a new commentary source for a publication if it 
 * does not already exist 
 * 
 * @private
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['publicationId'] publication ID 
 * @param $args['newCommentarySource'] new source of the commentary
 * @returns array
 * @return $issues
 */
function newsletter_adminapi_newcommentarysource($args)
{
    // Extract args
    extract($args);

    // Set initial source
    $commentarySource = '';

    // Argument check
    $invalid = array();
    if (!isset($publicationId)) {
        $invalid[] = 'publication ID';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'adminapi', 'newcommentarysource', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return $commentarySource;
    }

    // Get the list of commentary sources from module var
    $sourceList = xarModGetVar('newsletter', 'commentarysource');
    if (!empty($sourceList)) {
        if (!is_array($sourceList = @unserialize($sourceList))) {
            $sourceList = array();
        }
    } else {
        $sourceList = array();
    }

    // Check if publication is in commentary source array
    $foundSource = false;
    if (isset($sourceList[$publicationId])) {
        // See if source has already been added to array
        foreach ($sourceList[$publicationId] as $pubsource) {
            if ($pubsource['source'] == $newCommentarySource) {
                $foundSource = true;
                break;
            }
        }

        // Did we find a source for this publication?
        if (!$foundSource) {
            $sourceList[$publicationId][] = array('source' => $newCommentarySource);
            // Set commentary source
            $commentarySource = $newCommentarySource;
        }
    } else {
        $sourceList[$publicationId][] = array('source' => $newCommentarySource);
        // Set commentary source
        $commentarySource = $newCommentarySource;
    }

    if (!$foundSource) {
        // Set module var
        $sourceList = serialize($sourceList);
        xarModSetVar('newsletter', 'commentarysource', $sourceList);
    }
    
    return $commentarySource;
}
?>
