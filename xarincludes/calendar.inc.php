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

class calendar {
    //********************* Start of variable declarations *********************

    //*********** Class variables that apply to all calendar formats ***********

    /*
    Declare the main array to hold the calendar events.
    $events[]['date'] is the date and time of the event in unix time format.
    $events[]['event'] holds the event information.
    */
    var $events = array();
    /*
    Declare a variable indicating the day of the week that the calendar starts on.
    0 = Sunday
    1 = Monday
    */
    var $startingDOW;
    /*
    Decalare a variable to indicate the calendar format.
    smallMonth
    largeMonth
    fullYear
    weekly
    */
    var $calFormat;
    //Declare a boolean variable to determine weather to display events.
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
    var $monthFormat;
    //Declare a variable that will hold the month to display.
    var $calMonth;
    //Declare a variable that will hold the year to display.
    var $calYear;
    //Declare a boolean variable to determine weather the current day is highlighted.
    var $showToday;
    /*
    Decalare a variable to indicate how the calendar is outputted from the class.
    echo - echoes the output to the screen.
    return - returns the HTML formatted calendar to the calling variable.
    */
    var $outputFormat;
    /*
    This tells the calendar to add other form $_GET requests to the links for
    previous and next month and year.  This uses the function addGetRequests()
    which parses out the month ($_GET['mon']), year ($_GET['yr']), and calendar
    format ($_GET['fmt']) get requests and passes all others to the links.
    */
    var $passGetRequests;

    //****** Class variables that apply only to the small calendar format ******

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
    //Declare a variable to hold the background image for lsrge formst cslendsrs.
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
    Decalare a variable to indicate the large calendar day of the week format.
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
    Decalre a boolean variable to determine weather the year is shown for small
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

    //********************** End of variable declarations **********************

    /*
    This function for the class has the same name as the class itself, therefore
    it is automatically invoked whenever a new instance of the class is created.
    This will clear the events array and set defaults for the calendar formatting
    variables.
    */
    function calendar() {
        include_once 'calendar.conf.php';
    } //End function calendar()

    /*
    This function for the class adds a new event to the events array.  The arguments
    passed are date and event.
    */
    function addEvent($date, $event) {
        //Get the next event ID for the events variable.
        $eventID = sizeof($this->events);
        //Add the event to the array.
        $this->events[$eventID]['date'] = $date;
        $this->events[$eventID]['event'] = $event;
    } //End function addEvent()

