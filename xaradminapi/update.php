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
    /* Get arguments from argument array - all arguments to this function
     * should be obtained from the $args array, getting them from other
     * places such as the environment is not allowed, as that makes
     * assumptions that will not hold in future versions of Xaraya
     */
    extract($args);
    /* Note the absence of a xarVarFetch function here. Remember that xarVarFetch
     * gets environmental variables, and therefore can fetch variables that you do not want in here.
     * This function can be called from others than just the admin_update one
     */
    /* Argument check - make sure that all required arguments are present
     * and in the right format, if not then set an appropriate error
     * message and return
     * Note : since we have several arguments we want to check here, we'll
     * report all those that are invalid at the same time...
     */
    $invalid = array();
    if (!isset($event_id) || !is_numeric($event_id)) {
        $invalid[] = 'item ID';
    }
    /*
    if (!isset($name) || !is_string($name)) {
        $invalid[] = 'name';
    }
    if (!isset($number) || !is_numeric($number)) {
        $invalid[] = 'number';
    }
    */
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'update', 'Julian');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    /* The user API function is called. This takes the item ID which
     * we obtained from the input and gets us the information on the
     * appropriate item. If the item does not exist we post an appropriate
     * message and return
     */
    $item = xarModAPIFunc('julian',
        'user',
        'get',
        array('event_id' => $event_id));
    /*Check for exceptions */
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    /* Security check - important to do this as early on as possible to
     * avoid potential security holes or just too much wasted processing.
     * However, in this case we had to wait until we could obtain the item
     * name to complete the instance information so this is the first
     * chance we get to do the check
     * Note that at this stage we have two sets of item information, the
     * pre-modification and the post-modification. We need to check against
     * both of these to ensure that whoever is doing the modification has
     * suitable permissions to edit the item otherwise people can potentially
     * edit areas to which they do not have suitable access
     */
    if (!xarSecurityCheck('EditJulian', 1, 'Item', "$event_id:$organizer:$calendar_id:$catid")) {
        return;
    }
    if (!xarSecurityCheck('EditJulian', 1, 'Item', "$event_id:$organizer:$calendar_id:$catid")) {
        return;
    }
    /* Get database setup - note that both xarDBGetConn() and xarDBGetTables()
     * return arrays but we handle them differently. For xarDBGetConn()
     * we currently just want the first item, which is the official
     * database handle. For xarDBGetTables() we want to keep the entire
     * tables array together for easy reference later on
     */
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    /* It's good practice to name the table and column definitions you
     * are getting - $table and $column don't cut it in more complex
     * modules
     */
    $event_table = $xartable['julian_events'];


    $now = "now()";
    $query = "UPDATE " .  $event_table . "
            SET isallday= ?,
            contact= ?,
            url= ?,
            summary= ?,
            description= ?,
            class= ?,
            location= ?,
            share_uids= ?,
            street1= ?,
            street2= ?,
            city= ?,
            state= ?,
            zip= ?,
            phone= ?,
            email= ?,
            fee= ?,
            categories= ?,
            rrule= ?,
            recur_freq= ?,
            recur_until= ?,
            recur_count= ?,
            recur_interval= ?,
            duration= ?,
            dtstart= ?,
            last_modified= ?
            WHERE event_id =".$id."";
    $bindvars = array ($event_allday
                    , $contact
                    , $website
                    , $summary
                    , $description
                    , (int) $class
                    , $location
                    , $share
                    , $street1
                    , $street2
                    , $city
                    , $state
                    , $zip
                    , $phone
                    , $email
                    , $fee
                    , $category
                    , $rrule
                    , (int) $recur_freq
                    , $recur_until == '' ? NULL : $recur_until
                    , (int) $recur_count
                    , (int) $recur_interval
                    , $duration
                    , $eventstartdate == '' ? NULL : $eventstartdate
                    , $now);
    $result = $dbconn->Execute($query, $bindvars);
    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;

    // Call the hooks. Event already exists (we are just updating)
    $item = array();
    $item['module'] = 'julian';
    $hooks = xarModCallHooks('item', 'update', $id, $item);

    /* Let the calling process know that we have finished successfully */
    return true;
}
?>