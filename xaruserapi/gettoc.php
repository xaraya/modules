<?php

/**
 * Get a table of contents for a magazine issue.
 * Some inter-page cacheing may be added here to improve performance
 * substantially.
 * Only used for end-user display of TOCs, not for admin pages.
 *
 * @param mag array Magazine record.
 * @param issue array Issue record.
 * @param status_group string PUBLISHED or DRAFT; sets statuses at all levels appropriately
 * @return array Returns arrays for articles, series and the TOC grouping.
 *
 */

function mag_userapi_gettoc($args)
{
    extract($args);
    $return = array();

    // Get module parameters
    extract(xarModAPIfunc('mag', 'user', 'params',
        array(
            'knames' => 'module,sort_default_articles_toc,image_article_main_vpath'
        )
    ));

    // The magazine and issue are mandatory records.
    if (empty($mag) || empty($issue)) return $return;

    // The statuses of various records.
    // Can override these so that, for example, previews of draft
    // issues can be show to administrators.
    if (!empty($status_group)) {
        if ($status_group == 'PUBLISHED') {
            $series_status = array('ACTIVE');
            $article_status = array('PUBLISHED');
        } elseif ($status_group == 'DRAFT') {
            $article_status = array();
            $article_status = array();
        }
    }
    if (!isset($series_status)) $series_status = array('ACTIVE');
    if (!isset($article_status)) $article_status = array('PUBLISHED');

    // TODO: If cacheing is implemented, then this would make a good key.
    $cachekey = $mag['mid'] . '-' . $issue['iid'];

    // Fetch all the articles for this issue.
    $article_select = array(
        'status' => $article_status,
        'mid' => $mag['mid'],
        'iid' => $issue['iid'],
        'fieldset' => 'TOC',
        'sort' => 'page ASC', //$sort_default_articles_toc,
    );

    $articles = xarModAPIfunc($module, 'user', 'getarticles', $article_select);

    // Now we do some fancy grouping with the articles.
    // They should already be in page order.
    // We need to group them by series, in the order in which they come, i.e. each
    // article is moved forward to the group with the the first occurance of
    // that series.
    // We also need to discard any articles for non-active series.
    // There may be a bunch of articles not in any series, which we will leave 
    // where they are (set them up with a dummy series, but not group them).

    $groups = array();
    $series_list = array();
    $dummy_series_id = -1;

    if (!empty($articles)) {
        // Loop over articles, which should already be in page number order.
        foreach($articles as $key => $article) {
            // Expand article main image paths.
            // TODO: we actually only want to do this with a thumbnail of the image here;
            // the full image will be seen only when viewing the full article.
            if (isset($article['image1'])) {
                $article[$key]['image1_path'] = xarModAPIfunc(
                    'mag', 'user', 'imagepaths',
                    array(
                        'path' => $image_article_main_vpath,
                        'fields' => array(
                            'mag_ref' => $mag['ref'],
                            'issue_ref' => $issue['ref'],
                            'article_ref' => $article['ref'],
                            'article_id' => $article['aid'],
                            'image1' => $article['image1'],
                        )
                    )
                );
            }
            
            if (!empty($article['series_id'])) {
                if (!in_array($article['series_id'], $series_list)) {
                    // Series not yet encountered before - add it to the group.
                    $series_list[] = $article['series_id'];
                    $groups[$article['series_id']] = array();
                }

                // Add the article ID to the group.
                $groups[$article['series_id']][] = $article['aid'];
            } else {
                // No recognised series for this article, so find another
                // way to put it into the groups (use nagative IDs).
                $groups[$dummy_series_id] = array($article['aid']);
                $dummy_series_id -= 1;
            }

            // The premium flag should fall back to the issue and then the magazine
            // if not set on the article.
            if (empty($article['premium'])) {
                // The premium flag is not set on the article.
                if (!empty($issue['premium'])) {
                    // Fall back to the issue flag.
                    $articles[$key]['premium'] = $issue['premium'];
                } elseif (!empty($mag['premium'])) {
                    // Fall back to the magazine flag.
                    $articles[$key]['premium'] = $mag['premium'];
                } else {
                    // Default to 'OPEN'.
                    $articles[$key]['premium'] = 'OPEN';
                }
            }
        }

        // Now fetch the details of each series we are displaying.
        $series = xarModAPIfunc(
            $module, 'user', 'getseries',
            array('sids' => $series_list, 'status' => $series_status, 'mid' => $mag['mid'])
        );

        // Go through the articles and remove any for series that do not exist
        // (or more accurately, that are not active).
        foreach($articles as $key => $article) {
            if (isset($article['series_id']) && $article['series_id'] > 0 && !isset($series[$article['series_id']])) {
                // Remove articles if the series is disabled, or more accuratly, not available.
                unset($articles[$key]);
                unset($groups[$article['series_id']]);
            }
        }
    }

    // Only set the arrays if data is available.
    if (!empty($articles)) {
        // Return the series and the articles.
        $return['series'] = $series;
        $return['articles'] = $articles;

        // Return the grouping array for the series and articles.
        $return['groups'] = $groups;
    }

    return $return;
}

?>