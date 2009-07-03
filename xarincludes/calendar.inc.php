<?php
/*
###############################################################################

Created By  : Dan Bemowski
E-mail      : dbemowsk@charter.net
File        : calendar.inc.php

License     : This program is free software; you can redistribute it and/or
              modify it under the terms of the GNU General Public License
              as published by the Free Software Foundation; either version 2
              of the License, or (at your option) any later version.

              This program is distributed in the hope that it will be useful,
              but WITHOUT ANY WARRANTY; without even the implied warranty of
              MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
              GNU General Public License for more details.

###############################################################################

This is a PHP class used to create and display calendars in many forms.

The class can display calendars in the following formats:

Small month plain
    This will display a small calendar of a month.

Small month with events.
    This will display a small calendar of a month.  An array of dates can be given
    to indicate events.  The dates for these events will be highlighted with
    mouseover titles to display the events.

Large month plain
    This will display a plain full page calendar of a month.

Large month with previous and next month.
    This will display a plain full page calendar with small month plain calendars
    showing the previous month on the left of the header and the next month on
    the right of the header.

Large month with events.
    This will display a full page calendar of a month.  An array of dates can be
    given to indicate events.  The date cells for these events will show the events
    for that day.

Large month with previous and next month and events.
    This will display a full page calendar with small month calendars showing
    the previous month on the left of the header and the next month on the right
    of the header.  An array of dates can be given to indicate events.  The date
    cells for these events will show the events for that day.  The dates for the
    events of the small previous and next month calendars in the header will be
    highlighted with mouseover titles to display the events.

Full year plain
    This will display a full page calendar for an entire year showing all months
    in a small plain format.

Full year with events
    This will display a full page calendar of an entire year.  An array of dates
    can be given to indicate events.  The dates for these events will be highlighted
    with mouseover titles to display the events for a given month.
Weekly with events
    This will display a weekly calendar showing every quarter hour in the day.
*/

class ieventsCalendar {
    //********************* Start of variable declarations *********************

    //*********** Class variables that apply to all calendar formats ***********

	/*
	Declare a variable to define the calendar id
	*/
	var $cid = 0;

    /*
    Declare the main array to hold the calendar events.
    $events[]['date'] is the date and time of the event in unix time format.
    $events[]['event'] holds the event information.
    */
    var $events = array();

    // Events on each day, indexed as 'YYYYMMDD'
    var $day_events = array();

    // Links to events for any given day.
    // Only days that have events will be included.
    var $day_urls = array();

    /*
    Declare a variable indicating the day of the week that the calendar starts on.
    0 = Sunday
    1 = Monday
    */
    var $startingDOW;

    /*
    Declare a variable to indicate the calendar format.
    smallMonth
    largeMonth
    quarterYear
    fullYear
    weekly
    daily
    */
    var $calFormat;

    //Declare a boolean variable to determine whether to display events.
    var $displayEvents;

    /*
    Declare a boolean variable to determine if previous and next links are used
    when displaying a calendar.  This will show arrows for the previous and next
    months on month calendars, and arrows for the previous and next year for the
    full year calendar.
    */
    var $displayPrevNextLinks;

    /*
    Declare variables to hold the images for the previous and next arrows that
    are used for the previous and next links.  If these are not defined, the arrows
    are formatted as << for previous and >> for next.
    */
    var $largeFormatPrevArrow;
    var $largeFormatNextArrow;
    var $fullYearPrevArrow;
    var $fullYearNextArrow;

    //Declare a variable to tell how the month is displayed.  Values are long and short.
    var $monthFormat = 'long';
    //Declare a variable that will hold the day to display.
    var $calDay;
    //Declare a variable that will hold the week to display (first day of week) .
    var $calWeek;
    //Declare a variable that will hold the month to display.
    var $calMonth;
    //Declare a variable that will hold the quarter to display.
    var $calQuarter;
    //Declare a variable that will hold the year to display.
    var $calYear;
    //Declare a boolean variable to determine weather the current day is highlighted.
    var $showToday;
    //Declare a variable to set minute length of hour divisions.
    var $quanta;

