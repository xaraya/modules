<?php

/**
 * Get the vanilla forum settings. Decode some of them into various flags and warnings.
 * @return array of settings
 * If an error occurred, the element 'error' will be returned, with an explanation.
 * @todo: find a way to detect what extensions are installed and enabled.
 */

function vanilla_userapi_getsettings($args)
{
    static $data = array();

    $Context = new stdClass;
    global $Context;

    // Return the cached version if it is already set.
    if (!empty($data)) return $data;

    // Paths within Vanilla.
    $default_settings = 'appg/settings.php';
    $default_database = 'appg/database.php';
    $conf_database = 'conf/database.php';
    
    // Get the base directory.
    $basepath = xarModGetVar('vanilla', 'basepath');

    // Convert this into an absolute path.
    $realpath = realpath($basepath);

    if (empty($realpath) || !is_dir($realpath)) {
        $data['error'] = xarML('Path "#(1)" is not a valid directory', $basepath);
        return $data;
    }

    if (!is_readable($realpath)) {
        $data['error'] = xarML('Path "#(1)" is not a readable directory', $basepath);
        return $data;
    }

    // Include the default settings file
    if (!is_readable($realpath . '/' . $default_settings)) {
        $data['error'] = xarML('File "#(1)" is not a readable file', $default_settings);
        return $data;
    }

    // Include the main settings.
    @include_once($realpath . '/' . $default_settings);

    // Close off the ob_start that was opened in the settings file.
    ob_end_clean();

    // Include the local default database settings.
    if (!is_readable($realpath . '/' . $default_database)) {
        $data['error'] = xarML('File "#(1)" is not a readable file', $default_database);
        return $data;
    }

    @include_once($realpath . '/' . $default_database);

    // Include the local custom database settings.
    if (!is_readable($realpath . '/' . $conf_database)) {
        $data['error'] = xarML('File "#(1)" is not a readable file', $conf_database);
        return $data;
    }

    @include_once($realpath . '/' . $conf_database);

    // Now we should have all the relevant settings from Vanilla.
    //var_dump($Configuration);

    if (empty($Configuration)) {
        $data['error'] = xarML('Configuration data seems to be missing');
        return $data;
    }

    $data = array_merge($data, $Configuration);

    // Now perform some checks, to ensure things are set up right in Vanilla.

    // Test 1: must share the same database.
    $dbconn =& xarDBGetConn();

    if ($dbconn->database != $data['DATABASE_NAME']) {
        $data['error'] = xarML('Vanilla database "#(1)" must be the same as the Xaraya database "#(2)"', 
            $data['DATABASE_NAME'], $dbconn->database
        );
        return $data;
    }

    if ($dbconn->host != $data['DATABASE_HOST']) {
        $data['error'] = xarML('Vanilla database host "#(1)" must be the same as the Xaraya database host "#(2)"', 
            $data['DATABASE_HOST'], $dbconn->host
        );
        return $data;
    }

    if ($dbconn->user != $data['DATABASE_USER']) {
        $data['error'] = xarML('Vanilla database user "#(1)" must be the same as the Xaraya database user "#(2)"', 
            $data['DATABASE_USER'], $dbconn->user
        );
        return $data;
    }

    // Test 2: cookie path must be accessible to the current page.
    // TODO
    $vanilla_cookie_domain = $data['COOKIE_DOMAIN'];
    $vanilla_cookie_path = $data['COOKIE_PATH'];

    // Test 3: users must not be able to change their login details from within Vanilla.
    // TODO

    // Default the session name if not set.
    // Get the desfult session name from somewhere (session_name() just gives the Xaraya cookie name).
    // We need to do a trick here - reset it to its default, and then change it back.
    if (empty($data['SESSION_NAME'])) {
        // Reset the session name to the system default.
        $current_session_name = ini_get('session.name');
        ini_restore('session.name');
        $data['SESSION_NAME'] = ini_get('session.name');
        // Restore the session name.
        if ($current_session_name != ini_get('session.name')) ini_set('session.name', $current_session_name);
    }

    // Some plugins add their details to $Context rather than the direct globals.
    // Merge them in.
    if (isset($Context->DatabaseTables) && is_array($Context->DatabaseTables)) {
        $DatabaseTables = array_merge($DatabaseTables, $Context->DatabaseTables);
    }

    if (isset($Context->DatabaseColumns) && is_array($Context->DatabaseColumns)) {
        $DatabaseColumns = array_merge($DatabaseColumns, $Context->DatabaseColumns);
    }

    // Save the databae table and column details.
    $data['DatabaseTables'] = $DatabaseTables;
    $data['DatabaseColumns'] = $DatabaseColumns;

    $data['VanillaRealPath'] = $realpath;

    return $data;
}