    /*
    This function for the class return a <div> tag containing the events for the
    day defined by $date.  This function is used for large format calendars.
    */
    function getEvents($date, $cal, $highlightDate = false) {
        //Set a boolean variable to determine weather events were displayed or not.
        $displayed = false;
        //Clear an events variable based on the calendar format.
        switch ($cal) {
            case "smallMonth":
                if ($this->displayEvents) {
                    //display the event with hover titles
                    $events = "<a href='#' title='";
                } else {
                    //display the event without hover titles
                    $events = "";
                }
                break;
            case "largeMonth":
                //Check if this is the weekend
                $week = "";
                if (((date("w", $date) == "0") || (date("w", $date) == "1")) && $this->showWeek) {
                    $week = " - <span style=\"font-size: 12px;\">Week ".date("W", $date)."</span>";
                }
                if ($highlightDate) {
                    $dateColor = $this->colorLargeFormatDateHighlight;
                } else {
                    $dateColor = $this->colorLargeFormatDateText;
                }
                //display the event with full text
                $events = "<div style=\"font-size: 12px; color: ".$this->colorLargeFormatEventText."; width: 100%; height: ".$this->largeCellHeight."; overflow: auto;\">\n";
                $events .= "<span style=\"font-size: 18px; font-weight: bold; color: ".$dateColor.";\">&nbsp;".date("j", $date).$week."</span><br>\n";
                break;
            case "weekly":
                //display the event with full text
                $events = "<div style=\"font-size: 12px; color: ".$this->colorWeekFormatEventText."; width: 100%; height: ".$this->weekCellHeight."; overflow: auto;\">\n";
                break;
            default:
                $error = "Invalid calendar format passed to the getEvents function.";
                $this->displayError($error);
        }
        //Check if any events are defined.
        if (isset($this->events) && $this->displayEvents) {
            //Cycle through the events that are defined.
            for ($i = 0; $i < sizeof($this->events); $i++) {
                //Define a boolean variable that will tell us to show the event or not.
                $showEvent = false;
                //If we are searching for events in a weekly calendar we must also search the time.
                if ($cal == "weekly") {
                    //First determine if this is Am or Pm
                    if (date("A", $this->events[$i]['date']) == "AM") {
                        $ampm = 0;
                    } else if (date("g", $this->events[$i]['date']) == 12) {
                        $ampm = 0;
                    } else {
                        $ampm = 12;
                    }
                    //Define the event date down to the minute.
                    $eventDate = mktime((date("g", $this->events[$i]['date']) + $ampm), date("i", $this->events[$i]['date']), 0, date("m", $this->events[$i]['date']), date("d", $this->events[$i]['date']), date("Y", $this->events[$i]['date']));
                    //echo(date("y/m/d h:i:s A", $this->events[$i]['date'])." -- ".$ampm." - ".date("y/m/d h:i:s A", $eventDate)."<br>");
                    //Define 15 minutes in seconds
                    $quarterHour = 900;
                    //Check if the event is within a quarter hour of the date
                    if (($eventDate >= $date) && ($eventDate < ($date + $quarterHour))) {
                        $showEvent = true;
                    }
                } else {
                    /*
                    Since the calendar format was not weekly we only need to check
                    weather the event fell on the date specified.
                    */
                    $eventDate = mktime(0, 0, 0, date("m", $this->events[$i]['date']), date("d", $this->events[$i]['date']), date("Y", $this->events[$i]['date']));
                    if ($date == $eventDate) {
                        $showEvent = true;
                    }
                }
                if ($showEvent) {
                    //An event was found so determine the calendar format we need to display.
                    switch ($cal) {
                        case "smallMonth":
                            //Check if this is the first event displayed.
                            if ($displayed) {
                                //Display the event with hover titles on a new line.
                                $events .= "\n".date("h:i A", $this->events[$i]['date'])." - ".$this->events[$i]['event'];
                            } else {
                                //Display the event with hover titles.
                                $events .= date("h:i A", $this->events[$i]['date'])." - ".$this->events[$i]['event'];
                                $displayed = true;
                            }
                            break;
                        case "largeMonth":
                        case "weekly":
                            //Display the event with full text.
                            $events .= "<span style=\"font-weight: bold;\">".date("h:i A", $this->events[$i]['date'])."</span> - ".$this->events[$i]['event']."<br><br>\n";
                            break;
                        case "weekly":
                    }
                }
            }
        }
        switch ($cal) {
            case "smallMonth":
                if ($this->displayEvents) {
                    if ($displayed) {
                        //Continue to show the display the event with hover titles.
                        $events .= "' style=\"text-decoration: none; font-weight: bold;\"> ".date("j", $date)."</a>";
                    } else {
                        //No events were added to the title do just display the date.
                        $events = "&nbsp;".date("j", $date);
                    }
                } else {
                    //display the event without hover titles
                    $events = " ".date("j", $date);
                }
                break;
            case "largeMonth":
            case "weekly":
                //display the event with full text
                $events .= "</div>";
                break;
        }
        return $events;
    }

    /*
    This function for the class will return the month name.
    */
    function getMonth($m, $y) {
        //Get the name of the month based on the monthFormat variable.
        switch (strtolower($this->monthFormat)) {
            case "long":
                $month = date("F", mktime(0, 0, 0, $m, 1, $y));
                break;
            case "short":
                $month = date("M", mktime(0, 0, 0, $m, 1, $y));
                break;
            default:
                $error = "Invalid definition of the monthFormat variable in the getMonth function.";
                $this->displayError($error);
        }
        return $month;
    } //End function getMonth()