    //****** Class variables that apply only to the small calendar format ******

    var $showTitle;
    /*
    This defines the border width of the table cells for the small month format.
    */
    var $smallMonthBorder;
    //Declare color format variables.
    var $colorSmallFormatDayOfWeek;
    var $colorSmallFormatDateText;
    var $colorSmallFormatDateHighlight;
    var $colorSmallFormatHeaderText;
    var $colorSmallFormatWeekendHighlight;

    //****** Class variables that apply only to the large calendar format ******

    /*
    Declare a boolean variable to determine if previous and next month calendars
    are displayed. This only applies to the large month calendar format.
    */
    var $displayPrevNext;
    //Declare a variable to hold the background image for large format calendars.
    var $backgroundLargeFormatImage;
    /*
    Declare a variable that tells how a background image is repeated.
    repeat - Tiles the image both horizontally and vertically.
    repeat-x - Tiles the image in the horizontal direction only.
    repeat-y - Tiles the image in the vertical direction only.
    no-repeat - No repeating takes place; only one copy of the image is displayed.
    */
    var $backgroundImageRepeat;
    /*
    Declare a boolean variable to determine weather the week numbers are displayed
    for the large month calendar.
    */
    var $showWeek;
    /*
    Declare a variable to indicate the large calendar day of the week format.
    short - eg. Sun, Mon,Tue...
    long - eg. Sunday, Monday, Tuesday...
    */
    var $DOWformat;
    /*
    Declare a variable to determine the alignment of the large format calendar
    on the page. The options are left, center and right
    */
    var $largeFormatAlign;
    //Declare a variable for the height of the cell for large format calendars.
    var $largeCellHeight;
    //Declare color format variables.
    var $colorLargeFormatDayOfWeek;
    var $colorLargeFormatDateText;
    var $colorLargeFormatDateHighlight;
    var $colorLargeFormatHeaderText;
    var $colorLargeFormatEventText;
    var $colorLargeFormatWeekendHighlight;

    //**** Class variables that apply only to the full year calendar format ****

    /*
    Declare a boolean variable to determine weather the year is shown for small
    month calendars.  This is typically used when displaying the full year calendars.
    */
    var $displayYear;

    //****** Class variables that apply only to the weekly calendar format *****

    //Declare a variable for the height of the weekly format calendars.
    var $weekCalendarHeight;
    //Declare a variable for the height of the cell for weekly format calendars.
    var $weekCellHeight;
    /*
    Declare a boolean variable that tells weather or not to highlight the work
    hours for the week view.
    */
    var $showWorkHours;
    /*
    Declare a variable that defines the start time of a work day.  This is only
    relevant when showWorkHours is set to true.
    */
    var $workStartHour;
    var $workStartMinute;
    //AM = 0 : PM - 1
    var $workStartAmPm;
    /*
    Declare a variable that defines the end time of a work day.  This is only
    relevant when showWorkHours is set to true.
    */
    var $workEndHour;
    var $workEndMinute;
    //AM = 0 : PM - 1
    var $workEndAmPm;
    //Declare color format variables.
    var $colorWeekFormatHeaderText;
    var $colorWeekFormatDayOfWeek;
    var $colorWeekFormatEventText;

    // List of day names, zero-indexed, with 0=Sunday
    var $dayNames;
    var $dayNamesShort;
    var $dayNamesXShort;
    var $dayNamesXXShort;

    // List of month names, one-indexed, with 1=January
    var $monthNames;
    var $monthNamesShort;

    // boolean whether show month name as link 
    var $linkMonth;

    //********************** End of variable declarations **********************