/**
 * Get details for a Vanilla user.
 * @return array of details, empty array if not found, false in the event of an error
 * @param UserID The user ID
 * @param Name The login name of the user
 * Get the user and the main role, and in addition any extended roles if the multi-role
 * plugin is installed.
 */

function vanilla_userapi_getuser($args)
{
    extract($args);
    $dbconn =& xarDBGetConn();
    $user = array();

    // The columns in the user table that we are interested in.
    $column_list = array('UserID', 'RoleID', 'FirstName', 'LastName', 'Name', 'Password', 'VerificationKey', 'Email');

    $settings = xarModAPIfunc('vanilla', 'user', 'getsettings');
    if (!empty($settings['error'])) return false;

    // For some reason the prefix is already on the user table and should not be added again.
    $usertable = $settings['DatabaseTables']['User'];
    $usercolumns = $settings['DatabaseColumns']['User'];

    $sql = 'SELECT ';
    $columns = array();
    foreach($column_list as $column) {
        if (!empty($usercolumns[$column])) $columns[] = $usercolumns[$column];
    }

    if (empty($columns)) return false;

    $sql .= ' ' . implode(', ', $columns) . ' FROM ' . $usertable;

    if (!empty($UserID) && is_numeric($UserID)) {
        $sql .= ' WHERE ' . $usercolumns['UserID'] . ' = ' . (int)$UserID;
    } elseif (!empty($Name) && is_string($Name)) {
        $sql .= ' WHERE ' . $usercolumns['Name'] . ' = ' . $dbconn->qstr($Name);
    } else {
        // No lookup ID - error.
        return false;
    }

    $result = $dbconn->execute($sql);
    if (!$result) return false;

    // If there is a row, fetch the details.
    // If not, then just drop through and return an empty array.
    if (!$result->EOF) {
        foreach($column_list as $key => $column) {
            $user[$column] = $result->fields[$key];

            // Put the role IDs into an array
            if ($column == 'RoleID') {
                $user['RoleIDs'] = array($result->fields[$key]);
            }
        }

        // Now attempt to fetch any extended roles.
        if (!empty($settings['DatabaseTables']['UserRole'])) {
            // The MultiRoles plugin is installed, so we can fetch additional role information.
            $rolestable = $settings['DATABASE_TABLE_PREFIX'] . $settings['DatabaseTables']['UserRole'];
            $rolescolumns = $settings['DatabaseColumns']['UserRole'];
            $sql = 'SELECT ' . $rolescolumns['RoleID']
                . ' FROM ' . $rolestable
                . ' WHERE ' . $rolescolumns['UserID'] . ' = ' . $user['UserID']
                . ' AND ' . $rolescolumns['Activated'] . ' = 1';

            $result = $dbconn->execute($sql);
            if ($result) {
                while(!$result->EOF) {
                    list($RoleID) = $result->fields;
                    $user['RoleIDs'][] = $RoleID;
                    $result->MoveNext();
                }
            } else {
                // Discard any database errors that may have been raised.
                // TODO
            }
        }
    }

    return $user;
}

/**
 * Get details of the Vanilla roles.
 * @return array of roles, false in the event of an error
 * The most important roles are extracted first (e.g. Administrator)
 */

