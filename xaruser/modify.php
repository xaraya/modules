<?php

/**
 * Modify or add events.
 */

function ievents_user_modify($args)
{
    // These will tell us whether the form has been submitted.

    // Save and return
    xarVarFetch('submit', 'str', $submit, '', XARVAR_NOT_REQUIRED);

    // Save and stay here
    xarVarFetch('save', 'str', $save, '', XARVAR_NOT_REQUIRED);

    // Save and view the job
    xarVarFetch('submitview', 'str', $submitview, '', XARVAR_NOT_REQUIRED);

    // Save the job as a copy
    xarVarFetch('submitcopy', 'str', $submitcopy, '', XARVAR_NOT_REQUIRED);


    // Check authid if submitting
    if (!empty($submit) || !empty($save) || !empty($submitview) || !empty($submitcopy)) {
        if (!xarSecConfirmAuthKey()) return;
    }

    // Somewhere to redirect to on success.
    xarVarFetch('return_url', 'str:1:', $return_url, '', XARVAR_NOT_REQUIRED);

    // The optional event ID
    xarVarFetch('eid', 'id', $args['eid'], 0, XARVAR_NOT_REQUIRED);

    // If we are saving as a copy, then discard the event ID and make it look
    // like we are doing a 'save and edit'.
    if (!empty($submitcopy)) {
        $args['eid'] = 0;
        $save = 'save';
    }

    // The optional calendar ID.
    // Allows preselection of the calendar ID when creating a new event.
    xarVarFetch('cid', 'id', $args['cid'], 0, XARVAR_NOT_REQUIRED);

    // If NOT submitting changes then signal this to the API.
    // We will then get the event returned without anything being updated
    // in the database.
    if (empty($submit) && empty($save) && empty($submitview)) $args['save'] = false;

    // Call up the main API to do the processing (including update and create hooks,
    // or just to return the current item if not saving).
    $data = xarModAPIfunc('ievents', 'admin', 'modify', $args);

    // Redirect if necessary
    if ($data['result'] == 'SUCCESS' && (!empty($submit) || !empty($submitview))) {
        // Now redirect to where-ever, unless just saving, or there is an error
        // Redirect to overview if no return URL.
        if (!empty($submitview)) {
            xarResponseRedirect(xarModURL('ievents', 'user', 'view', array('eid' => $data['eid'])));
        } elseif (!empty($return_url)) {
            xarResponseRedirect($return_url);
        } else {
            xarResponseRedirect(xarModURL('ievents', 'user', 'view'));
        }
        return true;
    }

    // Some extra data for the form.
    $data['return_url'] = $return_url;

    // Call modify or update hooks preparation, depending on whether this is a new event or not.
    // TODO: do not do this if we do not have access to the calendar.
    if (empty($data['eid'])) {
        $data['hooks'] = xarModCallHooks(
            'item', 'new', '',
            array(
                'module' => $data['module'],
                'itemtype' => $data['itemtype_events'],
                'itemid' => '',
            )
        );
    } else {
        // Now a hack to work around another hack.
        // The modify hook will attempt to read linked categories from submitted
        // page data, and will use that rather than the categories in the database.
        // This hack will force the modify hook to use the database category links
        // by pre-fetching them and passing them into the modify hook.
        // Update: ONLY do this if we are really not previewing. If the submitted form
        // contains any invalid data, then we will automatically go into preview mode,
        // with the submitted data and errors displayed.

        $mod_hook_data = array(
            'module' => $data['module'],
            'itemtype' => $data['itemtype_events'],
            'itemid' => $data['eid'],
        );

        // If the result is not SUCCESS then assume we have been thrown into preview mode.
        if ($data['result'] == 'SUCCESS' && xarModIsHooked('categories', $data['module'], $data['itemtype_events'])) {
            $links = xarModAPIFunc('categories', 'user', 'getlinks',
                array(
                    'iids' => array($data['eid']),
                    'itemtype' => $data['itemtype_events'],
                    'modid' => $data['modid'],
                    'reverse' => 0
                )
            );
            if (!empty($links) && is_array($links) && count($links) > 0) {
                $mod_hook_data['modify_cids'] = array_keys($links);
            } else {
                $mod_hook_data['modify_cids'] = array();
            }
        }

        $data['hooks'] = xarModCallHooks('item', 'modify', $data['eid'], $mod_hook_data);
    }

    return $data;
}

?>