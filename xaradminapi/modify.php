<?php

/**
 * Modify or create an event.
 * Returns the details of an existing event if 'save' is empty.
 *
 * @todo Truncate the seconds from all times.
 * @todo Round times to the nearest quota (or round down)
 * @todo General security checks on new and update
 * @todo Validate date range - ensure end date comes after the start date
 * @todo Accept dates and times in other formats (separate fields) and feed them into the DD properties
 *       (they can be overridden in the display template)
 * @todo Put in checks for categories hooked to events (don't assume they are hooked)
 * @todo It is important to ensure the 'html' fields are encoded as HTML when saving, otherwise going back
 *       to edit with TinyMCE will be a problem, since TinyMCE cannot take text as its input without
 *       treating it as a single paragraph of unformatted text.
 */

function ievents_adminapi_modify($args)
{
    extract($args);

    // If 'save' is not set, then assume we DO want to save.
    // If set to false, then we just return data.
    $save = (!isset($save) || !empty($save) ? true : false);

    if (empty($eid)) $eid = 0;
    if (empty($cid)) $cid = 0;

    // Fetch all the config items we need at once.
    list($module, $modid, $itemtype_events, $itemtype_calendars, $maxcats, $html_fields, $text_fields) =
        xarModAPIfunc('ievents', 'user', 'params', array('names' => 'module,modid,itemtype_events,itemtype_calendars,maxcats,html_fields,text_fields'));

    // Set up initial data for passing to the template.
    $data = array();
    $data['module'] = $module;
    $data['modid'] = $modid;
    $data['itemtype_events'] = $itemtype_events;

    // Assume for now that we have not succeeded.
    // TODO: we have success, all-out failure, and warning (e.g. form items failed)
    $data['result'] = 'FAIL';
    $data['message'] = '';

    // Get some information needed whether we are presenting the form
    // or updating the object.

    // Set properties we don't want the user to update and/or see
    // Only do this if not an administrator
    // Do this whether creating or modifying.
    // We have to leave the 'jid' in otherwise DD has no primary key to use for a new object.
    // If an administrator, then anything goes.
    $props_hidden = array();
    $props_display = array();

    // Hide the eid if adding an event.
    // It will automatically show as display-only when updating.
    if (empty($eid)) $props_hidden[] = 'eid';

    // Some items passed in may need casting to more appropriate types.
    // Cast an array of flags to a string.
    if (isset($args['flags']) && is_array($args['flags'])) $args['flags'] = implode(',', $args['flags']);

    // If no event ID, then this is a new event, otherwise we are modifying an event.
    if (!empty($eid)) {
        // Updating an event.

        // Get the object with the supplied item id
        $object = xarModAPIFunc(
            'dynamicdata', 'user', 'getobject',
            array('modid' => $modid, 'itemid' => $eid, 'itemtype' => $itemtype_events)
        );

        // Get the item data.
        $id = $object->getItem();

        // Make a copy of the object (so we can restore missing items after an update)
        $object_orig = $object;

        // Error if object does not exist.
        if (empty($id)) {
            $data['message'] = xarML('Event #(1) does not exist', $eid);
            return $data;
        }

        // Check this is for a calendar we have access to.
        // The cid of the event will over-ride any cid passed in.
        // Note: this means we can't change the calendar ID once set.
        // *if* this functionality is needed, then check the privileges on both
        // the new and the old cid are high enough to allow event modification.
        $cid = $object->properties['calendar_id']->value;

        // Determine which properties are hidden and/or readonly.

        // Set all audit records to display-only
        $props_display[] = 'created_by';
        $props_display[] = 'created_time';
        $props_display[] = 'updated_by';
        $props_display[] = 'updated_time';

        // Don't show the external reference details unless an editor
        if (!xarSecurityCheck('DeleteIEvent', 0, 'IEvent', $cid . ':' . $eid . ':All')) {
            $props_hidden[] = 'external_source';
            $props_hidden[] = 'external_ref';
        }
    } else {
        // Creating a new event.

        // Get the object, without an item id.
        $object = xarModAPIFunc(
            'dynamicdata', 'user', 'getobject',
            array('modid' => $modid, 'itemtype' => $itemtype_events)
        );

        // Determine which properties are hidden and/or readonly.

        // Don't show the event ID (since we don't have one at this stage)
        $props_hidden[] = 'eid';

        // Update audit properties will always be hidden.
        // TODO: These will be set to the created_* values.
        $props_hidden[] = 'updated_by';
        $props_hidden[] = 'updated_time';

        // Don't show the audit fields unless at least an editor.
        if (!xarSecurityCheck('DeleteIEvent', 0, 'IEvent', 'All:All:All')) {
            $props_hidden[] = 'created_by';
            $props_hidden[] = 'created_time';
        }

        // Set all other audit records to display-only
        $props_display[] = 'created_by';
        $props_display[] = 'created_time';

        // Don't show the external reference details unless an editor
        if (!xarSecurityCheck('DeleteIEvent', 0, 'IEvent', 'All:All:All')) {
            $props_hidden[] = 'external_source';
            $props_hidden[] = 'external_ref';
        }
   }

    // We need an event object at this stage.
    // Return an error if not got one.
    if (empty($object)) {
        $data['message'] = xarML('Event object does not exist - itemtype #(1)', $itemtype_events);
        return $data;
    }

    // Check we are allowed to update events on this calendar at all.
    // If a calendar ID is supplied, then check against that.
    if (!empty($cid)) {
        // Different privileges depending whether we are submitting a new event
        // or updating an existing event.
        if (!empty($eid)) {
            // Updating an event.
            if (!xarSecurityCheck('DeleteIEvent', 0, 'IEvent', $cid . ':' . $eid . ':' . xarUserGetVar('uid'))) {
                $data['message'] = xarML('No permissions to update this event');
                return $data;
            }
        } else {
            // Creating a new event.
            if (!xarSecurityCheck('CommentIEvent', 0, 'IEvent', $cid . ':All:All:' . xarUserGetVar('uid'))) {
                $data['message'] = xarML('No permissions to update this event');
                return $data;
            }
        }
    } else {
        // No calendar ID supplied.
        // Are there any calendars we are able to update?
        $calendars = xarModAPIfunc('ievents', 'user', 'list_calendars');
        if (empty($calendars)) {
            $data['message'] = xarML('There are no calendars you are permitted to submit to');
            return $data;
        }
    }


    // If we are submitting a form.
    if ($save) {
        // Calendar ID supplied for new event.
        if (empty($eid) && !empty($cid)) {
            $object->properties['calendar_id']->value = $cid;
        }

        // TODO: If alternative date parameters have been passed in, then decode them
        // here and set them in the args array.
        // e.g. time in 12-hour clock format, and dates as individual fields.

        // Get the input from the form (or from args input) and check the values.
        $isvalid = $object->checkInput($args);

        // Update the object only if it is valid.
        if (!empty($isvalid)) {
            // At this point we can alter some of the values 
            // before storing (e.g. remove time from dates)

            // Here we should have the calendar ID, whether creating or updating.
            if (empty($cid)) $cid = $object->properties['calendar_id']->getValue();

            // Transform any HTML fields on the way in.
            $html_fields = explode(',', $html_fields);
            foreach($html_fields as $html_field) {
                if (!empty($object->properties[$html_field])) {
                    $object->properties[$html_field]->setValue(
                        xarModAPIfunc('ievents','user','transform',
                            array('html' => $object->properties[$html_field]->GetValue())
                        )
                    );
                }
            }

            // Transform any text fields.
            // TODO: do the same transform in the view page
            $text_fields = explode(',', $text_fields);
            foreach($text_fields as $text_field) {
                if (!empty($object->properties[$text_field])) {
                    $object->properties[$text_field]->setValue(
                        xarModAPIfunc('ievents','user','transform',
                            array('text' => $object->properties[$text_field]->GetValue())
                        )
                    );
                }
            }


            // If we still don't have a calendar ID, then see if the user has a
            // default calendar ID. This will be the case if the user is allowed
            // to update just one calendar.

            // TODO: check this calendar is in the list of calendars allowed.

            if (empty($eid)) {
                // Set some default values.

                // When creating an event, the updated time/by should be the same as a the created time/by.
                $object->properties['created_time']->setValue(time());
                $object->properties['created_by']->setValue(xarUserGetVar('uid'));
                $object->properties['updated_time']->setValue($object->properties['created_time']->getValue());
                $object->properties['updated_by']->setValue($object->properties['created_by']->getValue());

                // Quantise the start and end dates.
                $object->properties['startdate']->setValue(
                    xarModAPIfunc($module, 'user', 'quantise', array('time' => $object->properties['startdate']->getValue()))
                );
                // Undo the timezone correction put in by the Dynamic_Calendar property.
                // We do not want the posted date to be adjusted according to the timezone (or DST) of the posting user.
                // The time and date is assumed to be the time and date as at the location of the website.
                //
                // A more sophisticated approach would be to store the server time *and* the timezone for the event (not
                // the timezone for the server, or the website, or the posting user, but the *event*). That way it would
                // always be possible to display the date corrected for both the end user time and the local event time.
                // e.g. a tele-conference in Germany (GMT+2) could be posted to an English website (GMT+0). A French
                // visitor (GMT+1) would know that the event at 3pm in Germnay, would be 2pm their local time, and it would
                // be posted to the site as "1pm GMT/timezone GMT+2". When the time is POSTED to the web form, it should be
                // interpreted as local to the *event*, and adjusted back to GMT at that point. Anyway, that's for another
                // day when this system goes truly international...
                //$object->properties['startdate']->setValue(
                //    $object->properties['startdate']->getValue() + (xarMLS_userOffset($object->properties['startdate']->getValue()) * 3600)
                //);
                $object->properties['enddate']->setValue(
                    xarModAPIfunc($module, 'user', 'quantise', array('time' => $object->properties['enddate']->getValue()))
                );
                // Undo the local user timezone adjustment.
                //$object->properties['enddate']->setValue(
                //    $object->properties['enddate']->getValue() + xarMLS_userOffset($object->properties['enddate']->getValue()) * 3600
                //);

                // If our maximum privilege is COMMENT then force the status to DRAFT.
                if (!xarSecurityCheck('ModerateIEvent', 0, 'IEvent', $cid . ':All:All')) {
                    // No moderate privilege, so we cannot create anything other than DRAFT events
                    $object->properties['status']->setValue('DRAFT');
                }

                // Create a new event
                $eid = $object->createItem();

                // Handle any create hooks
                // Only appropriate if we have an event ID, i.e. if the create succeeded
                // TODO: handle the catids correctly
                if (!empty($eid)) {
                    xarModCallHooks('item', 'create', $eid, array(
                        'module' => $module,
                        'itemtype' => $itemtype_events,
                        'itemid' => $eid,
                        'cids' => (!empty($args['catids']) ? $args['catids'] : array()),
                    ));

                    $data['message'] = xarML('Event created successfuly');
                    $data['result'] = 'SUCCESS';
                }
            } else {
                // Set some override values.
                $object->properties['updated_time']->setValue(time());
                $object->properties['updated_by']->setValue(xarUserGetVar('uid'));

                // Quantise the start and end dates.
                $object->properties['startdate']->setValue(
                    xarModAPIfunc($module, 'user', 'quantise', array('time' => $object->properties['startdate']->getValue()))
                );
                // Undo the local user timezone adjustment.
                //$object->properties['startdate']->setValue(
                //    $object->properties['startdate']->getValue() + (xarMLS_userOffset($object->properties['startdate']->getValue()) * 3600)
                //);
                $object->properties['enddate']->setValue(
                    xarModAPIfunc($module, 'user', 'quantise', array('time' => $object->properties['enddate']->getValue()))
                );
                // Undo the local user timezone adjustment.
                //$object->properties['enddate']->setValue(
                //    $object->properties['enddate']->getValue() + xarMLS_userOffset($object->properties['enddate']->getValue()) * 3600
                //);

                // If our maximum privilege is COMMENT then force the status to DRAFT.
                if (!xarSecurityCheck('ModerateIEvent', 0, 'IEvent', $cid . ':All:All')) {
                    // No moderate privilege, so we cannot create anything other than DRAFT events
                    $object->properties['status']->setValue('DRAFT');
                }

                // Update the existing event
                $id = $object->updateItem();

                // Handle any update hooks
                // TODO: how to modify and limit the way the categories hooks are displayed
                xarModCallHooks('item', 'update', $eid,
                    array(
                        'module' => $module,
                        'itemtype' => $itemtype_events,
                        'itemid' => $eid,
                        'cids' => (!empty($args['catids']) ? $args['catids'] : array()),
                    )
                );

                $data['message'] = xarML('Event updated successfuly');
                $data['result'] = 'SUCCESS';
            }

            // Check the user has not selected too many categories or selected the base categories.
            if ($data['result'] == 'SUCCESS') {
                xarModAPIfunc('ievents', 'admin', 'limit_categories',
                    array (
                        'module' => $module,
                        'itemtype' => $itemtype_events,
                        'itemid' => $eid,
                        'maxcats' => $maxcats,
                        'nobase' => true,
                    )
                );
            }
        } else {
            // Errors will be displayed along with the original fields.
            $data['message'] = xarML('Invalid form data');
            $data['result'] = 'WARNING';
        }
    } else {
        // Return successful flag, since we are not doing anything that can fail.
        $data['result'] = 'SUCCESS';
    }

    // If creating a new event, load up some default values in the first presentation of the screen
    if (empty($save) && empty($eid)) {
        // Set the calendar if one was passed into this page.
        if (!empty($cid)) {
            $object->properties['calendar_id']->setValue($cid);
        }
        $object->properties['created_time']->setValue(time());
        $object->properties['updated_time']->setValue(time());
        $object->properties['created_by']->setValue(xarUserGetVar('uid'));
        $object->properties['updated_by']->setValue(xarUserGetVar('uid'));

        // TODO: If we don't have edit privileges then limit the status to 'DRAFT'
    }

    // If updating an event, change some of the values before it hits the form.
    if (empty($save) && !empty($eid)) {
    }

    // If our maximum privilege is COMMENT then limit the status options to DRAFT.
    if (!xarSecurityCheck('ModerateIEvent', 0, 'IEvent', (empty($cid) ? 'All' : $cid) . ':All:All')) {
        // No moderate privilege, so we cannot create anything other than DRAFT events
        $object->properties['status']->default = 'DRAFT';

        // Remove all non-draft options from the drop-down
        if (is_array($object->properties['status']->options)) {
            foreach($object->properties['status']->options as $key => $option) {
                if ($option['id'] != 'DRAFT') unset($object->properties['status']->options[$key]);
            }
        }
    }

   
    // Pass special DD property properties into the template,
    // so that properties can be hidden completely or set to display-only.
    $data['properties_extra'] = array();
    foreach($props_hidden as $prop_name) {
        $data['properties_extra'][$prop_name]['hidden'] = true;
    }
    foreach($props_display as $prop_name) {
        $data['properties_extra'][$prop_name]['displayonly'] = true;
    }

    // Return the properties for passing into the template.
    $data['properties'] = $object->properties;
    $data['eid'] = $eid;

    // TODO: just return the same data regardless
    // TODO: make a 'create' API that just extracts the eid to return.
    if ($save === 'API') {
        return array(
            'result' => $data['result'],
            'message' => $data['message'],
            'eid' => $eid,
        );
    } else {
        return $data;
    }
}


?>