function vanilla_userapi_getroles($args)
{
    extract($args);
    $dbconn =& xarDBGetConn();
    $roles = array();

    $settings = xarModAPIfunc('vanilla', 'user', 'getsettings');
    if (!empty($settings['error'])) return false;

    $rolestable = $settings['DATABASE_TABLE_PREFIX'] . $settings['DatabaseTables']['Role'];
    $rolescolumns = $settings['DatabaseColumns']['Role'];

    $sql = 'SELECT ' . $rolescolumns['RoleID'] . ', '  . $rolescolumns['Name']
        . ' FROM ' . $rolestable
        . ' ORDER BY ' . $rolescolumns['Priority'] . ' DESC';

    $result = $dbconn->execute($sql);
    if (!$result) return false;

    while(!$result->EOF) {
        list($roleid, $name) = $result->fields;
        $roles[$roleid] = $name;
        $result->MoveNext();
    }

    return $roles;
}

/**
 * Create a new Vanilla user.
 * @return 
 * @todo Put some error checking in here
 */

function vanilla_userapi_createuser($args)
{
    extract($args);
    $dbconn =& xarDBGetConn();

    // Create a password and verification key hash.
    // We won't be using the password directly, so something
    // completely random would be good.
    $Password = xarModAPIfunc('vanilla', 'user', 'createkey');
    $VerificationKey = xarModAPIfunc('vanilla', 'user', 'createkey');

    // The main role is the first one off the list.
    $RoleID = array_shift($RoleIDs);

    $settings = xarModAPIfunc('vanilla', 'user', 'getsettings');
    if (!empty($settings['error'])) return true;

    // Table and column details (note no prefix on the User table - seems to be 
    // just the way Vanilla is, and will probably break this interface once it is
    // fixed).
    $usertable = $settings['DatabaseTables']['User'];
    $usercolumns = $settings['DatabaseColumns']['User'];

    $sql = 'INSERT INTO ' . $usertable . ' ('
        . $usercolumns['Name']
        . ', ' . $usercolumns['Email']
        . ', ' . $usercolumns['FirstName']
        . ', ' . $usercolumns['LastName']
        . ', ' . $usercolumns['RoleID']
        . ', ' . $usercolumns['Password']
        . ', ' . $usercolumns['VerificationKey']
        . ', ' . $usercolumns['UtilizeEmail']
        . ', ' . $usercolumns['DateFirstVisit']
        . ', ' . $usercolumns['DateLastActive']
        . ', ' . $usercolumns['RemoteIp']
        . ', ' . $usercolumns['DefaultFormatType']
        . ') VALUES (?, ?, ?, ?, ?, ?, ?, ?, ' . $dbconn->sysTimeStamp . ', ' . $dbconn->sysTimeStamp . ', ?, ?)';

    $bind = array(
        $Name,
        $Email,
        $FirstName,
        $LastName,
        $RoleID,
        $Password,
        $VerificationKey,
        $settings['DEFAULT_EMAIL_VISIBLE'],
        $_SERVER['REMOTE_ADDR'],
        $settings['DEFAULT_FORMAT_TYPE'],
    );

    // We hope this will go smoothly.
    $result = $dbconn->execute($sql, $bind);

    // Get the user ID we have auto-generated.
    $UserID = $dbconn->PO_Insert_ID($usertable, $usercolumns['UserID']);

    // Now deal with any additional roles.
    xarModAPIfunc('vanilla', 'user', 'setroles', array('UserID' => $UserID, 'RoleIDs' => $RoleIDs));
}

/**
 * Set the roles for a user.
 * @param UserID ID of the user
 * @param RoleID Main role ID (optional)
 * @param RoleIDs Array of additional role IDs (optional)
 * @return 
 * @todo Put some error checking in here
 */