    /*
        Calendar class constructor.
    */
    function ieventsCalendar() {
        // Set up the default day names.
        if (!isset($this->dayNames)) $this->dayNames = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
        if (!isset($this->dayNamesShort)) $this->dayNamesShort = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
        if (!isset($this->dayNamesXShort)) $this->dayNamesXShort = array('Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa');
        if (!isset($this->dayNamesXXShort)) $this->dayNamesXXShort = array('S', 'M', 'T', 'W', 'T', 'F', 'S');

        // Set up the default month names.
        if (!isset($this->monthNames)) {
            for($i=1; $i<=12; $i++) $this->monthNames[$i] = date('F', mktime(0, 0, 0, $i, 1, 2007));
        }
        if (!isset($this->monthNamesShort)) {
            for($i=1; $i<=12; $i++) $this->monthNamesShort[$i] = date('M', mktime(0, 0, 0, $i, 1, 2007));
        }

        include_once dirname(__FILE__) . '/calendar.conf.php';
    }

    /*
        Add a new event to the events array.
        The arguments passed are date and event.
        // TODO: Accept further details:
        // * category or colour flag
        // * URL for the day
        // TODO: build a hash table of dates for easy lookup when displaying.
    */
    function addEvent($date, $title, $url = '', $allday = true, $day_url = '') {
        $event_details = array();

        // Add the event to the array.
        $event_details['date'] = $date;
        $event_details['event'] = $title;
        $event_details['url'] = $url;
        $event_details['allday'] = (empty($allday) ? false : true);

        // Add the event to the overall array.
        $this->events[] = $event_details;

        // Add the event to the day_events array also.
        $this->day_events[date('Ymd', $date)][] = $event_details;

        // if there is a URL supplied for the day, then add that to our list.
        if (!empty($day_url) && empty($this->day_urls[date('Ymd', $date)])) {
            $this->day_urls[date('Ymd', $date)] = $day_url;
        }
    }

    /*
    This function for the class return a <div> tag containing the events for the
    day defined by $date.  This function is used for large format calendars.
    */

