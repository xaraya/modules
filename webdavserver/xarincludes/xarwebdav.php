<?php

/**
 * File: $Id$
 *
 * The Xaraya WebDAV server
 *
 * @package modules
 * @copyright (C) 2004 Marcel van der Boom
 * @link http://www.xaraya.com
 * 
 * @subpackage webdavserver
 * @author Marcel van der Boom <marcel@hsdev.com> 
*/

$here = dirname(__FILE__);
require_once $here . "/webdav/server.php";

/**
 * Virtual Filesystem access using WebDAV for Xaraya
 *
 * @access public
 */
class xarwebdav extends HTTP_WebDAV_Server 
{
    /**
     * Root directory for WebDAV access
     *
     * In Xarayas virtual filesystem this is (now) always '/' (set by ServeRequest)
     *
     * @access private
     * @var    string
     */
    var $base = "";
    
    /**
     * Serve a webdav request
     *
     * This is the entry point for each webdav request, i.e.
     * after the request has been identified as a webdav request
     * it is immediately redirected here.
     *
     * @access public
     * @param  string  
     */
    function ServeRequest($base = false) 
    {
        xarLogMessage("WebDAV: Request started");
        // special treatment for litmus compliance test
        // reply on its identifier header
        // not needed for the test itself but eases debugging
        foreach(apache_request_headers() as $key => $value) {
            if(stristr($key,"litmus")) {
                xarLogMessage("Litmus test $value");
                header("X-Litmus-reply: ".$value);
            }
        }
        
        // set root directory, for Xaraya, this is a virtual root. 
        // We are serving a virtual filesystem, so this is always the same
        // the actual rewrite rule may contain a 'short url like' path to 
        // where this root directory should be served up
        $this->base = '/';
        
        // let the base class do all the work
        parent::ServeRequest();
        xarLogMessage('WebDAV: request served');
    }
    
    /**
     * No authentication is needed here
     *
     * @access private
     * @param  string  HTTP Authentication type (Basic, Digest, ...)
     * @param  string  Username
     * @param  string  Password
     * @return bool    true on successful authentication
     */
    function check_auth($type, $user, $pass) 
    {
        //xarLogMessage('WebDAV: checking authentication');
        return true;
    }
    
    
    /**
     * PROPFIND method handler
     *
     * @param  array  general parameter passing array
     * @param  array  return array for file properties
     * @return bool   true on success
     */
    function PROPFIND(&$options, &$files) 
    {
        //xarLogMessage('WebDAV: in PROPFIND method handler');
        // prepare property array
        $files["files"] = array();
        
        // store information for the requested path itself
        $files["files"][] = $this->fileinfo($options["path"]);
        
        // information for contained resources requested?
        if (!empty($options["depth"]))  { // TODO: check for is_collection first?
            //xarLogMessage('WebDAV: there is more in ' . $options['path']);
            // make sure path ends with '/'
            if (substr($options["path"],-1) != "/") $options["path"] .= "/";
            $slashes =  substr_count ($options['path'], '/');

            switch($slashes) {
            case 1: // '/' - Root directory - get a list of module names
                $modlist = xarModAPIFunc('modules','admin','getlist',array('state' => XARMOD_STATE_ACTIVE));
                foreach($modlist as $mod) {
                    $files['files'][] = $this->fileinfo($options['path']. $mod['name'] .'/');
                }
                break;
            case 2: // '/modulename/' - get a list of itemtypes for the module or optionally a list of files
                $module = trim($options['path'],'/');
                if(xarModIsAvailable($module)) {
                    // See if we can get the itemtypes
                    if($itemtypes = xarModAPIFunc($module,'user','getitemtypes')) {
                        // Add them as collections
                        foreach($itemtypes as $itemtype) {
                            $files['files'][] = $this->fileinfo($options['path'] . $itemtype['label'] . '/');
                        }
                    }
                }
                break;
            case 3: // '/modulename/itemtype/' - get a list of items for this itemtype
                break;
            default:
                // zilch
                // TODO recursion needed if "Depth: infinite"
            }
        }
        
        // ok, all done
        return true;
    } 
    