function vanilla_userapi_setroles($args)
{
    extract($args);
    $dbconn =& xarDBGetConn();

    $settings = xarModAPIfunc('vanilla', 'user', 'getsettings');
    if (!empty($settings['error'])) return true;

    if (!empty($RoleID)) {
        // User table details
        $usertable = $settings['DatabaseTables']['User'];
        $usercolumns = $settings['DatabaseColumns']['User'];

        // SQL to update the user main role.
        $sql = 'UPDATE ' . $usertable
            . ' SET ' . $usercolumns['RoleID'] . ' = ?'
            . ' WHERE ' . $usercolumns['UserID'] . ' = ?';

        $result = $dbconn->execute($sql, array((int)$RoleID, (int)$UserID));
    }

    // Now deal with any additional roles.
    // These all go into the UserRole table, assuming the MultiRoles plugin is installed.
    if (!empty($RoleIDs) && !empty($settings['DatabaseTables']['UserRole'])) {
        // The MultiRoles plugin is installed, so we can fetch additional role information.
        $rolestable = $settings['DATABASE_TABLE_PREFIX'] . $settings['DatabaseTables']['UserRole'];
        $rolescolumns = $settings['DatabaseColumns']['UserRole'];

        // First query to update any statuses for roles that are already there.
        $sql1 = 'UPDATE ' . $rolestable
            . ' SET ' . $rolescolumns['Activated'] . ' = ?'
            . ' WHERE ' . $rolescolumns['UserID'] . ' = ?'
            . ' AND ' . $rolescolumns['RoleID'] . ' = ?';

        // Second query to insert any new roles that are not present.
        $sql2 = 'INSERT INTO ' . $rolestable . ' ('
            . $rolescolumns['UserID']
            . ', ' . $rolescolumns['RoleID']
            . ', ' . $rolescolumns['Activated']
            . ') VALUES (?, ?, ?)';

        // Third query to disable any roles that are set but not in the list (just do all of them).
        $sql3 = 'UPDATE ' . $rolestable
            . ' SET ' . $rolescolumns['Activated'] . ' = ?'
            . ' WHERE ' . $rolescolumns['UserID'] . ' = ?';

        // Start by turning off any roles that are enabled.
        // This serves two purposes:
        // - it means we don't have to disable them afterwards
        // - the AffectedRows() function only reports rows that have actually changed,
        //   so the flag needs to be flipped from 0 to 1 to register.
        // Must cast the Activated flag to a string, as it is an enumerated type and will
        // not accept zero unless quoted.
        $bind = array((string)0, (int)$UserID);
        $result = $dbconn->execute($sql3, $bind);

        foreach($RoleIDs as $RoleID) {
            // Update the status.
            $bind = array(1, (int)$UserID, (int)$RoleID);
            $result = $dbconn->execute($sql1, $bind);

            // If no rows updated, then insert a new row.
            if ($dbconn->Affected_Rows() == 0) $result = $dbconn->execute($sql2, array((int)$UserID, (int)$RoleID, 1));
        }
    }
}


/**
 * Update a columns for an existing Vanilla user.
 * @param column name/value pairs, UserID is mandatory, the remainder optional.
 * @return 
 */

function vanilla_userapi_updateuser($args)
{
    extract($args);
    $dbconn =& xarDBGetConn();

    $settings = xarModAPIfunc('vanilla', 'user', 'getsettings');
    if (!empty($settings['error'])) return true;

    // User table details
    $usertable = $settings['DatabaseTables']['User'];
    $usercolumns = $settings['DatabaseColumns']['User'];

    if (!empty($args) || empty($UserID)) {
        // Just do one column at a time - not the most efficient, but 
        // not onerous.
        // Make sure the datatypes are set correctly before passing in.
        foreach($args as $name => $value) {
            // If we don't recognise the column name, then skip it.
            if (!in_array($name, $usercolumns) || $name == 'UserID') continue;

            // SQL to update the user main role.
            $sql = 'UPDATE ' . $usertable
                . ' SET ' . $name . ' = ?'
                . ' WHERE ' . $usercolumns['UserID'] . ' = ?';

            $result = $dbconn->execute($sql, array($value, (int)$UserID));
        }
    }
}

/**
 * Handle the login event.
 * @return 
 */

