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
include_once "modules/bkview/xarincludes/scmcset.php";
class bkChangeSet extends bkDelta // A changeset is, in bk, basically a delta on the changeset file
{
    var $deltas = array();   // array of file/rev combos which hold the deltas in this cset
    var $tag    = '';        // tag, if any
    var $key    = '';        // cset key
    
    function bkChangeset($repo,$rev='+') 
   {
        parent::bkDelta($repo,'ChangeSet', $rev);
 
        $this->tag = $this->GetTag();
        $this->key = $this->GetKey();
        
        // Fill delta array with identification of deltas
        $this->deltas=$this->DeltaList();
   }
    
    // Private function to initialize delta array
    function DeltaList() 
   {
        $cmd="changes -vn -r".$this->rev." -d':GFILE:|:REV:'";
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
    
    function Deltas($formatstring="':GFILE:|:REV:'") 
   {
        $cmd="changes -vn -r".$this->rev." -d$formatstring";
        return $this->repo->_run($cmd);
   }
    
    function GetComments()
   {
        return $this->comments;
   }
    
    function GetKey()
   {
        $cmd = "changes -n -r" . $this->rev . " -d':KEY:'";
        $key = $this->repo->_run($cmd);
        if(!empty($key)) {
            return $key[0];
        } else {
            return '';
        }
   }
    
    function GetTag()
   {
        $cmd = "changes -t -r" . $this->rev . " -d':TAGS:'";
        $tags = $this->repo->_run($cmd);
        if(!empty($tags)) {
            return str_replace('S ', '',implode(',', $tags));
        } else {
            return '';
        }
   }
}
?>