    /**
     * Get properties for a single file/resource
     *
     * @param  string  resource path
     * @return array   resource properties
     */
    function fileinfo($path) 
    {
        //xarLogMessage("WebDAV: Getting fileinfo for $path");
        // create result array for the path
        $info = array();
        $info["path"]  = $path;    
        $info["props"] = array();
        
        // no special beautified displayname here ...
        $info["props"][] = $this->mkprop("displayname", strtoupper($path));

        // path has the following generic form
        // 1. / 
        // 2. /modulename/
        // 3. /modulename/itemtype/
        // 4. /modulename/itemtype/item/
        // All but the last are directories, the latter needs to be mapped
        // to some kind of mime type providing the information on the content
        // We can count on the /-es both on beginning and end, so it's basically
        // a matter of counting them to see what we got.
        // if we have 1,2 or 3 it's a directory, otherwise it's something else (module dependent)
        $slashes = substr_count ($path, '/');
        
        // creation and modification time
        // FIXME: How to determine this in virtual filesystem.
        $info["props"][] = $this->mkprop("creationdate",    time());
        $info["props"][] = $this->mkprop("getlastmodified", time());
        switch($slashes) {
        case 1 : // Root directory  /
        case 2 : // Module          /modulename/
        case 3 : // Itemtype        /modulename/itemtype/
            // directory (WebDAV collection)
            $info["props"][] = $this->mkprop("resourcetype", "collection");
            $info["props"][] = $this->mkprop("getcontenttype", "httpd/unix-directory");             
            $info["props"][] = $this->mkprop("getcontentlength", 0);
            break;
        default:
            // plain file (WebDAV resource)
            // set props resourcetype, getcontenttype and getcontentlength for them
            $info["props"][] = $this->mkprop("resourcetype", "");
            $info["props"][] = $this->mkprop("getcontenttype", "application/x-non-readable");
            $info["props"][] = $this->mkprop("getcontentlength", 0);
        }
        
        // get additional properties from database
        // FIXME: either do this later, or use DD to provide the properties here
        // or scan module for properties on a certain item
        //$query = "SELECT ns, name, value FROM properties WHERE path = '$path'";
        //$res = mysql_query($query);
        //while($row = mysql_fetch_assoc($res)) {
        //    $info["props"][] = $this->mkprop($row["ns"], $row["name"], $row["value"]);
        //}
        //mysql_free_result($res);
        
        return $info;
    }
    
    /**
     * detect if a given program is found in the search PATH
     *
     * helper function used by _mimetype() to detect if the 
     * external 'file' utility is available
     *
     * @param  string  program name
     * @param  string  optional search path, defaults to $PATH
     * @return bool    true if executable program found in path
     */
    function _can_execute($name, $path = false) 
    {
        // path defaults to PATH from environment if not set
        if ($path === false) {
            $path = getenv("PATH");
        }
        
        // check method depends on operating system
        if (!strncmp(PHP_OS, "WIN", 3)) {
            // on Windows an appropriate COM or EXE file needs to exist
            $exts = array(".exe", ".com");
            $check_fn = "file_exists";
        } else { 
            // anywhere else we look for an executable file of that name
            $exts = array("");
            $check_fn = "is_executable";
        }
        
        // now check the directories in the path for the program
        foreach (explode(PATH_SEPARATOR, $path) as $dir) {
            // skip invalid path entries
            if (!file_exists($dir)) continue;
            if (!is_dir($dir)) continue;
            
            // and now look for the file
            foreach ($exts as $ext) {
                if ($check_fn("$dir/$name".$ext)) return true;
            }
        }
        
        return false;
    }
    
    
    /**
     * try to detect the mime type of a file
     *
     * @param  string  file path
     * @return string  guessed mime type
     */
    function _mimetype($fspath) 
    {
        if (@is_dir($fspath)) {
            // directories are easy
            return "httpd/unix-directory"; 
        } else if (function_exists("mime_content_type")) {
            // use mime magic extension if available
            $mime_type = mime_content_type($fspath);
        } else if ($this->_can_execute("file")) {
            // it looks like we have a 'file' command, 
            // lets see it it does have mime support
            $fp = popen("file -i '$fspath' 2>/dev/null", "r");
            $reply = fgets($fp);
            pclose($fp);
            
            // popen will not return an error if the binary was not found
            // and find may not have mime support using "-i"
            // so we test the format of the returned string 
            
            // the reply begins with the requested filename
            if (!strncmp($reply, "$fspath: ", strlen($fspath)+2)) {                     
                $reply = substr($reply, strlen($fspath)+2);
                // followed by the mime type (maybe including options)
                if (ereg("^[[:alnum:]_-]+/[[:alnum:]_-]+;?.*", $reply, $matches)) {
                    $mime_type = $matches[0];
                }
            }
        } 
        
        if (empty($mime_type)) {
            // Fallback solution: try to guess the type by the file extension
            // TODO: add more ...
            // TODO: it has been suggested to delegate mimetype detection 
            //       to apache but this has at least three issues:
            //       - works only with apache
            //       - needs file to be within the document tree
            //       - requires apache mod_magic 
            // TODO: can we use the registry for this on Windows?
            //       OTOH if the server is Windos the clients are likely to 
            //       be Windows, too, and tend do ignore the Content-Type
            //       anyway (overriding it with information taken from
            //       the registry)
            // TODO: have a seperate PEAR class for mimetype detection?
            switch (strtolower(strrchr(basename($fspath), "."))) {
            case ".html":
                $mime_type = "text/html";
                break;
            case ".gif":
                $mime_type = "image/gif";
                break;
            case ".jpg":
                $mime_type = "image/jpeg";
                break;
            default: 
                $mime_type = "application/octet-stream";
                break;
            }
        }
        
        return $mime_type;
    }
    
