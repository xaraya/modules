<?php
/*
 * File: $Id: $
 *
 * xarldap :  LDAP API for Xaraya
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team 
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage xarldap module
 * @author Richard Cave <rcave@xaraya.com>
*/

/**
 * xarldap: class for LDAP (Lightweight Directory Access Protocol)
 *
 * Represents the repository containing all roles
 *
 * @author Richard Cave <rcave@xaraya.com>
 * @access public
 * @throws none
 * @todo none
 */
class xarldap {
    var $server;         // The LDAP server
    var $port_number;    // The port number (default is 389)
    var $anonymous_bind; // Anonymous bind to the LDAP server
    var $bind_dn;        // Bind DN
    var $uid_field;      // UID field
    var $search_user_dn; // Search the user DN
    var $admin_login;    // Admin login
    var $admin_password; // Admin password
    var $connection;     // The connection to the LDAP server
    var $key;            // Key used to encrypt/decrypt the admin password
    var $tls;            // Use TLS connection (LDAP Protocol 3 only)
    
    /**
     * xarldap: constructor for the class
     *
     * Initializes variables for xarldap class 
     *
     * @author  Richard Cave <rcave@xaraya.com>
     * @access  public
     * @param   'bind_dn'
     * @param   'uid_field'
     * @return  none
     * @throws  none
     * @todo    none
    */
    function xarldap() {
        $this->server = '127.0.0.1';
        $this->port_number = 389;
        $this->anonymous_bind = true;
        $this->bind_dn = 'o=dept';
        $this->uid_field = 'cn';
        $this->search_user_dn = true;
        $this->admin_login = '';
        $this->admin_password = '';
        $this->connection = '';
        $this->key = '';
        $this->tls = false; // LDAP Protocol 3 only
    }

    /**
     * exists
     *
	 * Check for existence of the LDAP PHP extension
     *
     * @author  Richard Cave <rcave@xaraya.com>
     * @access  public
     * @param   none
     * @returns true on success, false on failure
     * @return  boolean 
     * @throws  none
     * @todo    none
    */
    function exists() {
        if (!extension_loaded('ldap')) {
            $msg=xarML('Your PHP configuration does not seem to include the required LDAP extension. Please refer to http://www.php.net/manual/en/ref.ldap.php on how to install it.');
            xarExceptionSet(XAR_SYSTEM_EXCEPTION,'MODULE_DEPENDENCY',
                        new SystemException($msg));
            return false;
        }

        return true;
    }


