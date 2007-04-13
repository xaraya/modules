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
  public $id = 0;

  /*!
  Loads a process form the database
  */
  function getProcess($id)
  {
    $query = "select * from ".self::tbl('processes')."where `id`=?";
    $result = $this->query($query,array($id));
    if(!$result->numRows()) return false;
    $res = $result->fetchRow();
    $this->name = $res['name'];
    $this->description = $res['description'];
    $this->normalizedName = $res['normalized_name'];
    $this->version = $res['version'];
    $this->id = $res['id'];
  }

  /*!
  Gets the normalized name of the process
  */
  function getNormalizedName()
  {
    return $this->normalizedName;
  }

  /*!
  Gets the process name
  */
  function getName()
  {
    return $this->name;
  }

  /*!
  Gets the process version
  */
  function getVersion()
  {
    return $this->version;
  }

  /*!
  Gets information about an activity in this process by name,
  e.g. $actinfo = $process->getActivityByName('Approve CD Request');
    if ($actinfo) {
      $some_url = 'tiki-g-run_activity.php?activityId=' . $actinfo['activityId'];
    }
  */
  function getActivityByName($actname)
  {
    // Get the activity data
    $query = "select * from ".self::tbl('activities')."where `id`=? and `name`=?";
    $id = $this->id;
    $result = $this->query($query,array($id,$actname));
    if(!$result->numRows()) return false;
    $res = $result->fetchRow();
    return $res;
  }

}

?>
