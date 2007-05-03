<?php

/**
 * Manage recruiters
 *
 * Security checks:
 * - no access if not logged in
 * - no access if recuiter ID of job not in list available to user
 * - if the user is not an administrator, disable update of ???
 * DONE: limit the number of categories, and the ranges of categories that can be selected
 * @todo move the core of this to API, so calendars can be created and updated via API
 */

function ievents_user_modifycal($args)
{
   extract($args);

   // This will tell us whether the form has been submitted.
   xarVarFetch('submit', 'str', $submit, '', XARVAR_NOT_REQUIRED);
   xarVarFetch('save', 'str', $save, '', XARVAR_NOT_REQUIRED);

   xarVarFetch('return_url', 'str:1:200', $return_url, '', XARVAR_NOT_REQUIRED);

   // The calendar ID
   xarVarFetch('cid', 'id', $cid, 0, XARVAR_NOT_REQUIRED);

   $itemtype_calendar = 2;
   $module = 'ievents';
   $modid = xarModGetIDFromName($module);

   // Set up initial data for passing to the template.
   $data = array();
   $data['itemtype_calendar'] = $itemtype_calendar;
   $data['return_url'] = $return_url;

   // Get some information needed whether we are presenting the form
   // or updating the object.

   // Get the object
   // Need to handle the itemid separately, and validate it
   // before attempting to update it.
   $object = xarModAPIFunc(
      'dynamicdata', 'user', 'getobject',
       array('modid' => $modid, 'itemid' => $cid, 'itemtype' => $itemtype_calendar)
   );

   // TODO: raise a better error, in a standard template page.
   if (empty($object)) return "CALENDAR OBJECT DOES NOT EXIST";

   // If no calendar ID, then this is a new calendar.
   if (!empty($cid)) {
      // Get the item data.
      $id = $object->getItem();

      // Error if object does not exist.
      if (empty($id)) return "CALENDAR DOES NOT EXIST";

      // Check this is for a recruiter we have access to.
      //$recruiter_id = $object->properties['recruiter_id']->value;
   }

   /*
   if (xarModAPIfunc($module, 'user', 'checkprivs', array('rid' => $rid)) < 600) {
      // Current recruiter not in list, or not a recruiter editor.
      return "NO ACCESS TO THIS RECRUITER";
   }
   */

   // Remove properties we don't want the user to update
   // Only do this if not an administrator
/*
   if (xarModAPIfunc('envjobs', 'user', 'checkprivs', array('rid' => $rid)) < 800) {
      $object = xarModAPIfunc(
         'envjobs', 'user', 'hideproperties',
         array(
            'object' => $object,
            'properties' => 'job_icon,micro_icon,button2_icon,advert_icon,'
               . 'join_date,expiry_date,status,featured,options,quota',
         )
      );
   }
*/

   // Pass the properties into the template.
   $data['properties'] = $object->properties;

   if (!empty($submit) || !empty($save)) {
      // Get the input from the form and check the values
      $isvalid = $object->checkInput();

      // Update the object only if it is valid.
      if (!empty($isvalid)) {
         // At this point we can alter some of the values 
         // before storing (e.g. remove time from dates)

         // TODO: if a non-admin submitting a new recruiter, then ensure the status is 'pending'.

         // Truncate the time component from all date-type properties.
/*
         foreach($object->properties as $property_name => $property) {
            if (get_class($property) == 'dynamic_calendar_property') {
               $object->properties[$property_name]->value =
                  mktime(0, 0, 0,
                     date('m', $property->value),
                     date('d', $property->value),
                     date('Y', $property->value));
            }
         }
*/

         if (empty($cid)) {
            // Create a new recruiter
            $cid = $object->createItem();

            // Handle any create hooks
            // Only appropriate if we have a job ID, i.e. if the create succeeded
            if (!empty($cid)) {
               xarModCallHooks('item', 'create', $cid, array(
                  'module' => $module,
                  'itemtype' => $itemtype_calendar,
                  'itemid' => $cid,
               ));
            }
         } else {
            // Update an existing recruiter
            $id = $object->updateItem();

            // Handle any update hooks
            // TODO: how to modify and limit the way the categories hooks are displayed
            xarModCallHooks('item', 'update', $cid,
               array(
                  'module' => $module,
                  'itemtype' => $itemtype_calendar,
                  'itemid' => $cid,
               )
            );
         }

         if (!empty($submit)) {
            // Redirect to overview if no return URL.
            if (!empty($return_url)) {
               xarResponseRedirect($return_url);
            } else {
                // TODO: go to the calendar view, not the event view.
               xarResponseRedirect(xarModURL($module, 'user', 'view'));
            }
            return true;
         }
      }
   }

   $data['cid'] = $cid;

   // Pass in a list of calendars to the template
   $data['calendars'] = xarModAPIfunc($module, 'user', 'getcalendars');

   // Call modify or update hooks preparation, depending on whether this is a new calendar or not.
   if (empty($cid)) {
      $data['hooks'] = xarModCallHooks(
         'item', 'new', '',
         array(
            'module' => $module,
            'itemtype' => $itemtype_calendar,
            'itemid' => '',
         )
      );
   } else {
      $data['hooks'] = xarModCallHooks(
         'item', 'modify', $cid,
         array(
            'module' => $module,
            'itemtype' => $itemtype_calendar,
            'itemid' => $cid,
         )
      );
   }
   //echo "<pre>"; var_dump($data['hooks']); echo "</pre>";

   return $data;
}

?>