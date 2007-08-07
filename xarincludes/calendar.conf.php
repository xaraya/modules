<?php
    /*
    ###############################################################################
    Created By : Dan Bemowski
    E-mail     : dbemowsk@charter.net
    File       : calendar.conf.php
    Used By    : calendar.inc.php

    Definition : This is the configuration file used to set the default values
                 for use in the claendar.inc.php file.  Calendar.inc.php includes
                 this file any time a new instance of the calendar class is
                 defined.  This defines defaults for all calendar formats so that
                 if a different calendar format is chosen, any required variables
                 are pre-set.  Any of these variables can be re-defined as needed
                 to display the desired output.

    To use     : First, include the class using "include 'calendar.inc.php';".
                 Next, Create an instance of the class using "$cal = new Calendar;".
                 Next, re-define variables to suit.
                 Last, display the calendar using "$cal->display();".
                 The following configuration will default the class to show the
                 small calendar format using the current month and year. The
                 current day of the month will be highlighted in red.  The weekday
                 headers will display in blue. Some of the variables defined are
                 not used by the small calendar format.  These are set so that the
                 user can simply change the calFormat to "largeMonth" or "fullYear"
                 to start using these calendar formats. Any of these variables can
                 be redefined in the script using the class after an instance of
                 the class is created.  The calendar class uses the GET method
                 In order to use the links for previous and next month or year when
                 the variable $cal->displayPrevNext is set to true, the following
                 code must be included in the PHP script using the class:

                 //Not needed when displaying the full year format.
                 //Only used for the small and large month formats.
                 if (!empty($_GET['mon'])) {
                     $cal->calMonth = $_GET['mon'];
                 }
                 //Used for small, large and full year formats.
                 if (!empty($_GET['yr'])) {
                     $cal->calYear = $_GET['yr'];
                 }
    ###############################################################################
    */

    //************************* Set the main defaults  *************************

    //Clear the events array.
    $this->events = array();
    //Set the default calendar format to smallMonth.
    $this->calFormat = "smallMonth";
    //Set the calendar to display the current month and year.
    $this->calMonth = date("n");
    $this->calYear = date("Y");
    //Tell the calendar to highlight the current day when viewing the current month.
    $this->showToday = true;
    //Set the month format to long;
    $this->monthFormat = "long";
    //Start the week on Sunday
    $this->startingDOW = 0;
    //Set the display events variable to not show events.
    $this->displayEvents = false;
    //Define how the calendar is outputted from the class.
    $this->outputFormat = "echo";
    //Tell the calendar to add all get requests passed to the page.
    $this->passGetRequests = true;

    //******************** Set defaults for small calendars ********************

    //Set the small month border to 0.
    $this->smallMonthBorder = "0";
    //Set the small month border to 0.
    $this->smallMonthBorder = "0";
    //Set the color formats
    $this->colorSmallFormatDayOfWeek = "blue";
    $this->colorSmallFormatDateText = "black";
    $this->colorSmallFormatDateHighlight = "red";
    $this->colorSmallFormatHeaderText = "purple";

    //******************** Set defaults for large calendars ********************

    //Tell the calendar to not show the week numbers.
    $this->showWeek = false;
    //Tell the calendar to use the long day of week format
    $this->DOWformat = "long";
    //Set the height of large format calendar cells.
    $this->largeCellHeight = "8em";
    //Set the attribute for aligning the large format calendar.
    $this->largeFormatAlign = "center";
    //Set the display previous next to not show.
    $this->displayPrevNext = false;
    //Set the color formats
    $this->colorLargeFormatDayOfWeek = "";
    $this->colorLargeFormatDateText = "";
    $this->colorLargeFormatDateHighlight = "";
    $this->colorLargeFormatHeaderText = "";
    $this->colorLargeFormatEventText = "";
    $this->colorLargeFormatWeekendHighlight = "#fcfcfc";

    //******************** Set defaults for weekly calendars *******************

    //Set the default height of the weekly calendar
    $this->weekCalendarHeight = "520px";
    //Set the default cell height of the weekly calendar
    $this->weekCellHeight = "50px";
    $this->calWeek = time();

?>