function vanilla_userapi_loginevent($args)
{
    extract($args);
    $dbconn =& xarDBGetConn();
    $uid = xarUserGetVar('uid');

    $settings = xarModAPIfunc('vanilla', 'user', 'getsettings');
    if (!empty($settings['error'])) return true;

    // Get the roles of the current Xaraya user.
    $xar_user_roles = array();
    $roles = new xarRole(array('uid' => $uid));
    $user_roles = $roles->getParents();
    foreach($user_roles as $user_role) {
        $xar_user_roles[] = $user_role->name;
    }

    // Get the full list of Vanilla roles
    $van_roles = xarModAPIfunc('vanilla', 'user', 'getroles');

    // Check for overlap. If any role names are shared, then we need to take action.

    $shared_roles = array();
    foreach($van_roles as $van_role_id => $van_role_name) {
        if (in_array($van_role_name, $xar_user_roles)) $shared_roles[$van_role_id] = $van_role_name;
    }

    // If there are not shared roles, then there is nothing more to do.
    if (empty($shared_roles)) return true;

    // Get details of the Vanilla user matching the current Xar user.
    // We could synchronise the user IDs, or the login names.
    // I have chosen the login name, but derived the name from the Xaraya ID.
    $van_name = 'user_' . $uid;
    $van_user = xarModAPIfunc('vanilla', 'user', 'getuser', array('Name' => $van_name));

    // Several things could happen here:
    // 1. The user does not exist, so we need to create it.
    // 2. The user does exist and needs to be updated.

    // Flip the array so we have the IDs. We don't care about the names now.
    $RoleIDs = array_keys($shared_roles);

    if (empty($van_user)) {
        // User does not exist - create it.

        // Guess at the firstname and surname by splitting the display name into two parts.
        $display_name = xarUserGetVar('name');
        $name_parts = explode(' ', $display_name, 2);
        if (count($name_parts) == 1) $name_parts[] = xarML('Surname');

        // Only pass in the options we need. The rest is filled in
        // by the called function.
        $van_user = xarModAPIfunc('vanilla', 'user', 'createuser',
            array(
                'Name' => $van_name,
                'Email' => xarUserGetVar('email'),
                'RoleIDs' => $RoleIDs,
                'FirstName' => $name_parts[0],
                'LastName' => $name_parts[1],
            )
        );
    } else {
        // User exists - update it if required.
        // Only the roles need be updated, for now at least.
        $RoleID = array_shift($RoleIDs);
        xarModAPIfunc('vanilla', 'user', 'setroles',
            array('UserID' => $van_user['UserID'], 'RoleID' => $RoleID, 'RoleIDs' => $RoleIDs)
        );

        // Update the e-mail address if required.
        // Only do this if the user cannot update the e-mails from within Vanilla.
        if ($van_user['Email'] != xarUserGetVar('email') && empty($settings['ALLOW_EMAIL_CHANGE'])) {
            xarModAPIfunc('vanilla', 'user', 'updateuser', array('UserID' => $van_user['UserID'], 'Email' => xarUserGetVar('email')));
        }
    }

    // Now we have a user, set the cookies we need to log into the forum.
    if (!empty($van_user['VerificationKey']) && !empty($van_user['UserID'])) {
        // Two cookies need to be set, to log a person in automatically.

        // Set the user ID cookie
        setcookie(
            $settings['COOKIE_USER_KEY'],
            $van_user['UserID'], 0,
            $settings['COOKIE_PATH'],
            $settings['COOKIE_DOMAIN']
        ); 
        // Set the Verification Key cookie
        setcookie(
            $settings['COOKIE_VERIFICATION_KEY'],
            $van_user['VerificationKey'], 0,
            $settings['COOKIE_PATH'],
            $settings['COOKIE_DOMAIN']
        );
    }

    // An important final step:
    // We need to be able to create a Vanilla session before we
    // get to the application.
    // The may or may not have already visited the forums. If they
    // have, then we will have the session cookie. If not, then we
    // need to make a quick visit now, and fetch a session cookie
    // value.

    // Check if we have a vannilla cookie. If not, fetch a new one.
    $session_name = $settings['SESSION_NAME'];
    if (!isset($_COOKIE[$session_name])) {
        //
        $vanilla_main_page = xarServerGetProtocol() . '://' . xarServerGetHost() . $settings['WEB_ROOT'] . 'index.php';
        // Pass the two known cookies into the forums, and capture the session
        // cookie that is returned.
        $headers = array(
            'Cookie: ' => $settings['COOKIE_USER_KEY'] . '=' . $van_user['UserID']
                . '; ' . $settings['COOKIE_VERIFICATION_KEY'] . '=' . $van_user['VerificationKey']
        );

        // Reset the cookie global.
        $GLOBALS['vanilla_userapi_curl_headers'] = array();

        // Set up the curl script.
        $curl = curl_init($vanilla_main_page);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADERFUNCTION, 'vanilla_userapi_on_curl_header');
        @curl_exec($curl);
        @curl_close($curl);

        // If the session cookie was returned, then pass it back to the current
        // browser. We are going to assume these are real 'sesssion' cookies in
        // that they do not have a date.
        if (isset($GLOBALS['vanilla_userapi_curl_headers'][$session_name])) {
            // Send this Vanilla session cookie back to the browser.
            setcookie(
                $session_name,
                $GLOBALS['vanilla_userapi_curl_headers'][$session_name], 0,
                $settings['COOKIE_PATH'],
                $settings['COOKIE_DOMAIN']
            );
        }
    }
}

