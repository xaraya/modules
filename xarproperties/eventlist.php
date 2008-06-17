<?php
/**
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage Calendar module
 */

/**
 * EventList Property
 * @author Marc Lutolf (mfl@netspan.ch)
 */

sys::import('modules.query.class.query');

class EventList extends DataProperty
{
    public $id   = 30060;
    public $name = 'eventlist';
    public $desc = 'Event List';
    public $reqmodules = array('calendar');

    protected $localmodule;

    function __construct(ObjectDescriptor $descriptor)
    {
        parent::__construct($descriptor);

        $this->tplmodule = 'calendar';
        $this->filepath   = 'modules/calendar/xarproperties';
    }

    public function showInput(Array $data = array())
    {
        if (empty($data['start'])) {
            $timeargs = xarModAPIFunc('calendar','user','getUserDateTimeInfo');
            $data['start'] = $timeargs['cal_date'];
        }
        if (empty($data['end'])) $data['end'] = $data['start'];
        if (isset($data['localmodule'])) {
            $this->localmodule = $data['localmodule'];
        } else {
            $info = xarRequestGetInfo();
            $this->localmodule = $info[0];
            $data['localmodule'] = $this->localmodule;
        }

        $xartable = xarDB::getTables();
        $q = new Query('SELECT', $xartable['calendar_event']);

        if (empty($data¨['fields'])) {
            // we'll put fields into the output of the query that have status active in the object
            $myobject = xarModApiFunc('dynamicdata','user','getobject', array('name' => 'calendar_event'));
            $data['properties'] = $myobject->getProperties();
            $activefields = array();
            foreach ($data['properties'] as $property) {
                if ($property->getDisplayStatus() != DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE) continue;
                // Ignore fields with dd storage for now
                if ($property->source == 'dynamic_data') continue;
                $q->addfield($property->name);
                $activefields[$property->name] = $property->label;
            }
            $data['fields'] = $activefields;
        }

        $a[] = $q->plt('start_time',$data['start']);
        $a[] = $q->pge('end_time',$data['start']);
        $b[] = $q->plt('start_time',$data['end']);
        $b[] = $q->pge('end_time',$data['end']);
        $c[] = $q->pgt('start_time',$data['start']);
        $c[] = $q->ple('end_time',$data['end']);

        $d[] = $q->pqand($a);
        $d[] = $q->pqand($b);
        $d[] = $q->pqand($c);
        $q->qor($d);

//        $q->qecho();
        if (!$q->run()) return;
        $data['events'] = $q->output();
        $data['keyfieldalias'] = 'name';


        return parent::showInput($data);
    }
}
?>