<?php
/**
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage Calendar module
 */

/**
 * Calendar Property
 * @author Marc Lutolf (mfl@netspan.ch)
 */

/* Include parent class */
sys::import('modules.dynamicdata.class.properties');
sys::import('modules.query.class.query');

class CalendarDisplayProperty extends DataProperty
{
    public $id         = 30081;
    public $name       = 'calendardisplay';
    public $desc       = 'Calendar Display';
    public $reqmodules = array('calendar');
    
    public $timeframe  = 'week';
    public $owner      ;

    function __construct(ObjectDescriptor $descriptor)
    {
        parent::__construct($descriptor);

        // Set for runtime
        $this->tplmodule = 'calendar';
        $this->filepath   = 'modules/calendar/xarproperties';
        $this->owner = xarSession::getVar('role_id');
    }

    public function showInput(Array $data = array())
    {
        if (empty($data['role_id'])) $data['role_id'] = $this->owner;
        if (empty($data['timeframe'])) $data['timeframe'] = $this->timeframe;
        $this->template = 'calendardisplay_' . $data['timeframe'];
        $this->includes($data['timeframe']); 

        $data = array_merge($data, $this->setup($data['timeframe'],$data['role_id']));
        return parent::showInput($data);
    }

    public function includes($timeframe)
    {
        switch ($timeframe) {
            case 'week':
                include_once(CALENDAR_ROOT.'Week.php');
                sys::import("modules.calendar.class.Calendar.Decorator.event");
                sys::import("modules.calendar.class.Calendar.Decorator.weekevent");
                break;
            case 'month':
                include_once(CALENDAR_ROOT.'Month/Weekdays.php');
                include_once(CALENDAR_ROOT.'Day.php');
                sys::import("modules.calendar.class.Calendar.Decorator.event");
                sys::import("modules.calendar.class.Calendar.Decorator.monthevent");
                break;
            case 'year':
                include_once(CALENDAR_ROOT.'Year.php');
                break;
        }
        sys::import("modules.calendar.class.Calendar.Decorator.Xaraya");
    }
    
    public function setup($timeframe, $role_id)
    {
        $data = xarModAPIFunc('calendar','user','getUserDateTimeInfo');
        switch ($timeframe) {
            case 'week':
                $WeekEvents = new Calendar_Week($data['cal_year'],$data['cal_month'],$data['cal_day'],CALENDAR_FIRST_DAY_OF_WEEK);
                $start_time = $WeekEvents->thisWeek;
                $end_time = $WeekEvents->nextWeek;

                $events = $this->getEvents($start_time, $end_time, $role_id); 

                $WeekDecorator = new WeekEvent_Decorator($WeekEvents);
                $WeekDecorator->build($events);
                $data['Week'] =& $WeekDecorator; // pass a reference to the object to the template
                $data['cal_sdow'] = CALENDAR_FIRST_DAY_OF_WEEK;
                break;
            case 'month':
                $MonthEvents = new Calendar_Month_Weekdays(
                    $data['cal_year'],
                    $data['cal_month'],
                    xarModVars::get('calendar', 'cal_sdow'));
                $start_time = $MonthEvents->getTimestamp();
                $MonthEvents = new Calendar_Month_Weekdays(
                    $data['cal_year'],
                    $data['cal_month'] + 1,
                    xarModVars::get('calendar', 'cal_sdow'));
                $end_time = $MonthEvents->getTimestamp();

                $events = $this->getEvents($start_time, $end_time, $role_id); 

                $MonthDecorator = new MonthEvent_Decorator($MonthEvents);
                $MonthDecorator->build($events);
                $data['Month'] =& $MonthDecorator;
                break;
            case 'year':
                $Year = new Calendar_Year($data['cal_year']);
                $Year->build(); // TODO: find a better way to handle this
                $data['Year'] =& $Year;
                $data['cal_sdow'] = CALENDAR_FIRST_DAY_OF_WEEK;
                break;
        }
        return $data;
    }

    public function getEvents($start_time, $end_time, $role_id)
    {
        // get all the events. need to improve this query and combine it with the query in the template
        $xartable = xarDB::getTables();
        $q = new Query('SELECT', $xartable['calendar_event']);
        $q->ge('start_time', $start_time);
        $q->lt('start_time', $end_time);
        $q->eq('role_id',$role_id);
//        $q->qecho();
        if (!$q->run()) return;
        return $q->output();
    }
}

?>