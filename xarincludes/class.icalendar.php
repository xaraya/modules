<?php

/**
 * File: $Id$
 *
 * iCalendar base class
 *
 * @package unassigned
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage calendar
 * @link  link to information for the subpackage
 * @author Roger Raymond <iansym@xaraya.com> 
 */

/**
 * iCalendar base class
 *
 * Base class for the iCalendar object.  Contains common values and
 * other calendar objects.
 *
 * @package calendar
 * @author Roger Raymond <iansym@xaraya.com> 
 */
 
class iCalendar
{

    /**
     * Purpose: This property defines the calendar scale used for the
     * calendar information specified in the iCalendar object.
     */
    var $calscale; 
    /**
     * Purpose: To specify the language for text values in a property or
     * property parameter.
     */
    var $language;
    /**
     * Purpose: This property specifies the identifier for the product that
     * created the iCalendar object.
     */
    var $prodid;
    /**
     * Purpose: This property defines the iCalendar object method associated
     * with the calendar object.
     */
    var $method;
    /**
     * Purpose: This property specifies the identifier corresponding to the
     * highest version number or the minimum and maximum range of the
     * iCalendar specification that is required in order to interpret the
     * iCalendar object.
     */
    var $version;
    
    /**
     *  An array containing VEVENT objects for this icalendar object
     */
    var $vevent;
    /**
     *  An array containing VTODO objects for this icalendar object
     */
    var $vtodo;
    /**
     *  An array containing VJOURNAL objects for this icalendar object
     */
    var $vjournal;
    /**
     *  An array containing VTIMEZONE objects for this icalendar object
     */
    var $vtimezone;
    /**
     *  An array containing VFREEBUSY objects for this icalendar object
     */
    var $vfreebusy;
    
    /**
     * Some internal counters
     */
    var $__vevent_count;
    var $__vtodo_count;
    var $__vjournal_count;
    var $__vtimezone_count;
    var $__vfreebusy_count;
    
    function iCalendar()
    {
        // initialize our properties
        $this->calscale = 'GREGORIAN';
        $this->language = NULL;
        $this->prodid   = '-//Xaraya//NONSGML Xaraya Calendar Version 1.0//EN';
        $this->method   = NULL;
        $this->version  = NULL;
        
        // initialize our arrays
        $this->vevent    = array();
        $this->vtodo     = array();
        $this->vjournal  = array();
        $this->vtimezone = array();
        $this->vfreebusy = array();
        
        // do some initial maintenance for the counters
        $this->__update_vevent_count();
        $this->__update_vtodo_count();
        $this->__update_vjournal_count();
        $this->__update_vfreebusy_count();
        //$this->__update_vtimezone_count();
    }
    
    /**
     * Set CALSCALE
     *
     * Sets the CALSCALE Property for the iCalendar object
     *
     * @author  Roger Raymond <iansym@xaraya.com>
     * @access  public
     * @param   string calscale value [GREGORIAN,etc.]
     * @return  void
     * @throws  list of exception identifiers which can be thrown
     * @todo    <iansym> <1> input validation
     */
    function set_calscale($v) 
    {
        $this->calscale =& $v;
    }
    
    function get_calscale() 
    {
        return $this->calscale;
    }
        
    function set_language($v) 
    {
        $this->language =& $v;
    }
    
    function get_language() 
    {
        return $this->language;
    }
    
    function set_prodid($v)
    {
        $this->prodid =& $v;
    }
    
    function get_prodid()
    {
        return $this->prodid;
    }
    
    function set_method($v)
    {
        $this->method =& $v;
    }
    
    function get_method()
    {
        return $this->method;
    }
    
    function set_version($v)
    {
        $this->version =& $v;
    }
    
    function get_version()
    {
        return $this->version;
    }
    
    function create_vevent()
    {
        // we'll be needing this class
        require_once 'class.icalendar_event.php';
        
        // create the new VEVENT Object for this calendar object
        $this->vevent[$this->__vevent_count] =& new iCalendar_Event();
        
        /*
        if($this->__vevent_count > 0) {
            // make linked list elements if we have more than one event
            $this->vevent[$this->__vevent_count - 1]->next =& $this->vevent[$this->__vevent_count];
            $this->vevent[$this->__vevent_count]->prev     =& $this->vevent[$this->__vevent_count - 1];
        } else {
            // just link the same event recurssively
            $this->vevent[$this->__vevent_count]->next =& $this->vevent[$this->__vevent_count];
            $this->vevent[$this->__vevent_count]->prev =& $this->vevent[$this->__vevent_count];
        }
        */
        
        // update the event count
        $this->__update_vevent_count();        
    }
    
    function __update_vevent_count()
    {
        $this->__vevent_count = count($this->vevent);
    }
    
    function num_vevent()
    {
        return $this->__vevent_count;
    } 
    
    function create_vtodo()
    {
        // we'll be needing this class
        require_once 'class.icalendar_todo.php';
        
        // create the new VTODO Object for this calendar object
        $this->vtodo[$this->__vtodo_count] =& new iCalendar_Todo();
        
        $this->__update_vtodo_count();        
    }   
     
    function __update_vtodo_count()
    {
        $this->__vtodo_count = count($this->vtodo);
    }
    
    function num_vtodo()
    {
        return $this->__vtodo_count;
    } 
    
    function create_vjournal()
    {
        // we'll be needing this class
        require_once 'class.icalendar_journal.php';
        
        // create the new vjournal Object for this calendar object
        $this->vjournal[$this->__vjournal_count] =& new iCalendar_Journal();
        
        $this->__update_vjournal_count();        
    }
    
    function __update_vjournal_count()
    {
        $this->__vjournal_count = count($this->vjournal);
    }
    
    
    function num_vjournal()
    {
        return $this->__vjournal_count;
    }
    
    function create_vfreebusy()
    {
        // we'll be needing this class
        require_once 'class.icalendar_freebusy.php';
        
        // create the new vfreebusy Object for this calendar object
        $this->vfreebusy[$this->__vfreebusy_count] =& new iCalendar_Freebusy();
        
        $this->__update_vfreebusy_count();        
    }
    
    function __update_vfreebusy_count()
    {
        $this->__vfreebusy_count = count($this->vfreebusy);
    }
    
    
    function num_vfreebusy()
    {
        return $this->__vfreebusy_count;
    }   
}

?>