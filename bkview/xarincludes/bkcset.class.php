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
class bkChangeSet extends bkDelta
{
    var $deltas;   // array of file/rev combos which hold the deltas in this cset
    var $tag;      // tag, if any
    var $key;      // cset key
    
    function bkChangeset($repo,$rev='+') 
   {
        parent::bkDelta($repo,'ChangeSet', $rev);
 
        $this->tag = $this->bkGetTag();
        $this->key = $this->bkGetKey();
        
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
                $this->deltas[$did]=new bkDelta($this->repo,$file,$rev);
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
        $cmd = "bk changes -n -r" . $this->rev . " -d':KEY:'";
        $key = $this->repo->_run($cmd);
        if(!empty($key)) {
            return $key[0];
        } else {
            return '';
        }
   }
    
    function bkGetTag()
   {
        $cmd = "bk changes -n -r" . $this->rev . " -d':TAG:'";
        $tags = $this->repo->_run($cmd);
        if(!empty($tags)) {
            return $tags[0];
        } else {
            return '';
        }
   }
    
    function bkGetAge()
   {
        return $this->age;
   }
}
?>
