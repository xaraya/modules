<?php

/**
 * redirect to a site based on some URL field of the item
 */
function articles_user_redirect($args)
{
    // Get parameters from user
    $aid = xarVarCleanFromInput('aid');

    // Override if needed from argument array
    extract($args);

    if (!isset($aid) || !is_numeric($aid) || $aid < 1) {
        return xarML('Invalid article ID');
    }

    // Load API
    if (!xarModAPILoad('articles', 'user')) return;

    // Get article
    $article = xarModAPIFunc('articles',
                            'user',
                            'get',
                            array('aid' => $aid));

    if (!is_array($article)) {
        return xarML('Failed to retrieve article');
    }

    $ptid = $article['pubtypeid'];

    // Get publication types
    $pubtypes = xarModAPIFunc('articles','user','getpubtypes');

// TODO: improve this e.g. when multiple URL fields are present
    // Find an URL field based on the pubtype configuration
    foreach ($pubtypes[$ptid]['config'] as $field => $value) {
        if (empty($value['label'])) {
            continue;
        }
        if ($value['format'] == 'url' && !empty($article[$field])) {
// TODO: add some verifications here !
            xarResponseRedirect($article[$field]);
            return true;
        }
    }

    return xarML('Unable to find valid redirect field');
}

?>