    /*
    This function for the class will return the day of the week.
    */
    function getDOW($dow) {
        $dow = $dow + $this->startingDOW;
        //Get the name of the month based on the DOWformat variable.
        switch (strtolower($this->DOWformat)) {
            case "long":
                switch ($dow) {
                    case "1":
                        $weeekday = "Sunday";
                        break;
                    case "2":
                        $weeekday = "Monday";
                        break;
                    case "3":
                        $weeekday = "Tuesday";
                        break;
                    case "4":
                        $weeekday = "Wednesday";
                        break;
                    case "5":
                        $weeekday = "Thursday";
                        break;
                    case "6":
                        $weeekday = "Friday";
                        break;
                    case "7":
                        $weeekday = "Saturday";
                        break;
                    case "8":
                        $weeekday = "Sunday";
                        break;
                    default:
                        $error = "Invalid day of the week passed to the getDOW function.";
                        $this->displayError($error);
                }
                break;
            case "short":
                switch ($dow) {
                    case "1":
                        $weeekday = "Sun";
                        break;
                    case "2":
                        $weeekday = "Mon";
                        break;
                    case "3":
                        $weeekday = "Tue";
                        break;
                    case "4":
                        $weeekday = "Wed";
                        break;
                    case "5":
                        $weeekday = "Thu";
                        break;
                    case "6":
                        $weeekday = "Fri";
                        break;
                    case "7":
                        $weeekday = "Sat";
                        break;
                    case "8":
                        $weeekday = "Sun";
                        break;
                    default:
                        $error = "Invalid day of the week passed to the getDOW function.";
                        $this->displayError($error);
                }
                break;
            default:
                $error = "Invalid definition of the DOWformat variable in the getDOW function.";
                $this->displayError($error);
        }
        return $weeekday;
    } //End function getDOW()

    /*
    This function for the class will display a month in small format. The inputs
    to the function are as follows:

    $m - Month to display.
    $y - Year to display.
    $np - A boolean value indicating weather to display the links for the previous and next months.

    */
    function showSmallMonth($m, $y, $np = false, $showyear = true, $linkMonth = false) {
        //Calculate the number of days in the month
        $days = date('t',mktime(0,0,0,$m, 1, $y));
        //Calculate the day of the week that the month starts on
        $startDay = date('w',mktime(0,0,0,$m, 1, $y)) - $this->startingDOW;
        //set the column offset for the starting day of the week.
        $offset = "";
        if ($startDay > 0) {
            $offset .= "        <td width=\"14%\" colspan=\"".$startDay."\">&nbsp;</td>\n";
        } else if ($startDay == -1) {
            $offset .= "        <td width=\"14%\" colspan=\"6\">&nbsp;</td>\n";
            $startDay = 6;
        }
        //Get the textual representation of the month
        $month = $this->getMonth($m, $y);
        //Calculate the previous month and year for the header link.
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
        if ($this->passGetRequests) {
            $get = $this->addGetRequests();
        } else {
            $get = "";
        }
        //
        if ($np) {
            $prevLink = "<a href='".$_SERVER['PHP_SELF']."?mon=".$prevMonth."&yr=".$prevYear."&fmt=smallMonth".$get."' style=\"text-decoration: none;\"><<</a> &nbsp;";
            $nextLink = " &nbsp;<a href='".$_SERVER['PHP_SELF']."?mon=".$nextMonth."&yr=".$nextYear."&fmt=smallMonth".$get."' style=\"text-decoration: none;\">>></a>";
        } else {
            $prevLink = "";
            $nextLink = "";
        }
        //Get the currrent date to display if the month showing is the current month.
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

        //Create the header
        $output = "<div style=\"vertical-align: top;\">\n";
        $output .= "<table class=\"calendar\" border=\"".$this->smallMonthBorder."\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\" style=\"font-size: 14px;\">\n";
        $output .= "    <tr>\n";
        $output .= "        <th colspan=\"7\" style=\"text-align: center;\">\n";
        $output .= "            <span style=\"font-size: 25px; font-weight: bold; color: ".$this->colorSmallFormatHeaderText.";\">".$prevLink.$month."&nbsp;".$year.$nextLink."</span>\n";
        $output .= "        </th>\n";
        $output .= "    </tr>\n";
        $output .= "    <tr style=\"color: ".$this->colorSmallFormatDayOfWeek.";\">\n";
        //now create the weekday headers
        if ($this->startingDOW == 0) {
            $output .= "        <td style=\"width: 14%; text-align: center;\">S</td>\n";
        }
        $output .= "        <td style=\"width: 14%; text-align: center;\">M</td>\n";
        $output .= "        <td style=\"width: 14%; text-align: center;\">T</td>\n";
        $output .= "        <td style=\"width: 14%; text-align: center;\">W</td>\n";
        $output .= "        <td style=\"width: 14%; text-align: center;\">T</td>\n";
        $output .= "        <td style=\"width: 14%; text-align: center;\">F</td>\n";
        $output .= "        <td style=\"width: 14%; text-align: center;\">S</td>\n";
        if ($this->startingDOW == 1) {
            $output .= "        <td style=\"width: 14%; text-align: center;\">S</td>\n";
        }
        $output .= "    </tr>\n";
        $output .= "    <tr>\n";
        //Now generate the calendar
        for($i=1; $i<=$days; $i++){
            if ($i == $day && $this->showToday) {
                $output .= $offset."        <td style=\"width: 14%; text-align: center; color: ".$this->colorSmallFormatDateHighlight."; font-weight: bold;\">&nbsp;".$this->getEvents(mktime(0, 0, 0, $m, $i, $y), "smallMonth")."&nbsp;</td>\n";
            } else {
                $output .= $offset."        <td style=\"width: 14%; text-align: center; color: ".$this->colorSmallFormatDateText.";\">&nbsp;".$this->getEvents(mktime(0, 0, 0, $m, $i, $y), "smallMonth")."&nbsp;</td>\n";
            }
            $offset = "";
            $startDay ++;
            if ($startDay == 7) {
                $output .= "    </tr>\n";
                $output .= "    <tr>\n";
                $startDay = 0;
            }
        }
        if ($startDay > 0) {
            $output .= "        <td colspan=\"".(7 - $startDay)."\" style=\"width: 14%;\">&nbsp;</td>\n";
        }
        $output .= "    </tr>\n";
        $output .= "</table>\n";
        $output .= "</div>\n";
        //Now output the calendar
        return $output;
    } //End function showSmallMonth()