    /**
     * open
     *
     * Open the LDAP connection
     *
     * @author  Richard Cave <rcave@xaraya.com>
     * @access  public
     * @param   none
     * @returns true on success, false on failure
     * @return  LDAP link identifier on connect, false otherwise
     * @throws  XAR_SYSTEM_EXCEPTION
     * @todo    none
    */
    function open() {
        // Get/set the current xarldap paramteres
        $this->get_parameters();

        // Connect to the LDAP server
        $this->connection = ldap_connect($this->server, $this->port_number);

        if (!$this->connection) {
            $msg = xarML('LDAP Error:  Connection to server #(1) port_number #(2) failed', $this->server, $this->port_number);
            xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
            return;
        }

        // Fix for Bug 2669 - Allow TLS for LDAP Protocol 3
        if ($this->tls == 'true') {
            if (!$this->set_option(LDAP_OPT_PROTOCOL_VERSION, 3)) {
                $msg = xarML('LDAP Error:  Failed to set LDAP Protocol version to 3');
                xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                           new SystemException($msg));
                return;
            }
            if (!ldap_start_tls($this->connection)) {
                $msg = xarML('LDAP Error:  Failed to start TLS');
                xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                           new SystemException($msg));
                return;
            }
        }

        return true;
    }

    /**
     * close
     *
     * Close the LDAP connection
     *
     * @author  Richard Cave <rcave@xaraya.com>
     * @access  public
     * @param   none
     * @returns true on success, false on failure
     * @return  LDAP link identifier on connect, false otherwise
     * @throws  XAR_SYSTEM_EXCEPTION
     * @todo    none
    */
    function close() {

        // Close LDAP connection
        return ldap_close($this->connection);
    }

    /**
     * bind
     *
     * Bind to the LDAP server
     *
     * @author  Richard Cave <rcave@xaraya.com>
     * @access  public
     * @param   'bindrdn' LDAP rdn or dn
     * @param   'password' the associated password
     * @return  true on success, false on failure 
     * @throws  none
     * @todo    none
    */
    function bind($bindrdn = '', $password = '') {

        if ($bindrdn != '' and $password != '') {
            $ldapbind = @ldap_bind($this->connection,
                                   $bindrdn,
                                   $password);
        } else {
            $ldapbind = @ldap_bind($this->connection);
        }
        
        return $ldapbind;
    }

    /**
     * anonymous_bind
     *
     * Anonymous bind to the LDAP server
     *
     * @author  Richard Cave <rcave@xaraya.com>
     * @access  public
     * @param   'bindrdn' LDAP rdn or dn
     * @param   'password' the associated password
     * @return  true on success, false on failure 
     * @throws  none
     * @todo    none
    */
    function anonymous_bind() {

        return $this->bind();
    }

    /**
     * admin_bind
     *
     * Bind to the LDAP server using the admin login and password
     *
     * @author  Richard Cave <rcave@xaraya.com>
     * @access  public
     * @param   none
     * @return  true on success, false on failure 
     * @throws  none
     * @todo    none
    */
    function admin_bind() {

        // Admin password is encrypted - so decrypt
        $admin_password = $this->encrypt($this->admin_password, 0);

        return $this->bind($this->admin_login, $admin_password);
    }

    /**
     * bind_to_server
     *
     * Bind to the LDAP server using the admin login or anonymous
     *
     * @author  Richard Cave <rcave@xaraya.com>
     * @access  public
     * @param   none
     * @return  true on success, false on failure 
     * @throws  none
     * @todo    none
    */
    function bind_to_server() {

        $bindResult = false;

        // Check if anonymouse bind specified
        if ($this->anonymous_bind == 'true') {
            $bindResult = $this->anonymous_bind();
        } else if ($this->admin_login != "" && $this->admin_password!= "") {
            // Bind as admin
            $bindResult = $this->admin_bind();
        }

        return $bindResult;
    }

    /**
     * search
     *
     * Search an LDAP Tree
     *
     * @author  Richard Cave <rcave@xaraya.com>
     * @access  public
     * @param   'base_dn'  base dn to start search
     * @param   'filter' search filter
     * @return  search result identifier of false on failure
     * @throws  none
     * @todo    expand search parameters
    */
    function search($base_dn, $filter) {
        // A resource ID is always returned when using URLs for the 
        // host parameter even if the host does not exist.  This will
        // cause a PHP exception if the host does not exist, so 
        // suppress hard error return as the PHP exception will contain
        // the user's password.
        $result = @ldap_search($this->connection, $base_dn, $filter);
        if (!$result) {
            $msg = xarML('LDAP Error:  Search LDAP base_dn #(1) filter #(2) failed', $base_dn, $filter);
            xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
            return;
        }

        return $result;
    }
    
    /**
     * search_user_dn
     *
     * Search an LDAP Tree for a user dn 
     *
     * @author  Richard Cave <rcave@xaraya.com>
     * @access  public
     * @param   'username' user dn to search
     * @return  search result identifier of false on failure
     * @throws  none
     * @todo    expand search parameters
    */
    function search_user_dn($username) {
        if ($this->search_user_dn == 'true') {
            $searchResult = $this->search($this->bind_dn,
                                          $this->uid_field . "=" . $username);

            if (!$searchResult)
                return false;

            // Check if user exists
            $userInfo = $this->get_entries($searchResult);
            if($userInfo['count']==0)
                return false;

            $user_dn = $userInfo[0]['dn'];
        } else {
            // Validate password
            // Fix for bug #1413 submitted by Court Shrock 
            $user_dn = $this->uid_field."=" . $username . "," . $this->bind_dn;
        }

        return $user_dn;
    } 

    /**
     * user_search
     *
     * All in one function to connect to LDAP and search for a user
     *
     * @author  Richard Cave <rcave@xaraya.com>
     * @access  public
     * @param   'username' the username to search
     * @return  returns array of entries or false on failure
     * @throws  none
     * @todo    none
    */
    function user_search($username) {

        // Open ldap connection
        if (!$this->open())
            return false;

        // Bind to LDAP server
        $bindResult = $this->bind_to_server();
        if (!$bindResult)
            return false;

        // Search for user information
        $searchResult=$this->search($this->bind_dn, $this->uid_field."=". $username);
        if (!$searchResult)
            return false;

        $userInfo = $this->get_entries($searchResult);
        if (!$userInfo)
            return false;

        // ldap_get_entries returns true even if no results
        // are found, so check for number of rows in array
        if ($userInfo['count'] == 0)
            return false;

        // close LDAP connection
        $this->close();

        return $userInfo;
    }

    /**
     * get_entries: 
     *
     * 
     *
     * @author  Richard Cave <rcave@xaraya.com>
     * @access  public
     * @param   'result' result from an LDAP search
     * @return  returns array of entries or false on failure
     * @throws  none
     * @todo    none
    */
    function get_entries($result) {
        $info = ldap_get_entries($this->connection, $result);
        return $info;
    }

    /**
     * get_attribute_value: 
     *
     * 
     *
     * @author  Richard Cave <rcave@xaraya.com>
     * @access  public
     * @param   'entry' 
     * @param   'attrbitue' 
     * @return  returns array of entries or false on failure
     * @throws  none
     * @todo    none
    */
    function get_attribute_value($entry, $attribute) {
        // what to do with more than one entry for user info?
        //$num_entries = ldap_count_entries($this->connection,$entry);

        // get attribute value
        $value = $entry[0][$attribute][0];
        return $value;

        /*
        for ($i=0; $i<$num_entries; $i++) {  // loop though ldap search result
            error_log("user dn: " . $user_info[$i]['dn']);
            for ($ii=0; $ii<$user_info[$i]['count']; $ii++) {
                $attrib = $user_info[$i][$ii];
                eval("error_log( \$user_info[\$i][\"$attrib\"][0]);"); 
        }
        */
    }


    /**
     * get_option
     *
     * Get the value of the given LDAP option 
     *
     * @author  Richard Cave <rcave@xaraya.com>
     * @access  public
     * @param   'option' the LDAP option to retrieve
     * @return  the value of the option 
     * @throws  none
     * @todo    check error return
    */
    function get_option($option) {
        if (ldap_get_option($this->connection, $option, $value)) {
            return $value;
        } else {
            return false; // Could this be an LDAP value?
        }
    }

    /**
     * set_option
     *
     * Set the value of the given LDAP option 
     *
     * @author  Richard Cave <rcave@xaraya.com>
     * @access  public
     * @param   'option' the LDAP option to set
     * @param   'value' the value of the LDAP option
     * @return  true on success, false on failure
     * @throws  none
     * @todo    none
    */
    function set_option($option, $value) {

        // Check that values are the right type
        switch ($option){ 
            case LDAP_OPT_DEREF:
            case LDAP_OPT_SIZELIMIT: 
            case LDAP_OPT_TIMELIMIT:
            case LDAP_OPT_PROTOCOL_VERSION:
            case LDAP_OPT_ERROR_NUMBER:
                if (!is_integer($value))
                    return false;
                break;

            case LDAP_OPT_REFERRALS:
            case LDAP_OPT_RESTART:
                if (!is_bool($value))
                    return false;
                break;

            case LDAP_OPT_HOST_NAME: 
            case LDAP_OPT_ERROR_STRING:
            case LDAP_OPT_MATCHED_DN:
                if (!is_string($value))
                    return false;
                break;

            case LDAP_OPT_SERVER_CONTROLS:
            case LDAP_OPT_CLIENT_CONTROLS:
                if (!is_array($value))
                    return false;
                break;

            default:
                return false;
        }

        return @ldap_set_option( $this->connection, $option, $value); 
    }

    /**
     * get_variable
     *
     * Get a xarldap module variable
     *
     * @author  Richard Cave <rcave@xaraya.com>
     * @access  private
     * @param   'key' the module variable to set
     * @return  value
     * @throws  none
     * @todo    none
    */
    function get_variable($key)
    {
        if (!isset($key))
            return;

        return xarModGetVar('xarldap', $key);
    }

    /**
     * set_variable
     *
     * Set a xarldap module variable
     *
     * @author  Richard Cave <rcave@xaraya.com>
     * @access  private
     * @param   'key' the module variable to set
     * @param   'value' the value to set
     * @return  string
     * @throws  none
     * @todo    none
    */
    function set_variable($key, $value)
    {
        if (!isset($key) || !isset($value))
            return;

        return xarModSetVar('xarldap', $key, $value);
    }

    /**
     * get_parameters
     *
     * Get the current parameters from xar_module_vars
     *
     * @author  Richard Cave <rcave@xaraya.com>
     * @access  public
     * @param   none
     * @returns true on success, false on failure
     * @return  boolean 
     * @throws  none
     * @todo    none
    */
    function get_parameters() {
        // Get all the current values out of the module variables
        $this->server = $this->get_variable('server');
        $this->port_number = $this->get_variable('port_number');
        $this->anonymous_bind = $this->get_variable('anonymous_bind');
        $this->bind_dn = $this->get_variable('bind_dn');
        $this->uid_field = $this->get_variable('uid_field');
        $this->search_user_dn = $this->get_variable('search_user_dn');
        $this->admin_login = $this->get_variable('admin_login');
        $this->admin_password = $this->get_variable('admin_password');
        $this->key = $this->get_variable('key');
        $this->tls = $this->get_variable('tls');
        
        return true;
    }

    /**
     * set_parameters
     *
     * Set the current parameters to xar_module_vars
     *
     * @author  Richard Cave <rcave@xaraya.com>
     * @access  public
     * @param   none
     * @returns true on success, false on failure
     * @return  boolean 
     * @throws  none
     * @todo    none
    */
    function set_parameters() {
        // Get all the current values out of the module variables
        $this->set_variable('server', $this->server);
        $this->set_variable('port_number', $this->port_number);
        $this->set_variable('anonymous_bind', $this->anonymous_bind);
        $this->set_variable('bind_dn', $this->bind_dn);
        $this->set_variable('uid_field', $this->uid_field);
        $this->set_variable('search_user_dn', $this->search_user_dn);
        $this->set_variable('admin_login', $this->admin_login);
        $this->set_variable('admin_password', $this->admin_password);
        $this->set_variable('key', $this->key);
        $this->set_variable('tls', $this->tls);
        
        return true;
    }



    /**

    /**
     * generate_key
     *
     * Generate the key used to encrypt or decrypt a value.
     *
     * @author  Richard Cave <rcave@xaraya.com>
     * @access  private
     * @param   'password' the password used to generate the key
     * @param   'level' the number of levels to iterate
     * @return  string
     * @throws  XAR_SYSTEM_EXCEPTION
     * @todo    none
    */
    function generate_key($password, $level = 30)
    {   
        if (!isset($password)) {
            $msg = xarML('Empty password (#(1)).', $password);
            xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
            return;
        }

        for ($i=0; $i<$level; $i++) {
            $mymd[$i]=md5(substr($password,($i%strlen($password)),1));
        }

        for ($a=0; $a<32; $a++) {
             for ($i=0; $i<$level; $i++) {
                $this->key .= substr($mymd[$i],$a,1);
            }
        }

        return $this->key;
    }

    /**
     * encrypt
     *
     * Encrypt or decrypt a given value.  This is used for storing
     * the admin password value in table xar_module_vars table. 
     *
     * @author  Richard Cave <rcave@xaraya.com>
     * @access  private
     * @param   'body' the value to encrypt or decrypt 
     * @param   'method' 0 = decrypt or 1 = encrypt 
     * @return  true on success, false on failure
     * @throws  XAR_SYSTEM_EXCEPTION
     * @todo    none
    */
    function encrypt($body, $method = 1)
    {
        if (!isset($body)) {
            $msg = xarML('Empty body (#(1)).', $body);
            xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
            return;
        }

        if ($method==0) {
            $this->key=str_replace("3", "j", str_replace("2", "i", str_replace("1", "h", str_replace("0", "g", $this->key))));
            $this->key=str_replace("7", "n", str_replace("6", "m", str_replace("5", "l", str_replace("4", "k", $this->key))));
            $this->key=str_replace("b", "4", str_replace("a", "5", str_replace("9", "6", str_replace("8", "7", $this->key))));
            $this->key=str_replace("f", "0", str_replace("e", "1", str_replace("d", "2", str_replace("c", "3", $this->key))));
            $this->key=str_replace("j", "c", str_replace("i", "d", str_replace("h", "e", str_replace("g", "f", $this->key))));
            $this->key=str_replace("n", "8", str_replace("m", "9", str_replace("l", "a", str_replace("k", "b", $this->key))));
            $body=base64_decode($body);
        }

        for ($i=0; $i<strlen($this->key); $i=$i+2) 
            $d[$i/2]=hexdec(substr($this->key,$i,2));

        for ($i=0, $ntext=""; substr($body, $i, 1) !=""; $i++) 
            $ntext.=chr((ord(substr($body, $i, 1))+$d[($i%strlen($this->key))])%255);

        if ($method==1)
            $ntext=base64_encode($ntext);

        return ($ntext);
    }

} // end of class

?>
