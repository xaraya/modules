<?php
/**
 * Create a new event item
 *
 * @package modules
 * @copyright (C) 2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian Module Development Team
 */
/**
 * Update an event item
 *
 * This function takes in the data from admin_update and
 * saves the data it the appriopriate table
 *
 * @author the Julian module development team
 * @since 19 April 2007
 * @param  $args Details of the event
 * @return bool true on success of update
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function julian_adminapi_create($args)
{
    extract($args);

    // Validate and check arguments
    $invalid = array();

    // Security checks
    // FIXME: organizer, calendar_id and catid are not checked and may not even be set.
    // It is likely they come from the existing item.
    // TODO: support multiple categories

    $uid = xarUserGetVar('uid');
    if (!xarSecurityCheck('AddJulian', 1, 'Item', "All:$uid:All:All")) return;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $event_table = $xartable['julian_events'];

    // Map parameter names to database column names.
    // Column name as key, value as element.
    $fields = array(
        'isallday' => 'event_allday',
        'contact' => 'contact',
        'url' => 'website',
        'summary' => 'summary',
        'description' => 'description',
        'class' => 'class',
        'location' => 'location',
        'share_uids' => 'share',
        'street1' => 'street1',
        'street2' => 'street2',
        'city' => 'city',
        'state' => 'state',
        'zip' => 'zip',
        'phone' => 'phone',
        'email' => 'email',
        'fee' => 'fee', 
        'categories' => 'category',
        'rrule' => 'rrule', 
        'recur_freq' => 'recur_freq',
        'recur_until' => 'recur_until', 
        'recur_count' => 'recur_count',
        'recur_interval' => 'recur_interval',
        'duration' => 'duration',
        'dtstart' => 'eventstartdate'
    );

    // The column insert list is going to be generated dynamically.
    // TODO: many of these columns are mandatory - check them and raise errors as appropriate
    // TODO: much of this dynamic code is shared with the update API, and possibly useful in the get APIs; take out the duplication

    // Initialise database columns and bindvars arrays
    $colsql = array();
    $bindvars = array();

    // Check each expected field in turn.
    foreach ($fields as $key => $field) {
        if (isset($$field)) {
            //Set the field name
            $colsql[] = $key;

            // Set the bind variable.
            // Some need typecasting.
            if ($field == 'event_allday' || $field == 'class' || $field == 'recur_freq' || $field == 'recur_count' || $field == 'recur_interval') {
                // Typecast to an integer
                $bindvars[] = (int) $$field;
            } elseif ($field == 'recur_until' || $field == 'eventstartdate') {
                // Set to NULL if an empty string or zero.
                // These are datetime fields in the database so we expect them in the right format for MySQL.
                // FIXME: the situation is not good - date formats are handled as strings in the format that
                // MySQL expects, throughout the module. An API for converting to/from the DB format and
                // other formats would be very handy...
                if (empty($field)) $field = NULL;
                $bindvars[] = $$field;
            } else {
                // Everything else defaults to a string.
                $bindvars[] = (string)$$field;
            }
        }
    }

    // Additional fields
    $colsql[] = 'created';
    // TODO: format this through a central function.
    $bindvars[] = date("Y-m-d H:i:s");

    // Default calendar ID
    // TODO: support calendar IDs
    $colsql[] = 'calendar_id';
    $bindvars[] = 0;

    // Get the next event ID (for pre-fetch databases)
    $next_id = $dbconn->GenId($event_table);
    $colsql[] = 'event_id';
    $bindvars[] = $next_id;
    
    // Create the query.
    $query = 'INSERT INTO ' . $event_table
        . ' (' . implode(', ', $colsql) . ')'
        . ' VALUES (?' . str_repeat(', ?', count($bindvars)-1) . ')';

    $result = $dbconn->Execute($query, $bindvars);
    if (!$result) return;

    // Get the new event ID
    $event_id = $dbconn->Insert_ID($event_table, 'event_id', 'serial');
    
    // Call the hooks. Event already exists (we are just updating)
    // TODO: pass in categories and DD items so they can be added by API alone
    // TODO: pass in the itemtype
    $item = array();
    $item['module'] = 'julian';
    $item['itemid'] = $event_id;
    $hooks = xarModCallHooks('item', 'create', $event_id, $item);  

    // Return success
    return $event_id;
}

?>