    /*
    This function for the class will display a month in large format. The inputs
    to the function are as follows:

    $m - Month to display.
    $y - Year to display.
    $np - A boolean value indicating weather to display the links for the previous and next months.

    */
    function showLargeMonth($m, $y, $np) {
        // Calculate the number of days in the month
        $days = date('t', mktime(0, 0, 0, $m, 1, $y));

        // Calculate the day of the week that the month starts on
        $startDay = date('w', mktime(0, 0, 0, $m, 1, $y)) - $this->startingDOW;

        // Set the column offset for the starting day of the week.
        $offset = array();
        if ($startDay > 0) {
            $offset[] = '<td colspan="' . $startDay . '" class="ievents-spacer">&nbsp;</td>';
        } else if ($startDay == -1) {
            $offset[] = '<td colspan="6" class="ievents-spacer">&nbsp;</td>';
            $startDay = 6;
        }

        // Get the textual representation of the month
        // TODO: get this from the Xaraya ML system.
        $month = $this->getMonth($m, $y);

        // FIXME: We don't want to be messing with get requests here.
        if ($this->passGetRequests) {
            $get = $this->addGetRequests();
        } else {
            $get = '';
        }

        // Get the currrent date to display if the month showing is the current month.
        if (mktime(0, 0, 0, date('m'), 1, date('Y')) == mktime(0, 0, 0, $m, 1, $y)) {
            $day = date('j');
        } else {
            $day = 0;
        }

        // Set default attributes

        // Create the header
        $output = array();

        // No classes needed for te table, as we can select it in a wrapper.
        $output[] = '<table>';

        $output[] = '<tr>';
        //now create the weekday headers
        for ($i = 1; $i < 8; $i++) {
            $output[] = '<th>' . $this->getDOW($i) . '</th>';
        }
        $output[] = '</tr>';

        $output[] = '<tr>';

        //Now generate the calendar
        for($i=1; $i<=$days; $i++){
            $date = mktime(0, 0, 0, $m, $i, $y);

            // Set the class for the table cells.
            $classes = array();

            // Highlight weekends.
            if ((date('w', $date) == '0') || (date('w', $date) == '6')) $classes[] = 'ievents-weekend';

            // Highlight today.
            if ($i == $day) $classes[] = 'ievents-today';

            $output = array_merge($output, $offset);

            if (!empty($classes)) {
                $output[] = '<td class="' . implode(' ', $classes) . '">' . $this->getEvents($date, 'largeMonth') . '</td>';
            } else {
                $output[] = '<td>' . $this->getEvents($date, 'largeMonth') . '</td>';
            }

            $offset = array();
            $startDay ++;
            if ($startDay == 7) {
                $output[] = '</tr>';
                $output[] = '<tr>';
                $startDay = 0;
            }
        }

        if ($startDay > 0) {
            $output[] = '<td colspan="' . (7 - $startDay) . '" class="ievents-spacer">&nbsp;</td>';
        }

        $output[] = '</tr>';
        $output[] = '</table>';

        // Return the calendar markup.
        return implode("\n", $output) . "\n";
    } // End function showLargeMonth()


