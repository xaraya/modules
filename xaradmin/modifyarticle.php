<?php

/**
 * Modify or create an article.
 *
 * @todo Add privilege checks.
 * @todo Fix a bug: saving a new article multiple times, results in multiple copies.
 *
 * Privilege check:
 * - if an existing article, then fetch the article, then the issue, and check against the mag ID [done]
 * - if starting a new article, and the mag ID passed in, then check against that mag ID
 * - if starting a new article, and no mag ID passed in, then require the user to select from allowed magazines
 *
 */

function mag_admin_modifyarticle($args)
{
    extract($args);

    // Get module parameters
    extract(xarModAPIfunc('mag', 'user', 'params',
        array(
            'knames' => 'module,modid,itemtype_articles'
        )
    ));

    // Get the article ID.
    // '0' is considered 'new'
    xarVarFetch('aid', 'id', $aid, 0, XARVAR_NOT_REQUIRED);

    // An issue ID may have been passed in
    xarVarFetch('iid', 'id', $iid, 0, XARVAR_NOT_REQUIRED);

    // An issue ID may have been passed in
    xarVarFetch('mid', 'id', $mid, 0, XARVAR_NOT_REQUIRED);

    // Check whether we are submitting to this page.
    // Can submit with 'save' or 'save and return'.
    xarVarFetch('submit_save', 'str', $submit_save, '', XARVAR_NOT_REQUIRED);
    xarVarFetch('submit_return', 'str', $submit_return, '', XARVAR_NOT_REQUIRED);

    // Somewhere to redirect to on success.
    xarVarFetch('return_url', 'str:1:', $return_url, '', XARVAR_NOT_REQUIRED);

    $return = array();

    // Three actions: save, return or just display.
    if (!empty($submit_save)) {
        $action = 'save';
    } elseif (!empty($submit_return)) {
        $action = 'return';
    } else {
        $action = 'display';
    }

    $return['action'] = $action;

    // Regardless of action, we need to get the object, and possibly an item of that object.
    if (!empty($aid)) {
        // Get the object with the supplied item id
        $object = xarModAPIFunc(
            'dynamicdata', 'user', 'getobject',
            array('modid' => $modid, 'itemid' => $aid, 'itemtype' => $itemtype_articles)
        );

        // Get the item data.
        $id = $object->getItem();

        if (empty($id)) {
            // Error fetching the object data.
            $message = xarML('Error fetching article details');
            $action = 'display';
        } else {
            // Check we can edit articles for this magazine.
            // We can only get the magazine ID through the issue number.
            
            $iid = $object->properties['issue_id']->getValue();
            $issues = xarModAPIfunc($module, 'user', 'getissues', array('iid' => $iid));

            if (!empty($issues) && count($issues) == 1) {
                $issue = reset($issues);
                $mid = $issue['mag_id'];

                // Cache the mid so we can use it to restrict drop-downs.
                // (CHECKME: Possibly do the same for the issue, so images can be selected in context)
                xarVarSetCached($module, 'mid', $mid);

                // Security check here on the magazine id
                if (!xarSecurityCheck('EditMag', 0, 'Mag', "$mid")) {
                    $message = xarML('No privileges to edit an article from this magazine');
                    $action = 'display';
                    $object = NULL;
                }
            }
            // FIXME: what happens if the article has no issue? Can we still
            // allow the user to edit it? Can this actually happen?
        }
    } else {
        // Get 'new' object.

        // The user must have chosen a magazine before a new article can be created.
        // Must also have privileges to edit articles in this magazine.
        if (empty($mid)) {
            $message = xarML('Must chose a magazine to add the article to');
            $action = 'display';
            $object = NULL;
        } elseif (!xarSecurityCheck('EditMag', 0, 'Mag', "$mid")) {
            $message = xarML('No privileges to create an article in this magazine');
            $action = 'display';
            $object = NULL;
        } else {
            // Get the object, without an item id.
            $object = xarModAPIFunc(
                'dynamicdata', 'user', 'getobject',
                array('modid' => $modid, 'itemtype' => $itemtype_articles)
            );

            // Cache the mid so we can use it to restrict drop-downs.
            // (CHECKME: Possibly do the same for the issue, so images can be selected in context)
            if (isset($mid)) xarVarSetCached($module, 'mid', $mid);

            if ($action == 'display') {
                // Set some defaults, if passed in.
                // Set the default issue, if we have selected one.
                if (!empty($iid)) $object->properties['issue_id']->setValue($iid);

                // Set today's date as the default publication date.
                $pubdate = $object->properties['pubdate']->getValue();
                if (empty($pubdate)) $object->properties['pubdate']->setValue(time());
            }
        }
    }

    // Form is submitted (either 'save and stay' or 'save and return').
    // Some privilege checks have already been performed above, so if this
    // action is still set, then we are allowed to perform it without further
    // checks.
    if ($action == 'save' || $action == 'return') {
        // Read input and check all is okay.
        // TODO: pass in various arguments so this GUI function can double as an API.
        $isvalid = $object->checkInput();

        // The article issue may have been changed.
        // Get the issue ID to ensure we return to the right place.
        $iid = $object->properties['issue_id']->getValue();

        // TODO: get the issue ID, and the mag ID, and ensure we have privileges to write these changes.
        // ...

        // TODO: set some overrides, such as the 'ref' being a transform of the 'title'.
        $article_ref = $object->properties['ref']->getValue();

        if (empty($article_ref)) {
            $article_ref = str_replace(' ', '_', strtolower(trim($object->properties['title']->getValue())));
            $object->properties['ref']->setValue($article_ref);
        }

        if (!$isvalid) {
            $message = xarML('Error in form data - please check and try again');
            $action = 'display';
        } else {
            // We would like to save.
            if ($aid == 0) {
                // Creating a new article.

                // Update the existing item
                $id = $object->createItem();
            } else {
                // Modifying an existing article.

                // Update the existing item
                $id = $object->updateItem();
            }
        }
    }

    if ($action == 'display' || $action == 'save') {
        // Send the properties to the template.
        // To get here we must have a valid object.
        $return['properties'] = $object->properties;
    }

    // Provide a default return URL.
    if (!isset($iid)) $iid = 0;
    if (empty($return_url)) {
        if (empty($aid)) {
            $return_url = xarModURL($module, 'admin', 'view', array('mid' => $mid, 'iid' => $iid), false);
        } else {
            $return_url = xarModURL($module, 'admin', 'view', array('mid' => $mid, 'iid' => $iid), false, 'mag-article-' . $aid);
        }
    }

    if ($action == 'return' && !empty($return_url) && empty($message)) {
        xarResponseRedirect($return_url);
    }

    if (!empty($message)) $return['message'] = $message;
    $return['mid'] = $mid;
    $return['aid'] = $aid;
    $return['return_url'] = $return_url;

    // If we are displaying the article, and there is
    // no magazine ID, then send a list of magazines to the
    // template for the user to chose from.
    // If the list is empty, then include an error message to
    // that effect (in the template).
    if (empty($mid)) {
        // Get the list of mags the current user is allowed to edit articles on.
        $return['mags'] = xarModAPIfunc($module, 'list', 'mags', array('level' => 'edit'));
    }
    
    return $return;
}

?>