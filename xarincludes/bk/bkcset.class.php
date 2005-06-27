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
include_once "modules/bkview/xarincludes/scmcset.class.php";
class bkChangeSet extends bkDelta // A changeset is, in bk, basically a delta on the changeset file
{
    var $deltas = array();   // array of file/rev combos which hold the deltas in this cset
    var $tag    = '';        // tag, if any
    var $key    = '';        // cset key
    
    function bkChangeset($repo,$rev='+') 
   {
        parent::bkDelta($repo,'ChangeSet', $rev);
 
        $this->tag = $this->bkGetTag();
        $this->key = $this->bkGetKey();
        
        // Fill delta array with identification of deltas
        $this->deltas=$this->bkDeltaList();
   }
    
    // Private function to initialize delta array
    function bkDeltaList() 
   {
        $cmd="bk changes -vn -r".$this->rev." -d':GFILE:|:REV:'";
        $tmp = $this->repo->_run($cmd);
        $deltas = array();
        while (list(,$did) = each($tmp)) {
            list($file,$rev) = explode('|',$did);
            if (strtolower($file)!="changeset") {
                $deltas[$did]=new bkDelta($this->repo,$file,$rev);
            }
        }
        return $deltas;
   }
    
    function bkDeltas($formatstring="':GFILE:|:REV:'") 
   {
        $cmd="bk changes -vn -r".$this->rev." -d$formatstring";
        return $this->repo->_run($cmd);
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
        $cmd = "bk changes -t -r" . $this->rev . " -d':TAGS:'";
        $tags = $this->repo->_run($cmd);
        if(!empty($tags)) {
            return str_replace('S ', '',implode(',', $tags));
        } else {
            return '';
        }
   }
}
?>
