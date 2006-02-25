<?php
/**
 * Get a hooked item
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian Module Development Team
 */
/**
 * Get a hooked item
 *
 * The data for this hook is stored inside Julian in a separate table.
 * This function retrieves a hooked item
 * In the long run, Julian should hook to itself
 *
 * @author MichelV <michelv@xaraya.com>
 * @author Jorn
 * @param  $args ['objectid'] id of item to get
 * @param  $args ['modid'] module id
 * @return array item array, or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 * @since May 2005
 * @TODO MichelV #1 Hook julian to itself, so events are only stored in one table with extra info from other modules
 */
function julian_userapi_gethooked($args)
{
    if (!xarSecurityCheck('ReadJulian')) {
        return;
    }
    extract($args);
    /* Argument check - make sure that all required arguments are present and
     * in the right format, if not then set an appropriate error message
     * and return
     */
    if (!isset($modid) || !is_numeric($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'module ID', 'user', 'gethooked', 'Julian');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'user', 'gethooked', 'Julian');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
   // Load up database
   $dbconn =& xarDBGetConn();
   $xartable =& xarDBGetTables();
   $event_linkage_table = $xartable['julian_events_linkage'];

    // Try to find the link for the current module, item type and item id.
    // TODO: make this a cleaner call
   $query = "SELECT * FROM " .  $event_linkage_table . " WHERE ( hook_modid =$modid AND hook_itemtype = $itemtype AND hook_iid = $objectid)";
   $result = $dbconn->Execute($query);
   if (!empty($result)) {
        if (!$result->EOF) {
            $edit_obj = $result->FetchObject(false);
            // The local stored summary
            $item['event_summary'] = $edit_obj->summary;

            // Start/end date (and time)
            $event_startdate = strtotime($edit_obj->dtstart);
            $event_enddate   = strtotime($edit_obj->recur_until);

            $item['event_startdate'] = date("F j, Y",$event_startdate);// TODO: use xar Locale formatting
            $item['event_starttime'] = date("g:i A",$event_startdate);
            $item['event_enddate'] = strcmp($event_enddate,'')==0 ? '' : date("F j, Y",$event_enddate);

            // All day or not
            $item['event_allday'] = ($edit_obj->isallday==1);

            // Event duration
            if (strcmp($edit_obj->duration,'')!=0) {
                list($item['event_dur_hours'], $item['event_dur_minutes']) = explode(':',$edit_obj->duration);
            }

            //Checking to see which repeating rule was used so the event_repeat can be set.
            if ($edit_obj->rrule==3 && $edit_obj->recur_count && $edit_obj->recur_interval && $edit_obj->recur_freq) {
                $item['event_repeat'] = 2;
            } else if ($edit_obj->rrule && $edit_obj->recur_freq) {
               $item['event_repeat'] = 1;
            } else {
                $item['event_repeat'] = 0;
            }

            //Depending on which recurrence rule was used, set the appropriate form fields.
            switch ($item['event_repeat']) {
                case 1:
                    $item['event_repeat_every_freq'] = $edit_obj->recur_freq;  // time unit (1=day, 2=week, 3=month, 4=year)
                    $item['event_repeat_every_type'] = $edit_obj->rrule;       // every n time units
                    break;
                case 2:
                    $item['event_repeat_on_day'] = $edit_obj->recur_count;     // day of the week (mon-sun)
                    $item['event_repeat_on_num'] = $edit_obj->recur_interval;  // instance within month (1=1st, 2=2nd, ..., 5=last)
                    $item['event_repeat_on_freq'] = $edit_obj->recur_freq;     // every n months
                    break;
            }

            $result->Close();
        } else {
            return false;
            //return xarML('There is no event hooked to this item.'); // Bug 5189, the return should be an array or false
        }
    } else {
        return false;
        //return xarML('There is no event hooked to this item.');
    }
    // Return the item array
    return $item;
}
?>