    function getEvents($date, $cal, $highlightDate = false) {
        // Set a boolean variable to determine whether events were displayed or not.
        $displayed = false;

        $day_text_key = date('Ymd', $date);

		// Open a wrapper for the event.
        switch ($cal) {
            case 'smallMonth':
                $events = '';
                break;

            case 'largeMonth':
                // Display the event with full text
                // TODO: if there are any events displayed, then the day number should be a
                // hyperlink to a summary of that day (or the single event if there is only one event)
                $events = '<div class="ievents-day-wrapper">';
                $events .= '    <div class="ievents-day-number">';

                // If there is a URL for the day, then wrap the day number in that URL.
                if (!empty($this->day_urls[$day_text_key])) {
                    $events .= '<a href="' . $this->day_urls[$day_text_key] . '" rel="#ievents-day-' . $day_text_key . '" title="' . xarML("View this day's events") . '">' . date('j', $date) . '</a>';
                } else {
                    $events .= date('j', $date);
                }

                // At the start of each week, display the week number.
                if (date('w', $date) == $this->startingDOW) {
                    $events .= ' <span class="ievents-week-number"> ' . xarML('Week') . ' ' . date('W', $date) . '</span>';
                }

                $events .= '</div>' . "\n<ul>\n";
                break;

            case 'weekly':
                //display the event with full text
                $events = "\n<ul>";
                break;

            case 'daily': // TODO: daily format
                //display the event with full text
                $events = '<div style="color: ' . $this->colorWeekFormatEventText . ';">' . "\n<ul>\n";
                break;

            default:
                $error = 'Invalid calendar format passed to the getEvents function.';
                $this->displayError($error);
        }

		$numEvents = 0;

        // Check if any events are defined.
        if (isset($this->events) && $this->displayEvents) {
            // Cycle through the events that are defined.
            if (isset($this->day_events[$day_text_key])) {
				if ($cal != 'smallMonth' && $cal != 'weekly' && $cal != 'daily') {
	                $events .= '<div id="ievents-day-' . $day_text_key . '"><ul>';
				}
                foreach($this->day_events[$day_text_key] as $event) {
                    // An event was found so determine the calendar format we need to display.
                    $numEvents++;
                    switch ($cal) {
                        case 'smallMonth':
/*
                            // Check if this is the first event displayed.
                            if ($displayed) {
                                // Display the event with hover titles on a new line.
                                $events .= "\n" . date('h:i A', $event['date']) . ' - ' . $event['event'];
                            } else {
                                // Display the event with hover titles.
                                $events .= date('h:i A', $event['date']) . ' - ' . $event['event'];
                                $displayed = true;
                            }
*/
                            $displayed = true;
                            break;

                        case 'largeMonth':
                        case 'weekly':
                            // Put each event into a list item.
                            // TODO: handle html entities.
                            // Display the event with full text.
                            $events .= "            <li>\n";
							$events .= "                <strong>"  . xarLocaleGetFormattedTime('short', $event['date']) . "</strong><br />\n";
                            if (!empty($event['url'])) {
                                $events .= '            <a href="' . $event['url'] . '">' . $event['event'] . '</a>' . "\n";
                            } else {
                                $events .= $event['event'] . "\n";
                            }
                            $events .= "            </li>\n";
                            break;
                        case 'daily';
                        	$qstart = $date;
							$qend = $qstart + ($this->quanta * 60);
							$eventlocaletime =  strtotime(xarLocaleGetFormattedDate('short', $event['date']) . ' ' . xarLocaleGetFormattedTime('short', $event['date']));

							if (($eventlocaletime >= $qstart) && ($eventlocaletime < $qend)) {
	                            $events .= "            <li>\n";
	                            if (!empty($event['url'])) {
	                                $events .= '            <a href="' . $event['url'] . '">' . $event['event'] . '</a>' . "\n";
	                            } else {
	                                $events .= $event['event'] . "\n";
	                            }
	                            $events .= "            </li>\n";
							} else {
								// decrement the event counter
								$numEvents--;
							}
                            break;
                    }
                }
				if ($cal != 'smallMonth' && $cal != 'weekly' && $cal != 'daily') {
	                $events .= "</ul>\n";
	            }
            }
        }

        // Close the wrapper.
        switch ($cal) {
            case 'smallMonth':
            	// We're only interested in the number of events here, which is why we didn't do anything with this format before
                if ($this->displayEvents) {
                    if ($displayed) {
                        // Continue to show the display the event with hover titles.
                        $events .= '<a href="';
						if ($this->cid != 0) {
	                        $events .= xarModURL('ievents','user','view',array('cid' => $this->cid, 'startdate' => date('Ymd', $date), 'enddate' => date('Ymd', $date), 'group' => 'day', 'range' => 'custom')) . '" title="';
						} else {
	                        $events .= xarModURL('ievents','user','view',array('startdate' => date('Ymd', $date), 'enddate' => date('Ymd', $date), 'group' => 'day', 'range' => 'custom')) . '" title="';
						}
                        if ($numEvents == 1) {
                        	$events .= xarML('#(1) Event', $numEvents);
                        } else {
                        	$events .= xarML('#(1) Events', $numEvents);
                        }
                        $events .= '">' . date('j', $date) . '</a>';
                    } else {
                        // No events were added to the title so just display the date.
                        $events = date('j', $date);
                    }
                } else {
                    // display the event without hover titles
                    $events = date('j', $date);
                }
                break;

            case 'largeMonth':
            case 'weekly':
            case 'daily':
                $events .= '</div>';
                break;
        }
        if (($cal == 'weekly' || $cal == 'daily') && $numEvents == 0) {
			$events = '&nbsp;';
		}
        return $events;
    }


    /*
        Return the month name.
    */
    function getMonth($m, $y) {
        // Get the name of the month based on the monthFormat variable.
        $m = (int)$m;
        switch (strtolower($this->monthFormat)) {
            case 'long':
                if (isset($this->monthNames[$m])) {
                    $month = $this->monthNames[$m];
                } else {
                    $month = 'Unknown';
                }
                break;
            case 'short':
                if (isset($this->monthNamesShort[$m])) {
                    $month = $this->monthNamesShort[$m];
                } else {
                    $month = 'Unk';
                }
                break;
            default:
                $month = '';
        }
        return $month;
    }