    function showFullYear($y, $np = false) {
        //Get the previous and next years for the year selection links.
        $prevYear = $y - 1;
        $nextYear = $y + 1;
        //Set default arrows to use if no images are defined.
        $prevArrow = "<<";
        $nextArrow = ">>";
        //If images were set for the previous month and next month links, set the images.
        if (isset($this->fullYearPrevArrow)) {
            $prevArrow = "<img src=\"".$this->fullYearPrevArrow."\" border=\"0\" align=\"top\">";
        }
        if (isset($this->fullYearNextArrow)) {
            $nextArrow = "<img src=\"".$this->fullYearNextArrow."\" border=\"0\" align=\"top\">";
        }
        if ($this->passGetRequests) {
            $get = $this->addGetRequests();
        } else {
            $get = "";
        }
        //If chosen, prepare the links for the previous month and next month
        if ($np) {
            $prevLink = "<a href='".$_SERVER['PHP_SELF']."?yr=".$prevYear."&fmt=fullYear".$get."' style=\"text-decoration: none;\">".$prevArrow."</a> &nbsp;";
            $nextLink = " &nbsp;<a href='".$_SERVER['PHP_SELF']."?yr="."&fmt=fullYear".$get."' style=\"text-decoration: none;\">".$nextArrow."</a>";
        } else {
            $prevLink = "";
            $nextLink = "";
        }
        //Create the table that will contain the months and add the year header.
        $output = "<table border=\"1\" cellspacing=\"0\" cellpadding=\"3\" width=\"100%\" align=\"center\">\n";
        $output .= "    <tr>\n";
        $output .= "        <td colspan=\"3\" style=\"text-align: center;\">\n";
        $output .= "            <span style=\"font-size: 50px; font-weight: bold;\">".$prevLink.$y.$nextLink."</span>\n";
        $output .= "        </td>\n";
        $output .= "    </tr>\n";
        $output .= "    <tr>\n";
        //Create a variable to count the columns.
        $col = 1;
        //Now show the months for that year.
        for ($i = 1; $i <= 12; $i++) {
            $output .= "        <td style=\"text-align: center; vertical-align: top;\">\n";
            $output .= $this->showSmallMonth($i, $y, false, false);
            $output .= "        </td>\n";
            $col ++;
            if ($col == 4) {
                $output .= "    </tr>\n";
                $output .= "    <tr>\n";
                $col = 1;
            }
        }
        $output .= "    </tr>\n";
        $output .= "</table>\n";
        return $output;
    } //End function showFullYear()

