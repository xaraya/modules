<?php
/** 
 * PreDispatch Subject
 *
**/
sys::import('xaraya.structures.events.subject');
class WurflPreDispatchSubject extends EventSubject implements ixarEventSubject
{
    protected $subject = 'PreDispatch';   // name of this event subject
}
?>