    /*
        Return the day of the week.
        This is actually the day at the head of the month view
        columns, numbered 1 to 7, so the starting day of the week
        is taken into account.
    */
    function getDOW($dow) {
        $dow = $dow + $this->startingDOW - 1;

        // Get the name of the day based on the DOWformat variable.
        switch (strtolower($this->DOWformat)) {
            case 'long':
                if (isset($this->dayNames[$dow % 7])) {
                    $weekday = $this->dayNames[$dow % 7];
                } else {
                    $weekday = 'Unknown';
                }
            
                break;

            case 'short':
                if (isset($this->dayNamesShort[$dow % 7])) {
                    $weekday = $this->dayNamesShort[$dow % 7];
                } else {
                    $weekday = 'Unk';
                }
            
                break;
            case 'xshort':
                if (isset($this->dayNamesXShort[$dow % 7])) {
                    $weekday = $this->dayNamesXShort[$dow % 7];
                } else {
                    $weekday = 'Un';
                }
            
                break;

            case 'xxshort':
                if (isset($this->dayNamesXXShort[$dow % 7])) {
                    $weekday = $this->dayNamesXXShort[$dow % 7];
                } else {
                    $weekday = '?';
                }
            
                break;

            default:
                $weekday = '';
        }

        return $weekday;
    } // End function getDOW()

    /*
    This function for the class will display a month in small format. The inputs
    to the function are as follows:

    $m - Month to display.
    $y - Year to display.
    $np - A boolean value indicating weather to display the links for the previous and next months.

    */

    function showSmallMonth($m, $y, $np = false, $showyear = true, $linkMonth = false) {
        // Calculate the number of days in the month
        $days = date('t',mktime(0,0,0,$m, 1, $y));
        // Calculate the day of the week that the month starts on
		$startDay = (date('w', mktime(0, 0, 0, $m, 1, $y)) - $this->startingDOW + 7) % 7;
        // set the column offset for the starting day of the week.
        $offset = '';
        if ($startDay > 0) {
            $offset .= '<td width="14%" colspan="' . $startDay . '">&nbsp;</td>' . "\n";
        } else if ($startDay == -1) {
            $offset .= '<td width="14%" colspan="6">&nbsp;</td>' . "\n";
            $startDay = 6;
        }

        if (mktime(0, 0, 0, date('m'), 1, date('Y')) == mktime(0, 0, 0, $m, 1, $y)) {
            $today = date('j');
        } else {
            $today = 0;
        }

        // Get the textual representation of the month
        $month = $this->getMonth($m, $y);

        // Calculate the previous month and year for the header link.
        if (($m - 1) == 0) {
            $prevMonth = 12;
            $prevYear = $y - 1;
        } else {
            $prevMonth = $m - 1;
            $prevYear = $y;
        }
        //Calculate the next month and year for the header link.
        if (($m + 1) == 13) {
            $nextMonth = 1;
            $nextYear = $y + 1;
        } else {
            $nextMonth = $m + 1;
            $nextYear = $y;
        }

        //Get the current date to display if the month showing is the current month.
        if (mktime(0, 0, 0, date("m"), 1, date("Y")) == mktime(0, 0, 0, $m, 1, $y)) {
            $day = date("j");
        } else {
            $day = 0;
        }
        if ($showyear) {
            $year = $y;
        } else {
            $year = "";
        }
		$prevLink = '';
		$nextLink = '';

        $data = array(
        	'm' => $m,
        	'y' => $y,
        	'np' => $np,
        	'cid' => $this->cid,
        	'showyear' => $showyear,
            'showtitle' => $this->showTitle,
        	'linkmonth' => $linkMonth,
        	'month' => $month,
        	'year' => $year,
        	'prevmonth' => $prevMonth,
        	'prevyear' => $prevYear,
			'prevlink' => $prevLink,
			'nextlink' => $nextLink,
        	'days' => $days,
        	'startday' => $startDay,
        	'startdate' => date('Ymd', mktime(0, 0, 0, $m, 1, $y)),
        	'enddate' => date('Ymd', mktime(0, 0, 0, $m, $days, $y)),
        	'today' => $today,
        	'startingdow' => $this->startingDOW,
        	'offset' => $offset,
        	'cal' => $this
        	);

		$daysofweek = array();
        for ($i = 1; $i <= 7; $i++) {
            $daysofweek[] = $this->getDOW($i);
        }

		$data['daysofweek'] = $daysofweek;

        //Now output the calendar
        return xarTplObject('ievents', 'calendar', 'smallmonth', $data);

    } //End function showSmallMonth()


