<?php

/**
 * Modify the list of authors for an article.
 *
 */

function mag_admin_modifyartauthors($args)
{
    extract($args);

    // Get module parameters
    extract(xarModAPIfunc('mag', 'user', 'params',
        array(
            'knames' => 'module,$modid,itemtype_articles_authors'
        )
    ));

    $return = array();

    // The return URL
    xarVarFetch('return_url', 'str', $return_url, '', XARVAR_NOT_REQUIRED);

    // The article ID is mandatory.
    xarVarFetch('aid', 'id', $aid, 0, XARVAR_NOT_REQUIRED);

    if (!empty($aid)) {
        $articles = xarModAPIfunc($module, 'user', 'getarticles', array('aid' => $aid));
        if (!empty($articles) && count($articles) == 1) {
            $article = reset($articles);
            $iid = $article['issue_id'];
            $return['article'] = $article;
            $return['aid'] = $aid;

            if (!empty($iid)) {
                $issues = xarModAPIfunc($module, 'user', 'getissues', array('iid' => $iid));
                if (!empty($issues) && count($issues) == 1) {
                    $issue = reset($issues);
                    $mid = $issue['mag_id'];
                    $return['issue'] = $issue;
                    $return['iid'] = $iid;

                    // Cache the issue ID for use in the DD drop-down lists.
                    xarVarSetCached('mag', 'iid', $iid);

                    // Do a security check to ensure we have privileges
                    // for altering authors on articles in this magazine.
                    if (xarSecurityCheck('EditMag', 0, 'Mag', (string)$mid)) {
                        // Get the magazine details.
                        $mags = xarModAPIfunc($module, 'user', 'getmags', array('mid' => $mid));
                        if (!empty($mags) && count($mags) == 1) {
                            $mag = reset($mags);
                            $return['mag'] = $mag;
                            $return['mid'] = $mid;

                            // Get the current list of authors for this article
                            $authors = xarModAPIfunc(
                                $module, 'user', 'getauthors',
                                array('aid' => $aid, 'status_group' => 'DRAFT')
                            );
                            $return['authors'] = $authors;

                            // For each author, get their role information on the article.
                            $roles = xarModAPIfunc($module, 'user', 'getauthorroles', array('aid' => $aid));

                            // Now, we want to be able to handle any form submissions.
                            // Actions are:
                            // - present a new record to add (action=new) [done]
                            // - delete an existing record (action=delete, aaid set) [done]
                            // - present an existing record to update (action=modify, aaid set) [done]
                            // - handle submission of a new record (action=create, aaid not set) [done]
                            // - handle submission of an existing record (action=update and aaid set)

                            xarVarFetch('aaid', 'id', $aaid, 0, XARVAR_NOT_REQUIRED);
                            xarVarFetch('action', 'enum:show:new:create:modify:update:delete', $action, 'show', XARVAR_NOT_REQUIRED);
                            //echo "action=$action";

                            // Get the object - either with or without an aaid.
                            if (!empty($aaid) && $action != 'new' && $action != 'create') {
                                $object = xarModAPIFunc(
                                    'dynamicdata', 'user', 'getobject',
                                    array('modid' => $modid, 'itemid' => $aaid, 'itemtype' => $itemtype_articles_authors)
                                );
                            } else {
                                $object = xarModAPIFunc(
                                    'dynamicdata', 'user', 'getobject',
                                    array('modid' => $modid, 'itemtype' => $itemtype_articles_authors)
                                );
                            }

                            if ($action == 'delete' && !empty($aaid)) {
                                // Make sure it is in our list.
                                if (isset($roles[$aaid])) {
                                    // Delete the item.
                                    if ($object->deleteItem()) {
                                        unset($roles[$aaid]);
                                        $message = xarML('Author removed from article');
                                    }
                                }
                            }

                            if ($action == 'new') {
                                // Present a blank form, with some defaults filled in.
                                // Just the article ID is important; any author can be selected.
                                // We could lock the drop-down down to the individual article, but
                                // we will leave it open for flexibility, i.e. so the editor does not
                                // have to go in and out of article summaries to add a whole
                                // load of authors at once.
                                $object->properties['article_id']->setValue($aid);
                                $return['properties'] = $object->properties;
                                $return['action'] = 'create';
                                // TODO: set a cached value on the article ID to force the article property. Same for the 'create' action.
                            }

                            if ($action == 'create') {
                                // Read the form data.
                                $isvalid = $object->checkInput();

                                if ($isvalid) {
                                    $id = $object->createItem();

                                    if (!empty($id)) {
                                        // Successfuly created.
                                        // Do a redirect to refresh the view (go back to the article that has
                                        // just been added, not necessarily the list we came into).
                                        $new_aid = $object->properties['article_id']->getValue();
                                        if (empty($new_aid)) $new_aid = $aid;

                                        xarResponseRedirect(
                                            xarModURL(
                                                $module,'admin','modifyartauthors',
                                                array('return_url' => $return_url, 'aid' => $new_aid)
                                            )
                                         );
                                        return true;
                                    } else {
                                        // Error in creation.
                                        // Give the user another chance.
                                        $message = xarML('Error creating author link');
                                        $return['properties'] = $object->properties;
                                        $return['action'] = 'create';
                                    }
                                } else {
                                    $message = xarML('Form validation error');
                                    $return['properties'] = $object->properties;
                                    $return['action'] = 'create';
                                }
                            }

                            if ($action == 'modify') {
                                // Make sure the aaid is in our list.
                                if (isset($roles[$aaid])) {
                                    // Get the item
                                    $object->getItem();

                                    // Set the item to the template
                                    $return['properties'] = $object->properties;
                                    $return['action'] = 'update';
                                    $return['aaid'] = $aaid;

                                    // Lock the article down.
                                    xarVarSetCached('mag', 'aid', $aid);
                                }
                            }

                            if ($action == 'update') {
                                // Handle the update form submission.
                                // Read the form data.
                                $isvalid = $object->checkInput();

                                if ($isvalid) {
                                    $id = $object->updateItem();

                                    if (!empty($id)) {
                                        // Successfuly updated.
                                        // Do a redirect to refresh the view
                                        xarResponseRedirect(
                                            xarModURL(
                                                $module,'admin','modifyartauthors',
                                                array('return_url' => $return_url, 'aid' => $aid)
                                            )
                                         );
                                        return true;
                                    } else {
                                        // Error in update.
                                        // Give the user another chance.
                                        $message = xarML('Error updating author link');
                                        $return['properties'] = $object->properties;
                                        $return['action'] = 'update';
                                    }
                                } else {
                                    $message = xarML('Form validation error');
                                    $return['properties'] = $object->properties;
                                    $return['action'] = 'update';

                                    // Lock the article down.
                                    xarVarSetCached('mag', 'aid', $aid);
                                }
                            }

                            $return['roles'] = $roles;
                        }
                    }
                }
            }
        }
    }

    // If there is no return URL, then default it to the issue
    // summary page.
    if (empty($return_url)) {
        // TODO: finish this off, returning to different levels, depending on what data is available.
        $default_url = array();
        if (!empty($mid)) $default_url['mid'] = $mid;
        if (!empty($aid)) $default_url['aid'] = $aid;
        $return_url = xarModURL($module, 'admin', 'view', $default_url, false);
    }

    if (!empty($message)) $return['message'] = $message;

    $return['return_url'] = $return_url;

    return $return;
}

?>