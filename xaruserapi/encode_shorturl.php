<?php

/**
 * return the path for a short URL to xarModURL for this module
 * @param $args the function and arguments passed to xarModURL
 * @returns string
 * @return path to be added to index.php for a short URL, or empty if failed
 */
function newsgroups_userapi_encode_shorturl($args)
{
    // Get arguments from argument array
    extract($args);

    // check if we have something to work with
    if (!isset($func)) {
        return;
    }

    // make sure you don't pass the following variables as arguments too

    // default path is empty -> no short URL
    $path = '';
    // if we want to add some common arguments as URL parameters below
    $join = '?';
    // we can't rely on xarModGetName() here (yet) !
    $module = 'newsgroups';

    // specify some short URLs relevant to your module
    if ($func == 'main') {
        $path = '/' . $module . '/';
    } elseif ($func == 'group' && !empty($group) && is_string($group)) {
        $path = '/' . $module . '/' . $group . '/';
    } elseif ($func == 'article' && !empty($group) && is_string($group) && !empty($article)) {
        $path = '/' . $module . '/' . $group . '/' . $article;
    } elseif ($func == 'post' && !empty($phase) && !empty($group) && is_string($group)) {
        if ($phase == 'new') {
            $path = '/' . $module . '/' . $group . '/post';
        } elseif ($phase == 'reply' && !empty($article)) {
            $path = '/' . $module . '/' . $group . '/' . $article . '/reply';
        }
    }
    // anything else does not have a short URL equivalent

    // add some other module arguments as standard URL parameters
    if (!empty($path)) {
        // pager
        if (isset($startnum) && $startnum != 1) {
            $path .= $join . 'startnum=' . $startnum;
            $join = '&';
        }
    }

    return $path;
}

?>
