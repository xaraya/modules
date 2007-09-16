<?php
/**
 * Update an event item
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
 * @since 17 July 2006
 * @param  $args ['event_id'] the ID of the event
 * @return bool true on success of update
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function julian_adminapi_update($args)
{
    extract($args);

    // Validate and check arguments
    $invalid = array();

    // The event ID is mandatory when updating.
    if (!isset($event_id) || !is_numeric($event_id)) $invalid[] = 'item ID';

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1)', join(', ', $invalid));
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // Get the existing item
    $item = xarModAPIFunc('julian', 'user', 'get', array('event_id' => $event_id));

    // Check for exceptions, i.e. updating a non existing event
    // The returned item will be NULL if there was an error.
    // CHECKME: not sure what the exception check is all about
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // Security checks
    // FIXME: support multiple categories
    
    if (!xarSecurityCheck('EditJulian', 1, 'Item', "$event_id:" 
                                                    . $item['organizer'] . ":" 
                                                    . $item['calendar_id'] . ":" 
                                                    . $item['categories'])) return;

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

    // The column update list is going to be generated dynamically.

    // Initialise database columns and bindvars arrays
    $colsql = array();
    $bindvars = array();

    // Check each expected field in turn.
    foreach ($fields as $key => $field) {
        if (isset($$field)) {
            //Set the field name
            $colsql[] = "$key = ?";

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
    $colsql[] = 'last_modified = ?';
    // TODO: format this through a central function.
    $bindvars[] = date('Y-m-d H:i:s');
    
    // Nothing to update
    // CHECKME: would we still want to call the hooks, just in case 
    // there are DD or category updates? It is likely we would.
    if (!empty($bindvars)) {
        // Create the query.
        $bindvars[] = (int)$event_id;
        $query = "UPDATE " . $event_table
            . " SET " . implode(', ', $colsql)
            . " WHERE event_id = ?";

        $result = $dbconn->Execute($query, $bindvars);
        if (!$result) return;
    }

    // Call the hooks. Event already exists (we are just updating)
    // TODO: pass in categories and DD items so they can be updated by API alone
    // TODO: pass in the itemtype
    $item = array();
    $item['module'] = 'julian';
    $item['itemid'] = $event_id;
    $hooks = xarModCallHooks('item', 'update', $event_id, $item);

    // Return success
    return true;
}

?>