    /*
    This function for the class will display a month in large format. The inputs
    to the function are as follows:

    $m - Month to display.
    $y - Year to display.
    */

    function showLargeMonth($m, $y) {
        // Calculate the number of days in the month
        $days = date('t', mktime(0, 0, 0, $m, 1, $y));

        // Calculate the day of the week that the month starts on
        $startDay = (date('w', mktime(0, 0, 0, $m, 1, $y)) - $this->startingDOW + 7) % 7;

        // Get the currrent date to display if the month showing is the current month.
        if (mktime(0, 0, 0, date('m'), 1, date('Y')) == mktime(0, 0, 0, $m, 1, $y)) {
            $today = date('j');
        } else {
            $today = 0;
        }

        // Create the header
        $data = array(
        	'm' => $m,
        	'y' => $y,
        	'days' => $days,
        	'startday' => $startDay,
        	'today' => $today,
        	'cal' => $this,
        	'startingdow' => $this->startingDOW
        	);

		$daysofweek = array();
        for ($i = 1; $i <= 7; $i++) {
            $daysofweek[] = $this->getDOW($i);
        }

		$data['daysofweek'] = $daysofweek;

        return xarTplObject('ievents', 'calendar', 'largemonth', $data);
    } // End function showLargeMonth()


    function showQuarterYear($q, $y, $np = false) {
        //Get the previous and next years for the year selection links.

		$startmonth = ($q * 3) - 2;
		$endmonth = $startmonth + 2;

		$data = array(
			'q' => $q,
			'y' => $y,
			'startmonth' => $startmonth,
			'endmonth' => $endmonth
		);

		$months_output = array();
        for ($i = $startmonth; $i <= $endmonth; $i++) {
			$months_output[$i] = $this->showSmallMonth($i, $y, false, false, true);
		}

		$data['months_output'] = $months_output;

        return xarTplObject('ievents', 'calendar', 'quarter', $data);
    } //End function showFullYear()

    function showFullYear($y, $np = false) {
        //Get the previous and next years for the year selection links.

		$data = array(
			'y' => $y
		);

		$months_output = array();
        for ($i = 1; $i <= 12; $i++) {
			$months_output[$i] = $this->showSmallMonth($i, $y, false, false, true);
		}

		$data['months_output'] = $months_output;

        return xarTplObject('ievents', 'calendar', 'fullyear', $data);
    } //End function showFullYear()

    /*
    This function is used to show a weekly view of the calendar.
    */

    function showWeekView($date, $linkWeek = false) {
        //Define one day in seconds (60 seconds * 60 minutes * 24 hours).
        $oneDay = 86400;
        //Determine the first day of the week that the day falls on.
        $firstDayOfWeek = $date - (date("w",$date) * $oneDay); // ;

        $highlightWorkHours = false;
        $toggle = 0;


		$data = array(
			'date' => $date,
			'firstdayofweek' => $firstDayOfWeek,
			'oneday' => $oneDay,
			'startingdow' => $this->startingDOW,
			'cal' => $this
		);


		$dayheaders = array();
		for ($i = 1; $i <= 7; $i++) {
			$dayheaders[$i] = array(
				'd' => date("j", ($firstDayOfWeek + ($oneDay * ($i - 1)))),
				'day' => $this->getDOW($i)
			);
		}

		$data['dayheaders'] = $dayheaders;

        return xarTplObject('ievents', 'calendar', 'week', $data);
    } //End function showWeekView()