    /*
    This function is used to show a weekly view of the calendar.
    */
    function showWeekView($date) {
        //Determine what week of the year the date falls on.
        $week = date("W", $date);
        //Determine what day of the week the date falls on.
        $dayOfWeek = date("w",$date);
        //Define one day in seconds (60 seconds * 60 minutes * 24 hours).
        $oneDay = 60 * 60 * 24;
        //Determine the first day of the week that the day falls on.
        $firstDayOfWeek = $date - ($dayOfWeek * $oneDay); // ;
        $weekCalendarClass = "";
        $weekCalendarID = "";
        $prevLink = "";
        $nextLink = "";
        $width = "100%";

        $highlightWorkHours = false;
        $toggle = 0;

        if (isset($this->workStartHour) && isset($this->workStartMinute) && isset($this->workStartAmPm)) {
            /*
            Determine the quarter hour of the starting work time for highlighting the
            hours in a work day.
            */
            $unixWorkStartTime = mktime(($this->workStartHour + ($this->workStartAmPm * 12)), $this->workStartMinute, 0, date("m", $date), date("j", $date), date("Y", $date));
            /*
            Determine the quarter hour of the ending work time for highlighting the
            hours in a work day.
            */
            $unixWorkEndTime = mktime(($this->workEndHour + ($this->workEndAmPm * 12)), $this->workEndMinute, 0, date("m", $date), date("j", $date), date("Y", $date));
            $highlightWorkHours = true;
        } else {
            $unixWorkStartTime = mktime(0, 0, 0, date("m", $date), date("j", $date), date("Y", $date));
            $unixWorkEndTime = mktime(0, 0, 0, date("m", $date), date("j", $date), date("Y", $date));
        }

        //Create the header
        $output = "<div style=\"vertical-align: top;\">";
        $output .= "<table".$weekCalendarClass.$weekCalendarID." border=\"1\" cellspacing=\"0\" cellpadding=\"0\" align=\"".$this->largeFormatAlign."\" style=\"width: ".$width.";\">\n";
        $output .= "    <tr>\n";
        $output .= "        <td style=\"width: 100%; text-align: center; vertical-align: middle;\">\n";
        $output .= "            <span style=\"font-size: 30px; font-weight: bold; color: ".$this->colorWeekFormatHeaderText.";\">".$prevLink."Week ".$week.$nextLink."</span>\n";
        $output .= "        </td>\n";
        $output .= "    </tr>\n";
        $output .= "    <tr style=\"color: ".$this->colorWeekFormatDayOfWeek."; font-weight: bold;\">\n";
        $output .= "        <td>\n";
        $output .= "            <table border=\"1\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\" style=\"width: 100%;\">\n";
        $output .= "                <tr>\n";
        $output .= "                    <td style=\"width: 12.5%; text-align: center;\">Hour</td>\n";
        //now create the weekday headers
        for ($i = 1; $i < 8; $i++) {
            $output .= "        <td style=\"width: 12.5%; text-align: center;\">".$this->getDOW($i)."</td>\n";
        }
        $output .= "        <td style=\"width: 1.9%;\">&nbsp;</td>\n";
        $output .= "                </tr>\n";
        $output .= "            </table>\n";
        $output .= "        <td>\n";
        $output .= "    </tr>\n";
        $output .= "    <tr>\n";
        $output .= "        <td colspan=\"9\">\n";
        $output .= "            <div style=\"width: 100%; height: ".$this->weekCalendarHeight."; overflow: auto;\">\n";
        $output .= "                <table border=\"1\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\" style=\"width: 100%;\">\n";
        for($ampm = 0; $ampm < 2; $ampm ++) {
            for ($hour = 1; $hour < 13; $hour ++) {
                for ($minute = 0; $minute < 4; $minute ++) {
                    //Format the time display.
                    $unixTime = mktime((($hour + ($ampm * 12)) - 1), ($minute * 15), 0, date("m", $date), date("j", $date), date("Y", $date));
                    $time = date("g:i A", $unixTime);

                    if ($minute == 0) {
                        $highlightZeroHour = "font-weight: bold;";
                    } else {
                        $highlightZeroHour = "";
                    }

                    if ($minute == 0) {
                        $toggle ++;
                        if ($toggle > 1) {
                            $toggle = 0;
                        }
                    }

                    if ($toggle == 0) {
                        if ((($unixTime >= $unixWorkStartTime)) && ($unixTime < $unixWorkEndTime) && $highlightWorkHours) {
                            $highlightHour = " background-color: #DDDDFF;";
                        } else {
                            $highlightHour = " background-color: DDFFDD;";
                        }
                    } else {
                        if ((($unixTime >= $unixWorkStartTime)) && ($unixTime < $unixWorkEndTime) && $highlightWorkHours) {
                            $highlightHour = " background-color: #BBBBFF;";
                        } else {
                            $highlightHour = " background-color: #BBFFBB;";
                        }
                    }

                    $output .= "                    <tr style=\"".$highlightHour." height: ".$this->weekCellHeight.";\">\n";
                    $output .= "                        <td style=\"width: 12.5%; text-align: right;".$highlightZeroHour." vertical-align: top;\">\n";
                    $output .= "                        <a name=\"".$time."\">".$time."</a>\n";
                    $output .= "                        </td>\n";
                    for ($dow = 0; $dow < 7; $dow ++) {
                        $output .= "                        <td style=\"width: 12.5%; text-align: left; vertical-align: top;\">\n";
                        $dateCheck = mktime(($hour + ($ampm * 12) - 1), ($minute * 15), 0, date("m", ($firstDayOfWeek + ($oneDay * $dow))), date("d", ($firstDayOfWeek + ($oneDay * $dow))) + $this->startingDOW, date("Y", ($firstDayOfWeek + ($oneDay * $dow))));
                        $output .= "                        ".$this->getEvents($dateCheck, "weekly")."\n";
                        $output .= "                        </td>\n";
                    }
                    $output .= "                    </tr>\n";
                }
            }
        }
        $output .= "                    </tr>\n";
        $output .= "                </table>\n";
        $output .= "            </div>\n";
        $output .= "        </td>\n";
        $output .= "    </tr>\n";
        $output .= "    </table>\n";

        return $output;
    } //End function showWeekView()