    /**
     * GET method handler
     * 
     * @param  array  parameter passing array
     * @return bool   true on success
     */
    function GET(&$options) 
    {
        //xarLogMessage('WebDAV: in GET method handler');
        // get absolute fs path to requested resource
        $fspath = $this->base . $options["path"];
        
        // sanity check
        if (!file_exists($fspath)) return false;
        
        // detect resource type
        $options['mimetype'] = $this->_mimetype($fspath); 
        
        // detect modification time
        // see rfc2518, section 13.7
        // some clients seem to treat this as a reverse rule
        // requiering a Last-Modified header if the getlastmodified header was set
        $options['mtime'] = filemtime($fspath);
        
        // detect resource size
        $options['size'] = filesize($fspath);
        
        // no need to check result here, it is handled by the base class
        $options['stream'] = fopen($fspath, "r");
        
        return true;
    }
    
    
    /**
     * PUT method handler
     * 
     * @param  array  parameter passing array
     * @return bool   true on success
     */
    function PUT(&$options) 
    {
        //xarLogMessage('WebDAV: in PUT method handler');
        $fspath = $this->base . $options["path"];
        
        if(!@is_dir(dirname($fspath))) {
            return "409 Conflict";
        }
        
        $options["new"] = ! file_exists($fspath);
        
        $fp = fopen($fspath, "w");
        
        return $fp;
    }
    
    
    /**
     * MKCOL method handler
     *
     * @param  array  general parameter passing array
     * @return bool   true on success
     */
    function MKCOL($options) 
    {           
        //xarLogMessage('WebDAV: in MKCOL method handler');
        $path = $this->base .$options["path"];
        $parent = dirname($path);
        $name = basename($path);
        
        if(!file_exists($parent)) {
            return "409 Conflict";
        }
        
        if(!is_dir($parent)) {
            return "403 Forbidden";
        }
        
        if( file_exists($parent."/".$name) ) {
            return "405 Method not allowed";
        }
        
        if(!empty($_SERVER["CONTENT_LENGTH"])) { // no body parsing yet
            return "415 Unsupported media type";
        }
        
        $stat = mkdir ($parent."/".$name,0777);
        if(!$stat) {
            return "403 Forbidden";                 
        }
        
        return ("201 Created");
    }
    
    
    /**
     * DELETE method handler
     *
     * @param  array  general parameter passing array
     * @return bool   true on success
     */
    function delete($options) 
    {
        //xarLogMessage('WebDAV: in DELETE method handler');
        $path = $this->base . "/" .$options["path"];
        
        if(!file_exists($path)) return "404 Not found";
        
        if (is_dir($path)) {
            $query = "DELETE FROM properties WHERE path LIKE '$options[path]%'";
            mysql_query($query);
            system("rm -rf $path");
        } else {
            unlink ($path);
        }
        $query = "DELETE FROM properties WHERE path = '$options[path]'";
        mysql_query($query);
        
        return "204 No Content";
    }
    
    
    /**
     * MOVE method handler
     *
     * @param  array  general parameter passing array
     * @return bool   true on success
     */
    function move($options) 
    {
        //xarLogMessage('WebDAV: in MOVE method handler');
        return $this->copy($options, true);
    }
    