    /*
    This function is used to show a day view of the calendar.
    */

    function showDayView($date, $linkDay = false) {
        //Determine what week of the year the date falls on.
        $week = date("W", $date);
        //Determine what day of the week the date falls on.
        $dayOfWeek = date("w",$date);
        //Define when the day ends.
        $dayEnd = $date + 86400;

        $prevLink = "";
        $nextLink = "";
        $quanta = $this->quanta * 60;  // quanta stored as minutes

        $highlightWorkHours = false;
        $toggle = 0;

        if (isset($this->workStartHour) && isset($this->workStartMinute) && isset($this->workStartAmPm)) {
            // Determine the quarter hour of the starting work time for highlighting the
            // hours in a work day.
            $unixWorkStartTime = mktime(($this->workStartHour + ($this->workStartAmPm * 12)), $this->workStartMinute, 0, date("m", $date), date("j", $date), date("Y", $date));

            // Determine the quarter hour of the ending work time for highlighting the
            // hours in a work day.

            $unixWorkEndTime = mktime(($this->workEndHour + ($this->workEndAmPm * 12)), $this->workEndMinute, 0, date("m", $date), date("j", $date), date("Y", $date));
            $highlightWorkHours = true;
        } else {
            $unixWorkStartTime = mktime(0, 0, 0, date("m", $date), date("j", $date), date("Y", $date));
            $unixWorkEndTime = mktime(0, 0, 0, date("m", $date), date("j", $date), date("Y", $date));
        }

        // Create the header
        $data = array(
			'date' => $date,
			'dayend' => $dayEnd,
			'quanta' => $quanta,
			'toggle' => $toggle,
			'unixworkstarttime' => $unixWorkStartTime,
			'unixworkendtime' => $unixWorkEndTime,
			'highlightworkhours' => $highlightWorkHours,
        	'cal' => $this,
        	);


        return xarTplObject('ievents', 'calendar', 'day', $data);
    } //End function showDayView()


    /*
    This function for the class outputs the calendar based on the parameters given.
    */
    function display() {
        //Check which format to display
        switch ($this->calFormat) {
            case 'smallMonth':
                $displayCal = $this->showSmallMonth($this->calMonth, $this->calYear, $this->displayPrevNextLinks, true, $this->linkMonth);
                break;
            case 'largeMonth':
                $displayCal = $this->showLargeMonth($this->calMonth, $this->calYear);
                break;
            case 'quarterYear':
                $displayCal = $this->showQuarterYear($this->calQuarter, $this->calYear, $this->displayPrevNextLinks);
                break;
            case 'fullYear':
                $displayCal = $this->showFullYear($this->calYear, $this->displayPrevNextLinks);
                break;
            case 'weekly':
                $displayCal = $this->showWeekView($this->calWeek, $this->displayPrevNextLinks);
                break;
            case 'daily':
                $displayCal = $this->showDayView($this->calDay, $this->displayPrevNextLinks);
                break;
            default:
                $error = 'Invalid definition of the calFormat variable in display function.';
                $this->displayError($error);
        }

        return $displayCal;
    } //End function display()

    /*
    This function of the class is used to display errors generated by the class.
    // TODO: don't die - just return the details to the caller through a return status and property.
    */
    function displayError($error) {
        $output = "<table>\n";
        $output .= "    <tr>\n";
        $output .= "        <td style=\"text-align: center;\">\n";
        $output .= "        The ieventsCalendar class has generated the following error:<br />\n";
        $output .= "        <span style=\"color: red;\">".$error."</span>\n";
        $output .= "        </td>\n";
        $output .= "    <tr>\n";
        $output .= "</table>\n";
        return $output;
    } //End function displayError()
} //End class calendar

?>
