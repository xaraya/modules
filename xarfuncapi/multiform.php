<?php

/**
 * multiform helper function.
 *
 * Handles:
 * - presenting the current form
 * - handling submission of the current form
 * - errors and validation in the current page
 * - redirection to the next or previous page
 *
 * multiform differs from pageform in one fundamental way: all form submissions
 * are sent back into the current page. There are no split form/action pages.
 * The page template is not given lists of pages the user can jump to; instead
 * a request is posted back to the current page, and rules from there determine
 * where the user is taken next.
 *
 * TODO: a timeout if the sequence is not touch within a certain period (a timer set in the session)
 * TODO: secure pages (can this be done by chaining functions?)
 */

function xarpages_funcapi_multiform($args)
{
    // Get the master page for the current page.
    // TODO: without a master page, things get very difficult, so raise an error.
    $master_page = xarModAPIfunc('xarpages', 'multiform', 'getmasterpage', $args);

    // Find the entry point page. The entry point will be the first ACTIVE descendant
    // of the master page.
    $child_keys = $master_page['child_keys'];
    $entry_page_pid = $master_page['first_pid'];
    $page_sequence = $master_page['page_sequence'];
    $session_vars = xarModAPIfunc('xarpages', 'multiform', 'sessionvar');
    $current_page = $args['current_page'];
    // The name of the key that follows a session (through every URL and form POST).
    $multiform_key_name = 'mk';

    // Find the next and previous pages in the sequence.

    // This is the default next page, but the current page processing function could
    // still send us off in another direction - i.e. a branch.
    $current_index = array_search($current_page['pid'], $page_sequence);
    if (($current_index+1) < count($page_sequence)) {
        // Next page in the sequence.
        $next_page_pid = $page_sequence[$current_index+1];
    } else {
        // We are already on the last page.
        $next_page_pid = 0;
    }

    // The previous page is actually the previous page in the *history* not
    // in the linear sequence (because the linear sequence may not actually be followed).
    // There will be a back-page if we are not at the first page, and the previous page
    // has the 'revisit' flag set (allowing a revisit).
    $history = $session_vars['history'];
    $prev_page_pid = 0;
    if (count($history) > 1) {
        end($history);
        while (key($history)) {
            if (key($history) == $current_page['pid']) {
                // Jump back one more page
                prev($history);
                $hist_prev_page = current($history);

                // If the revisit flag is set, then we can go back there.
                if (!empty($hist_prev_page) && !empty($hist_prev_page['revisit'])) $prev_page_pid = $hist_prev_page['pid'];
                break;
            }

            prev($history);
        }
    }

    // Some helper variables and decoded options for the page.
    $current_page = $args['current_page'];
    $dd = $current_page['dd'];

    // List of required fields
    if (!empty($dd['required_fields'])) {
        xarVarValidate('strlist:,; :pre:trim:ftoken', $dd['required_fields']);
        $required_fields = explode(',', $dd['required_fields']);
    }

    // The whole sequence can be cancelled at any time.
    // The parameter 'multiform_cancel' will trigger this.
    // TODO: For now a cancel will take us back to the start of the sequence, but using
    // a config option in the master page, it could take us anywhere.
    xarVarFetch('multiform_cancel', 'str::100', $multiform_cancel, '', XARVAR_NOT_REQUIRED);
    if (!empty($multiform_cancel)) {
        // Clear the session - removes any session data we have collected so far.
        xarModAPIfunc('xarpages', 'multiform', 'sessionkey', array('reset' => true));
        $session_vars = xarModAPIfunc('xarpages', 'multiform', 'sessionvar');

        // TODO: Allow the redirect to go to any other page or URL we like.
        $redirect_pid = $entry_page_pid;
    }


    // There may not be a form object, in which case the page just looks like a html page with
    // submit buttons. i.e. not every page has to have a form.
    // Get the current page object ID (the form object), if there is one.
    // Note that the formobject will remain not set if there is not one selected for this page.
    if (!empty($current_page['dd']['formobject'])) {
        $formobjectid = $current_page['dd']['formobject'];

        // TODO: handle the error if there is no associated object
        // Get the form object for this page
        $formobject = xarModApiFunc(
            'dynamicdata', 'user', 'getobject',
            array('objectid' => $formobjectid)
        );
    } else {
        $formobject = NULL;
    }


    // Decide on the action and the result-action.
    //
    // Actions (what we do when coming into the page) are:
    // - validate form
    // - process form (only if validation successful)
    //
    // Result actions (how we hand control back to the user) are:
    // - present new form for data entry
    // - present existing form for amending
    // - represent form with error messages
    // - jump to another page (whether success or error page, and whether the next logical page, or a branch)

    // We recognise a form submission by a hidden key.
    // The key is random, and lasts for the length of the multiform session.
    // It is also used as validation, to prevent a user from jumping 
    // straight into the middle of a form sequence.
    xarVarFetch($multiform_key_name, 'str::100', $multiform_key, '', XARVAR_NOT_REQUIRED);

    if (!empty($multiform_key)) {
        // The user is submitting a form, or has already submitted at least one form.
        // Check whether the key is valid.

        // Get the current session key.
        $session_key = xarModAPIfunc('xarpages', 'multiform', 'sessionkey');

        // Compare the stored session key against the one submitted.
        if (empty($session_key) || $session_key != $multiform_key) {
            // There is no session key yet or they do not match.
            // Something has gone wrong, since it should
            // have been allocated when the first form was presented (and *only* when
            // the first form was presented, so people can't jump in halfway through).

            // Clear out anything in the current session...
            $session_key = xarModAPIfunc('xarpages', 'multiform', 'sessionkey', array('reset' => true));
            $session_vars = xarModAPIfunc('xarpages', 'multiform', 'sessionvar');

            // ...then jump to the first page in the sequence (i.e. the first page
            // owned by the master page, the entry page).
            $redirect_pid = $entry_page_pid;
        } else {
            // The session key matches. We can now process the submitted form.
            // First, there may not be a form object to handle on this page, though
            // there may still be a validate and process function to run.

            // The validate and process class will be returned by the custom class function
            // of the same name as the master page. If functions are to be shared between
            // multiform sequences, then the shared functionality should be put into APIs.
            // Individual page validation and processing functionality is implemented through
            // methods of the class.
            // Things the validation and processing could ask to be done:
            // - represent the form with errors displayed
            // - jump to another page (perhaps by name)
            // - go to the next page

            // The form can be submitted with and of a number of different buttons. It does not
            // matter what the label (the value) of the buttons are, but the names have specific
            // meanings, as follows:
            // - multiform_submit_next: submit and go to the next page
            // - multiform_submit_prev: submit and go to the previous page
            // - multiform_submit_cont: submit and save the form details, but don't go anywhere yet
            //   (this last one also bypasses any processing steps - it just does the validation)
            // If none of these are present, then assume we have just jumped direct to this page.
            // That is allowed if the page is one we have already visited *and* we are showing
            // the history of pages visited.

            // Check which button has been used to submit.
            $user_action_requested = 'none';
            foreach(array('next', 'prev', 'cont') as $check_submit_button) {
                unset($submit_button_value);
                xarVarFetch('multiform_submit_' . $check_submit_button, 'str', $submit_button_value, '', XARVAR_NOT_REQUIRED);
                if (!empty($submit_button_value)) $user_action_requested = $check_submit_button;
            }

            // If we have a session key, then the session should have a record of the pages we are
            // allowed to visit (in the history array). Check that list now.
            $history = $session_vars['history'];

            if (!isset($history[$current_page['pid']]) || (isset($history[$current_page['pid']]['revisit']) && !$history[$current_page['pid']]['revisit'])) {
                // We should not be on this page. It is likely that the user clicked their browser back-button.
                $last_history_page = end($history);
                if (!empty($last_history_page['revisit'])) {
                    // If there is a valid sequence in operation, we just jump to the last
                    // page in the history, so the user does not lose anything. They may have just been
                    // trying to use the browser forward/back-buttons, which should not be a punishable offence.
                    $redirect_pid = $last_history_page['pid'];
                } else {
                    // TODO: Clear the session and raise an error (redirect to the error page).
                    // (for now, just clear the session and jump to the start of the sequence)
                    $session_key = xarModAPIfunc('xarpages', 'multiform', 'sessionkey', array('reset' => true));
                    $session_vars = xarModAPIfunc('xarpages', 'multiform', 'sessionvar');
                    $redirect_pid = $entry_page_pid;
                }
            } elseif ($user_action_requested == 'none') {
                // The user is not submitting anything, but just jumping to this page
                // (e.g. after an automatic redirect from another page, or directly through
                // the history crumbtrail, or even after a redirect having submitted 'back'
                // in the prior page).

                // Check they are allowed to do this.
                // We are allowed to jump back to any page in the history that has
                // its 'revisit' flag set.
                // TODO: Also need to allow a jump to a new page not in the history (i.e. after a
                // redirect from the previous page). We need a simple entry to allow that.

                // If we are not on the last page of the history, then truncate anything that follows us.
                // The form data for those visited pages is retained, but the user has to go through
                // the proper sequence again.
                foreach(array_reverse($history, true) as $key => $value) {
                    if ($key == $current_page['pid']) break;
                    array_pop($history);
                }
                if (count($history) != count($session_vars['history'])) $session_vars['history'] = $history;

                if (!empty($formobject)) {
                    // If there is data in the session 'formdata' array, then pre-populate the form object.
                    foreach($formobject->properties as $name => $property) {
                        if (isset($session_vars['formdata'][$name])) $formobject->properties[$name]->setValue($session_vars['formdata'][$name]);
                    }

                    // Now call the 'initialise' function for this page, if there is one
                    // and allow it to further initialise the form data.
                    $init_object = xarModAPIfunc(
                        'xarpages', 'multiform', 'getvalobject',
                        array(
                            'name' => $master_page['name'],
                            'formobject' => $formobject,
                            'workdata' => $session_vars['workdata'],
                            'formdata' => $session_vars['formdata'],
                        )
                    );
                    if (!empty($init_object)) {
                        // We have a validation object.
                        // If we have an initialisation method for this page, then we can use it.
                        $custom_init_method = 'initialise_' . $current_page['name'];
                        if (method_exists($init_object, $custom_init_method)) {
                            // Call the validation method. We don't care about return values.
                            $init_result = call_user_func(array(&$init_object, $custom_init_method), array());

                            // Compact the form object within the validation object (put the arrays back
                            // onto the form object).
                            if ($init_result) $formobject = $init_object->compact_formobject();
                        }

                        // We have finished with the validation object now.
                        unset($init_object);
                    }
                }
            } else {
                // The user is submitting actual form data.
                if (!empty($formobject)) {
                    // There is form data to handle.
                    $form_isvalid = $formobject->checkInput();

                    // Check required fields have been set.
                    if (!empty($required_fields)) {
                        foreach($required_fields as $required_field) {
                            if (!empty($formobject->properties[$required_field])) {
                                $property_value = $formobject->properties[$required_field]->getValue();
                                if (!isset($property_value) || $property_value === '') {
                                    $formobject->properties[$required_field]->invalid = xarML('Required field');
                                    if ($form_isvalid) $form_isvalid = false;
                                }
                            }
                        }
                    }

                    // Do any custom validation for this form.
                    // We need to pass the object enough data for it to work with.
                    // The 'workdata' is available for validation, but we don't allow the
                    // validation functions to change it.
                    //$session_vars = xarModAPIfunc('xarpages', 'multiform', 'sessionvar');
                    $validation_object = xarModAPIfunc(
                        'xarpages', 'multiform', 'getvalobject',
                        array(
                            'name' => $master_page['name'],
                            'formobject' => $formobject,
                            'workdata' => $session_vars['workdata'],
                            'formdata' => $session_vars['formdata'],
                        )
                    );
                    if (!empty($validation_object)) {
                        // We have a validation object.
                        // If we have a validation method for this page, then we can use it.
                        $custom_validate_method = 'validate_' . $current_page['name'];
                        if (method_exists($validation_object, $custom_validate_method)) {
                            // Call the validation method.
                            $custom_isvalid = call_user_func(array(&$validation_object, $custom_validate_method), array());

                            // Compact the form object within the validation object (put the arrays back
                            // onto the form object).
                            // We do this regardless of whether 
                            $formobject = $validation_object->compact_formobject();

                            // If we have raised an explict fail here, or there are any invalid messages,
                            // then set the complete form to 'invalid'.
                            if (empty($custom_isvalid) || !empty($validation_object->invalids)) $form_isvalid = false;
                        }

                        // We have finished with the validation object now.
                        unset($validation_object);
                    }
                } else {
                    // There is no form data to handle.
                    $form_isvalid = true;
                }

                if ($form_isvalid && $user_action_requested == 'next') {
                    // The form is valid, and we are moving on, so we can do some custom processing.
                    // We only do the processing on submission of 'next', so processing happens only in a forward direction.

                    $processing_object = xarModAPIfunc(
                        'xarpages', 'multiform', 'getvalobject',
                        array(
                            'name' => $master_page['name'],
                            'formobject' => $formobject,
                            'workdata' => $session_vars['workdata'],
                            'formdata' => $session_vars['formdata'],
                        )
                    );

                    if (!empty($processing_object)) {
                        // We have a processing object.
                        // If we have a processing method for this page, then we can use it.
                        // TODO: allow the page to override this process name.
                        $custom_process_method = 'process_' . $current_page['name'];
                        if (method_exists($processing_object, $custom_process_method)) {
                            // Call the processing method.
                            $process_success = call_user_func(array(&$processing_object, $custom_process_method), array());

                            // Compact the form object within the validation object (put the arrays back
                            // onto the form object).
                            // We do this regardless of whether 
                            $formobject = $processing_object->compact_formobject();

                            // Write any work data back to the session.
                            if (!empty($processing_object->workdata)) {
                                $session_vars['workdata'] = $processing_object->workdata;
                            }

                            // If we have raised an explict fail here, then set the complete form to 'invalid'.
                            if (empty($process_success)) {
                                $form_isvalid = false;

                                // TODO: if this happens to be a milestone page, then we should stop
                                // processing immediately with an 'unexpected error'. We do not want
                                // the user being able to process this page again, so we will not be
                                // presenting the form again for amendment.
                            }

                            // Handle a few settings that the object may pass back.

                            // The processing function may have set the next page ID.
                            if (!empty($processing_object->next_page_pid)) $next_page_pid = $processing_object->next_page_pid;

                            // The processing function may have set a redirect url.
                            if (!empty($processing_object->redirect_url)) $redirect_url = $processing_object->redirect_url;

                            // The processing function may have set the 'last page' flag.
                            // This indicates the session should be cleared before going to the last page.
                            if (!empty($processing_object->last_page)) $last_page_flag = true;
                        }

                        // We have finished with the processing object now.
                        unset($processing_object);
                    }
                }

                // Store the current form values in the session data array.
                if (!empty($formobject)) {
                    foreach($formobject->properties as $name => $property) {
                        $session_vars['formdata'][$name] = $property->getValue();
                    }
                }

                // If there are any errors in the current form, then do not allow 'next'.

                if ($form_isvalid && $user_action_requested == 'next') {
                    // Add (or update) this page to the history array.
                    $history = array(
                        'pid' => $current_page['pid'],
                        'name' => $current_page['name'],
                        'isvalid' => $form_isvalid,
                        'revisit' => true,
                    );

                    if (!empty($current_page['dd']['milestone_page'])) {
                        // This is a milestone page, which means we can never come back to it.
                        // It is important to ensure the last page in a sequence is not a milestone
                        // page otherwise the user will be sent back to the beginning again when it
                        // is submitted.
                        $history['revisit'] = false;
                    }

                    // Only set the milestone flag in the history if we are going to 'next',
                    // so force it to 'true' if we are *not* jumping to the 'next' page.
                    if ($user_action_requested != 'next') $history['revisit'] = true;

                    $session_vars['history'][$current_page['pid']] = $history;

                    // If current page does not allow a revisit, then ensure this gets applied to
                    // all previous pages too.
                    if (!$history['revisit']) {
                        foreach($session_vars['history'] as $pid => $page) {
                            if ($pid == $current_page['pid']) break;
                            $session_vars['history'][$pid]['revisit'] = false;
                        }
                    }
                }

                // Allow a jump to the next or previous pages, depending on what button the user pressed.
                if ($form_isvalid) {
                    if ($user_action_requested == 'next' && !empty($next_page_pid)) $redirect_pid = $next_page_pid;
                    if ($user_action_requested == 'prev' && !empty($prev_page_pid)) $redirect_pid = $prev_page_pid;
                }
            }
        }
    } elseif (empty($multiform_cancel)) {
        // No session key submitted. We would only expect that on the first page
        // the user enters. Make sure we are on the first page in the sequence then
        // set up a session.
        // The corrolary of this is that every page *must* have a valid session_key submitted,
        // otherwise the sequence will be halted and sent back to the start. That also
        // means that every page must contain a POST or GET session key; it is not sufficient
        // to just provide a link (e.g. 'skip') to the next page.

        $session_key = xarModAPIfunc('xarpages', 'multiform', 'sessionkey', array('set' => true));
        $session_vars = xarModAPIfunc('xarpages', 'multiform', 'sessionvar');
        $redirect_pid = $entry_page_pid;
    }


    // If redirect data has been set up, then deal with that.
    if (!empty($redirect_pid) && empty($redirect_url)) {
        // A page ID has been set somewhere above.
        // We must include the session key if it is set, otherwise the page
        // at the other end will fail its session check.
        $redirect_args = array('pid' => $redirect_pid);
        if (!empty($session_key)) $redirect_args[$multiform_key_name] = $session_key;

        $redirect_url = xarModURL('xarpages', 'user', 'display', $redirect_args, false);

        // Put an entry onto the end of the history, so we are allowed to come into that page.
        // This is a simple entry, more a stub, ready to hold the full page details when we get to it.
        if (!isset($session_vars['history'][$redirect_pid])) {
            $session_vars['history'][$redirect_pid] = array('revisit' => true, 'pid' => $redirect_pid);
        }
    }

    // Write the session vars back to the session.
    xarModAPIfunc('xarpages', 'multiform', 'sessionvar', $session_vars);

    if (!empty($redirect_url)) {
        // If the 'last page' flag has been set, then clear out the session as the last
        // thing we do before the final redirect (likely to a non-multiform page).
        // If the use presses the browser 'back' button on the 'thankyou' page, they will
        // end up back at the start of the sequence again.
        if (!empty($last_page_flag)) {
            xarModAPIfunc('xarpages', 'multiform', 'sessionkey', array('reset' => true));
        }

        xarResponseRedirect($redirect_url);
        return true;
    }

    // We are not redirecting, so data can be set up for the template.

    // All form data is placed in the 'multiform' array.
    $multiform = array();

    if (!empty($formobject)) $multiform['formobject'] = $formobject;
    if (!empty($formobject)) $multiform['multiform_key'] = $session_key;
    $multiform['multiform_key_name'] = $multiform_key_name;
    if (!empty($session_vars['history'])) $multiform['history'] = $session_vars['history'];
    if (!empty($session_vars['formdata'])) $multiform['formdata'] = $session_vars['formdata'];
    if (!empty($session_vars['workdata'])) $multiform['workdata'] = $session_vars['workdata'];

    $multiform['prev_page_pid'] = $prev_page_pid;
    $multiform['next_page_pid'] = $next_page_pid;

    $args['multiform'] = $multiform;

    return $args;
}

?>