    /**
     * COPY method handler
     *
     * @param  array  general parameter passing array
     * @return bool   true on success
     */
    function copy($options, $del=false) 
    {
        //xarLogMesssage('WebDAV: in COPY method handler');
        // TODO Property updates still broken (Litmus should detect this?)
        
        
        if(!empty($_SERVER["CONTENT_LENGTH"])) { // no body parsing yet
            return "415 Unsupported media type";
        }
        
        // no copying to different WebDAV Servers yet
        if(isset($options["dest_url"])) {
            return "502 bad gateway";
        }
        
        $source = $this->base .$options["path"];
        if(!file_exists($source)) return "404 Not found";
        
        $dest = $this->base . $options["dest"];
        
        $new = !file_exists($dest);
        $existing_col = false;
        
        if(!$new) {
            if($del && is_dir($dest)) {
                if(!$options["overwrite"]) {
                    return "412 precondition failed";
                }
                $dest .= basename($source);
                if(file_exists($dest.basename($source))) {
                    $options["dest"] .= basename($source);
                } else {
                    $new = true;
                    $existing_col = true;
                }
            }
        }
        
        if(!$new) {
            if($options["overwrite"]) {
                $stat = $this->delete(array("path" => $options["dest"]));
                if($stat{0} != "2") return $stat; 
            } else {                
                return "412 precondition failed";
            }
        }
        
        if (is_dir($source)) {
            // RFC 2518 Section 9.2, last paragraph
            if ($options["depth"] != "infinity") {
                error_log("---- ".$options["depth"]);
                return "400 Bad request";
            }
            system(escapeshellcmd("cp -R ".escapeshellarg($source) ." " .  escapeshellarg($dest)));
            
            if($del) {
                system(escapeshellcmd("rm -rf ".escapeshellarg($source)) );
            }
        } else {                
            if($del) {
                @unlink($dest);
                $query = "DELETE FROM properties WHERE path = '$options[dest]'";
                mysql_query($query);
                rename($source, $dest);
                $query = "UPDATE properties SET path = '$options[dest]' WHERE path = '$options[path]'";
                mysql_query($query);
            } else {
                if(substr($dest,-1)=="/") $dest = substr($dest,0,-1);
                copy($source, $dest);
            }
        }
        
        return ($new && !$existing_col) ? "201 Created" : "204 No Content";         
    }
    
    /**
     * PROPPATCH method handler
     *
     * @param  array  general parameter passing array
     * @return bool   true on success
     */
    function proppatch(&$options) 
    {
        //xarLogMessage('WebDAV: in PROPPATCH method handler');
        global $prefs, $tab;
        
        $msg = "";
        
        $path = $options["path"];
        
        $dir = dirname($path)."/";
        $base = basename($path);
        
        foreach($options["props"] as $key => $prop) {
            if($ns == "DAV:") {
                $options["props"][$key][$status] = "403 Forbidden";
            } else {
                if(isset($prop["val"])) {
                    $query = "REPLACE INTO properties SET path = '$options[path]', name = '$prop[name]', ns= '$prop[ns]', value = '$prop[val]'";
                } else {
                    $query = "DELETE FROM properties WHERE path = '$options[path]' AND name = '$prop[name]' AND ns = '$prop[ns]'";
                }       
                mysql_query($query);
            }
        }
        
        return "";
    }
    
    
    /**
     * LOCK method handler
     *
     * @param  array  general parameter passing array
     * @return bool   true on success
     */
    function lock(&$options) 
    {
        //xarLogMessage('WebDAV: in LOCK method handler');
        if(isset($options["update"])) { // Lock Update
            $query = "UPDATE locks SET expires = ".(time()+300);
            mysql_query($query);
            
            if(mysql_affected_rows()) {
                $options["timeout"] = 300; // 5min hardcoded
                return true;
            } else {
                return false;
            }
        }
        
        $options["timeout"] = time()+300; // 5min. hardcoded
        
        $query = "INSERT INTO locks
                        SET token   = '$options[locktoken]'
                          , path    = '$options[path]'
                          , owner   = '$options[owner]'
                          , expires = '$options[timeout]'
                          , exclusivelock  = " .($options['scope'] === "exclusive" ? "1" : "0")
            ;
        mysql_query($query);
        return mysql_affected_rows() > 0;
        
        return "200 OK";
    }
    
    /**
     * UNLOCK method handler
     *
     * @param  array  general parameter passing array
     * @return bool   true on success
     */
    function unlock(&$options) 
    {
        //xarLogMessage('WebDAV: in UNLOCK method handler');
        $query = "DELETE FROM locks
                      WHERE path = '$options[path]'
                        AND token = '$options[token]'";
        mysql_query($query);
        
        return mysql_affected_rows() ? "200 OK" : "409 Conflict";
    }
    
    /**
     * checkLock() helper
     *
     * @param  string resource path to check for locks
     * @return bool   true on success
     */
    function checkLock($path) 
    {
        $result = false;
        
        $query = "SELECT owner, token, expires, exclusivelock
                  FROM locks
                 WHERE path = '$path'
               ";
        $res = mysql_query($query);
        
        if($res) {
            $row = mysql_fetch_array($res);
            mysql_free_result($res);
            
            if($row) {
                $result = array( "type"    => "write",
                                 "scope"   => $row["exclusivelock"] ? "exclusive" : "shared",
                                 "depth"   => 0,
                                 "owner"   => $row['owner'],
                                 "token"   => $row['token'],
                                 "expires" => $row['expires']
                                 );
            }
        }
        
        return $result;
    }
}
?>
