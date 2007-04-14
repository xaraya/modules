<?php
include_once (GALAXIA_LIBRARY.'/src/common/Base.php');
/**
 * Workflow process class
 *
 * Models a workflow process and the actions ON it. In contract with the
 * process manager which models the action WITH (sets of) it.
 *
 * @package default
 * @author Marcel van der Boom
 **/
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
     * (De-)Activating is a two step process. We update the DB first and
     * the internal object representing the Process. The public methods refer
     * to the private method for their implementation, since it's just the
     * boolean value which differs.
     *
     * @return void
     * @see Process::SetActiveFlag
     * @todo apply phpdoc to all three methods.
    **/
    function activate()   { $this->SetActiveFlag(true);  }
    function deactivate() { $this->SetActiveFlag(false); }
    private function SetActiveFlag($value)
    {
        assert('$value === true or $value===false');
        // DB
        $query = "update ".self::tbl('processes')." set isActive=? where pId=?";
        $this->query($query,array($value ? 'y' : 'n',$this->pId));
        $msg = sprintf(tra('Process %d has been (de)-activated'),$this->pId);
        // Object
        $this->active = $value;
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
    // Process name
    function getName()           { return $this->name;}
    // Name for filesystem storage
    function getNormalizedName() { return $this->normalizedName;}
    // Version string
    function getVersion()        { return $this->version;}
    // Path to process graph
    function getGraph()          { return $this->graph;}

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
