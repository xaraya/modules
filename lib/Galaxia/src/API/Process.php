<?php
include_once (GALAXIA_LIBRARY.'/src/common/Base.php');
//!! Process.php
//! A class representing a process
/*!
This class representes the process that is being executed when an activity
is executed.
*/
class Process extends Base
{
    public $name;
    public $description;
    public $version;
    public $normalizedName;
    public $pId    = 0;
    public $graph  = '';

    private $active = false;            // Process activated?

    /**
     * Construct an object for a process with specified ID
     *
    **/
    function __construct($id)
    {
        parent::__construct();
        $this->getProcess($id);
    }

    /**
     * Activate a process
     *
    **/
    function activate()
    {
        // DB
        $query = "update ".self::tbl('processes')." set isActive=? where pId=?";
        $this->query($query,array('y',$this->pId));
        $msg = sprintf(tra('Process %d has been activated'),$this->pId);
        // Object
        $this->active = true;
        $this->notify_all(3,$msg);
    }

    /**
     * Deactivate a process
     *
    **/
    function deactivate()
    {
        // DB
        $query = "update ".self::tbl('processes')." set isActive=? where pId=?";
        $this->query($query,array('n',$this->pId));
        $msg = sprintf(tra('Process %d has been deactivated'),$this->pId);
        // Object
        $this->active = false;
        $this->notify_all(3,$msg);
    }

    /**
     * Loads a process from the database
    **/
    private function getProcess($pId)
    {
        $query = "select * from ".self::tbl('processes')."where `pId`=?";
        $result = $this->query($query,array($pId));
        if(!$result->numRows()) return false;
        $res = $result->fetchRow();
        $this->name = $res['name'];
        $this->description = $res['description'];
        $this->normalizedName = $res['normalized_name'];
        $this->version = $res['version'];
        $this->pId = $res['pId'];
        $this->graph = GALAXIA_PROCESSES."/".$this->normalizedName."/graph/".$this->normalizedName.".png";
    }

    /**
     * Various simple getters
     *
     * @todo make this phpdoc apply to all getters here (forgot how to do that)
     * @todo consider a helper like prepforstore instead of putting it in here.
    **/
    function getName()                  // Process name
    {   return $this->name;}
    function getNormalizedName()        // Name for filesystem storage
    {   return $this->normalizedName;}
    function getVersion()               // Version string
    {   return $this->version;}
    function getGraph()                 // Path to process graph
    {   return $this->graph;}

    /**
     * Gets information about an activity in this process by name,
     * e.g. $actinfo = $process->getActivityByName('Approve CD Request');
     *
     * if ($actinfo) {
     *  $some_url = 'tiki-g-run_activity.php?activityId=' . $actinfo['activityId'];
     * }
    **/
    function getActivityByName($actname)
    {
        // Get the activity data
        $query = "select * from ".self::tbl('activities')."where `pId`=? and `name`=?";
        $pId = $this->pId;
        $result = $this->query($query,array($pId,$actname));
        if(!$result->numRows()) return false;
        $res = $result->fetchRow();
        return $res;
    }

    function isActive()
    { return $this->active;}
}
?>
