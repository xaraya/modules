<?php

/**
 * Get a list of articles that are related to any combination of author(s), magazine, series or issue.
 *
 * @deprec Now moved to a more generic 'relatedarticles'
 * @param auids array List of integer author IDs
 * @param mid integer Magazine ID
 * @param iid integer Issue ID
 * @param sid integer Series ID
 * @param article_status array List of article statuses; default PUBLISHED
 * @param issue_status array List of issue statuses; default PUBLISHED
 * @param mag_status array List of magazine statuses; default ACTIVE
 * @param status_group string PUBLISHED or DRAFT; sets statuses at all levels appropriately
 * @param docount boolean If set, specifies that a count should be returned instead.
 * @param sort string The sort criteria, passed directly on to the getarticles API.
 *
 */

function mag_userapi_authorarticles($args)
{
    return xarModAPIfunc('mag', 'user', 'relatedarticles', $args);
}

?>