// Helper function to get the cookies from a curl execute.
// Seems a painful way to get the returned cookies, but it does not involve
// messing around trying to read and interpret cookie files. And it does work.
// The cookies are dumped into a global, which can then be inspected.
function vanilla_userapi_on_curl_header($curl, $header)
{
    if (strpos($header, 'Set-Cookie: ') === 0) {
        if (!isset($GLOBALS['vanilla_userapi_curl_headers'])) $GLOBALS['vanilla_userapi_curl_headers'] = array();

        // e.g. "Set-Cookie: VanillaSession=ekbaodjhg1f1n3qqpspqabtpo7; path=/; domain=www.example.com"
        // We want this bit  ^---------------------------------------^ as a name/value pair.
        $header_parts = preg_split('/[:; ]+/', $header);
        if (isset($header_parts[1])) {
            $cookie_parts = explode('=', $header_parts[1], 2);
            if (count($cookie_parts) == 2) $GLOBALS['vanilla_userapi_curl_headers'][$cookie_parts[0]] = $cookie_parts[1];
        }
    }
 
    return strlen($header);
}

/**
 * Handle the logout event.
 * @return 
 */

function vanilla_userapi_logoutevent($args)
{
    extract($args);

    $settings = xarModAPIfunc('vanilla', 'user', 'getsettings');
    if (!empty($settings['error'])) return true;

    $session_name = $settings['SESSION_NAME'];

    // Get the Vanilla session ID
    if (empty($_COOKIE[$session_name])) {
        // No session cookie available - bail out now.
        return true;
    }

    $session_id = $_COOKIE[$session_name];

    // Make sure the user and verification keys are reset, otherwise the user will end up
    // being logged back in again.
    setcookie(
        $settings['COOKIE_USER_KEY'], '',
        time() - 60*60*10, // 10 days ago
        $settings['COOKIE_PATH'],
        $settings['COOKIE_DOMAIN']
    ); 
    setcookie(
        $settings['COOKIE_VERIFICATION_KEY'], '',
        time() - 60*60*10, // 10 days ago
        $settings['COOKIE_PATH'],
        $settings['COOKIE_DOMAIN']
    );
    
    // The session logging out is done via a separate script, because that is the only
    // way to open the Vanilla session and clean it out. If Vanilla stored its sessions
    // in the database then we could just flick the switch there, but it doesn't.
    // If the 'logout.php' script is in the Vanilla home directory, then run it from
    // there, otherwise run it from here. Server security may only allow it to be run from
    // the vanilla directory.

    if (file_exists($settings['VanillaRealPath'] . 'custlogout.php')) {
        $logout_script = xarServerGetProtocol() . '://' . xarServerGetHost() . $settings['WEB_ROOT'] . 'custlogout.php';
    } else {
        $logout_script = xarServerGetBaseURL() . 'modules/vanilla/custlogout.php';
    }

    $curl = curl_init($logout_script);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, array($session_name => $session_id));
    @curl_exec($curl);
    @curl_close($curl);

    return true;
}

/**
 * Create a random Verfication Key.
 * @return 
 */

function vanilla_userapi_createkey($args)
{
    return md5(
		sprintf(
			'%04x%04x%04x%03x4%04x%04x%04x%04x',
			mt_rand(0, 65535),
			mt_rand(0, 65535),
			mt_rand(0, 4095),
			bindec(substr_replace(sprintf('%016b', mt_rand(0, 65535)), '01', 6, 2)),
			mt_rand(0, 65535),
			mt_rand(0, 65535),
			mt_rand(0, 65535),
			mt_rand(0, 65535)
		)
	);
}

?>