    /*
    This function for the class outputs the calendar based on the parameters given.
    */
    function display() {
        //Check which format to display
        switch ($this->calFormat) {
            case "smallMonth":
                $displayCal = $this->showSmallMonth($this->calMonth, $this->calYear, $this->displayPrevNextLinks);
                break;
            case "largeMonth":
                $displayCal = $this->showLargeMonth($this->calMonth, $this->calYear, $this->displayPrevNextLinks);
                break;
            case "fullYear":
                $displayCal = $this->showFullYear($this->calYear, $this->displayPrevNextLinks);
                break;
            case "weekly":
                $displayCal = $this->showWeekView($this->calWeek);
                break;
            default:
                $error = "Invalid definition of the calFormat variable in display function.";
                $this->displayError($error);
        }
        //Output the HTML based on the outputFormat variable
        switch ($this->outputFormat) {
            case "echo":
                echo ($displayCal);
                break;
            case "return":
                return $displayCal;
                break;
            default:
                $error = "Invalid definition of the outputFormat variable in the display function.";
                $this->displayError($error);

        }
    } //End function display()

    /*
    This function retrieves $_GET values supplied to the page and parses out the
    month ($_GET['mon']) and year ($_GET['yr']) values and returns all other values
    to the links for changing months and years.
    */
    function addGetRequests() {
        $returnRequests = false;
        if ($_SERVER['REQUEST_METHOD'] == "GET") {
            $getRequests = "";
            foreach ($_GET as $key => $value) {
                if ($key != "mon" && $key != "yr" && $key != "fmt") {
                    $getRequests .= "&".$key."=".$value;
                    $returnRequests = true;
                }
            }
        }
        if ($returnRequests) {
            return $getRequests;
        }
    } //End function addGetRequests()

    /*
    This function of the class is used to display errors generated by the class.
    */
    function displayError($error) {
        $output = "<table border=\"1\" cellspacing=\"0\" cellpadding=\"5\" align=\"left\">\n";
        $output .= "    <tr>\n";
        $output .= "        <td style=\"text-align: center;\">\n";
        $output .= "        The clendar class has generated the following error:<br>\n";
        $output .= "        <span style=\"color: red;\">".$error."</span>\n";
        $output .= "        </td>\n";
        $output .= "    <tr>\n";
        $output .= "</table>\n";
        die($output);
    } //End function displayError()
} //End class calendar

?>