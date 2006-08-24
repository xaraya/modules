<?php
/**
 * Main runner file to start the object server daemon
 *
 * @copyright HS-Development BV, 2006
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://hsdev.com
 * @author Marcel van der Boom <mrb@hsdev.com>
**/
include_once "server/php/import.php";

/* The server needs a minimum pcntl, sockets and PHP5 */
if (!extension_loaded('pcntl'))   
    die("Error: pcntl extension not available!\n");
if (!extension_loaded('sockets')) 
    die("Error: sockets extension not available!\n");

/* Load the libraries we will need */
fwrite(STDOUT,"Importing librariess...\n");
php::import('server.exceptions');
php::import('server.bean');
php::import('server.objectstore');
php::import('server.accessrules');
php::import('server.objectserver');

// Read in configuration with sections
$conf  = parse_ini_file('conf/server.conf',true);

// Configure and start the server
fwrite(STDOUT,"Starting objectserver...\n");
try
{
    // Create an object for the physical storage 
    // Example 1: SQLite
    $dbfile= '/var/mt/xar/core/core.2.x/html/var/cache/database/objectstore.db';
    $database = new SQLiteDatabase($dbfile, 0600, $error);

    // @todo other stores and rules connectors (not hard, just typing :-) )
    
    // Create the object server 
    $server = new ObjectServer(
        $conf,
        new ObjectStore($database), // interface to the object store
        new AccessRules($database)  // interface to the access rules
    );
    
    // Serve it up!
    $server->start();
} catch(Exception $e) {
    fwrite(STDERR,"FATAL: server died: ". $e->getMessage() ."\n");
}


?>