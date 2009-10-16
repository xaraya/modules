<?php
class Tracker extends Object
{
    private $id;                // current user
    private $now;               // current time
    private $lastvisit = 0;     // time of last visit
    private $numvisits = 0;     // total number of visits
    private $timeonline = 0;    // total time online
    private $visitstart = 0;    // time current visit began
    private $visitend = 0;      // time last visit ended
    private $tracker = array(); // forum specific values
    // user must have been away for x minutes for a visit to be considered new
    private $filter = 15;

    public function __construct($init=false)
    {
        // in theory, this object is only ever instantiated once during the
        // lifetime of this module. Once created, the object is
        // set as a modvar, from which all moduservars are spawned
        // so we make sure we're actually init'ing before setting defaults
        if ($init) {
            $this->init = true;
            $this->setNow(); // set lastvisit to module install time (ie now)
            $this->lastvisit = $this->now;
            $this->visitstart = $this->now;
        }
    }

    public function __destruct()
    {
        if (empty($this->init)) {
            if (xarUserIsLoggedIn() && $this->id == xarUserGetVar('id')) {
                // store the object for this user
                try {
                    xarModUserVars::set('crispbb', 'tracker_object', serialize($this), $this->id);
                } catch (Exception $e) {
                    xarModUserVars::delete('crispbb', 'tracker_object', $this->id);
                    xarModUserVars::set('crispbb', 'tracker_object', serialize($this), $this->id);
                }
            }
        } else {
            // at module init shutdown, just store the object to the mod var
            xarModVars::set('crispbb', 'tracker_object', serialize($this));
        }
    }

    public function setNow($time=0)
    {
        if (empty($time) || !is_numeric($time)) $time = time();
        $this->now = $time;
    }

    public function lastUpdate($fid)
    {
        if (isset($this->ftracker[$fid]))
            return $this->ftracker[$fid];
        return $this->now;
    }

    public function lastRead($fid, $tid=0)
    {
        if (isset($this->tracker[$fid][$tid]['lastread']))
            return $this->tracker[$fid][$tid]['lastread'];
        if (isset($this->tracker[$fid][0]['lastread']))
            return $this->tracker[$fid][0]['lastread'];
        return $this->lastvisit;
    }

    public function markRead($fid, $tid = 0)
    {
        if (empty($tid)) $this->tracker[$fid] = array();
        $this->tracker[$fid][$tid]['lastread'] = $this->now;
    }

    public function seenTids($fid)
    {
        $tids = array();
        if (!empty($this->tracker[$fid])) {
            foreach (array_keys($this->tracker[$fid]) as $tid) {
                if (empty($tid)) continue;
                $tids[] = $tid;
            }
        }
        return $tids;
    }
    public function setUserData()
    {
        if (!xarUserIsLoggedIn()) return true;
        if (empty($this->now)) $this->setNow();
        if (empty($this->id)) $this->id = xarUserGetVar('id');
        $this->name = xarUserGetVar('name', $this->id);
        $this->uname = xarUserGetVar('uname', $this->id);
        if ($this->id == xarUserGetVar('id')) {
            // more than 15 minutes since last visit?
            if ($this->now - ($this->filter*60) > $this->visitend) {
                // set lastvisit to time last visit ended
                $this->lastvisit = $this->visitend;
                // start a new visit
                $this->visitstart = $this->now;
                // increment visit count
                $this->numvisits++;
            } else {
            // current visit
                // increment time online
                $this->timeonline += $this->now - $this->visitend;
            }
            xarVarSetCached('Blocks.crispbb', 'tracker_object', $this);
        }
        return true;
    }

    public function getUserPanelInfo()
    {
        if (!xarUserIsLoggedIn()) return false;
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'uname' => $this->uname,
            'lastvisit' => $this->lastvisit,
            'numvisits' => $this->numvisits,
            'timeonline' => $this->timeonline,
            'visitstart' => $this->visitstart,
            'visitend' => $this->visitend,
            'now' => $this->now,
            'onlinestamp' => $this->now - $this->timeonline
        );
    }

    public function __wakeup()
    {
        $this->filter = xarModVars::get('crispbb', 'visit_timeout');
        $this->ftracker = unserialize(xarModVars::get('crispbb', 'ftracking'));
        $this->setUserData();
    }

    public function __sleep()
    {
        // set last visit before saving
        $this->visitend = $this->now;
        return array('id','lastvisit', 'numvisits', 'timeonline', 'tracker', 'visitstart', 'visitend');
    }
}
?>