<?php

/**
 * Class to model a bitkeeper changeset 
 *
 * @package modules
 * @copyright (C) 2004 The Digital Development Foundation, Inc.
 * @link http://www.xaraya.com
 * 
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@xaraya.com>
 */

/**
 * Class to model a bitkeeper changeset
 */
class bkChangeSet  
{
    var $repo;     // in which repository is this changeset?
    var $rev;      // which changeset to instantiate?
    var $deltas;   // array of file/rev combos which hold the deltas in this cset
    var $author;   // author of this cset
    var $comments; // cset comments
    var $key;      // fixed key of this cset
    var $tag;      // tag, if any
    var $age;      // how old is this
    
    function bkChangeset($repo,$rev='+') 
   {
        $this->repo=$repo;
        $this->rev=$rev;   // changeset revision number
                            // Fill basic properties
        $cmd = "bk changes -r".$rev. " -d':P:\n\$each(:C:){(:C:)".BK_NEWLINE_MARKER."}\n:KEY:\n:AGE:\n:TAG:'";
        $tmp = $this->repo->_run($cmd);
        $this->author = $tmp[0];
        $this->comments = explode(BK_NEWLINE_MARKER,$tmp[1]);
        $this->key = $tmp[2];
        $this->age = $tmp[3];
        if(array_key_exists(4,$tmp)) {
            $this->tag = $tmp[4];
        } else {
            $this->tag = '';
        }
        
        // Fill delta array with identification of deltas
        $this->deltas=NULL;
        $this->_deltas();
   }
    
    // Private function to initialize delta array
    function _deltas() 
   {
        $cmd="bk changes -vn -r".$this->rev." -d':GFILE:|:REV:'";
        $tmp = $this->repo->_run($cmd);
        while (list(,$did) = each($tmp)) {
            list($file,$rev) = explode('|',$did);
            if (strtolower($file)!="changeset") {
                $this->_deltas[$did]=new bkDelta($this,$file,$rev);
            }
        }
   }
    
    function bkDeltaList() 
   {
        return $this->deltas;
   }
    
    
    function bkDeltas($formatstring="':GFILE:|:REV:'") 
   {
        $cmd="bk changes -vn -r".$this->rev." -d$formatstring";
        return $this->repo->_run($cmd);
   }
    
    function bkRev() 
   {
        return $this->rev;
   }
    
    function bkGetAuthor()
   {
        return $this->author;
   }
    
    function bkGetComments()
   {
        return $this->comments;
   }
    
    function bkGetKey()
   {
        return $this->key;
   }
    
    function bkGetTag()
   {
        return $this->tag;
   }
    
    function bkGetAge()
   {
        return $this->age;
   }
}
?>
