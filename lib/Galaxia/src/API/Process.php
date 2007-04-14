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
 *
 * @todo make a distinction between a process as available for the framework and as available for the instance runtime.
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
    private $valid  = false;            // Process validated?

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
     * Activating and Deactivating a process
     *
     * (De-)Activating is a two step process. We update the DB first and
     * the internal object representing the Process. The public methods refer
     * to the private method for their implementation, since it's just the
     * boolean value which differs.
     *
     * @return void
     * @see Process::SetActiveFlag
     * @todo apply this phpdoc to all three methods.
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
     * Validating and Invalidating a process
     *
     * (In)validating is, much like (De)activating a two step process. We update
     * the DB and the internal object presentation. Unlike activation, validation
     * is more more complex, as it involves a test against a ruleset.
    **/
    function invalidate()
    {
        // Make sure we are inactive
        $this->deactivate();;

        $query = "update ".self::tbl('processes')." set isValid=? where pId=?";
        $this->query($query,array('n',$this->pId));
        $this->valid = false;
    }
    function validate() { throw new Exception('Not implemented');}

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

    // Process Active?
    function isActive()         { return $this->active;}
    // Process Valid?
    function isValid ()         { return $this->valid; }

    /**
     * Gets information about an activity in this process by name,
     * e.g. $actinfo = $process->getActivityByName('Approve CD Request');
     *
     * if ($actinfo) {
     *  $some_url = 'tiki-g-run_activity.php?activityId=' . $actinfo['activityId'];
     * }
     * @todo not sure why this is here, probably just for the runtime convenience.
    **/
    function getActivityByName($actname)
    {
        // Get the activity data
        $query = "select * from ".self::tbl('activities')."where `pId`=? and `name`=?";
        $result = $this->query($query,array($this->pId,$actname));
        if(!$result->numRows()) return false;
        $res = $result->fetchRow();
        return $res;
    }

    static function normalize($name, $version)
    {
         $name = $name.'_'.$version;
         return parent::normalize($name);
    }
}
?>
