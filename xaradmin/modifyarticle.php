<?php

/**
 * Modify or create an article.
 *
 * @todo Add privilege checks.
 */

function mag_admin_modifyarticle($args)
{
    extract(args);

    // Get module parameters
    extract(xarModAPIfunc('mag', 'user', 'params',
        array(
            'knames' => 'module,$modid,itemtype_articles'
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
            // TODO: Check we can edit articles for this magazine.
            // We can only get the magazine ID through the issue number.
            
            $iid = $object->properties['issue_id']->getValue();
            $issues = xarModAPIfunc($module, 'user', 'getissues', array('iid' => $iid));

            if (!empty($issues) && count($issues) == 1) {
                $issue = reset($issues);
                $mid = $issue['mag_id'];

                // Cache the mid so we can use it to restrict drop-downs.
                // (CHECKME: Possibly do the same for the issue, so images can be selected in context)
                xarVarSetCached($module, 'mid', $mid);

                // TODO: security check here on the magazine id
                if (false) {
                    $message = xarML('No privileges to edit this article');
                    $action = 'display';
                }
            }
        }
    } else {
        // Get 'new' object.

        // Get the object, without an item id.
        // TODO: the user must have chosen a magazine before a new article can be created.
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
            $return_url = xarModURL($module, 'admin', 'view', array('mid' => $mid, 'iid' => $iid));
        } else {
            $return_url = xarModURL($module, 'admin', 'view', array('mid' => $mid, 'iid' => $iid), true, 'mag-article-' . $aid);
        }
    }

    if ($action == 'return' && !empty($return_url) && empty($message)) {
        xarResponseRedirect($return_url);
    }

    if (!empty($message)) $return['message'] = $message;
    $return['mid'] = $mid;
    $return['aid'] = $aid;
    $return['return_url'] = $return_url;


    //var_dump($return);
    return $return;
}

?>