<?php
include_once (GALAXIA_LIBRARY.'/common/base.php');
/**
 * Base class for Workflow activities
 *
 * This class represents activities, and must be derived for
 * each activity type supported in the system. Derived activities extending this
 * class can be found in the activities subfolder.
 * This class is observable.
 *
**/
class WorkflowActivity extends Base
{
    public $name;
    public $description;
    public $isInteractive;
    public $isAutoRouted;
    public $roles=Array();
    public $outbound=Array();
    public $inbound=Array();
    public $pId;
    public $activityId;
    public $expirationTime = 0;

    /**
     * Factory method returning an activity of the desired type
     * loading the information from the database.
     *
    **/
    static function &get($activityId)
    {
        // Get an object with a db object for now
        $dummy = new Base();
        $query = "select * from ".self::tbl('activities')." where `activityId`=?";
        $result = $dummy->query($query,array($activityId));

        if(!$result->numRows()) return false;
        $res = $result->fetchRow();
        switch($res['type']) {
          case 'start':
            include_once (GALAXIA_LIBRARY.'/api/activities/start.php');
            $act = new StartActivity();
            break;
          case 'end':
            include_once (GALAXIA_LIBRARY.'/api/activities/end.php');
            $act = new EndActivity();
            break;
          case 'join':
            include_once (GALAXIA_LIBRARY.'/api/activities/join.php');
            $act = new JoinActivity();
            break;
          case 'split':
            include_once (GALAXIA_LIBRARY.'/api/activities/split.php');
            $act = new SplitActivity();
            break;
          case 'standalone':
            include_once (GALAXIA_LIBRARY.'/api/activities/standalone.php');
            $act = new StandaloneActivity();
            break;
          case 'switch':
            include_once (GALAXIA_LIBRARY.'/api/activities/switch.php');
            $act = new SwitchActivity();
            break;
          case 'activity':
            include_once (GALAXIA_LIBRARY.'/api/activities/standard.php');
            $act = new StandardActivity();
            break;
          default:
            trigger_error('Unknown activity type:'.$res['type'],E_USER_WARNING);
        }

        $act->setName($res['name']);
        $act->setProcessId($res['pId']);
        $act->setDescription($res['description']);
        $act->setIsInteractive($res['isInteractive']);
        $act->setIsAutoRouted($res['isAutoRouted']);
        $act->setActivityId($res['activityId']);

        //Now get forward transitions
        //Now get backward transitions

        //Now get roles
        $query = "select `roleId` from ".self::tbl('activity_roles')." where `activityId`=?";
        $result=$dummy->query($query,array($res['activityId']));
        $roles = array();
        while($res = $result->fetchRow()) {
            $roles[] = $res['roleId'];
        }
        $act->setRoles($roles);
        return $act;
    }

    /* Returns an Array of roleIds for the given user */
    function getUserRoles($user)
    {
        $query = "select `roleId` from ".self::tbl('user_roles')." where `user`=?";
        $result=$this->query($query,array($user));
        $ret = Array();
        while($res = $result->fetchRow()) {
            $ret[] = $res['roleId'];
        }
        return $ret;
    }

    /* Returns an Array of asociative arrays with roleId and name
        for the given user */
    function getActivityRoleNames()
    {
        $aid = $this->activityId;
        $query = "select gr.`roleId`, `name` from ".self::tbl('activity_roles')." gar, ".self::tbl('roles')." gr where gar.`roleId`=gr.`roleId` and gar.`activityId`=?";
        $result=$this->query($query,array($aid));
        $ret = Array();
        while($res = $result->fetchRow()) {
          $ret[] = $res;
        }
        return $ret;
    }

    /* Returns the normalized name for the activity */
    function getNormalizedName()
    {
        return self::normalize($this->getName());
    }

    /* Various getters / setters */
    function setName($name) { $this->name=$name; }
    function getName()      { return $this->name;}
    function setDescription($desc) { $this->description=$desc;  }
    function getDescription()      { return $this->description; }

    function getType()  {    return $this->type;  }

    /* Sets if the activity is interactive */
    function setIsInteractive($is)  {    $this->isInteractive=$is;  }

    /* Returns if the activity is interactive */
    function isInteractive()  {    return $this->isInteractive == 'y';  }

    /* Sets if the activity is auto-routed */
    function setIsAutoRouted($is)  {    $this->isAutoRouted = $is;  }

    /* Gets if the activity is auto routed */
    function isAutoRouted()  {    return $this->isAutoRouted == 'y';  }

    /* Sets the processId for this activity */
    function setProcessId($pid)  {    $this->pId=$pid;  }

    /* Gets the processId for this activity*/
    function getProcessId()  {    return $this->pId;  }

    /* Gets the activityId */
    function getActivityId()  {    return $this->activityId;  }

    /* Sets the activityId */
    function setActivityId($id)  {    $this->activityId=$id;  }

    /* Gets array with roleIds asociated to this activity */
    function getRoles()  {    return $this->roles;  }

    /* Sets roles for this activities, shoule receive an
    array of roleIds */
    function setRoles($roles)  {    $this->roles = $roles;  }

    /* Checks if a user has a certain role (by name) for this activity,
      e.g. $isadmin = $activity->checkUserRole($user,'admin'); */
    function checkUserRole($user,$rolename)
    {
        $aid = $this->activityId;
        return $this->getOne("select count(*) from ".self::tbl('activity_roles')." gar, ".self::tbl('user_roles')."gur, ".self::tbl('roles')."gr where gar.`roleId`=gr.`roleId` and gur.`roleId`=gr.`roleId` and gar.`activityId`=? and gur.`user`=? and gr.`name`=?",array($aid, $user, $rolename));
    }

    /**
     * Return the shape of the activity
     *
     * @todo just a name now, could be an object later
     *
    **/
    public function getShape()
    {
        return $this->shape;
    }

    /**
     * Add a role to this activity
     *
    **/
    function addRole($roleId)
    {
        $this->removeRole($roleId);
        $query = "insert into ".self::tbl('activity_roles')." (`activityId`,`roleId`) values(?,?)";
        $this->query($query,array($this->activityId, $roleId));
    }

    /**
     * Remove a role from this activity
     *
    **/
    function removeRole($roleId)
    {
        $query = "delete from ".self::tbl('activity_roles')." where activityId=? and roleId=?";
        $this->query($query,array($this->activityId, $roleId));
    }
}
?>
