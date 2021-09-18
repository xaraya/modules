<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */


// {{{ Header

/**
 * TimeZone representation class, along with time zone information data
 *
 * PHP versions 4 and 5
 *
 * LICENSE:
 *
 * Copyright (c) 1997-2007 Baba Buehler, Pierre-Alain Joye, C.A. Woodcock
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted under the terms of the BSD License.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   Date and Time
 * @package    Date
 * @author     Baba Buehler <baba@babaz.com>
 * @author     Pierre-Alain Joye <pajoye@php.net>
 * @author     C.A. Woodcock <c01234@netcomuk.co.uk>
 * @copyright  1997-2007 Baba Buehler, Pierre-Alain Joye, C.A. Woodcock
 * @license    http://www.opensource.org/licenses/bsd-license.php
 *             BSD License
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/Date
 */


// }}}
// {{{ Class Date_TimeZone

/**
 * TimeZone representation class, along with time zone information data
 *
 * The default timezone is set from the first valid timezone id found
 * in one of the following places, in this order:
 *   + global $_DATE_TIMEZONE_DEFAULT
 *   + system environment variable PHP_TZ
 *   + system environment variable TZ
 *   + the result of date('T')
 *
 * If no valid timezone id is found, the default timezone is set to 'UTC'.
 * You may also manually set the default timezone by passing a valid id to
 * {@link Date_TimeZone::setDefault()}.
 *
 * This class includes time zone data (from zoneinfo) in the form of a
 * global array, $_DATE_TIMEZONE_DATA.
 *
 * @category  Date and Time
 * @package   Date
 * @author    Baba Buehler <baba@babaz.com>
 * @author    C.A. Woodcock <c01234@netcomuk.co.uk>
 * @copyright 1997-2007 Baba Buehler, Pierre-Alain Joye, C.A. Woodcock
 * @license   http://www.opensource.org/licenses/bsd-license.php
 *            BSD License
 * @version   Release: 1.5.0a1
 * @link      http://pear.php.net/package/Date
 */
class Date_TimeZone
{
    // {{{ Properties

    /**
     * Unique time Zone ID of this time zone
     *
     * @var      string
     * @access   private
     * @since    Property available since Release 1.0
     */
    public $id;

    /**
     * Offset, in milliseconds, of this timezone
     *
     * @var      int
     * @access   private
     * @since    Property available since Release 1.0
     */
    public $offset;

    /**
     * Short name of this time zone (e.g. "CST")
     *
     * @var      string
     * @access   private
     * @since    Property available since Release 1.0
     */
    public $shortname;

    /**
     * DST short name of this timezone (e.g. 'BST')
     *
     * @var      string
     * @access   private
     * @since    Property available since Release 1.0
     */
    public $dstshortname;

    /**
     * Long name of this time zone (e.g. "Central Standard Time")
     *
     * N.B. this is not necessarily unique
     *
     * @since    1.0
     * @access   private
     * @since    Property available since Release 1.0
     */
    public $longname;

    /**
     * DST long name of this time zone (e.g. 'British Summer Time')
     *
     * @var      string
     * @access   private
     * @since    Property available since Release 1.0
     */
    public $dstlongname;

    /**
     * Whether this time zone observes daylight savings time
     *
     * @var      bool
     * @access   private
     * @since    Property available since Release 1.0
     */
    public $hasdst;

    /**
     * Additional offset of Summer time from the standard time of the
     * time zone in milli-seconds
     *
     * The value is usually 3600000, i.e. one hour, and always positive
     *
     * @var      int
     * @access   private
     * @since    Property available since Release 1.5.0
     */
    public $on_summertimeoffset;

    /**
     * Month no (1-12) in which Summer time starts (the clocks go forward)
     *
     * @var      int
     * @access   private
     * @since    Property available since Release 1.5.0
     */
    public $on_summertimestartmonth;

    /**
     * Definition of when Summer time starts in the specified month
     *
     * Can take one of the following forms:
     *
     *  5        the fifth of the month
     *  lastSun  the last Sunday in the month
     *  lastMon  the last Monday in the month
     *  Sun>=8   first Sunday on or after the 8th
     *  Sun<=25  last Sunday on or before the 25th
     *
     * @var      string
     * @access   private
     * @since    Property available since Release 1.5.0
     */
    public $os_summertimestartday;

    /**
     * Time in milli-seconds relative to midnight UTC when
     * Summer time starts (the clocks go forward)
     *
     * @var      int
     * @access   private
     * @since    Property available since Release 1.5.0
     */
    public $on_summertimestarttime;

    /**
     * Month no (1-12) in which Summer time ends (the clocks go back)
     *
     * @var      int
     * @access   private
     * @since    Property available since Release 1.5.0
     */
    public $on_summertimeendmonth;

    /**
     * Definition of when Summer time ends in the specified month
     *
     * @var      string
     * @access   private
     * @see      Date_TimeZone::$os_summertimestartday
     * @since    Property available since Release 1.5.0
     */
    public $os_summertimeendday;

    /**
     * Time in milli-seconds relative to midnight UTC when
     * Summer time ends (the clocks go back)
     *
     * @var      int
     * @access   private
     * @since    Property available since Release 1.5.0
     */
    public $on_summertimeendtime;


    // }}}
    // {{{ Constructor

    /**
     * Constructor
     *
     * If the supplied ID is invalid, the created time zone is "UTC".
     *
     * A note about time zones of the form 'Etc/*' (quoted from the public
     * domain 'tz' data-base (see {@link ftp://elsie.nci.nih.gov/pub/}
     * [file 'etcetera']):
     *
     *  These entries are mostly present for historical reasons, so that
     *  people in areas not otherwise covered by the tz files could use
     *  a time zone that was right for their area.  These days, the
     *  tz files cover almost all the inhabited world, and the only practical
     *  need now for the entries that are not on UTC are for ships at sea
     *  that cannot use POSIX TZ settings.
     *
     *  - <b>Etc/GMT</b>  (GMT)
     *  - <b>Etc/UTC</b>  (UTC)
     *  - <b>Etc/UCT</b>  (UCT)
     *
     *  The following link uses older naming conventions, but it belongs here.
     *  We want this to work even on installations that omit the other older
     *  names.
     *
     *  - <b>Etc/GMT</b>  (equivalent to GMT)
     *
     *  - <b>Etc/UTC</b>  (equivalent to Etc/Universal)
     *  - <b>Etc/UTC</b>  (equivalent to Etc/Zulu)
     *
     *  - <b>Etc/GMT</b>  (equivalent to Etc/Greenwich)
     *  - <b>Etc/GMT</b>  (equivalent to Etc/GMT-0)
     *  - <b>Etc/GMT</b>  (equivalent to Etc/GMT+0)
     *  - <b>Etc/GMT</b>  (equivalent to Etc/GMT0)
     *
     *  We use POSIX-style signs in the Zone names and the output abbreviations,
     *  even though this is the opposite of what many people expect.
     *  POSIX has positive signs west of Greenwich, but many people expect
     *  positive signs east of Greenwich.  For example, TZ='Etc/GMT+4' uses
     *  the abbreviation "GMT+4" and corresponds to 4 hours behind UTC
     *  (i.e. west of Greenwich) even though many people would expect it to
     *  mean 4 hours ahead of UTC (i.e. east of Greenwich).
     *
     *  In the draft 5 of POSIX 1003.1-200x, the angle bracket notation
     *  (which is not yet supported by the tz code) allows for
     *  TZ='<GMT-4>+4'; if you want time zone abbreviations conforming to
     *  ISO 8601 you can use TZ='<-0400>+4'.  Thus the commonly-expected
     *  offset is kept within the angle bracket (and is used for display)
     *  while the POSIX sign is kept outside the angle bracket (and is used
     *  for calculation).
     *
     *  Do not use a TZ setting like TZ='GMT+4', which is four hours behind
     *  GMT but uses the completely misleading abbreviation "GMT".
     *
     *  Earlier incarnations of this package were not POSIX-compliant, and
     *  we did not want things to change quietly if someone accustomed to the
     *  old way uses the codes from previous versions so we moved the names
     *  into the Etc subdirectory.
     *
     *  - <b>Etc/GMT-14</b>  (14 hours ahead of Greenwich)
     *  - <b>Etc/GMT-13</b>  (13)
     *  - <b>Etc/GMT-12</b>  (12)
     *  - <b>Etc/GMT-11</b>  (11)
     *  - <b>Etc/GMT-10</b>  (10)
     *  - <b>Etc/GMT-9</b>   (9)
     *  - <b>Etc/GMT-8</b>   (8)
     *  - <b>Etc/GMT-7</b>   (7)
     *  - <b>Etc/GMT-6</b>   (6)
     *  - <b>Etc/GMT-5</b>   (5)
     *  - <b>Etc/GMT-4</b>   (4)
     *  - <b>Etc/GMT-3</b>   (3)
     *  - <b>Etc/GMT-2</b>   (2)
     *  - <b>Etc/GMT-1</b>   (1)
     *  - <b>Etc/GMT+1</b>   (1 hour behind Greenwich)
     *  - <b>Etc/GMT+2</b>   (2)
     *  - <b>Etc/GMT+3</b>   (3)
     *  - <b>Etc/GMT+4</b>   (4)
     *  - <b>Etc/GMT+5</b>   (5)
     *  - <b>Etc/GMT+6</b>   (6)
     *  - <b>Etc/GMT+7</b>   (7)
     *  - <b>Etc/GMT+8</b>   (8)
     *  - <b>Etc/GMT+9</b>   (9)
     *  - <b>Etc/GMT+10</b>  (10)
     *  - <b>Etc/GMT+11</b>  (11)
     *  - <b>Etc/GMT+12</b>  (12)
     *
     * @param string $ps_id the time zone ID
     *
     * @return   void
     * @access   public
     * @see      Date::setTZ(), Date::setTZByID(), Date_TimeZone::isValidID()
     */
    public function Date_TimeZone($ps_id)
    {
        $_DATE_TIMEZONE_DATA =& $GLOBALS['_DATE_TIMEZONE_DATA'];

        if (isset($GLOBALS['_DATE_TIMEZONE_DATA'][$ps_id])) {
            $this->id = $ps_id;

            $this->shortname    = $_DATE_TIMEZONE_DATA[$ps_id]['shortname'];
            $this->longname     = $_DATE_TIMEZONE_DATA[$ps_id]['longname'];
            $this->offset       = $_DATE_TIMEZONE_DATA[$ps_id]['offset'];
            $this->dstshortname =
                array_key_exists(
                    "dstshortname",
                    $_DATE_TIMEZONE_DATA[$ps_id]
                ) ?
                $_DATE_TIMEZONE_DATA[$ps_id]['dstshortname'] :
                null;
            if ($this->hasdst = !is_null($this->dstshortname)) {
                $this->dstlongname =
                    array_key_exists(
                        "dstlongname",
                        $_DATE_TIMEZONE_DATA[$ps_id]
                    ) ?
                    $_DATE_TIMEZONE_DATA[$ps_id]['dstlongname'] :
                    null;
                if (isset($_DATE_TIMEZONE_DATA[$ps_id]["summertimeoffset"])) {
                    $this->on_summertimeoffset     = $_DATE_TIMEZONE_DATA[$ps_id]["summertimeoffset"];
                    $this->on_summertimestartmonth = $_DATE_TIMEZONE_DATA[$ps_id]["summertimestartmonth"];
                    $this->os_summertimestartday   = $_DATE_TIMEZONE_DATA[$ps_id]["summertimestartday"];
                    $this->on_summertimestarttime  = $_DATE_TIMEZONE_DATA[$ps_id]["summertimestarttime"];
                    $this->on_summertimeendmonth   = $_DATE_TIMEZONE_DATA[$ps_id]["summertimeendmonth"];
                    $this->os_summertimeendday     = $_DATE_TIMEZONE_DATA[$ps_id]["summertimeendday"];
                    $this->on_summertimeendtime    = $_DATE_TIMEZONE_DATA[$ps_id]["summertimeendtime"];
                } else {
                    $this->on_summertimeoffset = null;
                }
            }
        } else {
            $this->hasdst = false;

            if (preg_match(
                '/^UTC([+\-])([0-9]{2,2}):?([0-5][0-9])$/',
                $ps_id,
                $ha_matches
            )) {
                $this->id     = $ps_id;
                $this->offset = ($ha_matches[1] .
                                 ($ha_matches[2] * 3600 +
                                  $ha_matches[3] * 60)) * 1000;

                if (!($hb_isutc = $this->offset == 0)) {
                    $this->id        = $ps_id;
                    $this->shortname = "UTC" .
                                       $ha_matches[1] .
                                       ($ha_matches[3] == "00" ?
                                        ltrim($ha_matches[2], "0") :
                                        $ha_matches[2] . $ha_matches[3]);
                    $this->longname  = "UTC" .
                                       $ha_matches[1] .
                                       $ha_matches[2] .
                                       ":" .
                                       $ha_matches[3];
                }
            } elseif (preg_match(
                '/^UTC([+\-])([0-9]{1,2})$/',
                $ps_id,
                $ha_matches
            )) {
                $this->id     = $ps_id;
                $this->offset = ($ha_matches[1] .
                                 ($ha_matches[2] * 3600)) * 1000;

                if (!($hb_isutc = $this->offset == 0)) {
                    $this->shortname = "UTC" .
                                       $ha_matches[1] .
                                       ltrim($ha_matches[2], "0");
                    $this->longname  = "UTC" .
                                       $ha_matches[1] .
                                       sprintf("%02d", $ha_matches[2]) .
                                       ":00";
                }
            } else {
                $this->id = "UTC";
                $hb_isutc = true;
            }

            if ($hb_isutc) {
                $this->shortname = $_DATE_TIMEZONE_DATA["UTC"]['shortname'];
                $this->longname  = $_DATE_TIMEZONE_DATA["UTC"]['longname'];
                $this->offset    = $_DATE_TIMEZONE_DATA["UTC"]['offset'];
            }
        }
    }


    // }}}
    // {{{ getDefault()

    /**
     * Returns a TimeZone object representing the system default time zone
     *
     * The system default time zone is initialized during the loading of
     * this file.
     *
     * @return   object     Date_TimeZone object of the default time zone
     * @access   public
     */
    public function getDefault()
    {
        return new Date_TimeZone($GLOBALS['_DATE_TIMEZONE_DEFAULT']);
    }


    // }}}
    // {{{ setDefault()

    /**
     * Sets the system default time zone to the time zone in $id
     *
     * @param string $id the time zone id to use
     *
     * @return   void
     * @access   public
     */
    public function setDefault($id)
    {
        if (Date_TimeZone::isValidID($id)) {
            $GLOBALS['_DATE_TIMEZONE_DEFAULT'] = $id;
        } else {
            return PEAR::raiseError("Invalid time zone ID '$id'");
        }
    }


    // }}}
    // {{{ isValidID()

    /**
     * Tests if given time zone ID (e.g. 'London/Europe') is valid and unique
     *
     * Checks if given ID is either represented in the $_DATE_TIMEZONE_DATA
     * time zone data, or is a UTC offset in one of the following forms,
     * i.e. an offset with no geographical or political base:
     *
     *  - <b>UTC[+/-][hh]:[mm]</b> - e.g. UTC+03:00
     *  - <b>UTC[+/-][hh][mm]</b>  - e.g. UTC-0530
     *  - <b>UTC[+/-][hh]</b>      - e.g. UTC+03
     *  - <b>UTC[+/-][h]</b>       - e.g. UTC-1
     *
     * (the last is not an ISO 8601 standard but is the preferred form)
     *
     * N.B. these are not sanctioned by any ISO standard, but the form of
     * the offset itself, i.e. the part after the characters 'UTC', is the
     * ISO 8601 standard form for representing this part.
     *
     * The form '<b>[+/-][h]</b>' is not ISO conformant, but ISO 8601 only
     * defines the form of the time zone offset of a particular time, that
     * is, it actually defines the form '<b><time>UTC[+/-][hh]</b>', and
     * its purview does not apparently cover the name of the time zone
     * itself. For this there is no official international standard (or
     * even a non- international standard).  The closest thing to a
     * sanctioning body is the 'tz' database
     * ({@link http://www.twinsun.com/tz/tz-link.htm})
     * which is run by volunteers but which is heavily relied upon by
     * various programming languages and the internet community.  However
     * they mainly define geographical/political time zone names of the
     * form 'London/Europe' because their main aim is to collate the time
     * zone definitions which are set by individual countries/states, not
     * to prescribe any standard.
     *
     * However it seems that the de facto standard to describe time zones
     * as non-geographically/politically-based areas where the local time
     * on all clocks reads the same seems to be the form '<b>UTC[+/-][h]</b>'
     * for integral numbers of hours, and '<b>UTC[+/-][hh]:[mm]</b>'
     * otherwise.
     * (See {@link http://en.wikipedia.org/wiki/List_of_time_zones})
     *
     * N.B. 'GMT' is also commonly used instead of 'UTC', but 'UTC' seems
     * to be technically preferred.  GMT-based IDs still exist in the 'tz
     * data-base', but beware of POSIX-style offsets which are the opposite
     * way round to what people normally expect.
     *
     * @param string $ps_id the time zone ID to test
     *
     * @return   bool       true if the supplied ID is valid
     * @access   public
     * @see      Date::setTZByID(), Date_TimeZone::Date_TimeZone()
     */
    public function isValidID($ps_id)
    {
        if (isset($GLOBALS['_DATE_TIMEZONE_DATA'][$ps_id])) {
            return true;
        } elseif (preg_match(
            '/^UTC[+\-]([0-9]{2,2}:?[0-5][0-9]|[0-9]{1,2})$/',
            $ps_id
        )) {
            return true;
        } else {
            return false;
        }
    }


    // }}}
    // {{{ isEqual()

    /**
     * Is this time zone equal to another
     *
     * Tests to see if this time zone is equal (ids match)
     * to a given Date_TimeZone object.
     *
     * @param object $tz the Date_TimeZone object to test
     *
     * @return   bool       true if this time zone is equal to the supplied
     *                       time zone
     * @access   public
     * @see      Date_TimeZone::isEquivalent()
     */
    public function isEqual($tz)
    {
        if (strcasecmp($this->id, $tz->id) == 0) {
            return true;
        } else {
            return false;
        }
    }


    // }}}
    // {{{ isEquivalent()

    /**
     * Is this time zone equivalent to another
     *
     * Tests to see if this time zone is equivalent to a given time zone object.
     * Equivalence in this context consists in the two time zones having:
     *
     *  - an equal offset from UTC in both standard and Summer time (if
     *              the time zones observe Summer time)
     *  - the same Summer time start and end rules, that is, the two time
     *              zones must switch from standard time to Summer time,
     *              and vice versa, on the same day and at the same time
     *
     * @param object $pm_tz the Date_TimeZone object to test, or a valid time
     *                       zone ID
     *
     * @return   bool       true if this time zone is equivalent to the supplied
     *                       time zone
     * @access   public
     * @see      Date_TimeZone::isEqual(), Date::inEquivalentTimeZones()
     */
    public function isEquivalent($pm_tz)
    {
        if (is_a($pm_tz, "Date_TimeZone")) {
            if ($pm_tz->getID() == $this->id) {
                return true;
            }
        } else {
            if (!Date_TimeZone::isValidID($pm_tz)) {
                return PEAR::raiseError(
                    "Invalid time zone ID '$pm_tz'",
                    DATE_ERROR_INVALIDTIMEZONE
                );
            }
            if ($pm_tz == $this->id) {
                return true;
            }

            $pm_tz = new Date_TimeZone($pm_tz);
        }

        if ($this->getRawOffset() == $pm_tz->getRawOffset() &&
            $this->hasDaylightTime() == $pm_tz->hasDaylightTime() &&
            $this->getDSTSavings() == $pm_tz->getDSTSavings() &&
            $this->getSummerTimeStartMonth() == $pm_tz->getSummerTimeStartMonth() &&
            $this->getSummerTimeStartDay() == $pm_tz->getSummerTimeStartDay() &&
            $this->getSummerTimeStartTime() == $pm_tz->getSummerTimeStartTime() &&
            $this->getSummerTimeEndMonth() == $pm_tz->getSummerTimeEndMonth() &&
            $this->getSummerTimeEndDay() == $pm_tz->getSummerTimeEndDay() &&
            $this->getSummerTimeEndTime() == $pm_tz->getSummerTimeEndTime()
            ) {
            return true;
        } else {
            return false;
        }
    }


    // }}}
    // {{{ hasDaylightTime()

    /**
     * Returns true if this zone observes daylight savings time
     *
     * @return   bool       true if this time zone has DST
     * @access   public
     */
    public function hasDaylightTime()
    {
        return $this->hasdst;
    }


    // }}}
    // {{{ getSummerTimeLimitDay()

    /**
     * Returns day on which Summer time starts or ends for given year
     *
     * The limit (start or end) code can take the following forms:
     *
     *  - <b>5</b>       -       the fifth of the month
     *  - <b>lastSun</b> -       the last Sunday in the month
     *  - <b>lastMon</b> -       the last Monday in the month
     *  - <b>Sun>=8</b>  -       first Sunday on or after the 8th
     *  - <b>Sun<=25</b> -       last Sunday on or before the 25th
     *
     * @param string $ps_summertimelimitcode code which specifies Summer time
     *                                        limit day
     * @param int    $pn_month               start or end month
     * @param int    $pn_year                year for which to calculate Summer
     *                                        time limit day
     *
     * @return   int
     * @access   private
     * @see      Date_TimeZone::getSummerTimeStartDay()
     * @since    Method available since Release 1.5.0
     */
    public function getSummerTimeLimitDay($ps_summertimelimitcode, $pn_month, $pn_year)
    {
        if (preg_match('/^[0-9]+$/', $ps_summertimelimitcode)) {
            $hn_day = $ps_summertimelimitcode;
        } else {
            if (!isset($ha_daysofweek)) {
                static $ha_daysofweek = ["Sun" => 0,
                                              "Mon" => 1,
                                              "Tue" => 2,
                                              "Wed" => 3,
                                              "Thu" => 4,
                                              "Fri" => 5,
                                              "Sat" => 6, ];
            }

            if (preg_match(
                '/^last(Sun|Mon|Tue|Wed|Thu|Fri|Sat)$/',
                $ps_summertimelimitcode,
                $ha_matches
            )) {
                [$hn_nmyear, $hn_nextmonth, $hn_nmday] =
                    explode(" ", Date_Calc::beginOfMonthBySpan(
                        1,
                        $pn_month,
                        $pn_year,
                        "%Y %m %d"
                    ));
                [$hn_year, $hn_month, $hn_day] =
                    explode(
                        " ",
                        Date_Calc::prevDayOfWeek(
                            $ha_daysofweek[$ha_matches[1]],
                            $hn_nmday,
                            $hn_nextmonth,
                            $hn_nmyear,
                            "%Y %m %d",
                            false
                        )
                    ); // not including
                // this day

                if ($hn_month != $pn_month) {
                    // This code happen legitimately if the calendar jumped some days
                    // e.g. in a calendar switch, or the limit day is badly defined:
                    //
                    $hn_day = Date_Calc::getFirstDayOfMonth($pn_month, $pn_year);
                }
            } elseif (preg_match(
                '/^(Sun|Mon|Tue|Wed|Thu|Fri|Sat)([><]=)([0-9]+)$/',
                $ps_summertimelimitcode,
                $ha_matches
            )) {
                if ($ha_matches[2] == "<=") {
                    [$hn_year, $hn_month, $hn_day] =
                        explode(
                            " ",
                            Date_Calc::prevDayOfWeek(
                                $ha_daysofweek[$ha_matches[1]],
                                $ha_matches[3],
                                $pn_month,
                                $pn_year,
                                "%Y %m %d",
                                true
                            )
                        ); // including
                    // this day

                    if ($hn_month != $pn_month) {
                        $hn_day = Date_Calc::getFirstDayOfMonth($pn_month, $pn_year);
                    }
                } else {
                    [$hn_year, $hn_month, $hn_day] =
                        explode(
                            " ",
                            Date_Calc::nextDayOfWeek(
                                $ha_daysofweek[$ha_matches[1]],
                                $ha_matches[3],
                                $pn_month,
                                $pn_year,
                                "%Y %m %d",
                                true
                            )
                        ); // including
                    // this day

                    if ($hn_month != $pn_month) {
                        $hn_day = Date_Calc::daysInMonth($pn_month, $pn_year);
                    }
                }
            }
        }

        return $hn_day;
    }


    // }}}
    // {{{ inDaylightTime()

    /**
     * Returns whether the given date/time is in DST for this time zone
     *
     * Works for all years, positive and negative.  Possible problems
     * are that when the clocks go forward, there is an invalid hour
     * which is skipped.  If a time in this hour is specified, this
     * function returns an error.  When the clocks go back, there is an
     * hour which is repeated, that is, the hour is gone through twice -
     * once in Summer time and once in standard time.  If this time
     * is specified, then this function returns '$pb_repeatedhourdefault',
     * because there is no way of knowing which is correct, and
     * both possibilities are equally likely.
     *
     * Also bear in mind that the clocks go forward at the instant of
     * the hour specified in the time-zone array below, and if this
     * exact hour is specified then the clocks have actually changed,
     * and this function reflects this.
     *
     * @param object $pm_date                Date object to test or array of
     *                                        day, month, year, seconds past
     *                                        midnight
     * @param bool   $pb_repeatedhourdefault value to return if repeated hour is
     *                                        specified (defaults to false)
     *
     * @return   bool       true if this date is in Summer time for this time
     *                       zone
     * @access   public
     * @see      Date_TimeZone::inDaylightTimeStandard()
     */
    public function inDaylightTime($pm_date, $pb_repeatedhourdefault = false)
    {
        if (!$this->hasdst) {
            return false;
        }

        if (is_a($pm_date, "Date")) {
            $hn_day     = $pm_date->getDay();
            $hn_month   = $pm_date->getMonth();
            $hn_year    = $pm_date->getYear();
            $hn_seconds = $pm_date->getSecondsPastMidnight();
        } else {
            $hn_day     = $pm_date[0];
            $hn_month   = $pm_date[1];
            $hn_year    = $pm_date[2];
            $hn_seconds = $pm_date[3];  // seconds past midnight
        }

        if (($this->on_summertimestartmonth < $this->on_summertimeendmonth &&
             $hn_month >= $this->on_summertimestartmonth &&
             $hn_month <= $this->on_summertimeendmonth) ||
            ($this->on_summertimestartmonth > $this->on_summertimeendmonth &&
             $hn_month >= $this->on_summertimestartmonth &&
             $hn_month <= $this->on_summertimeendmonth)
            ) {
            if ($hn_month == $this->on_summertimestartmonth) {
                $hn_startday =
                    $this->getSummerTimeLimitDay(
                        $this->os_summertimestartday,
                        $this->on_summertimestartmonth,
                        $hn_year
                    );

                if ($hn_day < $hn_startday) {
                    return false;
                } elseif ($hn_day > $hn_startday) {
                    return true;
                } elseif (($hn_gmt = $hn_seconds * 1000 - $this->offset) -
                           $this->on_summertimeoffset >=
                           $this->on_summertimestarttime) {
                    return true;
                } elseif (($hn_gmt = $hn_seconds * 1000 - $this->offset) >=
                           $this->on_summertimestarttime) {
                    return PEAR::raiseError(
                        "Invalid time specified for date '" .
                                            Date_Calc::dateFormat(
                                                $hn_day,
                                                $hn_month,
                                                $hn_year,
                                                "%Y-%m-%d"
                                            ) .
                                            "'",
                        DATE_ERROR_INVALIDTIME
                    );
                } else {
                    return false;
                }
            } elseif ($hn_month == $this->on_summertimeendmonth) {
                $hn_endday =
                    $this->getSummerTimeLimitDay(
                        $this->os_summertimeendday,
                        $this->on_summertimeendmonth,
                        $hn_year
                    );

                if ($hn_day < $hn_endday) {
                    return true;
                } elseif ($hn_day > $hn_endday) {
                    return false;
                } elseif (($hn_gmt = $hn_seconds * 1000 - $this->offset) -
                           $this->on_summertimeoffset >=
                           $this->on_summertimeendtime) {
                    return false;
                } elseif ($hn_gmt >= $this->on_summertimeendtime) {
                    // There is a 50:50 chance that it's Summer time, but there
                    // is no way of knowing (the hour is repeated), so return
                    // default:
                    //
                    return $pb_repeatedhourdefault;
                } else {
                    return true;
                }
            }

            return true;
        }

        return false;
    }


    // }}}
    // {{{ inDaylightTimeStandard()

    /**
     * Returns whether the given date/time in local standard time is
     * in Summer time
     *
     * For example, if the clocks go forward at 1.00 standard time,
     * then if the specified date/time is at 1.00, the function will
     * return true, although the correct local time will actually
     * be 2.00.
     *
     * This function is reliable for all dates and times, unlike the
     * related function '{@link Date_TimeZone::inDaylightTime()}',
     * which will fail if passed
     * an invalid time (the skipped hour) and will be wrong half the
     * time if passed an ambiguous time (the repeated hour).
     *
     * @param object $pm_date Date object to test or array of day, month, year,
     *                         seconds past midnight
     *
     * @return   bool       true if this date is in Summer time for this time
     *                       zone
     * @access   public
     * @see      Date_TimeZone::inDaylightTime()
     * @since    Method available since Release 1.5.0
     */
    public function inDaylightTimeStandard($pm_date)
    {
        if (!$this->hasdst) {
            return false;
        }

        if (is_a($pm_date, "Date")) {
            $hn_day     = $pm_date->getDay();
            $hn_month   = $pm_date->getMonth();
            $hn_year    = $pm_date->getYear();
            $hn_seconds = $pm_date->getSecondsPastMidnight();
        } else {
            $hn_day     = $pm_date[0];
            $hn_month   = $pm_date[1];
            $hn_year    = $pm_date[2];
            $hn_seconds = $pm_date[3];
        }

        if (($this->on_summertimestartmonth < $this->on_summertimeendmonth &&
             $hn_month >= $this->on_summertimestartmonth &&
             $hn_month <= $this->on_summertimeendmonth) ||
            ($this->on_summertimestartmonth > $this->on_summertimeendmonth &&
             $hn_month >= $this->on_summertimestartmonth &&
             $hn_month <= $this->on_summertimeendmonth)
            ) {
            if ($hn_month == $this->on_summertimestartmonth) {
                $hn_startday =
                    $this->getSummerTimeLimitDay(
                        $this->os_summertimestartday,
                        $this->on_summertimestartmonth,
                        $hn_year
                    );

                if ($hn_day < $hn_startday) {
                    return false;
                } elseif ($hn_day > $hn_startday) {
                    return true;
                } elseif ($hn_seconds * 1000 - $this->offset >=
                           $this->on_summertimestarttime) {
                    return true;
                } else {
                    return false;
                }
            } elseif ($hn_month == $this->on_summertimeendmonth) {
                $hn_endday =
                    $this->getSummerTimeLimitDay(
                        $this->os_summertimeendday,
                        $this->on_summertimeendmonth,
                        $hn_year
                    );

                if ($hn_day < $hn_endday) {
                    return true;
                } elseif ($hn_day > $hn_endday) {
                    return false;
                } elseif ($hn_seconds * 1000 - $this->offset >=
                           $this->on_summertimeendtime) {
                    return false;
                } else {
                    return true;
                }
            }

            return true;
        }

        return false;
    }


    // }}}
    // {{{ getDSTSavings()

    /**
     * Get the DST offset for this time zone
     *
     * Returns the DST offset of this time zone, in milliseconds,
     * if the zone observes DST, zero otherwise.  If the offset is not
     * known, the function returns one hour.
     *
     * @return   int        the DST offset, in milliseconds or nought if the
     *                       zone does not observe DST
     * @access   public
     */
    public function getDSTSavings()
    {
        if ($this->hasdst) {
            // If offset is not specified, guess one hour.  (This is almost
            // always correct anyway).  This cannot be improved upon, because
            // where it is unset, the offset is either unknowable because the
            // time-zone covers more than one political area (which may have
            // different Summer time policies), or they might all have the
            // same policy, but there is no way to automatically maintain
            // this data at the moment, and manually it is simply not worth
            // the bother.  If a user wants this functionality and refuses
            // to use the standard time-zone IDs, then he can always update
            // the array himself.
            //
            return $this->on_summertimeoffset ??
                         3600000;
        } else {
            return 0;
        }
    }


    // }}}
    // {{{ getRawOffset()

    /**
     * Returns the raw (non-DST-corrected) offset from UTC/GMT for this time
     * zone
     *
     * @return   int        the offset, in milliseconds
     * @access   public
     * @see      Date_TimeZone::getOffset()
     */
    public function getRawOffset()
    {
        return $this->offset;
    }


    // }}}
    // {{{ getOffset()

    /**
     * Returns the DST-corrected offset from UTC for the given date
     *
     * Gets the offset to UTC for a given date/time, taking into
     * account daylight savings time, if the time zone observes it and if
     * it is in effect.
     *
     * N.B. that the offset is calculated historically
     * and in the future according to the current Summer time rules,
     * and so this function is proleptically correct, but not necessarily
     * historically correct.  (Although if you want to be correct about
     * times in the distant past, this class is probably not for you
     * because the whole notion of time zones does not apply, and
     * historically there are so many time zone changes, Summer time
     * rule changes, name changes, calendar changes, that calculating
     * this sort of information is beyond the scope of this package
     * altogether.)
     *
     * @param mixed $pm_insummertime a boolean specifying whether or not the
     *                                date is in Summer time, or,
     *                               a Date object to test for this condition
     *
     * @return   int        the corrected offset to UTC in milliseconds
     * @access   public
     * @see      Date_TimeZone::getRawOffset(), Date::getTZOffset()
     */
    public function getOffset($pm_insummertime)
    {
        if ($this->hasdst) {
            if (is_a($pm_insummertime, "Date")) {
                $hb_insummertime = $pm_insummertime->inDaylightTime();
                if (PEAR::isError($hb_insummertime)) {
                    return $hb_insummertime;
                }
            } else {
                $hb_insummertime = $pm_insummertime;
            }

            if ($hb_insummertime) {
                return $this->offset + $this->getDSTSavings();
            }
        }

        return $this->offset;
    }


    // }}}
    // {{{ getAvailableIDs()

    /**
     * Returns the list of valid time zone id strings
     *
     * @return   array      an array of strings with the valid time zone IDs
     * @access   public
     */
    public function getAvailableIDs()
    {
        return array_keys($GLOBALS['_DATE_TIMEZONE_DATA']);
    }


    // }}}
    // {{{ getID()

    /**
     * Returns the time zone id for this time zone, e.g. "America/Chicago"
     *
     * @return   string     the time zone ID
     * @access   public
     */
    public function getID()
    {
        return $this->id;
    }


    // }}}
    // {{{ getLongName()

    /**
     * Returns the long name for this time zone
     *
     * Long form of time zone name, e.g. 'Greenwich Mean Time'. Additionally
     * a Date object can be passed in which case the Summer time name will
     * be returned instead if the date falls in Summer time, e.g. 'British
     * Summer Time', or a Boolean can be passed which explicitly specifies
     * whether the date is in Summer time.
     *
     * N.B. this is not a unique identifier - for this purpose use the
     * time zone ID.
     *
     * @param mixed $pm_insummertime a boolean specifying whether or not the
     *                                date is in Summer time, or,
     *                               a Date object to test for this condition
     *
     * @return   string     the long name
     * @access   public
     * @see      Date_TimeZone::getShortName(), Date_TimeZone::getDSTLongName(),
     *            Date::getTZLongName()
     */
    public function getLongName($pm_insummertime = false)
    {
        if ($this->hasdst) {
            if (is_a($pm_insummertime, "Date")) {
                $hb_insummertime = $pm_insummertime->inDaylightTime();
                if (PEAR::isError($hb_insummertime)) {
                    return $hb_insummertime;
                }
            } else {
                $hb_insummertime = $pm_insummertime;
            }

            if ($hb_insummertime) {
                return $this->dstlongname;
            }
        }

        return $this->longname;
    }


    // }}}
    // {{{ getShortName()

    /**
     * Returns the short name for this time zone
     *
     * Returns abbreviated form of time zone name, e.g. 'GMT'. Additionally
     * a Date object can be passed in which case the Summer time name will
     * be returned instead if the date falls in Summer time, e.g. 'BST'.
     *
     * N.B. this is not a unique identifier - for this purpose use the
     * time zone ID.
     *
     * @param mixed $pm_insummertime a boolean specifying whether or not the
     *                                date is in Summer time, or,
     *                               a Date object to test for this condition
     *
     * @return   string     the short name
     * @access   public
     * @see      Date_TimeZone::getLongName(), Date_TimeZone::getDSTShortName(),
     *            Date::getTZShortName()
     */
    public function getShortName($pm_insummertime = false)
    {
        if ($this->hasdst) {
            if (is_a($pm_insummertime, "Date")) {
                $hb_insummertime = $pm_insummertime->inDaylightTime();
                if (PEAR::isError($hb_insummertime)) {
                    return $hb_insummertime;
                }
            } else {
                $hb_insummertime = $pm_insummertime;
            }

            if ($hb_insummertime) {
                return $this->dstshortname;
            }
        }

        return $this->shortname;
    }


    // }}}
    // {{{ getDSTLongName()

    /**
     * Returns the DST long name for this time zone, e.g.
     * 'Central Daylight Time'
     *
     * @return   string     the daylight savings time long name
     * @access   public
     * @see      Date_TimeZone::getDSTShortName(), Date_TimeZone::getLongName()
     */
    public function getDSTLongName()
    {
        return $this->hasdst ? $this->dstlongname : $this->longname;
    }


    // }}}
    // {{{ getDSTShortName()

    /**
     * Returns the DST short name for this time zone, e.g. 'CDT'
     *
     * @return   string     the daylight savings time short name
     * @access   public
     * @see      Date_TimeZone::getDSTLongName(), Date_TimeZone::getShortName()
     */
    public function getDSTShortName()
    {
        return $this->hasdst ? $this->dstshortname : $this->shortname;
    }


    // }}}
    // {{{ getSummerTimeStartMonth()

    /**
     * Returns the month number in which Summer time starts
     *
     * @return   int        integer representing the month (1 to 12)
     * @access   public
     * @see      Date_TimeZone::getSummerTimeEndMonth(),
     *            Date_TimeZone::getSummerTimeStartTime(),
     *            Date_TimeZone::getSummerTimeStartDay()
     * @since    Method available since Release 1.5.0
     */
    public function getSummerTimeStartMonth()
    {
        return $this->hasdst ? $this->on_summertimestartmonth : null;
    }


    // }}}
    // {{{ getSummerTimeStartDay()

    /**
     * Returns a code representing the day on which Summer time starts
     *
     * Returns a string in one of the following forms:
     *
     *  - <b>5</b>       - the fifth of the month
     *  - <b>lastSun</b> - the last Sunday in the month
     *  - <b>lastMon</b> - the last Monday in the month
     *  - <b>Sun>=8</b>  - first Sunday on or after the 8th
     *  - <b>Sun<=25</b> - last Sunday on or before the 25th
     *
     * @return   string
     * @access   public
     * @see      Date_TimeZone::getSummerTimeEndDay(),
     *            Date_TimeZone::getSummerTimeStartTime(),
     *            Date_TimeZone::getSummerTimeStartMonth(),
     *            Date_TimeZone::getSummerTimeLimitDay()
     * @since    Method available since Release 1.5.0
     */
    public function getSummerTimeStartDay()
    {
        return $this->hasdst ? $this->os_summertimestartday : null;
    }


    // }}}
    // {{{ getSummerTimeStartTime()

    /**
     * Returns the time of day at which which Summer time starts
     *
     * The returned time is an offset, in milliseconds, from midnight UTC.  Note
     * that the offset can be negative, which represents the fact that the time
     * zone is East of Greenwich, and that when the clocks change locally, the
     * time in Greenwich is actually a time belonging to the previous day in
     * UTC.  This, obviously, is unhelpful if you want to know the local time
     * at which the clocks change, but it is of immense value for the purpose
     * of calculation.
     *
     * @return   int        integer representing the month (1 to 12)
     * @access   public
     * @see      Date_TimeZone::getSummerTimeEndTime(),
     *            Date_TimeZone::getSummerTimeStartDay(),
     *            Date_TimeZone::getSummerTimeStartMonth()
     * @since    Method available since Release 1.5.0
     */
    public function getSummerTimeStartTime()
    {
        return $this->hasdst ? $this->on_summertimestarttime : null;
    }


    // }}}
    // {{{ getSummerTimeEndMonth()

    /**
     * Returns the month number in which Summer time ends
     *
     * @return   int        integer representing the month (1 to 12)
     * @access   public
     * @see      Date_TimeZone::getSummerTimeStartMonth(),
     *            Date_TimeZone::getSummerTimeEndTime(),
     *            Date_TimeZone::getSummerTimeEndDay()
     * @since    Method available since Release 1.5.0
     */
    public function getSummerTimeEndMonth()
    {
        return $this->hasdst ? $this->on_summertimeendmonth : null;
    }


    // }}}
    // {{{ getSummerTimeEndDay()

    /**
     * Returns a code representing the day on which Summer time ends
     *
     * Returns a string in one of the following forms:
     *
     *  - <b>5</b>       - the fifth of the month
     *  - <b>lastSun</b> - the last Sunday in the month
     *  - <b>lastMon</b> - the last Monday in the month
     *  - <b>Sun>=8</b>  - first Sunday on or after the 8th
     *  - <b>Sun<=25</b> - last Sunday on or before the 25th
     *
     * @return   string
     * @access   public
     * @see      Date_TimeZone::getSummerTimeStartDay(),
     *            Date_TimeZone::getSummerTimeEndTime(),
     *            Date_TimeZone::getSummerTimeEndMonth(),
     *            Date_TimeZone::getSummerTimeLimitDay()
     * @since    Method available since Release 1.5.0
     */
    public function getSummerTimeEndDay()
    {
        return $this->hasdst ? $this->os_summertimeendday : null;
    }


    // }}}
    // {{{ getSummerTimeEndTime()

    /**
     * Returns the time of day at which which Summer time ends
     *
     * @return   int        integer representing the month (1 to 12)
     * @access   public
     * @see      Date_TimeZone::getSummerTimeStartTime(),
     *            Date_TimeZone::getSummerTimeEndDay(),
     *            Date_TimeZone::getSummerTimeEndMonth()
     * @since    Method available since Release 1.5.0
     */
    public function getSummerTimeEndTime()
    {
        return $this->hasdst ? $this->on_summertimeendtime : null;
    }


    // }}}
}

// }}}

/**
 * Time Zone Data (correct as of 15.iii.2007)
 *
 * N.B. offsets are in milliseconds
 *
 * @global array $GLOBALS['_DATE_TIMEZONE_DATA']
 */
$GLOBALS['_DATE_TIMEZONE_DATA'] = [
    'Africa/Abidjan' => [
        'offset' => 0,
        'shortname' => 'GMT',
        'dstshortname' => null,
        'longname' => 'Greenwich Mean Time', ],
    'Africa/Accra' => [
        'offset' => 0,
        'shortname' => 'GMT',
        'dstshortname' => null,
        'longname' => 'Greenwich Mean Time', ],
    'Africa/Addis_Ababa' => [
        'offset' => 10800000,
        'shortname' => 'EAT',
        'dstshortname' => null,
        'longname' => 'Eastern African Time', ],
    'Africa/Algiers' => [
        'offset' => 3600000,
        'shortname' => 'CET',
        'dstshortname' => null,
        'longname' => 'Central European Time', ],
    'Africa/Asmara' => [
        'offset' => 10800000,
        'shortname' => 'EAT',
        'dstshortname' => null,
        'longname' => 'Eastern African Time', ],
    'Africa/Asmera' => [
        'offset' => 10800000,
        'shortname' => 'EAT',
        'dstshortname' => null,
        'longname' => 'Eastern African Time', ],
    'Africa/Bamako' => [
        'offset' => 0,
        'shortname' => 'GMT',
        'dstshortname' => null,
        'longname' => 'Greenwich Mean Time', ],
    'Africa/Bangui' => [
        'offset' => 3600000,
        'shortname' => 'WAT',
        'dstshortname' => null,
        'longname' => 'Western African Time', ],
    'Africa/Banjul' => [
        'offset' => 0,
        'shortname' => 'GMT',
        'dstshortname' => null,
        'longname' => 'Greenwich Mean Time', ],
    'Africa/Bissau' => [
        'offset' => 0,
        'shortname' => 'GMT',
        'dstshortname' => null,
        'longname' => 'Greenwich Mean Time', ],
    'Africa/Blantyre' => [
        'offset' => 7200000,
        'shortname' => 'CAT',
        'dstshortname' => null,
        'longname' => 'Central African Time', ],
    'Africa/Brazzaville' => [
        'offset' => 3600000,
        'shortname' => 'WAT',
        'dstshortname' => null,
        'longname' => 'Western African Time', ],
    'Africa/Bujumbura' => [
        'offset' => 7200000,
        'shortname' => 'CAT',
        'dstshortname' => null,
        'longname' => 'Central African Time', ],
    'Africa/Cairo' => [
        'offset' => 7200000,
        'shortname' => 'EET',
        'dstshortname' => 'EEST',
        'longname' => 'Eastern European Time',
        'dstlongname' => 'Eastern European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 4,
        'summertimestartday' => 'lastFri',
        'summertimestarttime' => -7200000,
        'summertimeendmonth' => 8,
        'summertimeendday' => 'lastThu',
        'summertimeendtime' => 75600000, ],
    'Africa/Casablanca' => [
        'offset' => 0,
        'shortname' => 'WET',
        'dstshortname' => null,
        'longname' => 'Western European Time', ],
    'Africa/Ceuta' => [
        'offset' => 3600000,
        'shortname' => 'CET',
        'dstshortname' => 'CEST',
        'longname' => 'Central European Time',
        'dstlongname' => 'Central European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Africa/Conakry' => [
        'offset' => 0,
        'shortname' => 'GMT',
        'dstshortname' => null,
        'longname' => 'Greenwich Mean Time', ],
    'Africa/Dakar' => [
        'offset' => 0,
        'shortname' => 'GMT',
        'dstshortname' => null,
        'longname' => 'Greenwich Mean Time', ],
    'Africa/Dar_es_Salaam' => [
        'offset' => 10800000,
        'shortname' => 'EAT',
        'dstshortname' => null,
        'longname' => 'Eastern African Time', ],
    'Africa/Djibouti' => [
        'offset' => 10800000,
        'shortname' => 'EAT',
        'dstshortname' => null,
        'longname' => 'Eastern African Time', ],
    'Africa/Douala' => [
        'offset' => 3600000,
        'shortname' => 'WAT',
        'dstshortname' => null,
        'longname' => 'Western African Time', ],
    'Africa/El_Aaiun' => [
        'offset' => 0,
        'shortname' => 'WET',
        'dstshortname' => null,
        'longname' => 'Western European Time', ],
    'Africa/Freetown' => [
        'offset' => 0,
        'shortname' => 'GMT',
        'dstshortname' => null,
        'longname' => 'Greenwich Mean Time', ],
    'Africa/Gaborone' => [
        'offset' => 7200000,
        'shortname' => 'CAT',
        'dstshortname' => null,
        'longname' => 'Central African Time', ],
    'Africa/Harare' => [
        'offset' => 7200000,
        'shortname' => 'CAT',
        'dstshortname' => null,
        'longname' => 'Central African Time', ],
    'Africa/Johannesburg' => [
        'offset' => 7200000,
        'shortname' => 'SAST',
        'dstshortname' => null,
        'longname' => 'South Africa Standard Time', ],
    'Africa/Kampala' => [
        'offset' => 10800000,
        'shortname' => 'EAT',
        'dstshortname' => null,
        'longname' => 'Eastern African Time', ],
    'Africa/Khartoum' => [
        'offset' => 10800000,
        'shortname' => 'EAT',
        'dstshortname' => null,
        'longname' => 'Eastern African Time', ],
    'Africa/Kigali' => [
        'offset' => 7200000,
        'shortname' => 'CAT',
        'dstshortname' => null,
        'longname' => 'Central African Time', ],
    'Africa/Kinshasa' => [
        'offset' => 3600000,
        'shortname' => 'WAT',
        'dstshortname' => null,
        'longname' => 'Western African Time', ],
    'Africa/Lagos' => [
        'offset' => 3600000,
        'shortname' => 'WAT',
        'dstshortname' => null,
        'longname' => 'Western African Time', ],
    'Africa/Libreville' => [
        'offset' => 3600000,
        'shortname' => 'WAT',
        'dstshortname' => null,
        'longname' => 'Western African Time', ],
    'Africa/Lome' => [
        'offset' => 0,
        'shortname' => 'GMT',
        'dstshortname' => null,
        'longname' => 'Greenwich Mean Time', ],
    'Africa/Luanda' => [
        'offset' => 3600000,
        'shortname' => 'WAT',
        'dstshortname' => null,
        'longname' => 'Western African Time', ],
    'Africa/Lubumbashi' => [
        'offset' => 7200000,
        'shortname' => 'CAT',
        'dstshortname' => null,
        'longname' => 'Central African Time', ],
    'Africa/Lusaka' => [
        'offset' => 7200000,
        'shortname' => 'CAT',
        'dstshortname' => null,
        'longname' => 'Central African Time', ],
    'Africa/Malabo' => [
        'offset' => 3600000,
        'shortname' => 'WAT',
        'dstshortname' => null,
        'longname' => 'Western African Time', ],
    'Africa/Maputo' => [
        'offset' => 7200000,
        'shortname' => 'CAT',
        'dstshortname' => null,
        'longname' => 'Central African Time', ],
    'Africa/Maseru' => [
        'offset' => 7200000,
        'shortname' => 'SAST',
        'dstshortname' => null,
        'longname' => 'South Africa Standard Time', ],
    'Africa/Mbabane' => [
        'offset' => 7200000,
        'shortname' => 'SAST',
        'dstshortname' => null,
        'longname' => 'South Africa Standard Time', ],
    'Africa/Mogadishu' => [
        'offset' => 10800000,
        'shortname' => 'EAT',
        'dstshortname' => null,
        'longname' => 'Eastern African Time', ],
    'Africa/Monrovia' => [
        'offset' => 0,
        'shortname' => 'GMT',
        'dstshortname' => null,
        'longname' => 'Greenwich Mean Time', ],
    'Africa/Nairobi' => [
        'offset' => 10800000,
        'shortname' => 'EAT',
        'dstshortname' => null,
        'longname' => 'Eastern African Time', ],
    'Africa/Ndjamena' => [
        'offset' => 3600000,
        'shortname' => 'WAT',
        'dstshortname' => null,
        'longname' => 'Western African Time', ],
    'Africa/Niamey' => [
        'offset' => 3600000,
        'shortname' => 'WAT',
        'dstshortname' => null,
        'longname' => 'Western African Time', ],
    'Africa/Nouakchott' => [
        'offset' => 0,
        'shortname' => 'GMT',
        'dstshortname' => null,
        'longname' => 'Greenwich Mean Time', ],
    'Africa/Ouagadougou' => [
        'offset' => 0,
        'shortname' => 'GMT',
        'dstshortname' => null,
        'longname' => 'Greenwich Mean Time', ],
    'Africa/Porto-Novo' => [
        'offset' => 3600000,
        'shortname' => 'WAT',
        'dstshortname' => null,
        'longname' => 'Western African Time', ],
    'Africa/Sao_Tome' => [
        'offset' => 0,
        'shortname' => 'GMT',
        'dstshortname' => null,
        'longname' => 'Greenwich Mean Time', ],
    'Africa/Timbuktu' => [
        'offset' => 0,
        'shortname' => 'GMT',
        'dstshortname' => null,
        'longname' => 'Greenwich Mean Time', ],
    'Africa/Tripoli' => [
        'offset' => 7200000,
        'shortname' => 'EET',
        'dstshortname' => null,
        'longname' => 'Eastern European Time', ],
    'Africa/Tunis' => [
        'offset' => 3600000,
        'shortname' => 'CET',
        'dstshortname' => 'CEST',
        'longname' => 'Central European Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Africa/Windhoek' => [
        'offset' => 3600000,
        'shortname' => 'WAT',
        'dstshortname' => 'WAST',
        'longname' => 'Western African Time',
        'dstlongname' => 'Western African Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 9,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 4,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 0, ],
    'America/Adak' => [
        'offset' => -36000000,
        'shortname' => 'HAST',
        'dstshortname' => 'HADT',
        'longname' => 'Hawaii-Aleutian Standard Time',
        'dstlongname' => 'Hawaii-Aleutian Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 43200000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 39600000, ],
    'America/Anchorage' => [
        'offset' => -32400000,
        'shortname' => 'AKST',
        'dstshortname' => 'AKDT',
        'longname' => 'Alaska Standard Time',
        'dstlongname' => 'Alaska Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 39600000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 36000000, ],
    'America/Anguilla' => [
        'offset' => -14400000,
        'shortname' => 'AST',
        'dstshortname' => null,
        'longname' => 'Atlantic Standard Time', ],
    'America/Antigua' => [
        'offset' => -14400000,
        'shortname' => 'AST',
        'dstshortname' => null,
        'longname' => 'Atlantic Standard Time', ],
    'America/Araguaina' => [
        'offset' => -10800000,
        'shortname' => 'BRT',
        'dstshortname' => null,
        'longname' => 'Brazil Time',
        'dstlongname' => 'Brazil Summer Time', ],
    'America/Argentina/Buenos_Aires' => [
        'offset' => -10800000,
        'shortname' => 'ART',
        'dstshortname' => 'ARST',
        'longname' => 'Argentina Time',
        'dstlongname' => 'Argentina Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => 0,
        'summertimeendmonth' => 3,
        'summertimeendday' => 'Sun>=15',
        'summertimeendtime' => 0, ],
    'America/Argentina/Catamarca' => [
        'offset' => -10800000,
        'shortname' => 'ART',
        'dstshortname' => 'ARST',
        'longname' => 'Argentina Time',
        'dstlongname' => 'Argentina Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => 0,
        'summertimeendmonth' => 3,
        'summertimeendday' => 'Sun>=15',
        'summertimeendtime' => 0, ],
    'America/Argentina/ComodRivadavia' => [
        'offset' => -10800000,
        'shortname' => 'ART',
        'dstshortname' => 'ARST',
        'longname' => 'Argentina Time',
        'dstlongname' => 'Argentina Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => 0,
        'summertimeendmonth' => 3,
        'summertimeendday' => 'Sun>=15',
        'summertimeendtime' => 0, ],
    'America/Argentina/Cordoba' => [
        'offset' => -10800000,
        'shortname' => 'ART',
        'dstshortname' => 'ARST',
        'longname' => 'Argentina Time',
        'dstlongname' => 'Argentina Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => 0,
        'summertimeendmonth' => 3,
        'summertimeendday' => 'Sun>=15',
        'summertimeendtime' => 0, ],
    'America/Argentina/Jujuy' => [
        'offset' => -10800000,
        'shortname' => 'ART',
        'dstshortname' => 'ARST',
        'longname' => 'Argentina Time',
        'dstlongname' => 'Argentina Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => 0,
        'summertimeendmonth' => 3,
        'summertimeendday' => 'Sun>=15',
        'summertimeendtime' => 0, ],
    'America/Argentina/La_Rioja' => [
        'offset' => -10800000,
        'shortname' => 'ART',
        'dstshortname' => 'ARST',
        'longname' => 'Argentina Time',
        'dstlongname' => 'Argentina Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => 0,
        'summertimeendmonth' => 3,
        'summertimeendday' => 'Sun>=15',
        'summertimeendtime' => 0, ],
    'America/Argentina/Mendoza' => [
        'offset' => -10800000,
        'shortname' => 'ART',
        'dstshortname' => 'ARST',
        'longname' => 'Argentina Time',
        'dstlongname' => 'Argentina Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => 0,
        'summertimeendmonth' => 3,
        'summertimeendday' => 'Sun>=15',
        'summertimeendtime' => 0, ],
    'America/Argentina/Rio_Gallegos' => [
        'offset' => -10800000,
        'shortname' => 'ART',
        'dstshortname' => 'ARST',
        'longname' => 'Argentina Time',
        'dstlongname' => 'Argentina Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => 0,
        'summertimeendmonth' => 3,
        'summertimeendday' => 'Sun>=15',
        'summertimeendtime' => 0, ],
    'America/Argentina/San_Juan' => [
        'offset' => -10800000,
        'shortname' => 'ART',
        'dstshortname' => 'ARST',
        'longname' => 'Argentina Time',
        'dstlongname' => 'Argentina Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => 0,
        'summertimeendmonth' => 3,
        'summertimeendday' => 'Sun>=15',
        'summertimeendtime' => 0, ],
    'America/Argentina/Tucuman' => [
        'offset' => -10800000,
        'shortname' => 'ART',
        'dstshortname' => 'ARST',
        'longname' => 'Argentina Time',
        'dstlongname' => 'Argentina Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => 0,
        'summertimeendmonth' => 3,
        'summertimeendday' => 'Sun>=15',
        'summertimeendtime' => 0, ],
    'America/Argentina/Ushuaia' => [
        'offset' => -10800000,
        'shortname' => 'ART',
        'dstshortname' => 'ARST',
        'longname' => 'Argentina Time',
        'dstlongname' => 'Argentina Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => 0,
        'summertimeendmonth' => 3,
        'summertimeendday' => 'Sun>=15',
        'summertimeendtime' => 0, ],
    'America/Aruba' => [
        'offset' => -14400000,
        'shortname' => 'AST',
        'dstshortname' => null,
        'longname' => 'Atlantic Standard Time', ],
    'America/Asuncion' => [
        'offset' => -14400000,
        'shortname' => 'PYT',
        'dstshortname' => 'PYST',
        'longname' => 'Paraguay Time',
        'dstlongname' => 'Paraguay Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'Sun>=15',
        'summertimestarttime' => 14400000,
        'summertimeendmonth' => 3,
        'summertimeendday' => 'Sun>=8',
        'summertimeendtime' => 10800000, ],
    'America/Atikokan' => [
        'offset' => -18000000,
        'shortname' => 'EST',
        'dstshortname' => null,
        'longname' => 'Eastern Standard Time', ],
    'America/Atka' => [
        'offset' => -36000000,
        'shortname' => 'HAST',
        'dstshortname' => 'HADT',
        'longname' => 'Hawaii-Aleutian Standard Time',
        'dstlongname' => 'Hawaii-Aleutian Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 43200000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 39600000, ],
    'America/Bahia' => [
        'offset' => -10800000,
        'shortname' => 'BRT',
        'dstshortname' => null,
        'longname' => 'Brazil Eastern Time', ],
    'America/Barbados' => [
        'offset' => -14400000,
        'shortname' => 'AST',
        'dstshortname' => null,
        'longname' => 'Atlantic Standard Time', ],
    'America/Belem' => [
        'offset' => -10800000,
        'shortname' => 'BRT',
        'dstshortname' => null,
        'longname' => 'Brazil Time', ],
    'America/Belize' => [
        'offset' => -21600000,
        'shortname' => 'CST',
        'dstshortname' => null,
        'longname' => 'Central Standard Time', ],
    'America/Blanc-Sablon' => [
        'offset' => -14400000,
        'shortname' => 'AST',
        'dstshortname' => null,
        'longname' => 'Atlantic Standard Time', ],
    'America/Boa_Vista' => [
        'offset' => -14400000,
        'shortname' => 'AMT',
        'dstshortname' => null,
        'longname' => 'Amazon Standard Time', ],
    'America/Bogota' => [
        'offset' => -18000000,
        'shortname' => 'COT',
        'dstshortname' => null,
        'longname' => 'Colombia Time', ],
    'America/Boise' => [
        'offset' => -25200000,
        'shortname' => 'MST',
        'dstshortname' => 'MDT',
        'longname' => 'Mountain Standard Time',
        'dstlongname' => 'Mountain Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 32400000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 28800000, ],
    'America/Buenos_Aires' => [
        'offset' => -10800000,
        'shortname' => 'ART',
        'dstshortname' => 'ARST',
        'longname' => 'Argentine Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => 0,
        'summertimeendmonth' => 3,
        'summertimeendday' => 'Sun>=15',
        'summertimeendtime' => 0, ],
    'America/Cambridge_Bay' => [
        'offset' => -25200000,
        'shortname' => 'MST',
        'dstshortname' => 'MDT',
        'longname' => 'Mountain Standard Time',
        'dstlongname' => 'Mountain Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 32400000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 28800000, ],
    'America/Campo_Grande' => [
        'offset' => -14400000,
        'shortname' => 'AMT',
        'dstshortname' => 'AMST',
        'longname' => 'Amazon Standard Time',
        'dstlongname' => 'Amazon Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 14400000,
        'summertimeendmonth' => 2,
        'summertimeendday' => 'Sun>=15',
        'summertimeendtime' => 10800000, ],
    'America/Cancun' => [
        'offset' => -21600000,
        'shortname' => 'CST',
        'dstshortname' => 'CDT',
        'longname' => 'Central Standard Time',
        'dstlongname' => 'Central Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 4,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => 28800000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 25200000, ],
    'America/Caracas' => [
        'offset' => -16200000,
        'shortname' => 'VET',
        'dstshortname' => null,
        'longname' => 'Venezuela Time', ],
    'America/Catamarca' => [
        'offset' => -10800000,
        'shortname' => 'ART',
        'dstshortname' => 'ARST',
        'longname' => 'Argentine Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => 0,
        'summertimeendmonth' => 3,
        'summertimeendday' => 'Sun>=15',
        'summertimeendtime' => 0, ],
    'America/Cayenne' => [
        'offset' => -10800000,
        'shortname' => 'GFT',
        'dstshortname' => null,
        'longname' => 'French Guiana Time', ],
    'America/Cayman' => [
        'offset' => -18000000,
        'shortname' => 'EST',
        'dstshortname' => null,
        'longname' => 'Eastern Standard Time', ],
    'America/Chicago' => [
        'offset' => -21600000,
        'shortname' => 'CST',
        'dstshortname' => 'CDT',
        'longname' => 'Central Standard Time',
        'dstlongname' => 'Central Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 28800000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 25200000, ],
    'America/Chihuahua' => [
        'offset' => -25200000,
        'shortname' => 'MST',
        'dstshortname' => 'MDT',
        'longname' => 'Mountain Standard Time',
        'dstlongname' => 'Mountain Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 4,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => 32400000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 28800000, ],
    'America/Coral_Harbour' => [
        'offset' => -18000000,
        'shortname' => 'EST',
        'dstshortname' => null,
        'longname' => 'Eastern Standard Time', ],
    'America/Cordoba' => [
        'offset' => -10800000,
        'shortname' => 'ART',
        'dstshortname' => 'ARST',
        'longname' => 'Argentine Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => 0,
        'summertimeendmonth' => 3,
        'summertimeendday' => 'Sun>=15',
        'summertimeendtime' => 0, ],
    'America/Costa_Rica' => [
        'offset' => -21600000,
        'shortname' => 'CST',
        'dstshortname' => null,
        'longname' => 'Central Standard Time', ],
    'America/Cuiaba' => [
        'offset' => -14400000,
        'shortname' => 'AMT',
        'dstshortname' => 'AMST',
        'longname' => 'Amazon Standard Time',
        'dstlongname' => 'Amazon Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 14400000,
        'summertimeendmonth' => 2,
        'summertimeendday' => 'Sun>=15',
        'summertimeendtime' => 10800000, ],
    'America/Curacao' => [
        'offset' => -14400000,
        'shortname' => 'AST',
        'dstshortname' => null,
        'longname' => 'Atlantic Standard Time', ],
    'America/Danmarkshavn' => [
        'offset' => 0,
        'shortname' => 'GMT',
        'dstshortname' => null,
        'longname' => 'Greenwich Mean Time', ],
    'America/Dawson' => [
        'offset' => -28800000,
        'shortname' => 'PST',
        'dstshortname' => 'PDT',
        'longname' => 'Pacific Standard Time',
        'dstlongname' => 'Pacific Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 36000000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 32400000, ],
    'America/Dawson_Creek' => [
        'offset' => -25200000,
        'shortname' => 'MST',
        'dstshortname' => null,
        'longname' => 'Mountain Standard Time', ],
    'America/Denver' => [
        'offset' => -25200000,
        'shortname' => 'MST',
        'dstshortname' => 'MDT',
        'longname' => 'Mountain Standard Time',
        'dstlongname' => 'Mountain Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 32400000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 28800000, ],
    'America/Detroit' => [
        'offset' => -18000000,
        'shortname' => 'EST',
        'dstshortname' => 'EDT',
        'longname' => 'Eastern Standard Time',
        'dstlongname' => 'Eastern Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 25200000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 21600000, ],
    'America/Dominica' => [
        'offset' => -14400000,
        'shortname' => 'AST',
        'dstshortname' => null,
        'longname' => 'Atlantic Standard Time', ],
    'America/Edmonton' => [
        'offset' => -25200000,
        'shortname' => 'MST',
        'dstshortname' => 'MDT',
        'longname' => 'Mountain Standard Time',
        'dstlongname' => 'Mountain Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 32400000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 28800000, ],
    'America/Eirunepe' => [
        'offset' => -18000000,
        'shortname' => 'ACT',
        'dstshortname' => null,
        'longname' => 'Acre Time', ],
    'America/El_Salvador' => [
        'offset' => -21600000,
        'shortname' => 'CST',
        'dstshortname' => null,
        'longname' => 'Central Standard Time', ],
    'America/Ensenada' => [
        'offset' => -28800000,
        'shortname' => 'PST',
        'dstshortname' => 'PDT',
        'longname' => 'Pacific Standard Time',
        'dstlongname' => 'Pacific Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 4,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => 36000000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 32400000, ],
    'America/Fort_Wayne' => [
        'offset' => -18000000,
        'shortname' => 'EST',
        'dstshortname' => 'EDT',
        'longname' => 'Eastern Standard Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 25200000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 21600000, ],
    'America/Fortaleza' => [
        'offset' => -10800000,
        'shortname' => 'BRT',
        'dstshortname' => null,
        'longname' => 'Brazil Time',
        'dstlongname' => 'Brazil Summer Time', ],
    'America/Glace_Bay' => [
        'offset' => -14400000,
        'shortname' => 'AST',
        'dstshortname' => 'ADT',
        'longname' => 'Atlantic Standard Time',
        'dstlongname' => 'Atlantic Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 21600000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 18000000, ],
    'America/Godthab' => [
        'offset' => -10800000,
        'shortname' => 'WGT',
        'dstshortname' => 'WGST',
        'longname' => 'Western Greenland Time',
        'dstlongname' => 'Western Greenland Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'America/Goose_Bay' => [
        'offset' => -14400000,
        'shortname' => 'AST',
        'dstshortname' => 'ADT',
        'longname' => 'Atlantic Standard Time',
        'dstlongname' => 'Atlantic Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 14460000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 10860000, ],
    'America/Grand_Turk' => [
        'offset' => -18000000,
        'shortname' => 'EST',
        'dstshortname' => 'EDT',
        'longname' => 'Eastern Standard Time',
        'dstlongname' => 'Eastern Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 25200000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 21600000, ],
    'America/Grenada' => [
        'offset' => -14400000,
        'shortname' => 'AST',
        'dstshortname' => null,
        'longname' => 'Atlantic Standard Time', ],
    'America/Guadeloupe' => [
        'offset' => -14400000,
        'shortname' => 'AST',
        'dstshortname' => null,
        'longname' => 'Atlantic Standard Time', ],
    'America/Guatemala' => [
        'offset' => -21600000,
        'shortname' => 'CST',
        'dstshortname' => null,
        'longname' => 'Central Standard Time', ],
    'America/Guayaquil' => [
        'offset' => -18000000,
        'shortname' => 'ECT',
        'dstshortname' => null,
        'longname' => 'Ecuador Time', ],
    'America/Guyana' => [
        'offset' => -14400000,
        'shortname' => 'GYT',
        'dstshortname' => null,
        'longname' => 'Guyana Time', ],
    'America/Halifax' => [
        'offset' => -14400000,
        'shortname' => 'AST',
        'dstshortname' => 'ADT',
        'longname' => 'Atlantic Standard Time',
        'dstlongname' => 'Atlantic Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 21600000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 18000000, ],
    'America/Havana' => [
        'offset' => -18000000,
        'shortname' => 'CST',
        'dstshortname' => 'CDT',
        'longname' => 'Central Standard Time',
        'dstlongname' => 'Central Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 18000000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 18000000, ],
    'America/Hermosillo' => [
        'offset' => -25200000,
        'shortname' => 'MST',
        'dstshortname' => null,
        'longname' => 'Mountain Standard Time', ],
    'America/Indiana/Indianapolis' => [
        'offset' => -18000000,
        'shortname' => 'EST',
        'dstshortname' => 'EDT',
        'longname' => 'Eastern Standard Time',
        'dstlongname' => 'Eastern Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 25200000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 21600000, ],
    'America/Indiana/Knox' => [
        'offset' => -21600000,
        'shortname' => 'CST',
        'dstshortname' => 'CDT',
        'longname' => 'Central Standard Time',
        'dstlongname' => 'Central Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 28800000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 25200000, ],
    'America/Indiana/Marengo' => [
        'offset' => -18000000,
        'shortname' => 'EST',
        'dstshortname' => 'EDT',
        'longname' => 'Eastern Standard Time',
        'dstlongname' => 'Eastern Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 25200000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 21600000, ],
    'America/Indiana/Petersburg' => [
        'offset' => -18000000,
        'shortname' => 'EST',
        'dstshortname' => 'EDT',
        'longname' => 'Eastern Standard Time',
        'dstlongname' => 'Eastern Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 25200000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 21600000, ],
    'America/Indiana/Tell_City' => [
        'offset' => -21600000,
        'shortname' => 'CST',
        'dstshortname' => 'CDT',
        'longname' => 'Central Standard Time',
        'dstlongname' => 'Central Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 28800000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 25200000, ],
    'America/Indiana/Vevay' => [
        'offset' => -18000000,
        'shortname' => 'EST',
        'dstshortname' => 'EDT',
        'longname' => 'Eastern Standard Time',
        'dstlongname' => 'Eastern Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 25200000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 21600000, ],
    'America/Indiana/Vincennes' => [
        'offset' => -18000000,
        'shortname' => 'EST',
        'dstshortname' => 'EDT',
        'longname' => 'Eastern Standard Time',
        'dstlongname' => 'Eastern Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 25200000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 21600000, ],
    'America/Indiana/Winamac' => [
        'offset' => -18000000,
        'shortname' => 'EST',
        'dstshortname' => 'EDT',
        'longname' => 'Eastern Standard Time',
        'dstlongname' => 'Eastern Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 25200000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 21600000, ],
    'America/Indianapolis' => [
        'offset' => -18000000,
        'shortname' => 'EST',
        'dstshortname' => 'EDT',
        'longname' => 'Eastern Standard Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 25200000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 21600000, ],
    'America/Inuvik' => [
        'offset' => -25200000,
        'shortname' => 'MST',
        'dstshortname' => 'MDT',
        'longname' => 'Mountain Standard Time',
        'dstlongname' => 'Mountain Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 32400000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 28800000, ],
    'America/Iqaluit' => [
        'offset' => -18000000,
        'shortname' => 'EST',
        'dstshortname' => 'EDT',
        'longname' => 'Eastern Standard Time',
        'dstlongname' => 'Eastern Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 25200000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 21600000, ],
    'America/Jamaica' => [
        'offset' => -18000000,
        'shortname' => 'EST',
        'dstshortname' => null,
        'longname' => 'Eastern Standard Time', ],
    'America/Jujuy' => [
        'offset' => -10800000,
        'shortname' => 'ART',
        'dstshortname' => 'ARST',
        'longname' => 'Argentine Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => 0,
        'summertimeendmonth' => 3,
        'summertimeendday' => 'Sun>=15',
        'summertimeendtime' => 0, ],
    'America/Juneau' => [
        'offset' => -32400000,
        'shortname' => 'AKST',
        'dstshortname' => 'AKDT',
        'longname' => 'Alaska Standard Time',
        'dstlongname' => 'Alaska Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 39600000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 36000000, ],
    'America/Kentucky/Louisville' => [
        'offset' => -18000000,
        'shortname' => 'EST',
        'dstshortname' => 'EDT',
        'longname' => 'Eastern Standard Time',
        'dstlongname' => 'Eastern Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 25200000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 21600000, ],
    'America/Kentucky/Monticello' => [
        'offset' => -18000000,
        'shortname' => 'EST',
        'dstshortname' => 'EDT',
        'longname' => 'Eastern Standard Time',
        'dstlongname' => 'Eastern Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 25200000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 21600000, ],
    'America/Knox_IN' => [
        'offset' => -21600000,
        'shortname' => 'CST',
        'dstshortname' => 'CDT',
        'longname' => 'Central Standard Time',
        'dstlongname' => 'Central Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 28800000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 25200000, ],
    'America/La_Paz' => [
        'offset' => -14400000,
        'shortname' => 'BOT',
        'dstshortname' => null,
        'longname' => 'Bolivia Time', ],
    'America/Lima' => [
        'offset' => -18000000,
        'shortname' => 'PET',
        'dstshortname' => null,
        'longname' => 'Peru Time', ],
    'America/Los_Angeles' => [
        'offset' => -28800000,
        'shortname' => 'PST',
        'dstshortname' => 'PDT',
        'longname' => 'Pacific Standard Time',
        'dstlongname' => 'Pacific Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 36000000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 32400000, ],
    'America/Louisville' => [
        'offset' => -18000000,
        'shortname' => 'EST',
        'dstshortname' => 'EDT',
        'longname' => 'Eastern Standard Time',
        'dstlongname' => 'Eastern Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 25200000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 21600000, ],
    'America/Maceio' => [
        'offset' => -10800000,
        'shortname' => 'BRT',
        'dstshortname' => null,
        'longname' => 'Brazil Time',
        'dstlongname' => 'Brazil Summer Time', ],
    'America/Managua' => [
        'offset' => -21600000,
        'shortname' => 'CST',
        'dstshortname' => null,
        'longname' => 'Central Standard Time', ],
    'America/Manaus' => [
        'offset' => -14400000,
        'shortname' => 'AMT',
        'dstshortname' => null,
        'longname' => 'Amazon Standard Time', ],
    'America/Marigot' => [
        'offset' => -14400000,
        'shortname' => 'AST',
        'dstshortname' => null,
        'longname' => 'Atlantic Standard Time', ],
    'America/Martinique' => [
        'offset' => -14400000,
        'shortname' => 'AST',
        'dstshortname' => null,
        'longname' => 'Atlantic Standard Time', ],
    'America/Mazatlan' => [
        'offset' => -25200000,
        'shortname' => 'MST',
        'dstshortname' => 'MDT',
        'longname' => 'Mountain Standard Time',
        'dstlongname' => 'Mountain Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 4,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => 32400000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 28800000, ],
    'America/Mendoza' => [
        'offset' => -10800000,
        'shortname' => 'ART',
        'dstshortname' => 'ARST',
        'longname' => 'Argentine Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => 0,
        'summertimeendmonth' => 3,
        'summertimeendday' => 'Sun>=15',
        'summertimeendtime' => 0, ],
    'America/Menominee' => [
        'offset' => -21600000,
        'shortname' => 'CST',
        'dstshortname' => 'CDT',
        'longname' => 'Central Standard Time',
        'dstlongname' => 'Central Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 28800000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 25200000, ],
    'America/Merida' => [
        'offset' => -21600000,
        'shortname' => 'CST',
        'dstshortname' => 'CDT',
        'longname' => 'Central Standard Time',
        'dstlongname' => 'Central Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 4,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => 28800000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 25200000, ],
    'America/Mexico_City' => [
        'offset' => -21600000,
        'shortname' => 'CST',
        'dstshortname' => 'CDT',
        'longname' => 'Central Standard Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 4,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => 28800000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 25200000, ],
    'America/Miquelon' => [
        'offset' => -10800000,
        'shortname' => 'PMST',
        'dstshortname' => 'PMDT',
        'longname' => 'Pierre & Miquelon Standard Time',
        'dstlongname' => 'Pierre & Miquelon Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 18000000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 14400000, ],
    'America/Moncton' => [
        'offset' => -14400000,
        'shortname' => 'AST',
        'dstshortname' => 'ADT',
        'longname' => 'Atlantic Standard Time',
        'dstlongname' => 'Atlantic Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 21600000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 18000000, ],
    'America/Monterrey' => [
        'offset' => -21600000,
        'shortname' => 'CST',
        'dstshortname' => 'CDT',
        'longname' => 'Central Standard Time',
        'dstlongname' => 'Central Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 4,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => 28800000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 25200000, ],
    'America/Montevideo' => [
        'offset' => -10800000,
        'shortname' => 'UYT',
        'dstshortname' => 'UYST',
        'longname' => 'Uruguay Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => 18000000,
        'summertimeendmonth' => 3,
        'summertimeendday' => 'Sun>=8',
        'summertimeendtime' => 14400000, ],
    'America/Montreal' => [
        'offset' => -18000000,
        'shortname' => 'EST',
        'dstshortname' => 'EDT',
        'longname' => 'Eastern Standard Time',
        'dstlongname' => 'Eastern Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 25200000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 21600000, ],
    'America/Montserrat' => [
        'offset' => -14400000,
        'shortname' => 'AST',
        'dstshortname' => null,
        'longname' => 'Atlantic Standard Time', ],
    'America/Nassau' => [
        'offset' => -18000000,
        'shortname' => 'EST',
        'dstshortname' => 'EDT',
        'longname' => 'Eastern Standard Time',
        'dstlongname' => 'Eastern Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 25200000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 21600000, ],
    'America/New_York' => [
        'offset' => -18000000,
        'shortname' => 'EST',
        'dstshortname' => 'EDT',
        'longname' => 'Eastern Standard Time',
        'dstlongname' => 'Eastern Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 25200000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 21600000, ],
    'America/Nipigon' => [
        'offset' => -18000000,
        'shortname' => 'EST',
        'dstshortname' => 'EDT',
        'longname' => 'Eastern Standard Time',
        'dstlongname' => 'Eastern Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 25200000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 21600000, ],
    'America/Nome' => [
        'offset' => -32400000,
        'shortname' => 'AKST',
        'dstshortname' => 'AKDT',
        'longname' => 'Alaska Standard Time',
        'dstlongname' => 'Alaska Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 39600000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 36000000, ],
    'America/Noronha' => [
        'offset' => -7200000,
        'shortname' => 'FNT',
        'dstshortname' => null,
        'longname' => 'Fernando de Noronha Time', ],
    'America/North_Dakota/Center' => [
        'offset' => -21600000,
        'shortname' => 'CST',
        'dstshortname' => 'CDT',
        'longname' => 'Central Standard Time',
        'dstlongname' => 'Central Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 28800000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 25200000, ],
    'America/North_Dakota/New_Salem' => [
        'offset' => -21600000,
        'shortname' => 'CST',
        'dstshortname' => 'CDT',
        'longname' => 'Central Standard Time',
        'dstlongname' => 'Central Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 28800000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 25200000, ],
    'America/Panama' => [
        'offset' => -18000000,
        'shortname' => 'EST',
        'dstshortname' => null,
        'longname' => 'Eastern Standard Time', ],
    'America/Pangnirtung' => [
        'offset' => -18000000,
        'shortname' => 'EST',
        'dstshortname' => 'EDT',
        'longname' => 'Eastern Standard Time',
        'dstlongname' => 'Eastern Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 25200000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 21600000, ],
    'America/Paramaribo' => [
        'offset' => -10800000,
        'shortname' => 'SRT',
        'dstshortname' => null,
        'longname' => 'Suriname Time', ],
    'America/Phoenix' => [
        'offset' => -25200000,
        'shortname' => 'MST',
        'dstshortname' => null,
        'longname' => 'Mountain Standard Time', ],
    'America/Port-au-Prince' => [
        'offset' => -18000000,
        'shortname' => 'EST',
        'dstshortname' => null,
        'longname' => 'Eastern Standard Time', ],
    'America/Port_of_Spain' => [
        'offset' => -14400000,
        'shortname' => 'AST',
        'dstshortname' => null,
        'longname' => 'Atlantic Standard Time', ],
    'America/Porto_Acre' => [
        'offset' => -18000000,
        'shortname' => 'ACT',
        'dstshortname' => null,
        'longname' => 'Acre Time', ],
    'America/Porto_Velho' => [
        'offset' => -14400000,
        'shortname' => 'AMT',
        'dstshortname' => null,
        'longname' => 'Amazon Standard Time', ],
    'America/Puerto_Rico' => [
        'offset' => -14400000,
        'shortname' => 'AST',
        'dstshortname' => null,
        'longname' => 'Atlantic Standard Time', ],
    'America/Rainy_River' => [
        'offset' => -21600000,
        'shortname' => 'CST',
        'dstshortname' => 'CDT',
        'longname' => 'Central Standard Time',
        'dstlongname' => 'Central Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 28800000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 25200000, ],
    'America/Rankin_Inlet' => [
        'offset' => -21600000,
        'shortname' => 'CST',
        'dstshortname' => 'CDT',
        'longname' => 'Central Standard Time',
        'dstlongname' => 'Central Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 28800000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 25200000, ],
    'America/Recife' => [
        'offset' => -10800000,
        'shortname' => 'BRT',
        'dstshortname' => null,
        'longname' => 'Brazil Time',
        'dstlongname' => 'Brazil Summer Time', ],
    'America/Regina' => [
        'offset' => -21600000,
        'shortname' => 'CST',
        'dstshortname' => null,
        'longname' => 'Central Standard Time', ],
    'America/Resolute' => [
        'offset' => -18000000,
        'shortname' => 'EST',
        'dstshortname' => null,
        'longname' => 'Eastern Standard Time', ],
    'America/Rio_Branco' => [
        'offset' => -18000000,
        'shortname' => 'ACT',
        'dstshortname' => null,
        'longname' => 'Acre Time', ],
    'America/Rosario' => [
        'offset' => -10800000,
        'shortname' => 'ART',
        'dstshortname' => 'ARST',
        'longname' => 'Argentine Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => 0,
        'summertimeendmonth' => 3,
        'summertimeendday' => 'Sun>=15',
        'summertimeendtime' => 0, ],
    'America/Santiago' => [
        'offset' => -14400000,
        'shortname' => 'CLT',
        'dstshortname' => 'CLST',
        'longname' => 'Chile Time',
        'dstlongname' => 'Chile Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'Sun>=9',
        'summertimestarttime' => 14400000,
        'summertimeendmonth' => 3,
        'summertimeendday' => '30',
        'summertimeendtime' => 10800000, ],
    'America/Santo_Domingo' => [
        'offset' => -14400000,
        'shortname' => 'AST',
        'dstshortname' => null,
        'longname' => 'Atlantic Standard Time', ],
    'America/Sao_Paulo' => [
        'offset' => -10800000,
        'shortname' => 'BRT',
        'dstshortname' => 'BRST',
        'longname' => 'Brazil Time',
        'dstlongname' => 'Brazil Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 10800000,
        'summertimeendmonth' => 2,
        'summertimeendday' => 'Sun>=15',
        'summertimeendtime' => 7200000, ],
    'America/Scoresbysund' => [
        'offset' => -3600000,
        'shortname' => 'EGT',
        'dstshortname' => 'EGST',
        'longname' => 'Eastern Greenland Time',
        'dstlongname' => 'Eastern Greenland Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'America/Shiprock' => [
        'offset' => -25200000,
        'shortname' => 'MST',
        'dstshortname' => 'MDT',
        'longname' => 'Mountain Standard Time',
        'dstlongname' => 'Mountain Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 32400000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 28800000, ],
    'America/St_Barthelemy' => [
        'offset' => -14400000,
        'shortname' => 'AST',
        'dstshortname' => null,
        'longname' => 'Atlantic Standard Time', ],
    'America/St_Johns' => [
        'offset' => -12600000,
        'shortname' => 'NST',
        'dstshortname' => 'NDT',
        'longname' => 'Newfoundland Standard Time',
        'dstlongname' => 'Newfoundland Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 12660000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 9060000, ],
    'America/St_Kitts' => [
        'offset' => -14400000,
        'shortname' => 'AST',
        'dstshortname' => null,
        'longname' => 'Atlantic Standard Time', ],
    'America/St_Lucia' => [
        'offset' => -14400000,
        'shortname' => 'AST',
        'dstshortname' => null,
        'longname' => 'Atlantic Standard Time', ],
    'America/St_Thomas' => [
        'offset' => -14400000,
        'shortname' => 'AST',
        'dstshortname' => null,
        'longname' => 'Atlantic Standard Time', ],
    'America/St_Vincent' => [
        'offset' => -14400000,
        'shortname' => 'AST',
        'dstshortname' => null,
        'longname' => 'Atlantic Standard Time', ],
    'America/Swift_Current' => [
        'offset' => -21600000,
        'shortname' => 'CST',
        'dstshortname' => null,
        'longname' => 'Central Standard Time', ],
    'America/Tegucigalpa' => [
        'offset' => -21600000,
        'shortname' => 'CST',
        'dstshortname' => null,
        'longname' => 'Central Standard Time', ],
    'America/Thule' => [
        'offset' => -14400000,
        'shortname' => 'AST',
        'dstshortname' => 'ADT',
        'longname' => 'Atlantic Standard Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 21600000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 18000000, ],
    'America/Thunder_Bay' => [
        'offset' => -18000000,
        'shortname' => 'EST',
        'dstshortname' => 'EDT',
        'longname' => 'Eastern Standard Time',
        'dstlongname' => 'Eastern Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 25200000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 21600000, ],
    'America/Tijuana' => [
        'offset' => -28800000,
        'shortname' => 'PST',
        'dstshortname' => 'PDT',
        'longname' => 'Pacific Standard Time',
        'dstlongname' => 'Pacific Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 4,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => 36000000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 32400000, ],
    'America/Toronto' => [
        'offset' => -18000000,
        'shortname' => 'EST',
        'dstshortname' => 'EDT',
        'longname' => 'Eastern Standard Time',
        'dstlongname' => 'Eastern Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 25200000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 21600000, ],
    'America/Tortola' => [
        'offset' => -14400000,
        'shortname' => 'AST',
        'dstshortname' => null,
        'longname' => 'Atlantic Standard Time', ],
    'America/Vancouver' => [
        'offset' => -28800000,
        'shortname' => 'PST',
        'dstshortname' => 'PDT',
        'longname' => 'Pacific Standard Time',
        'dstlongname' => 'Pacific Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 36000000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 32400000, ],
    'America/Virgin' => [
        'offset' => -14400000,
        'shortname' => 'AST',
        'dstshortname' => null,
        'longname' => 'Atlantic Standard Time', ],
    'America/Whitehorse' => [
        'offset' => -28800000,
        'shortname' => 'PST',
        'dstshortname' => 'PDT',
        'longname' => 'Pacific Standard Time',
        'dstlongname' => 'Pacific Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 36000000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 32400000, ],
    'America/Winnipeg' => [
        'offset' => -21600000,
        'shortname' => 'CST',
        'dstshortname' => 'CDT',
        'longname' => 'Central Standard Time',
        'dstlongname' => 'Central Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 28800000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 25200000, ],
    'America/Yakutat' => [
        'offset' => -32400000,
        'shortname' => 'AKST',
        'dstshortname' => 'AKDT',
        'longname' => 'Alaska Standard Time',
        'dstlongname' => 'Alaska Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 39600000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 36000000, ],
    'America/Yellowknife' => [
        'offset' => -25200000,
        'shortname' => 'MST',
        'dstshortname' => 'MDT',
        'longname' => 'Mountain Standard Time',
        'dstlongname' => 'Mountain Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 32400000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 28800000, ],
    'Antarctica/Casey' => [
        'offset' => 28800000,
        'shortname' => 'WST',
        'dstshortname' => null,
        'longname' => 'Western Standard Time (Australia)', ],
    'Antarctica/Davis' => [
        'offset' => 25200000,
        'shortname' => 'DAVT',
        'dstshortname' => null,
        'longname' => 'Davis Time', ],
    'Antarctica/DumontDUrville' => [
        'offset' => 36000000,
        'shortname' => 'DDUT',
        'dstshortname' => null,
        'longname' => 'Dumont-d\'Urville Time', ],
    'Antarctica/Mawson' => [
        'offset' => 21600000,
        'shortname' => 'MAWT',
        'dstshortname' => null,
        'longname' => 'Mawson Time', ],
    'Antarctica/McMurdo' => [
        'offset' => 43200000,
        'shortname' => 'NZST',
        'dstshortname' => 'NZDT',
        'longname' => 'New Zealand Standard Time',
        'dstlongname' => 'New Zealand Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 9,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => -36000000,
        'summertimeendmonth' => 4,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => -36000000, ],
    'Antarctica/Palmer' => [
        'offset' => -14400000,
        'shortname' => 'CLT',
        'dstshortname' => 'CLST',
        'longname' => 'Chile Time',
        'dstlongname' => 'Chile Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'Sun>=9',
        'summertimestarttime' => 14400000,
        'summertimeendmonth' => 3,
        'summertimeendday' => 'Sun>=9',
        'summertimeendtime' => 10800000, ],
    'Antarctica/Rothera' => [
        'offset' => -10800000,
        'shortname' => 'ROTT',
        'dstshortname' => null,
        'longname' => 'Rothera Time', ],
    'Antarctica/South_Pole' => [
        'offset' => 43200000,
        'shortname' => 'NZST',
        'dstshortname' => 'NZDT',
        'longname' => 'New Zealand Standard Time',
        'dstlongname' => 'New Zealand Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 9,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => -36000000,
        'summertimeendmonth' => 4,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => -36000000, ],
    'Antarctica/Syowa' => [
        'offset' => 10800000,
        'shortname' => 'SYOT',
        'dstshortname' => null,
        'longname' => 'Syowa Time', ],
    'Antarctica/Vostok' => [
        'offset' => 21600000,
        'shortname' => 'VOST',
        'dstshortname' => null,
        'longname' => 'Vostok time', ],
    'Arctic/Longyearbyen' => [
        'offset' => 3600000,
        'shortname' => 'CET',
        'dstshortname' => 'CEST',
        'longname' => 'Central European Time',
        'dstlongname' => 'Central European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Asia/Aden' => [
        'offset' => 10800000,
        'shortname' => 'AST',
        'dstshortname' => null,
        'longname' => 'Arabia Standard Time', ],
    'Asia/Almaty' => [
        'offset' => 21600000,
        'shortname' => 'ALMT',
        'dstshortname' => null,
        'longname' => 'Alma-Ata Time',
        'dstlongname' => 'Alma-Ata Summer Time', ],
    'Asia/Amman' => [
        'offset' => 7200000,
        'shortname' => 'EET',
        'dstshortname' => 'EEST',
        'longname' => 'Eastern European Time',
        'dstlongname' => 'Eastern European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastThu',
        'summertimestarttime' => -7200000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastFri',
        'summertimeendtime' => -7200000, ],
    'Asia/Anadyr' => [
        'offset' => 43200000,
        'shortname' => 'ANAT',
        'dstshortname' => 'ANAST',
        'longname' => 'Anadyr Time',
        'dstlongname' => 'Anadyr Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => -36000000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => -36000000, ],
    'Asia/Aqtau' => [
        'offset' => 18000000,
        'shortname' => 'AQTT',
        'dstshortname' => null,
        'longname' => 'Aqtau Time',
        'dstlongname' => 'Aqtau Summer Time', ],
    'Asia/Aqtobe' => [
        'offset' => 18000000,
        'shortname' => 'AQTT',
        'dstshortname' => null,
        'longname' => 'Aqtobe Time',
        'dstlongname' => 'Aqtobe Summer Time', ],
    'Asia/Ashgabat' => [
        'offset' => 18000000,
        'shortname' => 'TMT',
        'dstshortname' => null,
        'longname' => 'Turkmenistan Time', ],
    'Asia/Ashkhabad' => [
        'offset' => 18000000,
        'shortname' => 'TMT',
        'dstshortname' => null,
        'longname' => 'Turkmenistan Time', ],
    'Asia/Baghdad' => [
        'offset' => 10800000,
        'shortname' => 'AST',
        'dstshortname' => 'ADT',
        'longname' => 'Arabia Standard Time',
        'dstlongname' => 'Arabia Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 4,
        'summertimestartday' => '1',
        'summertimestarttime' => 0,
        'summertimeendmonth' => 10,
        'summertimeendday' => '1',
        'summertimeendtime' => 0, ],
    'Asia/Bahrain' => [
        'offset' => 10800000,
        'shortname' => 'AST',
        'dstshortname' => null,
        'longname' => 'Arabia Standard Time', ],
    'Asia/Baku' => [
        'offset' => 14400000,
        'shortname' => 'AZT',
        'dstshortname' => 'AZST',
        'longname' => 'Azerbaijan Time',
        'dstlongname' => 'Azerbaijan Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 0,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 0, ],
    'Asia/Bangkok' => [
        'offset' => 25200000,
        'shortname' => 'ICT',
        'dstshortname' => null,
        'longname' => 'Indochina Time', ],
    'Asia/Beirut' => [
        'offset' => 7200000,
        'shortname' => 'EET',
        'dstshortname' => 'EEST',
        'longname' => 'Eastern European Time',
        'dstlongname' => 'Eastern European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => -7200000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => -10800000, ],
    'Asia/Bishkek' => [
        'offset' => 21600000,
        'shortname' => 'KGT',
        'dstshortname' => null,
        'longname' => 'Kirgizstan Time',
        'dstlongname' => 'Kirgizstan Summer Time', ],
    'Asia/Brunei' => [
        'offset' => 28800000,
        'shortname' => 'BNT',
        'dstshortname' => null,
        'longname' => 'Brunei Time', ],
    'Asia/Calcutta' => [
        'offset' => 19800000,
        'shortname' => 'IST',
        'dstshortname' => null,
        'longname' => 'India Standard Time', ],
    'Asia/Choibalsan' => [
        'offset' => 32400000,
        'shortname' => 'CHOT',
        'dstshortname' => null,
        'longname' => 'Choibalsan Time', ],
    'Asia/Chongqing' => [
        'offset' => 28800000,
        'shortname' => 'CST',
        'dstshortname' => null,
        'longname' => 'China Standard Time', ],
    'Asia/Chungking' => [
        'offset' => 28800000,
        'shortname' => 'CST',
        'dstshortname' => null,
        'longname' => 'China Standard Time', ],
    'Asia/Colombo' => [
        'offset' => 19800000,
        'shortname' => 'IST',
        'dstshortname' => null,
        'longname' => 'India Standard Time', ],
    'Asia/Dacca' => [
        'offset' => 21600000,
        'shortname' => 'BDT',
        'dstshortname' => null,
        'longname' => 'Bangladesh Time', ],
    'Asia/Damascus' => [
        'offset' => 7200000,
        'shortname' => 'EET',
        'dstshortname' => 'EEST',
        'longname' => 'Eastern European Time',
        'dstlongname' => 'Eastern European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastFri',
        'summertimestarttime' => -7200000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Fri>=1',
        'summertimeendtime' => -10800000, ],
    'Asia/Dhaka' => [
        'offset' => 21600000,
        'shortname' => 'BDT',
        'dstshortname' => null,
        'longname' => 'Bangladesh Time', ],
    'Asia/Dili' => [
        'offset' => 32400000,
        'shortname' => 'TLT',
        'dstshortname' => null,
        'longname' => 'East Timor Time', ],
    'Asia/Dubai' => [
        'offset' => 14400000,
        'shortname' => 'GST',
        'dstshortname' => null,
        'longname' => 'Gulf Standard Time', ],
    'Asia/Dushanbe' => [
        'offset' => 18000000,
        'shortname' => 'TJT',
        'dstshortname' => null,
        'longname' => 'Tajikistan Time', ],
    'Asia/Gaza' => [
        'offset' => 7200000,
        'shortname' => 'EET',
        'dstshortname' => 'EEST',
        'longname' => 'Eastern European Time',
        'dstlongname' => 'Eastern European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 4,
        'summertimestartday' => '1',
        'summertimestarttime' => -7200000,
        'summertimeendmonth' => 9,
        'summertimeendday' => 'Thu>=8',
        'summertimeendtime' => -3600000, ],
    'Asia/Harbin' => [
        'offset' => 28800000,
        'shortname' => 'CST',
        'dstshortname' => null,
        'longname' => 'China Standard Time', ],
    'Asia/Hong_Kong' => [
        'offset' => 28800000,
        'shortname' => 'HKT',
        'dstshortname' => null,
        'longname' => 'Hong Kong Time', ],
    'Asia/Hovd' => [
        'offset' => 25200000,
        'shortname' => 'HOVT',
        'dstshortname' => null,
        'longname' => 'Hovd Time', ],
    'Asia/Irkutsk' => [
        'offset' => 28800000,
        'shortname' => 'IRKT',
        'dstshortname' => 'IRKST',
        'longname' => 'Irkutsk Time',
        'dstlongname' => 'Irkutsk Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => -21600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => -21600000, ],
    'Asia/Istanbul' => [
        'offset' => 7200000,
        'shortname' => 'EET',
        'dstshortname' => 'EEST',
        'longname' => 'Eastern European Time',
        'dstlongname' => 'Eastern European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Asia/Jakarta' => [
        'offset' => 25200000,
        'shortname' => 'WIT',
        'dstshortname' => null,
        'longname' => 'West Indonesia Time', ],
    'Asia/Jayapura' => [
        'offset' => 32400000,
        'shortname' => 'EIT',
        'dstshortname' => null,
        'longname' => 'East Indonesia Time', ],
    'Asia/Jerusalem' => [
        'offset' => 7200000,
        'shortname' => 'IST',
        'dstshortname' => 'IDT',
        'longname' => 'Israel Standard Time',
        'dstlongname' => 'Israel Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Fri>=26',
        'summertimestarttime' => 0,
        'summertimeendmonth' => 10,
        'summertimeendday' => '5',
        'summertimeendtime' => -3600000, ],
    'Asia/Kabul' => [
        'offset' => 16200000,
        'shortname' => 'AFT',
        'dstshortname' => null,
        'longname' => 'Afghanistan Time', ],
    'Asia/Kamchatka' => [
        'offset' => 43200000,
        'shortname' => 'PETT',
        'dstshortname' => 'PETST',
        'longname' => 'Petropavlovsk-Kamchatski Time',
        'dstlongname' => 'Petropavlovsk-Kamchatski Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => -36000000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => -36000000, ],
    'Asia/Karachi' => [
        'offset' => 18000000,
        'shortname' => 'PKT',
        'dstshortname' => null,
        'longname' => 'Pakistan Time', ],
    'Asia/Kashgar' => [
        'offset' => 28800000,
        'shortname' => 'CST',
        'dstshortname' => null,
        'longname' => 'China Standard Time', ],
    'Asia/Katmandu' => [
        'offset' => 20700000,
        'shortname' => 'NPT',
        'dstshortname' => null,
        'longname' => 'Nepal Time', ],
    'Asia/Krasnoyarsk' => [
        'offset' => 25200000,
        'shortname' => 'KRAT',
        'dstshortname' => 'KRAST',
        'longname' => 'Krasnoyarsk Time',
        'dstlongname' => 'Krasnoyarsk Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => -18000000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => -18000000, ],
    'Asia/Kuala_Lumpur' => [
        'offset' => 28800000,
        'shortname' => 'MYT',
        'dstshortname' => null,
        'longname' => 'Malaysia Time', ],
    'Asia/Kuching' => [
        'offset' => 28800000,
        'shortname' => 'MYT',
        'dstshortname' => null,
        'longname' => 'Malaysia Time', ],
    'Asia/Kuwait' => [
        'offset' => 10800000,
        'shortname' => 'AST',
        'dstshortname' => null,
        'longname' => 'Arabia Standard Time', ],
    'Asia/Macao' => [
        'offset' => 28800000,
        'shortname' => 'CST',
        'dstshortname' => null,
        'longname' => 'China Standard Time', ],
    'Asia/Macau' => [
        'offset' => 28800000,
        'shortname' => 'CST',
        'dstshortname' => null,
        'longname' => 'China Standard Time', ],
    'Asia/Magadan' => [
        'offset' => 39600000,
        'shortname' => 'MAGT',
        'dstshortname' => 'MAGST',
        'longname' => 'Magadan Time',
        'dstlongname' => 'Magadan Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => -32400000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => -32400000, ],
    'Asia/Makassar' => [
        'offset' => 28800000,
        'shortname' => 'CIT',
        'dstshortname' => null,
        'longname' => 'Central Indonesia Time', ],
    'Asia/Manila' => [
        'offset' => 28800000,
        'shortname' => 'PHT',
        'dstshortname' => null,
        'longname' => 'Philippines Time', ],
    'Asia/Muscat' => [
        'offset' => 14400000,
        'shortname' => 'GST',
        'dstshortname' => null,
        'longname' => 'Gulf Standard Time', ],
    'Asia/Nicosia' => [
        'offset' => 7200000,
        'shortname' => 'EET',
        'dstshortname' => 'EEST',
        'longname' => 'Eastern European Time',
        'dstlongname' => 'Eastern European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Asia/Novosibirsk' => [
        'offset' => 21600000,
        'shortname' => 'NOVT',
        'dstshortname' => 'NOVST',
        'longname' => 'Novosibirsk Time',
        'dstlongname' => 'Novosibirsk Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => -14400000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => -14400000, ],
    'Asia/Omsk' => [
        'offset' => 21600000,
        'shortname' => 'OMST',
        'dstshortname' => 'OMSST',
        'longname' => 'Omsk Time',
        'dstlongname' => 'Omsk Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => -14400000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => -14400000, ],
    'Asia/Oral' => [
        'offset' => 18000000,
        'shortname' => 'ORAT',
        'dstshortname' => null,
        'longname' => 'Oral Time', ],
    'Asia/Phnom_Penh' => [
        'offset' => 25200000,
        'shortname' => 'ICT',
        'dstshortname' => null,
        'longname' => 'Indochina Time', ],
    'Asia/Pontianak' => [
        'offset' => 25200000,
        'shortname' => 'WIT',
        'dstshortname' => null,
        'longname' => 'West Indonesia Time', ],
    'Asia/Pyongyang' => [
        'offset' => 32400000,
        'shortname' => 'KST',
        'dstshortname' => null,
        'longname' => 'Korea Standard Time', ],
    'Asia/Qatar' => [
        'offset' => 10800000,
        'shortname' => 'AST',
        'dstshortname' => null,
        'longname' => 'Arabia Standard Time', ],
    'Asia/Qyzylorda' => [
        'offset' => 21600000,
        'shortname' => 'QYZT',
        'dstshortname' => null,
        'longname' => 'Qyzylorda Time', ],
    'Asia/Rangoon' => [
        'offset' => 23400000,
        'shortname' => 'MMT',
        'dstshortname' => null,
        'longname' => 'Myanmar Time', ],
    'Asia/Riyadh' => [
        'offset' => 10800000,
        'shortname' => 'AST',
        'dstshortname' => null,
        'longname' => 'Arabia Standard Time', ],
    'Asia/Riyadh87' => [
        'offset' => 11224000,
        'shortname' => '',
        'dstshortname' => null,
        'longname' => 'GMT+03:07', ],
    'Asia/Riyadh88' => [
        'offset' => 11224000,
        'shortname' => '',
        'dstshortname' => null,
        'longname' => 'GMT+03:07', ],
    'Asia/Riyadh89' => [
        'offset' => 11224000,
        'shortname' => '',
        'dstshortname' => null,
        'longname' => 'GMT+03:07', ],
    'Asia/Saigon' => [
        'offset' => 25200000,
        'shortname' => 'ICT',
        'dstshortname' => null,
        'longname' => 'Indochina Time', ],
    'Asia/Sakhalin' => [
        'offset' => 36000000,
        'shortname' => 'SAKT',
        'dstshortname' => 'SAKST',
        'longname' => 'Sakhalin Time',
        'dstlongname' => 'Sakhalin Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => -28800000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => -28800000, ],
    'Asia/Samarkand' => [
        'offset' => 18000000,
        'shortname' => 'UZT',
        'dstshortname' => null,
        'longname' => 'Turkmenistan Time', ],
    'Asia/Seoul' => [
        'offset' => 32400000,
        'shortname' => 'KST',
        'dstshortname' => null,
        'longname' => 'Korea Standard Time', ],
    'Asia/Shanghai' => [
        'offset' => 28800000,
        'shortname' => 'CST',
        'dstshortname' => null,
        'longname' => 'China Standard Time', ],
    'Asia/Singapore' => [
        'offset' => 28800000,
        'shortname' => 'SGT',
        'dstshortname' => null,
        'longname' => 'Singapore Time', ],
    'Asia/Taipei' => [
        'offset' => 28800000,
        'shortname' => 'CST',
        'dstshortname' => null,
        'longname' => 'China Standard Time', ],
    'Asia/Tashkent' => [
        'offset' => 18000000,
        'shortname' => 'UZT',
        'dstshortname' => null,
        'longname' => 'Uzbekistan Time', ],
    'Asia/Tbilisi' => [
        'offset' => 14400000,
        'shortname' => 'GET',
        'dstshortname' => null,
        'longname' => 'Georgia Time',
        'dstlongname' => 'Georgia Summer Time', ],
    'Asia/Tehran' => [
        'offset' => 12600000,
        'shortname' => 'IRST',
        'dstshortname' => 'IRDT',
        'longname' => 'Iran Time',
        'dstlongname' => 'Iran Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => '21',
        'summertimestarttime' => -12600000,
        'summertimeendmonth' => 9,
        'summertimeendday' => '21',
        'summertimeendtime' => -16200000, ],
    'Asia/Tel_Aviv' => [
        'offset' => 7200000,
        'shortname' => 'IST',
        'dstshortname' => 'IDT',
        'longname' => 'Israel Standard Time',
        'dstlongname' => 'Israel Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Fri>=26',
        'summertimestarttime' => 0,
        'summertimeendmonth' => 10,
        'summertimeendday' => '5',
        'summertimeendtime' => -3600000, ],
    'Asia/Thimbu' => [
        'offset' => 21600000,
        'shortname' => 'BTT',
        'dstshortname' => null,
        'longname' => 'Bhutan Time', ],
    'Asia/Thimphu' => [
        'offset' => 21600000,
        'shortname' => 'BTT',
        'dstshortname' => null,
        'longname' => 'Bhutan Time', ],
    'Asia/Tokyo' => [
        'offset' => 32400000,
        'shortname' => 'JST',
        'dstshortname' => null,
        'longname' => 'Japan Standard Time', ],
    'Asia/Ujung_Pandang' => [
        'offset' => 28800000,
        'shortname' => 'CIT',
        'dstshortname' => null,
        'longname' => 'Central Indonesia Time', ],
    'Asia/Ulaanbaatar' => [
        'offset' => 28800000,
        'shortname' => 'ULAT',
        'dstshortname' => null,
        'longname' => 'Ulaanbaatar Time', ],
    'Asia/Ulan_Bator' => [
        'offset' => 28800000,
        'shortname' => 'ULAT',
        'dstshortname' => null,
        'longname' => 'Ulaanbaatar Time', ],
    'Asia/Urumqi' => [
        'offset' => 28800000,
        'shortname' => 'CST',
        'dstshortname' => null,
        'longname' => 'China Standard Time', ],
    'Asia/Vientiane' => [
        'offset' => 25200000,
        'shortname' => 'ICT',
        'dstshortname' => null,
        'longname' => 'Indochina Time', ],
    'Asia/Vladivostok' => [
        'offset' => 36000000,
        'shortname' => 'VLAT',
        'dstshortname' => 'VLAST',
        'longname' => 'Vladivostok Time',
        'dstlongname' => 'Vladivostok Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => -28800000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => -28800000, ],
    'Asia/Yakutsk' => [
        'offset' => 32400000,
        'shortname' => 'YAKT',
        'dstshortname' => 'YAKST',
        'longname' => 'Yakutsk Time',
        'dstlongname' => 'Yaktsk Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => -25200000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => -25200000, ],
    'Asia/Yekaterinburg' => [
        'offset' => 18000000,
        'shortname' => 'YEKT',
        'dstshortname' => 'YEKST',
        'longname' => 'Yekaterinburg Time',
        'dstlongname' => 'Yekaterinburg Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => -10800000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => -10800000, ],
    'Asia/Yerevan' => [
        'offset' => 14400000,
        'shortname' => 'AMT',
        'dstshortname' => 'AMST',
        'longname' => 'Armenia Time',
        'dstlongname' => 'Armenia Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => -7200000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => -7200000, ],
    'Atlantic/Azores' => [
        'offset' => -3600000,
        'shortname' => 'AZOT',
        'dstshortname' => 'AZOST',
        'longname' => 'Azores Time',
        'dstlongname' => 'Azores Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Atlantic/Bermuda' => [
        'offset' => -14400000,
        'shortname' => 'AST',
        'dstshortname' => 'ADT',
        'longname' => 'Atlantic Standard Time',
        'dstlongname' => 'Atlantic Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 21600000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 18000000, ],
    'Atlantic/Canary' => [
        'offset' => 0,
        'shortname' => 'WET',
        'dstshortname' => 'WEST',
        'longname' => 'Western European Time',
        'dstlongname' => 'Western European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Atlantic/Cape_Verde' => [
        'offset' => -3600000,
        'shortname' => 'CVT',
        'dstshortname' => null,
        'longname' => 'Cape Verde Time', ],
    'Atlantic/Faeroe' => [
        'offset' => 0,
        'shortname' => 'WET',
        'dstshortname' => 'WEST',
        'longname' => 'Western European Time',
        'dstlongname' => 'Western European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Atlantic/Faroe' => [
        'offset' => 0,
        'shortname' => 'WET',
        'dstshortname' => 'WEST',
        'longname' => 'Western Europe Time',
        'dstlongname' => 'Western Europe Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Atlantic/Jan_Mayen' => [
        'offset' => 3600000,
        'shortname' => 'CET',
        'dstshortname' => 'CEST',
        'longname' => 'Eastern Greenland Time',
        'dstlongname' => 'Eastern Greenland Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Atlantic/Madeira' => [
        'offset' => 0,
        'shortname' => 'WET',
        'dstshortname' => 'WEST',
        'longname' => 'Western European Time',
        'dstlongname' => 'Western European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Atlantic/Reykjavik' => [
        'offset' => 0,
        'shortname' => 'GMT',
        'dstshortname' => null,
        'longname' => 'Greenwich Mean Time', ],
    'Atlantic/South_Georgia' => [
        'offset' => -7200000,
        'shortname' => 'GST',
        'dstshortname' => null,
        'longname' => 'South Georgia Standard Time', ],
    'Atlantic/St_Helena' => [
        'offset' => 0,
        'shortname' => 'GMT',
        'dstshortname' => null,
        'longname' => 'Greenwich Mean Time', ],
    'Atlantic/Stanley' => [
        'offset' => -14400000,
        'shortname' => 'FKT',
        'dstshortname' => 'FKST',
        'longname' => 'Falkland Is. Time',
        'dstlongname' => 'Falkland Is. Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 9,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => 7200000,
        'summertimeendmonth' => 4,
        'summertimeendday' => 'Sun>=15',
        'summertimeendtime' => 7200000, ],
    'Australia/ACT' => [
        'offset' => 36000000,
        'shortname' => 'EST',
        'dstshortname' => 'EST',
        'longname' => 'Eastern Standard Time (New South Wales)',
        'dstlongname' => 'Eastern Summer Time (New South Wales)',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => -28800000,
        'summertimeendmonth' => 4,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => -28800000, ],
    'Australia/Adelaide' => [
        'offset' => 34200000,
        'shortname' => 'CST',
        'dstshortname' => 'CST',
        'longname' => 'Central Standard Time (South Australia)',
        'dstlongname' => 'Central Summer Time (South Australia)',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => -27000000,
        'summertimeendmonth' => 4,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => -27000000, ],
    'Australia/Brisbane' => [
        'offset' => 36000000,
        'shortname' => 'EST',
        'dstshortname' => null,
        'longname' => 'Eastern Standard Time (Queensland)', ],
    'Australia/Broken_Hill' => [
        'offset' => 34200000,
        'shortname' => 'CST',
        'dstshortname' => 'CST',
        'longname' => 'Central Standard Time (South Australia/New South Wales)',
        'dstlongname' => 'Central Summer Time (South Australia/New South Wales)',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => -27000000,
        'summertimeendmonth' => 4,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => -27000000, ],
    'Australia/Canberra' => [
        'offset' => 36000000,
        'shortname' => 'EST',
        'dstshortname' => 'EST',
        'longname' => 'Eastern Standard Time (New South Wales)',
        'dstlongname' => 'Eastern Summer Time (New South Wales)',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => -28800000,
        'summertimeendmonth' => 4,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => -28800000, ],
    'Australia/Currie' => [
        'offset' => 36000000,
        'shortname' => 'EST',
        'dstshortname' => 'EST',
        'longname' => 'Eastern Standard Time',
        'dstlongname' => 'Eastern Standard Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => -28800000,
        'summertimeendmonth' => 4,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => -28800000, ],
    'Australia/Darwin' => [
        'offset' => 34200000,
        'shortname' => 'CST',
        'dstshortname' => null,
        'longname' => 'Central Standard Time (Northern Territory)', ],
    'Australia/Eucla' => [
        'offset' => 31500000,
        'shortname' => 'CWST',
        'dstshortname' => 'CWST',
        'longname' => 'Central Western Standard Time',
        'dstlongname' => 'Central Western Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => -24300000,
        'summertimeendmonth' => 3,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => -24300000, ],
    'Australia/Hobart' => [
        'offset' => 36000000,
        'shortname' => 'EST',
        'dstshortname' => 'EST',
        'longname' => 'Eastern Standard Time (Tasmania)',
        'dstlongname' => 'Eastern Summer Time (Tasmania)',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => -28800000,
        'summertimeendmonth' => 4,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => -28800000, ],
    'Australia/LHI' => [
        'offset' => 37800000,
        'shortname' => 'LHST',
        'dstshortname' => 'LHST',
        'longname' => 'Load Howe Standard Time',
        'dstlongname' => 'Load Howe Summer Time',
        'summertimeoffset' => 1800000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => 7200000,
        'summertimeendmonth' => 4,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 7200000, ],
    'Australia/Lindeman' => [
        'offset' => 36000000,
        'shortname' => 'EST',
        'dstshortname' => null,
        'longname' => 'Eastern Standard Time (Queensland)', ],
    'Australia/Lord_Howe' => [
        'offset' => 37800000,
        'shortname' => 'LHST',
        'dstshortname' => 'LHST',
        'longname' => 'Load Howe Standard Time',
        'dstlongname' => 'Load Howe Summer Time',
        'summertimeoffset' => 1800000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => 7200000,
        'summertimeendmonth' => 4,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 7200000, ],
    'Australia/Melbourne' => [
        'offset' => 36000000,
        'shortname' => 'EST',
        'dstshortname' => 'EST',
        'longname' => 'Eastern Standard Time (Victoria)',
        'dstlongname' => 'Eastern Summer Time (Victoria)',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => -28800000,
        'summertimeendmonth' => 4,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => -28800000, ],
    'Australia/NSW' => [
        'offset' => 36000000,
        'shortname' => 'EST',
        'dstshortname' => 'EST',
        'longname' => 'Eastern Standard Time (New South Wales)',
        'dstlongname' => 'Eastern Summer Time (New South Wales)',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => -28800000,
        'summertimeendmonth' => 4,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => -28800000, ],
    'Australia/North' => [
        'offset' => 34200000,
        'shortname' => 'CST',
        'dstshortname' => null,
        'longname' => 'Central Standard Time (Northern Territory)', ],
    'Australia/Perth' => [
        'offset' => 28800000,
        'shortname' => 'WST',
        'dstshortname' => 'WST',
        'longname' => 'Western Standard Time (Australia)',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => -21600000,
        'summertimeendmonth' => 3,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => -21600000, ],
    'Australia/Queensland' => [
        'offset' => 36000000,
        'shortname' => 'EST',
        'dstshortname' => null,
        'longname' => 'Eastern Standard Time (Queensland)', ],
    'Australia/South' => [
        'offset' => 34200000,
        'shortname' => 'CST',
        'dstshortname' => 'CST',
        'longname' => 'Central Standard Time (South Australia)',
        'dstlongname' => 'Central Summer Time (South Australia)',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => -27000000,
        'summertimeendmonth' => 4,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => -27000000, ],
    'Australia/Sydney' => [
        'offset' => 36000000,
        'shortname' => 'EST',
        'dstshortname' => 'EST',
        'longname' => 'Eastern Standard Time (New South Wales)',
        'dstlongname' => 'Eastern Summer Time (New South Wales)',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => -28800000,
        'summertimeendmonth' => 4,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => -28800000, ],
    'Australia/Tasmania' => [
        'offset' => 36000000,
        'shortname' => 'EST',
        'dstshortname' => 'EST',
        'longname' => 'Eastern Standard Time (Tasmania)',
        'dstlongname' => 'Eastern Summer Time (Tasmania)',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => -28800000,
        'summertimeendmonth' => 4,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => -28800000, ],
    'Australia/Victoria' => [
        'offset' => 36000000,
        'shortname' => 'EST',
        'dstshortname' => 'EST',
        'longname' => 'Eastern Standard Time (Victoria)',
        'dstlongname' => 'Eastern Summer Time (Victoria)',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => -28800000,
        'summertimeendmonth' => 4,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => -28800000, ],
    'Australia/West' => [
        'offset' => 28800000,
        'shortname' => 'WST',
        'dstshortname' => 'WST',
        'longname' => 'Western Standard Time (Australia)',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => -21600000,
        'summertimeendmonth' => 3,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => -21600000, ],
    'Australia/Yancowinna' => [
        'offset' => 34200000,
        'shortname' => 'CST',
        'dstshortname' => 'CST',
        'longname' => 'Central Standard Time (South Australia/New South Wales)',
        'dstlongname' => 'Central Summer Time (South Australia/New South Wales)',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => -27000000,
        'summertimeendmonth' => 4,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => -27000000, ],
    'Brazil/Acre' => [
        'offset' => -18000000,
        'shortname' => 'ACT',
        'dstshortname' => null,
        'longname' => 'Acre Time', ],
    'Brazil/DeNoronha' => [
        'offset' => -7200000,
        'shortname' => 'FNT',
        'dstshortname' => null,
        'longname' => 'Fernando de Noronha Time', ],
    'Brazil/East' => [
        'offset' => -10800000,
        'shortname' => 'BRT',
        'dstshortname' => 'BRST',
        'longname' => 'Brazil Time',
        'dstlongname' => 'Brazil Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 10800000,
        'summertimeendmonth' => 2,
        'summertimeendday' => 'Sun>=15',
        'summertimeendtime' => 7200000, ],
    'Brazil/West' => [
        'offset' => -14400000,
        'shortname' => 'AMT',
        'dstshortname' => null,
        'longname' => 'Amazon Standard Time', ],
    'CET' => [
        'offset' => 3600000,
        'shortname' => 'CET',
        'dstshortname' => 'CEST',
        'longname' => 'Central European Time',
        'dstlongname' => 'Central European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'CST6CDT' => [
        'offset' => -21600000,
        'shortname' => 'CST',
        'dstshortname' => 'CDT',
        'longname' => 'Central Standard Time',
        'dstlongname' => 'Central Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 28800000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 25200000, ],
    'Canada/Atlantic' => [
        'offset' => -14400000,
        'shortname' => 'AST',
        'dstshortname' => 'ADT',
        'longname' => 'Atlantic Standard Time',
        'dstlongname' => 'Atlantic Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 21600000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 18000000, ],
    'Canada/Central' => [
        'offset' => -21600000,
        'shortname' => 'CST',
        'dstshortname' => 'CDT',
        'longname' => 'Central Standard Time',
        'dstlongname' => 'Central Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 28800000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 25200000, ],
    'Canada/East-Saskatchewan' => [
        'offset' => -21600000,
        'shortname' => 'CST',
        'dstshortname' => null,
        'longname' => 'Central Standard Time', ],
    'Canada/Eastern' => [
        'offset' => -18000000,
        'shortname' => 'EST',
        'dstshortname' => 'EDT',
        'longname' => 'Eastern Standard Time',
        'dstlongname' => 'Eastern Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 25200000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 21600000, ],
    'Canada/Mountain' => [
        'offset' => -25200000,
        'shortname' => 'MST',
        'dstshortname' => 'MDT',
        'longname' => 'Mountain Standard Time',
        'dstlongname' => 'Mountain Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 32400000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 28800000, ],
    'Canada/Newfoundland' => [
        'offset' => -12600000,
        'shortname' => 'NST',
        'dstshortname' => 'NDT',
        'longname' => 'Newfoundland Standard Time',
        'dstlongname' => 'Newfoundland Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 12660000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 9060000, ],
    'Canada/Pacific' => [
        'offset' => -28800000,
        'shortname' => 'PST',
        'dstshortname' => 'PDT',
        'longname' => 'Pacific Standard Time',
        'dstlongname' => 'Pacific Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 36000000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 32400000, ],
    'Canada/Saskatchewan' => [
        'offset' => -21600000,
        'shortname' => 'CST',
        'dstshortname' => null,
        'longname' => 'Central Standard Time', ],
    'Canada/Yukon' => [
        'offset' => -28800000,
        'shortname' => 'PST',
        'dstshortname' => 'PDT',
        'longname' => 'Pacific Standard Time',
        'dstlongname' => 'Pacific Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 36000000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 32400000, ],
    'Chile/Continental' => [
        'offset' => -14400000,
        'shortname' => 'CLT',
        'dstshortname' => 'CLST',
        'longname' => 'Chile Time',
        'dstlongname' => 'Chile Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'Sun>=9',
        'summertimestarttime' => 14400000,
        'summertimeendmonth' => 3,
        'summertimeendday' => '30',
        'summertimeendtime' => 10800000, ],
    'Chile/EasterIsland' => [
        'offset' => -21600000,
        'shortname' => 'EAST',
        'dstshortname' => 'EASST',
        'longname' => 'Easter Is. Time',
        'dstlongname' => 'Easter Is. Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'Sun>=9',
        'summertimestarttime' => 14400000,
        'summertimeendmonth' => 3,
        'summertimeendday' => '30',
        'summertimeendtime' => 10800000, ],
    'Cuba' => [
        'offset' => -18000000,
        'shortname' => 'CST',
        'dstshortname' => 'CDT',
        'longname' => 'Central Standard Time',
        'dstlongname' => 'Central Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 18000000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 18000000, ],
    'EET' => [
        'offset' => 7200000,
        'shortname' => 'EET',
        'dstshortname' => 'EEST',
        'longname' => 'Eastern European Time',
        'dstlongname' => 'Eastern European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'EST' => [
        'offset' => -18000000,
        'shortname' => 'EST',
        'dstshortname' => null,
        'longname' => 'Eastern Standard Time',
        'dstlongname' => 'Eastern Daylight Time', ],
    'EST5EDT' => [
        'offset' => -18000000,
        'shortname' => 'EST',
        'dstshortname' => 'EDT',
        'longname' => 'Eastern Standard Time',
        'dstlongname' => 'Eastern Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 25200000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 21600000, ],
    'Egypt' => [
        'offset' => 7200000,
        'shortname' => 'EET',
        'dstshortname' => 'EEST',
        'longname' => 'Eastern European Time',
        'dstlongname' => 'Eastern European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 4,
        'summertimestartday' => 'lastFri',
        'summertimestarttime' => -7200000,
        'summertimeendmonth' => 8,
        'summertimeendday' => 'lastThu',
        'summertimeendtime' => 75600000, ],
    'Eire' => [
        'offset' => 0,
        'shortname' => 'GMT',
        'dstshortname' => 'IST',
        'longname' => 'Greenwich Mean Time',
        'dstlongname' => 'Irish Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Etc/GMT' => [
        'offset' => 0,
        'shortname' => 'GMT',
        'dstshortname' => null,
        'longname' => 'GMT+00:00', ],
    'Etc/GMT+0' => [
        'offset' => 0,
        'shortname' => 'GMT',
        'dstshortname' => null,
        'longname' => 'GMT+00:00', ],
    'Etc/GMT+1' => [
        'offset' => -3600000,
        'shortname' => 'GMT+1',
        'dstshortname' => null,
        'longname' => 'GMT-01:00', ],
    'Etc/GMT+10' => [
        'offset' => -36000000,
        'shortname' => 'GMT+10',
        'dstshortname' => null,
        'longname' => 'GMT-10:00', ],
    'Etc/GMT+11' => [
        'offset' => -39600000,
        'shortname' => 'GMT+11',
        'dstshortname' => null,
        'longname' => 'GMT-11:00', ],
    'Etc/GMT+12' => [
        'offset' => -43200000,
        'shortname' => 'GMT+12',
        'dstshortname' => null,
        'longname' => 'GMT-12:00', ],
    'Etc/GMT+2' => [
        'offset' => -7200000,
        'shortname' => 'GMT+2',
        'dstshortname' => null,
        'longname' => 'GMT-02:00', ],
    'Etc/GMT+3' => [
        'offset' => -10800000,
        'shortname' => 'GMT+3',
        'dstshortname' => null,
        'longname' => 'GMT-03:00', ],
    'Etc/GMT+4' => [
        'offset' => -14400000,
        'shortname' => 'GMT+4',
        'dstshortname' => null,
        'longname' => 'GMT-04:00', ],
    'Etc/GMT+5' => [
        'offset' => -18000000,
        'shortname' => 'GMT+5',
        'dstshortname' => null,
        'longname' => 'GMT-05:00', ],
    'Etc/GMT+6' => [
        'offset' => -21600000,
        'shortname' => 'GMT+6',
        'dstshortname' => null,
        'longname' => 'GMT-06:00', ],
    'Etc/GMT+7' => [
        'offset' => -25200000,
        'shortname' => 'GMT+7',
        'dstshortname' => null,
        'longname' => 'GMT-07:00', ],
    'Etc/GMT+8' => [
        'offset' => -28800000,
        'shortname' => 'GMT+8',
        'dstshortname' => null,
        'longname' => 'GMT-08:00', ],
    'Etc/GMT+9' => [
        'offset' => -32400000,
        'shortname' => 'GMT+9',
        'dstshortname' => null,
        'longname' => 'GMT-09:00', ],
    'Etc/GMT-0' => [
        'offset' => 0,
        'shortname' => 'GMT',
        'dstshortname' => null,
        'longname' => 'GMT+00:00', ],
    'Etc/GMT-1' => [
        'offset' => 3600000,
        'shortname' => 'GMT-1',
        'dstshortname' => null,
        'longname' => 'GMT+01:00', ],
    'Etc/GMT-10' => [
        'offset' => 36000000,
        'shortname' => 'GMT-10',
        'dstshortname' => null,
        'longname' => 'GMT+10:00', ],
    'Etc/GMT-11' => [
        'offset' => 39600000,
        'shortname' => 'GMT-11',
        'dstshortname' => null,
        'longname' => 'GMT+11:00', ],
    'Etc/GMT-12' => [
        'offset' => 43200000,
        'shortname' => 'GMT-12',
        'dstshortname' => null,
        'longname' => 'GMT+12:00', ],
    'Etc/GMT-13' => [
        'offset' => 46800000,
        'shortname' => 'GMT-13',
        'dstshortname' => null,
        'longname' => 'GMT+13:00', ],
    'Etc/GMT-14' => [
        'offset' => 50400000,
        'shortname' => 'GMT-14',
        'dstshortname' => null,
        'longname' => 'GMT+14:00', ],
    'Etc/GMT-2' => [
        'offset' => 7200000,
        'shortname' => 'GMT-2',
        'dstshortname' => null,
        'longname' => 'GMT+02:00', ],
    'Etc/GMT-3' => [
        'offset' => 10800000,
        'shortname' => 'GMT-3',
        'dstshortname' => null,
        'longname' => 'GMT+03:00', ],
    'Etc/GMT-4' => [
        'offset' => 14400000,
        'shortname' => 'GMT-4',
        'dstshortname' => null,
        'longname' => 'GMT+04:00', ],
    'Etc/GMT-5' => [
        'offset' => 18000000,
        'shortname' => 'GMT-5',
        'dstshortname' => null,
        'longname' => 'GMT+05:00', ],
    'Etc/GMT-6' => [
        'offset' => 21600000,
        'shortname' => 'GMT-6',
        'dstshortname' => null,
        'longname' => 'GMT+06:00', ],
    'Etc/GMT-7' => [
        'offset' => 25200000,
        'shortname' => 'GMT-7',
        'dstshortname' => null,
        'longname' => 'GMT+07:00', ],
    'Etc/GMT-8' => [
        'offset' => 28800000,
        'shortname' => 'GMT-8',
        'dstshortname' => null,
        'longname' => 'GMT+08:00', ],
    'Etc/GMT-9' => [
        'offset' => 32400000,
        'shortname' => 'GMT-9',
        'dstshortname' => null,
        'longname' => 'GMT+09:00', ],
    'Etc/GMT0' => [
        'offset' => 0,
        'shortname' => 'GMT',
        'dstshortname' => null,
        'longname' => 'GMT+00:00', ],
    'Etc/Greenwich' => [
        'offset' => 0,
        'shortname' => 'GMT',
        'dstshortname' => null,
        'longname' => 'Greenwich Mean Time', ],
    'Etc/UCT' => [
        'offset' => 0,
        'shortname' => 'UCT',
        'dstshortname' => null,
        'longname' => 'Coordinated Universal Time', ],
    'Etc/UTC' => [
        'offset' => 0,
        'shortname' => 'UTC',
        'dstshortname' => null,
        'longname' => 'Coordinated Universal Time', ],
    'Etc/Universal' => [
        'offset' => 0,
        'shortname' => 'UTC',
        'dstshortname' => null,
        'longname' => 'Coordinated Universal Time', ],
    'Etc/Zulu' => [
        'offset' => 0,
        'shortname' => 'UTC',
        'dstshortname' => null,
        'longname' => 'Coordinated Universal Time', ],
    'Europe/Amsterdam' => [
        'offset' => 3600000,
        'shortname' => 'CET',
        'dstshortname' => 'CEST',
        'longname' => 'Central European Time',
        'dstlongname' => 'Central European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Andorra' => [
        'offset' => 3600000,
        'shortname' => 'CET',
        'dstshortname' => 'CEST',
        'longname' => 'Central European Time',
        'dstlongname' => 'Central European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Athens' => [
        'offset' => 7200000,
        'shortname' => 'EET',
        'dstshortname' => 'EEST',
        'longname' => 'Eastern European Time',
        'dstlongname' => 'Eastern European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Belfast' => [
        'offset' => 0,
        'shortname' => 'GMT',
        'dstshortname' => 'BST',
        'longname' => 'Greenwich Mean Time',
        'dstlongname' => 'British Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Belgrade' => [
        'offset' => 3600000,
        'shortname' => 'CET',
        'dstshortname' => 'CEST',
        'longname' => 'Central European Time',
        'dstlongname' => 'Central European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Berlin' => [
        'offset' => 3600000,
        'shortname' => 'CET',
        'dstshortname' => 'CEST',
        'longname' => 'Central European Time',
        'dstlongname' => 'Central European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Bratislava' => [
        'offset' => 3600000,
        'shortname' => 'CET',
        'dstshortname' => 'CEST',
        'longname' => 'Central European Time',
        'dstlongname' => 'Central European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Brussels' => [
        'offset' => 3600000,
        'shortname' => 'CET',
        'dstshortname' => 'CEST',
        'longname' => 'Central European Time',
        'dstlongname' => 'Central European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Bucharest' => [
        'offset' => 7200000,
        'shortname' => 'EET',
        'dstshortname' => 'EEST',
        'longname' => 'Eastern European Time',
        'dstlongname' => 'Eastern European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Budapest' => [
        'offset' => 3600000,
        'shortname' => 'CET',
        'dstshortname' => 'CEST',
        'longname' => 'Central European Time',
        'dstlongname' => 'Central European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Chisinau' => [
        'offset' => 7200000,
        'shortname' => 'EET',
        'dstshortname' => 'EEST',
        'longname' => 'Eastern European Time',
        'dstlongname' => 'Eastern European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Copenhagen' => [
        'offset' => 3600000,
        'shortname' => 'CET',
        'dstshortname' => 'CEST',
        'longname' => 'Central European Time',
        'dstlongname' => 'Central European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Dublin' => [
        'offset' => 0,
        'shortname' => 'GMT',
        'dstshortname' => 'IST',
        'longname' => 'Greenwich Mean Time',
        'dstlongname' => 'Irish Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Gibraltar' => [
        'offset' => 3600000,
        'shortname' => 'CET',
        'dstshortname' => 'CEST',
        'longname' => 'Central European Time',
        'dstlongname' => 'Central European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Guernsey' => [
        'offset' => 0,
        'shortname' => 'GMT',
        'dstshortname' => 'BST',
        'longname' => 'Greenwich Mean Time',
        'dstlongname' => 'British Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Helsinki' => [
        'offset' => 7200000,
        'shortname' => 'EET',
        'dstshortname' => 'EEST',
        'longname' => 'Eastern European Time',
        'dstlongname' => 'Eastern European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Isle_of_Man' => [
        'offset' => 0,
        'shortname' => 'GMT',
        'dstshortname' => 'BST',
        'longname' => 'Greenwich Mean Time',
        'dstlongname' => 'British Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Istanbul' => [
        'offset' => 7200000,
        'shortname' => 'EET',
        'dstshortname' => 'EEST',
        'longname' => 'Eastern European Time',
        'dstlongname' => 'Eastern European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Jersey' => [
        'offset' => 0,
        'shortname' => 'GMT',
        'dstshortname' => 'BST',
        'longname' => 'Greenwich Mean Time',
        'dstlongname' => 'British Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Kaliningrad' => [
        'offset' => 7200000,
        'shortname' => 'EET',
        'dstshortname' => 'EEST',
        'longname' => 'Eastern European Time',
        'dstlongname' => 'Eastern European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 0,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 0, ],
    'Europe/Kiev' => [
        'offset' => 7200000,
        'shortname' => 'EET',
        'dstshortname' => 'EEST',
        'longname' => 'Eastern European Time',
        'dstlongname' => 'Eastern European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Lisbon' => [
        'offset' => 0,
        'shortname' => 'WET',
        'dstshortname' => 'WEST',
        'longname' => 'Western European Time',
        'dstlongname' => 'Western European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Ljubljana' => [
        'offset' => 3600000,
        'shortname' => 'CET',
        'dstshortname' => 'CEST',
        'longname' => 'Central European Time',
        'dstlongname' => 'Central European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/London' => [
        'offset' => 0,
        'shortname' => 'GMT',
        'dstshortname' => 'BST',
        'longname' => 'Greenwich Mean Time',
        'dstlongname' => 'British Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Luxembourg' => [
        'offset' => 3600000,
        'shortname' => 'CET',
        'dstshortname' => 'CEST',
        'longname' => 'Central European Time',
        'dstlongname' => 'Central European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Madrid' => [
        'offset' => 3600000,
        'shortname' => 'CET',
        'dstshortname' => 'CEST',
        'longname' => 'Central European Time',
        'dstlongname' => 'Central European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Malta' => [
        'offset' => 3600000,
        'shortname' => 'CET',
        'dstshortname' => 'CEST',
        'longname' => 'Central European Time',
        'dstlongname' => 'Central European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Mariehamn' => [
        'offset' => 7200000,
        'shortname' => 'EET',
        'dstshortname' => 'EEST',
        'longname' => 'Eastern European Time',
        'dstlongname' => 'Eastern European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Minsk' => [
        'offset' => 7200000,
        'shortname' => 'EET',
        'dstshortname' => 'EEST',
        'longname' => 'Eastern European Time',
        'dstlongname' => 'Eastern European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 0,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 0, ],
    'Europe/Monaco' => [
        'offset' => 3600000,
        'shortname' => 'CET',
        'dstshortname' => 'CEST',
        'longname' => 'Central European Time',
        'dstlongname' => 'Central European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Moscow' => [
        'offset' => 10800000,
        'shortname' => 'MSK',
        'dstshortname' => 'MSD',
        'longname' => 'Moscow Standard Time',
        'dstlongname' => 'Moscow Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => -3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => -3600000, ],
    'Europe/Nicosia' => [
        'offset' => 7200000,
        'shortname' => 'EET',
        'dstshortname' => 'EEST',
        'longname' => 'Eastern European Time',
        'dstlongname' => 'Eastern European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Oslo' => [
        'offset' => 3600000,
        'shortname' => 'CET',
        'dstshortname' => 'CEST',
        'longname' => 'Central European Time',
        'dstlongname' => 'Central European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Paris' => [
        'offset' => 3600000,
        'shortname' => 'CET',
        'dstshortname' => 'CEST',
        'longname' => 'Central European Time',
        'dstlongname' => 'Central European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Podgorica' => [
        'offset' => 3600000,
        'shortname' => 'CET',
        'dstshortname' => 'CEST',
        'longname' => 'Central European Time',
        'dstlongname' => 'Central European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Prague' => [
        'offset' => 3600000,
        'shortname' => 'CET',
        'dstshortname' => 'CEST',
        'longname' => 'Central European Time',
        'dstlongname' => 'Central European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Riga' => [
        'offset' => 7200000,
        'shortname' => 'EET',
        'dstshortname' => 'EEST',
        'longname' => 'Eastern European Time',
        'dstlongname' => 'Eastern European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Rome' => [
        'offset' => 3600000,
        'shortname' => 'CET',
        'dstshortname' => 'CEST',
        'longname' => 'Central European Time',
        'dstlongname' => 'Central European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Samara' => [
        'offset' => 14400000,
        'shortname' => 'SAMT',
        'dstshortname' => 'SAMST',
        'longname' => 'Samara Time',
        'dstlongname' => 'Samara Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => -7200000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => -7200000, ],
    'Europe/San_Marino' => [
        'offset' => 3600000,
        'shortname' => 'CET',
        'dstshortname' => 'CEST',
        'longname' => 'Central European Time',
        'dstlongname' => 'Central European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Sarajevo' => [
        'offset' => 3600000,
        'shortname' => 'CET',
        'dstshortname' => 'CEST',
        'longname' => 'Central European Time',
        'dstlongname' => 'Central European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Simferopol' => [
        'offset' => 7200000,
        'shortname' => 'EET',
        'dstshortname' => 'EEST',
        'longname' => 'Eastern European Time',
        'dstlongname' => 'Eastern European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Skopje' => [
        'offset' => 3600000,
        'shortname' => 'CET',
        'dstshortname' => 'CEST',
        'longname' => 'Central European Time',
        'dstlongname' => 'Central European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Sofia' => [
        'offset' => 7200000,
        'shortname' => 'EET',
        'dstshortname' => 'EEST',
        'longname' => 'Eastern European Time',
        'dstlongname' => 'Eastern European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Stockholm' => [
        'offset' => 3600000,
        'shortname' => 'CET',
        'dstshortname' => 'CEST',
        'longname' => 'Central European Time',
        'dstlongname' => 'Central European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Tallinn' => [
        'offset' => 7200000,
        'shortname' => 'EET',
        'dstshortname' => 'EEST',
        'longname' => 'Eastern European Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Tirane' => [
        'offset' => 3600000,
        'shortname' => 'CET',
        'dstshortname' => 'CEST',
        'longname' => 'Central European Time',
        'dstlongname' => 'Central European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Tiraspol' => [
        'offset' => 7200000,
        'shortname' => 'EET',
        'dstshortname' => 'EEST',
        'longname' => 'Eastern European Time',
        'dstlongname' => 'Eastern European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Uzhgorod' => [
        'offset' => 7200000,
        'shortname' => 'EET',
        'dstshortname' => 'EEST',
        'longname' => 'Eastern European Time',
        'dstlongname' => 'Eastern European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Vaduz' => [
        'offset' => 3600000,
        'shortname' => 'CET',
        'dstshortname' => 'CEST',
        'longname' => 'Central European Time',
        'dstlongname' => 'Central European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Vatican' => [
        'offset' => 3600000,
        'shortname' => 'CET',
        'dstshortname' => 'CEST',
        'longname' => 'Central European Time',
        'dstlongname' => 'Central European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Vienna' => [
        'offset' => 3600000,
        'shortname' => 'CET',
        'dstshortname' => 'CEST',
        'longname' => 'Central European Time',
        'dstlongname' => 'Central European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Vilnius' => [
        'offset' => 7200000,
        'shortname' => 'EET',
        'dstshortname' => 'EEST',
        'longname' => 'Eastern European Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Volgograd' => [
        'offset' => 10800000,
        'shortname' => 'VOLT',
        'dstshortname' => 'VOLST',
        'longname' => 'Volograd Time',
        'longname' => 'Volograd Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => -3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => -3600000, ],
    'Europe/Warsaw' => [
        'offset' => 3600000,
        'shortname' => 'CET',
        'dstshortname' => 'CEST',
        'longname' => 'Central European Time',
        'dstlongname' => 'Central European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Zagreb' => [
        'offset' => 3600000,
        'shortname' => 'CET',
        'dstshortname' => 'CEST',
        'longname' => 'Central European Time',
        'dstlongname' => 'Central European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Zaporozhye' => [
        'offset' => 7200000,
        'shortname' => 'EET',
        'dstshortname' => 'EEST',
        'longname' => 'Eastern European Time',
        'dstlongname' => 'Eastern European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Europe/Zurich' => [
        'offset' => 3600000,
        'shortname' => 'CET',
        'dstshortname' => 'CEST',
        'longname' => 'Central European Time',
        'dstlongname' => 'Central European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'GB' => [
        'offset' => 0,
        'shortname' => 'GMT',
        'dstshortname' => 'BST',
        'longname' => 'Greenwich Mean Time',
        'dstlongname' => 'British Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'GB-Eire' => [
        'offset' => 0,
        'shortname' => 'GMT',
        'dstshortname' => 'BST',
        'longname' => 'Greenwich Mean Time',
        'dstlongname' => 'British Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'GMT' => [
        'offset' => 0,
        'shortname' => 'GMT',
        'dstshortname' => null,
        'longname' => 'Greenwich Mean Time', ],
    'GMT+0' => [
        'offset' => 0,
        'shortname' => 'GMT+0',
        'dstshortname' => null, ],
    'GMT+00:00' => [
        'offset' => 0,
        'shortname' => 'GMT+00:00',
        'dstshortname' => null,
        'longname' => 'GMT+00:00', ],
    'GMT+01:00' => [
        'offset' => 3600000,
        'shortname' => 'GMT+01:00',
        'dstshortname' => null,
        'longname' => 'GMT+01:00', ],
    'GMT+02:00' => [
        'offset' => 7200000,
        'shortname' => 'GMT+02:00',
        'dstshortname' => null,
        'longname' => 'GMT+02:00', ],
    'GMT+03:00' => [
        'offset' => 10800000,
        'shortname' => 'GMT+03:00',
        'dstshortname' => null,
        'longname' => 'GMT+03:00', ],
    'GMT+04:00' => [
        'offset' => 14400000,
        'shortname' => 'GMT+04:00',
        'dstshortname' => null,
        'longname' => 'GMT+04:00', ],
    'GMT+05:00' => [
        'offset' => 18000000,
        'shortname' => 'GMT+05:00',
        'dstshortname' => null,
        'longname' => 'GMT+05:00', ],
    'GMT+06:00' => [
        'offset' => 21600000,
        'shortname' => 'GMT+06:00',
        'dstshortname' => null,
        'longname' => 'GMT+06:00', ],
    'GMT+07:00' => [
        'offset' => 25200000,
        'shortname' => 'GMT+07:00',
        'dstshortname' => null,
        'longname' => 'GMT+07:00', ],
    'GMT+08:00' => [
        'offset' => 28800000,
        'shortname' => 'GMT+08:00',
        'dstshortname' => null,
        'longname' => 'GMT+08:00', ],
    'GMT+09:00' => [
        'offset' => 32400000,
        'shortname' => 'GMT+09:00',
        'dstshortname' => null,
        'longname' => 'GMT+09:00', ],
    'GMT+1' => [
        'offset' => 3600000,
        'shortname' => 'GMT+1',
        'dstshortname' => null, ],
    'GMT+10' => [
        'offset' => 36000000,
        'shortname' => 'GMT+10',
        'dstshortname' => null, ],
    'GMT+10:00' => [
        'offset' => 36000000,
        'shortname' => 'GMT+10:00',
        'dstshortname' => null,
        'longname' => 'GMT+10:00', ],
    'GMT+11' => [
        'offset' => 39600000,
        'shortname' => 'GMT+11',
        'dstshortname' => null, ],
    'GMT+11:00' => [
        'offset' => 39600000,
        'shortname' => 'GMT+11:00',
        'dstshortname' => null,
        'longname' => 'GMT+11:00', ],
    'GMT+12' => [
        'offset' => 43200000,
        'shortname' => 'GMT+12',
        'dstshortname' => null, ],
    'GMT+12:00' => [
        'offset' => 43200000,
        'shortname' => 'GMT+12:00',
        'dstshortname' => null,
        'longname' => 'GMT+12:00', ],
    'GMT+13' => [
        'offset' => 46800000,
        'shortname' => 'GMT+13',
        'dstshortname' => null, ],
    'GMT+13:00' => [
        'offset' => 46800000,
        'shortname' => 'GMT+13:00',
        'dstshortname' => null,
        'longname' => 'GMT+13:00', ],
    'GMT+14' => [
        'offset' => 50400000,
        'shortname' => 'GMT+14',
        'dstshortname' => null, ],
    'GMT+14:00' => [
        'offset' => 50400000,
        'shortname' => 'GMT+14:00',
        'dstshortname' => null,
        'longname' => 'GMT+14:00', ],
    'GMT+2' => [
        'offset' => 7200000,
        'shortname' => 'GMT+2',
        'dstshortname' => null, ],
    'GMT+3' => [
        'offset' => 10800000,
        'shortname' => 'GMT+3',
        'dstshortname' => null, ],
    'GMT+4' => [
        'offset' => 14400000,
        'shortname' => 'GMT+4',
        'dstshortname' => null, ],
    'GMT+5' => [
        'offset' => 18000000,
        'shortname' => 'GMT+5',
        'dstshortname' => null, ],
    'GMT+6' => [
        'offset' => 21600000,
        'shortname' => 'GMT+6',
        'dstshortname' => null, ],
    'GMT+7' => [
        'offset' => 25200000,
        'shortname' => 'GMT+7',
        'dstshortname' => null, ],
    'GMT+8' => [
        'offset' => 28800000,
        'shortname' => 'GMT+8',
        'dstshortname' => null, ],
    'GMT+9' => [
        'offset' => 32400000,
        'shortname' => 'GMT+9',
        'dstshortname' => null, ],
    'GMT-0' => [
        'offset' => 0,
        'shortname' => 'GMT-0',
        'dstshortname' => null, ],
    'GMT-00:00' => [
        'offset' => 0,
        'shortname' => 'GMT-00:00',
        'dstshortname' => null, ],
    'GMT-01:00' => [
        'offset' => -3600000,
        'shortname' => 'GMT-01:00',
        'dstshortname' => null,
        'longname' => 'GMT-01:00', ],
    'GMT-02:00' => [
        'offset' => -7200000,
        'shortname' => 'GMT-02:00',
        'dstshortname' => null,
        'longname' => 'GMT-02:00', ],
    'GMT-03:00' => [
        'offset' => -10800000,
        'shortname' => 'GMT-03:00',
        'dstshortname' => null,
        'longname' => 'GMT-03:00', ],
    'GMT-04:00' => [
        'offset' => -14400000,
        'shortname' => 'GMT-04:00',
        'dstshortname' => null,
        'longname' => 'GMT-04:00', ],
    'GMT-05:00' => [
        'offset' => -18000000,
        'shortname' => 'GMT-05:00',
        'dstshortname' => null,
        'longname' => 'GMT-05:00', ],
    'GMT-06:00' => [
        'offset' => -21600000,
        'shortname' => 'GMT-06:00',
        'dstshortname' => null,
        'longname' => 'GMT-06:00', ],
    'GMT-07:00' => [
        'offset' => -25200000,
        'shortname' => 'GMT-07:00',
        'dstshortname' => null,
        'longname' => 'GMT-07:00', ],
    'GMT-08:00' => [
        'offset' => -28800000,
        'shortname' => 'GMT-08:00',
        'dstshortname' => null,
        'longname' => 'GMT-08:00', ],
    'GMT-09:00' => [
        'offset' => -32400000,
        'shortname' => 'GMT-09:00',
        'dstshortname' => null,
        'longname' => 'GMT-09:00', ],
    'GMT-1' => [
        'offset' => -3600000,
        'shortname' => 'GMT-1',
        'dstshortname' => null, ],
    'GMT-10' => [
        'offset' => -36000000,
        'shortname' => 'GMT-10',
        'dstshortname' => null, ],
    'GMT-10:00' => [
        'offset' => -36000000,
        'shortname' => 'GMT-10:00',
        'dstshortname' => null,
        'longname' => 'GMT-10:00', ],
    'GMT-11' => [
        'offset' => -39600000,
        'shortname' => 'GMT-11',
        'dstshortname' => null, ],
    'GMT-11:00' => [
        'offset' => -39600000,
        'shortname' => 'GMT-11:00',
        'dstshortname' => null,
        'longname' => 'GMT-11:00', ],
    'GMT-12' => [
        'offset' => -43200000,
        'shortname' => 'GMT-12',
        'dstshortname' => null, ],
    'GMT-12:00' => [
        'offset' => -43200000,
        'shortname' => 'GMT-12:00',
        'dstshortname' => null,
        'longname' => 'GMT-12:00', ],
    'GMT-2' => [
        'offset' => -7200000,
        'shortname' => 'GMT-2',
        'dstshortname' => null, ],
    'GMT-3' => [
        'offset' => -10800000,
        'shortname' => 'GMT-3',
        'dstshortname' => null, ],
    'GMT-4' => [
        'offset' => -14400000,
        'shortname' => 'GMT-4',
        'dstshortname' => null, ],
    'GMT-5' => [
        'offset' => -18000000,
        'shortname' => 'GMT-5',
        'dstshortname' => null, ],
    'GMT-6' => [
        'offset' => -21600000,
        'shortname' => 'GMT-6',
        'dstshortname' => null, ],
    'GMT-7' => [
        'offset' => -25200000,
        'shortname' => 'GMT-7',
        'dstshortname' => null, ],
    'GMT-8' => [
        'offset' => -28800000,
        'shortname' => 'GMT-8',
        'dstshortname' => null, ],
    'GMT-9' => [
        'offset' => -32400000,
        'shortname' => 'GMT-9',
        'dstshortname' => null, ],
    'GMT0' => [
        'offset' => 0,
        'shortname' => 'GMT',
        'dstshortname' => null,
        'longname' => 'GMT+00:00', ],
    'Greenwich' => [
        'offset' => 0,
        'shortname' => 'GMT',
        'dstshortname' => null,
        'longname' => 'Greenwich Mean Time', ],
    'HST' => [
        'offset' => -36000000,
        'shortname' => 'HST',
        'dstshortname' => null,
        'longname' => 'Hawaii Standard Time', ],
    'Hongkong' => [
        'offset' => 28800000,
        'shortname' => 'HKT',
        'dstshortname' => null,
        'longname' => 'Hong Kong Time', ],
    'Iceland' => [
        'offset' => 0,
        'shortname' => 'GMT',
        'dstshortname' => null,
        'longname' => 'Greenwich Mean Time', ],
    'Indian/Antananarivo' => [
        'offset' => 10800000,
        'shortname' => 'EAT',
        'dstshortname' => null,
        'longname' => 'Eastern African Time', ],
    'Indian/Chagos' => [
        'offset' => 21600000,
        'shortname' => 'IOT',
        'dstshortname' => null,
        'longname' => 'Indian Ocean Territory Time', ],
    'Indian/Christmas' => [
        'offset' => 25200000,
        'shortname' => 'CXT',
        'dstshortname' => null,
        'longname' => 'Christmas Island Time', ],
    'Indian/Cocos' => [
        'offset' => 23400000,
        'shortname' => 'CCT',
        'dstshortname' => null,
        'longname' => 'Cocos Islands Time', ],
    'Indian/Comoro' => [
        'offset' => 10800000,
        'shortname' => 'EAT',
        'dstshortname' => null,
        'longname' => 'Eastern African Time', ],
    'Indian/Kerguelen' => [
        'offset' => 18000000,
        'shortname' => 'TFT',
        'dstshortname' => null,
        'longname' => 'French Southern & Antarctic Lands Time', ],
    'Indian/Mahe' => [
        'offset' => 14400000,
        'shortname' => 'SCT',
        'dstshortname' => null,
        'longname' => 'Seychelles Time', ],
    'Indian/Maldives' => [
        'offset' => 18000000,
        'shortname' => 'MVT',
        'dstshortname' => null,
        'longname' => 'Maldives Time', ],
    'Indian/Mauritius' => [
        'offset' => 14400000,
        'shortname' => 'MUT',
        'dstshortname' => null,
        'longname' => 'Mauritius Time', ],
    'Indian/Mayotte' => [
        'offset' => 10800000,
        'shortname' => 'EAT',
        'dstshortname' => null,
        'longname' => 'Eastern African Time', ],
    'Indian/Reunion' => [
        'offset' => 14400000,
        'shortname' => 'RET',
        'dstshortname' => null,
        'longname' => 'Reunion Time', ],
    'Iran' => [
        'offset' => 12600000,
        'shortname' => 'IRST',
        'dstshortname' => 'IRDT',
        'longname' => 'Iran Time',
        'dstlongname' => 'Iran Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => '21',
        'summertimestarttime' => -12600000,
        'summertimeendmonth' => 9,
        'summertimeendday' => '21',
        'summertimeendtime' => -16200000, ],
    'Israel' => [
        'offset' => 7200000,
        'shortname' => 'IST',
        'dstshortname' => 'IDT',
        'longname' => 'Israel Standard Time',
        'dstlongname' => 'Israel Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Fri>=26',
        'summertimestarttime' => 0,
        'summertimeendmonth' => 10,
        'summertimeendday' => '5',
        'summertimeendtime' => -3600000, ],
    'Jamaica' => [
        'offset' => -18000000,
        'shortname' => 'EST',
        'dstshortname' => null,
        'longname' => 'Eastern Standard Time', ],
    'Japan' => [
        'offset' => 32400000,
        'shortname' => 'JST',
        'dstshortname' => null,
        'longname' => 'Japan Standard Time', ],
    'Kwajalein' => [
        'offset' => 43200000,
        'shortname' => 'MHT',
        'dstshortname' => null,
        'longname' => 'Marshall Islands Time', ],
    'Libya' => [
        'offset' => 7200000,
        'shortname' => 'EET',
        'dstshortname' => null,
        'longname' => 'Eastern European Time', ],
    'MET' => [
        'offset' => 3600000,
        'shortname' => 'MET',
        'dstshortname' => 'MEST',
        'longname' => 'Middle Europe Time',
        'dstlongname' => 'Middle Europe Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'MST' => [
        'offset' => -25200000,
        'shortname' => 'MST',
        'dstshortname' => null,
        'longname' => 'Mountain Standard Time',
        'dstlongname' => 'Mountain Daylight Time', ],
    'MST7MDT' => [
        'offset' => -25200000,
        'shortname' => 'MST',
        'dstshortname' => 'MDT',
        'longname' => 'Mountain Standard Time',
        'dstlongname' => 'Mountain Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 32400000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 28800000, ],
    'Mexico/BajaNorte' => [
        'offset' => -28800000,
        'shortname' => 'PST',
        'dstshortname' => 'PDT',
        'longname' => 'Pacific Standard Time',
        'dstlongname' => 'Pacific Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 4,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => 36000000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 32400000, ],
    'Mexico/BajaSur' => [
        'offset' => -25200000,
        'shortname' => 'MST',
        'dstshortname' => 'MDT',
        'longname' => 'Mountain Standard Time',
        'dstlongname' => 'Mountain Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 4,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => 32400000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 28800000, ],
    'Mexico/General' => [
        'offset' => -21600000,
        'shortname' => 'CST',
        'dstshortname' => 'CDT',
        'longname' => 'Central Standard Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 4,
        'summertimestartday' => 'Sun>=1',
        'summertimestarttime' => 28800000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 25200000, ],
    'Mideast/Riyadh87' => [
        'offset' => 11224000,
        'shortname' => '',
        'dstshortname' => null,
        'longname' => 'GMT+03:07', ],
    'Mideast/Riyadh88' => [
        'offset' => 11224000,
        'shortname' => '',
        'dstshortname' => null,
        'longname' => 'GMT+03:07', ],
    'Mideast/Riyadh89' => [
        'offset' => 11224000,
        'shortname' => '',
        'dstshortname' => null,
        'longname' => 'GMT+03:07', ],
    'NZ' => [
        'offset' => 43200000,
        'shortname' => 'NZST',
        'dstshortname' => 'NZDT',
        'longname' => 'New Zealand Standard Time',
        'dstlongname' => 'New Zealand Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 9,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => -36000000,
        'summertimeendmonth' => 4,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => -36000000, ],
    'NZ-CHAT' => [
        'offset' => 45900000,
        'shortname' => 'CHAST',
        'dstshortname' => 'CHADT',
        'longname' => 'Chatham Standard Time',
        'dstlongname' => 'Chatham Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 9,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => -36000000,
        'summertimeendmonth' => 4,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => -36000000, ],
    'Navajo' => [
        'offset' => -25200000,
        'shortname' => 'MST',
        'dstshortname' => 'MDT',
        'longname' => 'Mountain Standard Time',
        'dstlongname' => 'Mountain Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 32400000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 28800000, ],
    'PRC' => [
        'offset' => 28800000,
        'shortname' => 'CST',
        'dstshortname' => null,
        'longname' => 'China Standard Time', ],
    'PST8PDT' => [
        'offset' => -28800000,
        'shortname' => 'PST',
        'dstshortname' => 'PDT',
        'longname' => 'Pacific Standard Time',
        'dstlongname' => 'Pacific Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 36000000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 32400000, ],
    'Pacific/Apia' => [
        'offset' => -39600000,
        'shortname' => 'WST',
        'dstshortname' => null,
        'longname' => 'West Samoa Time', ],
    'Pacific/Auckland' => [
        'offset' => 43200000,
        'shortname' => 'NZST',
        'dstshortname' => 'NZDT',
        'longname' => 'New Zealand Standard Time',
        'dstlongname' => 'New Zealand Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 9,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => -36000000,
        'summertimeendmonth' => 4,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => -36000000, ],
    'Pacific/Chatham' => [
        'offset' => 45900000,
        'shortname' => 'CHAST',
        'dstshortname' => 'CHADT',
        'longname' => 'Chatham Standard Time',
        'dstlongname' => 'Chatham Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 9,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => -36000000,
        'summertimeendmonth' => 4,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => -36000000, ],
    'Pacific/Easter' => [
        'offset' => -21600000,
        'shortname' => 'EAST',
        'dstshortname' => 'EASST',
        'longname' => 'Easter Is. Time',
        'dstlongname' => 'Easter Is. Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 10,
        'summertimestartday' => 'Sun>=9',
        'summertimestarttime' => 14400000,
        'summertimeendmonth' => 3,
        'summertimeendday' => '30',
        'summertimeendtime' => 10800000, ],
    'Pacific/Efate' => [
        'offset' => 39600000,
        'shortname' => 'VUT',
        'dstshortname' => null,
        'longname' => 'Vanuatu Time', ],
    'Pacific/Enderbury' => [
        'offset' => 46800000,
        'shortname' => 'PHOT',
        'dstshortname' => null,
        'longname' => 'Phoenix Is. Time', ],
    'Pacific/Fakaofo' => [
        'offset' => -36000000,
        'shortname' => 'TKT',
        'dstshortname' => null,
        'longname' => 'Tokelau Time', ],
    'Pacific/Fiji' => [
        'offset' => 43200000,
        'shortname' => 'FJT',
        'dstshortname' => null,
        'longname' => 'Fiji Time', ],
    'Pacific/Funafuti' => [
        'offset' => 43200000,
        'shortname' => 'TVT',
        'dstshortname' => null,
        'longname' => 'Tuvalu Time', ],
    'Pacific/Galapagos' => [
        'offset' => -21600000,
        'shortname' => 'GALT',
        'dstshortname' => null,
        'longname' => 'Galapagos Time', ],
    'Pacific/Gambier' => [
        'offset' => -32400000,
        'shortname' => 'GAMT',
        'dstshortname' => null,
        'longname' => 'Gambier Time', ],
    'Pacific/Guadalcanal' => [
        'offset' => 39600000,
        'shortname' => 'SBT',
        'dstshortname' => null,
        'longname' => 'Solomon Is. Time', ],
    'Pacific/Guam' => [
        'offset' => 36000000,
        'shortname' => 'ChST',
        'dstshortname' => null,
        'longname' => 'Chamorro Standard Time', ],
    'Pacific/Honolulu' => [
        'offset' => -36000000,
        'shortname' => 'HST',
        'dstshortname' => null,
        'longname' => 'Hawaii Standard Time', ],
    'Pacific/Johnston' => [
        'offset' => -36000000,
        'shortname' => 'HST',
        'dstshortname' => null,
        'longname' => 'Hawaii Standard Time', ],
    'Pacific/Kiritimati' => [
        'offset' => 50400000,
        'shortname' => 'LINT',
        'dstshortname' => null,
        'longname' => 'Line Is. Time', ],
    'Pacific/Kosrae' => [
        'offset' => 39600000,
        'shortname' => 'KOST',
        'dstshortname' => null,
        'longname' => 'Kosrae Time', ],
    'Pacific/Kwajalein' => [
        'offset' => 43200000,
        'shortname' => 'MHT',
        'dstshortname' => null,
        'longname' => 'Marshall Islands Time', ],
    'Pacific/Majuro' => [
        'offset' => 43200000,
        'shortname' => 'MHT',
        'dstshortname' => null,
        'longname' => 'Marshall Islands Time', ],
    'Pacific/Marquesas' => [
        'offset' => -34200000,
        'shortname' => 'MART',
        'dstshortname' => null,
        'longname' => 'Marquesas Time', ],
    'Pacific/Midway' => [
        'offset' => -39600000,
        'shortname' => 'SST',
        'dstshortname' => null,
        'longname' => 'Samoa Standard Time', ],
    'Pacific/Nauru' => [
        'offset' => 43200000,
        'shortname' => 'NRT',
        'dstshortname' => null,
        'longname' => 'Nauru Time', ],
    'Pacific/Niue' => [
        'offset' => -39600000,
        'shortname' => 'NUT',
        'dstshortname' => null,
        'longname' => 'Niue Time', ],
    'Pacific/Norfolk' => [
        'offset' => 41400000,
        'shortname' => 'NFT',
        'dstshortname' => null,
        'longname' => 'Norfolk Time', ],
    'Pacific/Noumea' => [
        'offset' => 39600000,
        'shortname' => 'NCT',
        'dstshortname' => null,
        'longname' => 'New Caledonia Time', ],
    'Pacific/Pago_Pago' => [
        'offset' => -39600000,
        'shortname' => 'SST',
        'dstshortname' => null,
        'longname' => 'Samoa Standard Time', ],
    'Pacific/Palau' => [
        'offset' => 32400000,
        'shortname' => 'PWT',
        'dstshortname' => null,
        'longname' => 'Palau Time', ],
    'Pacific/Pitcairn' => [
        'offset' => -28800000,
        'shortname' => 'PST',
        'dstshortname' => null,
        'longname' => 'Pitcairn Standard Time', ],
    'Pacific/Ponape' => [
        'offset' => 39600000,
        'shortname' => 'PONT',
        'dstshortname' => null,
        'longname' => 'Ponape Time', ],
    'Pacific/Port_Moresby' => [
        'offset' => 36000000,
        'shortname' => 'PGT',
        'dstshortname' => null,
        'longname' => 'Papua New Guinea Time', ],
    'Pacific/Rarotonga' => [
        'offset' => -36000000,
        'shortname' => 'CKT',
        'dstshortname' => null,
        'longname' => 'Cook Is. Time', ],
    'Pacific/Saipan' => [
        'offset' => 36000000,
        'shortname' => 'ChST',
        'dstshortname' => null,
        'longname' => 'Chamorro Standard Time', ],
    'Pacific/Samoa' => [
        'offset' => -39600000,
        'shortname' => 'SST',
        'dstshortname' => null,
        'longname' => 'Samoa Standard Time', ],
    'Pacific/Tahiti' => [
        'offset' => -36000000,
        'shortname' => 'TAHT',
        'dstshortname' => null,
        'longname' => 'Tahiti Time', ],
    'Pacific/Tarawa' => [
        'offset' => 43200000,
        'shortname' => 'GILT',
        'dstshortname' => null,
        'longname' => 'Gilbert Is. Time', ],
    'Pacific/Tongatapu' => [
        'offset' => 46800000,
        'shortname' => 'TOT',
        'dstshortname' => null,
        'longname' => 'Tonga Time', ],
    'Pacific/Truk' => [
        'offset' => 36000000,
        'shortname' => 'TRUT',
        'dstshortname' => null,
        'longname' => 'Truk Time', ],
    'Pacific/Wake' => [
        'offset' => 43200000,
        'shortname' => 'WAKT',
        'dstshortname' => null,
        'longname' => 'Wake Time', ],
    'Pacific/Wallis' => [
        'offset' => 43200000,
        'shortname' => 'WFT',
        'dstshortname' => null,
        'longname' => 'Wallis & Futuna Time', ],
    'Pacific/Yap' => [
        'offset' => 36000000,
        'shortname' => 'TRUT',
        'dstshortname' => null,
        'longname' => 'Yap Time', ],
    'Poland' => [
        'offset' => 3600000,
        'shortname' => 'CET',
        'dstshortname' => 'CEST',
        'longname' => 'Central European Time',
        'dstlongname' => 'Central European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Portugal' => [
        'offset' => 0,
        'shortname' => 'WET',
        'dstshortname' => 'WEST',
        'longname' => 'Western European Time',
        'dstlongname' => 'Western European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'ROC' => [
        'offset' => 28800000,
        'shortname' => 'CST',
        'dstshortname' => null, ],
    'ROK' => [
        'offset' => 32400000,
        'shortname' => 'KST',
        'dstshortname' => null,
        'longname' => 'Korea Standard Time', ],
    'Singapore' => [
        'offset' => 28800000,
        'shortname' => 'SGT',
        'dstshortname' => null,
        'longname' => 'Singapore Time', ],
    'Turkey' => [
        'offset' => 7200000,
        'shortname' => 'EET',
        'dstshortname' => 'EEST',
        'longname' => 'Eastern European Time',
        'dstlongname' => 'Eastern European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'UCT' => [
        'offset' => 0,
        'shortname' => 'UCT',
        'dstshortname' => null,
        'longname' => 'Coordinated Universal Time', ],
    'US/Alaska' => [
        'offset' => -32400000,
        'shortname' => 'AKST',
        'dstshortname' => 'AKDT',
        'longname' => 'Alaska Standard Time',
        'dstlongname' => 'Alaska Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 39600000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 36000000, ],
    'US/Aleutian' => [
        'offset' => -36000000,
        'shortname' => 'HAST',
        'dstshortname' => 'HADT',
        'longname' => 'Hawaii-Aleutian Standard Time',
        'dstlongname' => 'Hawaii-Aleutian Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 43200000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 39600000, ],
    'US/Arizona' => [
        'offset' => -25200000,
        'shortname' => 'MST',
        'dstshortname' => null,
        'longname' => 'Mountain Standard Time', ],
    'US/Central' => [
        'offset' => -21600000,
        'shortname' => 'CST',
        'dstshortname' => 'CDT',
        'longname' => 'Central Standard Time',
        'dstlongname' => 'Central Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 28800000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 25200000, ],
    'US/East-Indiana' => [
        'offset' => -18000000,
        'shortname' => 'EST',
        'dstshortname' => 'EDT',
        'longname' => 'Eastern Standard Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 25200000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 21600000, ],
    'US/Eastern' => [
        'offset' => -18000000,
        'shortname' => 'EST',
        'dstshortname' => 'EDT',
        'longname' => 'Eastern Standard Time',
        'dstlongname' => 'Eastern Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 25200000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 21600000, ],
    'US/Hawaii' => [
        'offset' => -36000000,
        'shortname' => 'HST',
        'dstshortname' => null,
        'longname' => 'Hawaii Standard Time', ],
    'US/Indiana-Starke' => [
        'offset' => -21600000,
        'shortname' => 'CST',
        'dstshortname' => 'CDT',
        'longname' => 'Central Standard Time',
        'dstlongname' => 'Central Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 28800000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 25200000, ],
    'US/Michigan' => [
        'offset' => -18000000,
        'shortname' => 'EST',
        'dstshortname' => 'EDT',
        'longname' => 'Eastern Standard Time',
        'dstlongname' => 'Eastern Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 25200000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 21600000, ],
    'US/Mountain' => [
        'offset' => -25200000,
        'shortname' => 'MST',
        'dstshortname' => 'MDT',
        'longname' => 'Mountain Standard Time',
        'dstlongname' => 'Mountain Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 32400000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 28800000, ],
    'US/Pacific' => [
        'offset' => -28800000,
        'shortname' => 'PST',
        'dstshortname' => 'PDT',
        'longname' => 'Pacific Standard Time',
        'dstlongname' => 'Pacific Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 36000000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 32400000, ],
    'US/Pacific-New' => [
        'offset' => -28800000,
        'shortname' => 'PST',
        'dstshortname' => 'PDT',
        'longname' => 'Pacific Standard Time',
        'dstlongname' => 'Pacific Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'Sun>=8',
        'summertimestarttime' => 36000000,
        'summertimeendmonth' => 11,
        'summertimeendday' => 'Sun>=1',
        'summertimeendtime' => 32400000, ],
    'US/Samoa' => [
        'offset' => -39600000,
        'shortname' => 'SST',
        'dstshortname' => null,
        'longname' => 'Samoa Standard Time', ],
    'UTC' => [
        'offset' => 0,
        'shortname' => 'UTC',
        'dstshortname' => null,
        'longname' => 'Coordinated Universal Time', ],
    'Universal' => [
        'offset' => 0,
        'shortname' => 'UTC',
        'dstshortname' => null,
        'longname' => 'Coordinated Universal Time', ],
    'W-SU' => [
        'offset' => 10800000,
        'shortname' => 'MSK',
        'dstshortname' => 'MSD',
        'longname' => 'Moscow Standard Time',
        'dstlongname' => 'Moscow Daylight Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => -3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => -3600000, ],
    'WET' => [
        'offset' => 0,
        'shortname' => 'WET',
        'dstshortname' => 'WEST',
        'longname' => 'Western European Time',
        'dstlongname' => 'Western European Summer Time',
        'summertimeoffset' => 3600000,
        'summertimestartmonth' => 3,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 3600000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 3600000, ],
    'Zulu' => [
        'offset' => 0,
        'shortname' => 'UTC',
        'dstshortname' => null,
        'longname' => 'Coordinated Universal Time', ],
    //
    // Following time-zones are the long names for the time-zones above, thus N.B.
    // that the Summer-Time for each zone cannot really be reliable, because two
    // zones may share the same zone name, but differ in Summer-Time arrangements;
    // and also that the data cannot be maintained as easily and thus may also
    // be inaccurate or out-of-date
    //
    'ACT' => [
        'offset' => 34200000,
        'shortname' => 'CST',
        'longname' => 'Central Standard Time (Northern Territory)', ],
    'AET' => [
        'offset' => 36000000,
        'shortname' => 'EST',
        'dstshortname' => 'EST',
        'longname' => 'Eastern Standard Time (New South Wales)',
        'dstlongname' => 'Eastern Summer Time (New South Wales)', ],
    'AGT' => [
        'offset' => -10800000,
        'shortname' => 'ART',
        'longname' => 'Argentine Time', ],
    'ART' => [
        'offset' => 7200000,
        'shortname' => 'EET',
        'dstshortname' => 'EEST',
        'longname' => 'Eastern European Time',
        'dstlongname' => 'Eastern European Summer Time', ],
    'AST' => [
        'offset' => -32400000,
        'shortname' => 'AKST',
        'dstshortname' => 'AKDT',
        'longname' => 'Alaska Standard Time',
        'dstlongname' => 'Alaska Daylight Time', ],
    'Acre Time' => [
        'offset' => -18000000,
        'shortname' => 'ACT',
        'longname' => 'Acre Time', ],
    'Afghanistan Time' => [
        'offset' => 16200000,
        'shortname' => 'AFT',
        'longname' => 'Afghanistan Time', ],
    'Alaska Standard Time' => [
        'offset' => -32400000,
        'shortname' => 'AKST',
        'dstshortname' => 'AKDT',
        'longname' => 'Alaska Standard Time',
        'dstlongname' => 'Alaska Daylight Time', ],
    'Alma-Ata Time' => [
        'offset' => 21600000,
        'shortname' => 'ALMT',
        'dstshortname' => 'ALMST',
        'longname' => 'Alma-Ata Time',
        'dstlongname' => 'Alma-Ata Summer Time', ],
    'Amazon Standard Time' => [
        'offset' => -14400000,
        'shortname' => 'AMT',
        'longname' => 'Amazon Standard Time', ],
    'Anadyr Time' => [
        'offset' => 43200000,
        'shortname' => 'ANAT',
        'dstshortname' => 'ANAST',
        'longname' => 'Anadyr Time',
        'dstlongname' => 'Anadyr Summer Time', ],
    'Aqtau Time' => [
        'offset' => 14400000,
        'shortname' => 'AQTT',
        'dstshortname' => 'AQTST',
        'longname' => 'Aqtau Time',
        'dstlongname' => 'Aqtau Summer Time', ],
    'Aqtobe Time' => [
        'offset' => 18000000,
        'shortname' => 'AQTT',
        'dstshortname' => 'AQTST',
        'longname' => 'Aqtobe Time',
        'dstlongname' => 'Aqtobe Summer Time', ],
    'Arabia Standard Time' => [
        'offset' => 10800000,
        'shortname' => 'AST',
        'longname' => 'Arabia Standard Time', ],
    'Argentine Time' => [
        'offset' => -10800000,
        'shortname' => 'ART',
        'longname' => 'Argentine Time', ],
    'Armenia Time' => [
        'offset' => 14400000,
        'shortname' => 'AMT',
        'dstshortname' => 'AMST',
        'longname' => 'Armenia Time',
        'dstlongname' => 'Armenia Summer Time', ],
    'Atlantic Standard Time' => [
        'offset' => -14400000,
        'shortname' => 'AST',
        'dstshortname' => 'ADT',
        'longname' => 'Atlantic Standard Time',
        'dstlongname' => 'Atlantic Daylight Time', ],
    'Azerbaijan Time' => [
        'offset' => 14400000,
        'shortname' => 'AZT',
        'dstshortname' => 'AZST',
        'longname' => 'Azerbaijan Time',
        'dstlongname' => 'Azerbaijan Summer Time', ],
    'Azores Time' => [
        'offset' => -3600000,
        'shortname' => 'AZOT',
        'dstshortname' => 'AZOST',
        'longname' => 'Azores Time',
        'dstlongname' => 'Azores Summer Time', ],
    'BDT' => [
        'offset' => 21600000,
        'shortname' => 'BDT',
        'longname' => 'Bangladesh Time', ],
    'BET' => [
        'offset' => -10800000,
        'shortname' => 'BRT',
        'dstshortname' => 'BRST',
        'longname' => 'Brazil Time',
        'dstlongname' => 'Brazil Summer Time', ],
    'Bangladesh Time' => [
        'offset' => 21600000,
        'shortname' => 'BDT',
        'longname' => 'Bangladesh Time', ],
    'Bhutan Time' => [
        'offset' => 21600000,
        'shortname' => 'BTT',
        'longname' => 'Bhutan Time', ],
    'Bolivia Time' => [
        'offset' => -14400000,
        'shortname' => 'BOT',
        'longname' => 'Bolivia Time', ],
    'Brazil Time' => [
        'offset' => -10800000,
        'shortname' => 'BRT',
        'dstshortname' => 'BRST',
        'longname' => 'Brazil Time',
        'dstlongname' => 'Brazil Summer Time', ],
    'Brunei Time' => [
        'offset' => 28800000,
        'shortname' => 'BNT',
        'longname' => 'Brunei Time', ],
    'CAT' => [
        'offset' => 7200000,
        'shortname' => 'CAT',
        'longname' => 'Central African Time', ],
    'CEST' => [
        'offset' => 3600000,
        'shortname' => 'CET',
        'dstshortname' => 'CEST',
        'longname' => 'Central European Time',
        'dstlongname' => 'Central European Summer Time', ],
    'CNT' => [
        'offset' => -12600000,
        'shortname' => 'NST',
        'dstshortname' => 'NDT',
        'longname' => 'Newfoundland Standard Time',
        'dstlongname' => 'Newfoundland Daylight Time', ],
    'CST' => [
        'offset' => -21600000,
        'shortname' => 'CST',
        'dstshortname' => 'CDT',
        'longname' => 'Central Standard Time',
        'dstlongname' => 'Central Daylight Time', ],
    'CTT' => [
        'offset' => 28800000,
        'shortname' => 'CST',
        'longname' => 'China Standard Time', ],
    'Cape Verde Time' => [
        'offset' => -3600000,
        'shortname' => 'CVT',
        'longname' => 'Cape Verde Time', ],
    'Central African Time' => [
        'offset' => 7200000,
        'shortname' => 'CAT',
        'longname' => 'Central African Time', ],
    'Central European Time' => [
        'offset' => 3600000,
        'shortname' => 'CET',
        'dstshortname' => 'CEST',
        'longname' => 'Central European Time',
        'dstlongname' => 'Central European Summer Time', ],
    'Central Indonesia Time' => [
        'offset' => 28800000,
        'shortname' => 'CIT',
        'longname' => 'Central Indonesia Time', ],
    'Central Standard Time' => [
        'offset' => -18000000,
        'shortname' => 'CST',
        'dstshortname' => 'CDT',
        'longname' => 'Central Standard Time',
        'dstlongname' => 'Central Daylight Time', ],
    'Central Standard Time (Northern Territory)' => [
        'offset' => 34200000,
        'shortname' => 'CST',
        'longname' => 'Central Standard Time (Northern Territory)', ],
    'Central Standard Time (South Australia)' => [
        'offset' => 34200000,
        'shortname' => 'CST',
        'dstshortname' => 'CST',
        'longname' => 'Central Standard Time (South Australia)',
        'dstlongname' => 'Central Summer Time (South Australia)', ],
    'Central Standard Time (South Australia/New South Wales)' => [
        'offset' => 34200000,
        'shortname' => 'CST',
        'dstshortname' => 'CST',
        'longname' => 'Central Standard Time (South Australia/New South Wales)',
        'dstlongname' => 'Central Summer Time (South Australia/New South Wales)', ],
    'Chamorro Standard Time' => [
        'offset' => 36000000,
        'shortname' => 'ChST',
        'longname' => 'Chamorro Standard Time', ],
    'Chatham Standard Time' => [
        'offset' => 45900000,
        'shortname' => 'CHAST',
        'dstshortname' => 'CHADT',
        'longname' => 'Chatham Standard Time',
        'dstlongname' => 'Chatham Daylight Time', ],
    'Chile Time' => [
        'offset' => -14400000,
        'shortname' => 'CLT',
        'dstshortname' => 'CLST',
        'longname' => 'Chile Time',
        'dstlongname' => 'Chile Summer Time', ],
    'China Standard Time' => [
        'offset' => 28800000,
        'shortname' => 'CST',
        'longname' => 'China Standard Time', ],
    'Choibalsan Time' => [
        'offset' => 32400000,
        'shortname' => 'CHOT',
        'longname' => 'Choibalsan Time', ],
    'Christmas Island Time' => [
        'offset' => 25200000,
        'shortname' => 'CXT',
        'longname' => 'Christmas Island Time', ],
    'Cocos Islands Time' => [
        'offset' => 23400000,
        'shortname' => 'CCT',
        'longname' => 'Cocos Islands Time', ],
    'Colombia Time' => [
        'offset' => -18000000,
        'shortname' => 'COT',
        'longname' => 'Colombia Time', ],
    'Cook Is. Time' => [
        'offset' => -36000000,
        'shortname' => 'CKT',
        'longname' => 'Cook Is. Time', ],
    'Coordinated Universal Time' => [
        'offset' => 0,
        'shortname' => 'UTC',
        'longname' => 'Coordinated Universal Time', ],
    'Davis Time' => [
        'offset' => 25200000,
        'shortname' => 'DAVT',
        'longname' => 'Davis Time', ],
    'Dumont-d\'Urville Time' => [
        'offset' => 36000000,
        'shortname' => 'DDUT',
        'longname' => 'Dumont-d\'Urville Time', ],
    'EAT' => [
        'offset' => 10800000,
        'shortname' => 'EAT',
        'longname' => 'Eastern African Time', ],
    'ECT' => [
        'offset' => 3600000,
        'shortname' => 'CET',
        'dstshortname' => 'CEST',
        'longname' => 'Central European Time',
        'dstlongname' => 'Central European Summer Time', ],
    'East Indonesia Time' => [
        'offset' => 32400000,
        'shortname' => 'EIT',
        'longname' => 'East Indonesia Time', ],
    'East Timor Time' => [
        'offset' => 32400000,
        'shortname' => 'TPT',
        'longname' => 'East Timor Time', ],
    'Easter Is. Time' => [
        'offset' => -21600000,
        'shortname' => 'EAST',
        'dstshortname' => 'EASST',
        'longname' => 'Easter Is. Time',
        'dstlongname' => 'Easter Is. Summer Time', ],
    'Eastern African Time' => [
        'offset' => 10800000,
        'shortname' => 'EAT',
        'longname' => 'Eastern African Time', ],
    'Eastern European Time' => [
        'offset' => 7200000,
        'shortname' => 'EET',
        'dstshortname' => 'EEST',
        'longname' => 'Eastern European Time',
        'dstlongname' => 'Eastern European Summer Time', ],
    'Eastern Greenland Time' => [
        'offset' => 3600000,
        'shortname' => 'EGT',
        'dstshortname' => 'EGST',
        'longname' => 'Eastern Greenland Time',
        'dstlongname' => 'Eastern Greenland Summer Time', ],
    'Eastern Standard Time' => [
        'offset' => -18000000,
        'shortname' => 'EST',
        'dstshortname' => 'EDT',
        'longname' => 'Eastern Standard Time',
        'dstlongname' => 'Eastern Daylight Time', ],
    'Eastern Standard Time (New South Wales)' => [
        'offset' => 36000000,
        'shortname' => 'EST',
        'dstshortname' => 'EST',
        'longname' => 'Eastern Standard Time (New South Wales)',
        'dstlongname' => 'Eastern Summer Time (New South Wales)', ],
    'Eastern Standard Time (Queensland)' => [
        'offset' => 36000000,
        'shortname' => 'EST',
        'longname' => 'Eastern Standard Time (Queensland)', ],
    'Eastern Standard Time (Tasmania)' => [
        'offset' => 36000000,
        'shortname' => 'EST',
        'dstshortname' => 'EST',
        'longname' => 'Eastern Standard Time (Tasmania)',
        'dstlongname' => 'Eastern Summer Time (Tasmania)', ],
    'Eastern Standard Time (Victoria)' => [
        'offset' => 36000000,
        'shortname' => 'EST',
        'dstshortname' => 'EST',
        'longname' => 'Eastern Standard Time (Victoria)',
        'dstlongname' => 'Eastern Summer Time (Victoria)', ],
    'Ecuador Time' => [
        'offset' => -18000000,
        'shortname' => 'ECT',
        'longname' => 'Ecuador Time', ],
    'Falkland Is. Time' => [
        'offset' => -14400000,
        'shortname' => 'FKT',
        'dstshortname' => 'FKST',
        'longname' => 'Falkland Is. Time',
        'dstlongname' => 'Falkland Is. Summer Time', ],
    'Fernando de Noronha Time' => [
        'offset' => -7200000,
        'shortname' => 'FNT',
        'longname' => 'Fernando de Noronha Time', ],
    'Fiji Time' => [
        'offset' => 43200000,
        'shortname' => 'FJT',
        'longname' => 'Fiji Time', ],
    'French Guiana Time' => [
        'offset' => -10800000,
        'shortname' => 'GFT',
        'longname' => 'French Guiana Time', ],
    'French Southern & Antarctic Lands Time' => [
        'offset' => 18000000,
        'shortname' => 'TFT',
        'longname' => 'French Southern & Antarctic Lands Time', ],
    'GMT+03:07' => [
        'offset' => 11224000,
        'shortname' => 'GMT+03:07',
        'longname' => 'GMT+03:07', ],
    'Galapagos Time' => [
        'offset' => -21600000,
        'shortname' => 'GALT',
        'longname' => 'Galapagos Time', ],
    'Gambier Time' => [
        'offset' => -32400000,
        'shortname' => 'GAMT',
        'longname' => 'Gambier Time', ],
    'Georgia Time' => [
        'offset' => 14400000,
        'shortname' => 'GET',
        'dstshortname' => 'GEST',
        'longname' => 'Georgia Time',
        'dstlongname' => 'Georgia Summer Time', ],
    'Gilbert Is. Time' => [
        'offset' => 43200000,
        'shortname' => 'GILT',
        'longname' => 'Gilbert Is. Time', ],
    'Greenwich Mean Time' => [
        'offset' => 0,
        'shortname' => 'GMT',
        'longname' => 'Greenwich Mean Time', ],
    'Gulf Standard Time' => [
        'offset' => 14400000,
        'shortname' => 'GST',
        'longname' => 'Gulf Standard Time', ],
    'Guyana Time' => [
        'offset' => -14400000,
        'shortname' => 'GYT',
        'longname' => 'Guyana Time', ],
    'Hawaii Standard Time' => [
        'offset' => -36000000,
        'shortname' => 'HST',
        'longname' => 'Hawaii Standard Time', ],
    'Hawaii-Aleutian Standard Time' => [
        'offset' => -36000000,
        'shortname' => 'HAST',
        'dstshortname' => 'HADT',
        'longname' => 'Hawaii-Aleutian Standard Time',
        'dstlongname' => 'Hawaii-Aleutian Daylight Time', ],
    'Hong Kong Time' => [
        'offset' => 28800000,
        'shortname' => 'HKT',
        'longname' => 'Hong Kong Time', ],
    'Hovd Time' => [
        'offset' => 25200000,
        'shortname' => 'HOVT',
        'longname' => 'Hovd Time', ],
    'IET' => [
        'offset' => -18000000,
        'shortname' => 'EST',
        'longname' => 'Eastern Standard Time', ],
    'IST' => [
        'offset' => 19800000,
        'shortname' => 'IST',
        'longname' => 'India Standard Time', ],
    'India Standard Time' => [
        'offset' => 19800000,
        'shortname' => 'IST',
        'longname' => 'India Standard Time', ],
    'Indian Ocean Territory Time' => [
        'offset' => 21600000,
        'shortname' => 'IOT',
        'longname' => 'Indian Ocean Territory Time', ],
    'Indochina Time' => [
        'offset' => 25200000,
        'shortname' => 'ICT',
        'longname' => 'Indochina Time', ],
    'Iran Time' => [
        'offset' => 12600000,
        'shortname' => 'IRT',
        'dstshortname' => 'IRST',
        'longname' => 'Iran Time',
        'dstlongname' => 'Iran Summer Time', ],
    'Irkutsk Time' => [
        'offset' => 28800000,
        'shortname' => 'IRKT',
        'dstshortname' => 'IRKST',
        'longname' => 'Irkutsk Time',
        'dstlongname' => 'Irkutsk Summer Time', ],
    'Israel Standard Time' => [
        'offset' => 7200000,
        'shortname' => 'IST',
        'dstshortname' => 'IDT',
        'longname' => 'Israel Standard Time',
        'dstlongname' => 'Israel Daylight Time', ],
    'JST' => [
        'offset' => 32400000,
        'shortname' => 'JST',
        'longname' => 'Japan Standard Time', ],
    'Japan Standard Time' => [
        'offset' => 32400000,
        'shortname' => 'JST',
        'longname' => 'Japan Standard Time', ],
    'Kirgizstan Time' => [
        'offset' => 18000000,
        'shortname' => 'KGT',
        'dstshortname' => 'KGST',
        'longname' => 'Kirgizstan Time',
        'dstlongname' => 'Kirgizstan Summer Time', ],
    'Korea Standard Time' => [
        'offset' => 32400000,
        'shortname' => 'KST',
        'longname' => 'Korea Standard Time', ],
    'Kosrae Time' => [
        'offset' => 39600000,
        'shortname' => 'KOST',
        'longname' => 'Kosrae Time', ],
    'Krasnoyarsk Time' => [
        'offset' => 25200000,
        'shortname' => 'KRAT',
        'dstshortname' => 'KRAST',
        'longname' => 'Krasnoyarsk Time',
        'dstlongname' => 'Krasnoyarsk Summer Time', ],
    'Line Is. Time' => [
        'offset' => 50400000,
        'shortname' => 'LINT',
        'longname' => 'Line Is. Time', ],
    'Load Howe Standard Time' => [
        'offset' => 37800000,
        'shortname' => 'LHST',
        'dstshortname' => 'LHST',
        'longname' => 'Load Howe Standard Time',
        'dstlongname' => 'Load Howe Summer Time', ],
    'MIT' => [
        'offset' => -39600000,
        'shortname' => 'WST',
        'longname' => 'West Samoa Time', ],
    'Magadan Time' => [
        'offset' => 39600000,
        'shortname' => 'MAGT',
        'dstshortname' => 'MAGST',
        'longname' => 'Magadan Time',
        'dstlongname' => 'Magadan Summer Time', ],
    'Malaysia Time' => [
        'offset' => 28800000,
        'shortname' => 'MYT',
        'longname' => 'Malaysia Time', ],
    'Maldives Time' => [
        'offset' => 18000000,
        'shortname' => 'MVT',
        'longname' => 'Maldives Time', ],
    'Marquesas Time' => [
        'offset' => -34200000,
        'shortname' => 'MART',
        'longname' => 'Marquesas Time', ],
    'Marshall Islands Time' => [
        'offset' => 43200000,
        'shortname' => 'MHT',
        'longname' => 'Marshall Islands Time', ],
    'Mauritius Time' => [
        'offset' => 14400000,
        'shortname' => 'MUT',
        'longname' => 'Mauritius Time', ],
    'Mawson Time' => [
        'offset' => 21600000,
        'shortname' => 'MAWT',
        'longname' => 'Mawson Time', ],
    'Middle Europe Time' => [
        'offset' => 3600000,
        'shortname' => 'MET',
        'dstshortname' => 'MEST',
        'longname' => 'Middle Europe Time',
        'dstlongname' => 'Middle Europe Summer Time', ],
    'Moscow Standard Time' => [
        'offset' => 10800000,
        'shortname' => 'MSK',
        'dstshortname' => 'MSD',
        'longname' => 'Moscow Standard Time',
        'dstlongname' => 'Moscow Daylight Time', ],
    'Mountain Standard Time' => [
        'offset' => -25200000,
        'shortname' => 'MST',
        'dstshortname' => 'MDT',
        'longname' => 'Mountain Standard Time',
        'dstlongname' => 'Mountain Daylight Time', ],
    'Myanmar Time' => [
        'offset' => 23400000,
        'shortname' => 'MMT',
        'longname' => 'Myanmar Time', ],
    'NET' => [
        'offset' => 14400000,
        'shortname' => 'AMT',
        'dstshortname' => 'AMST',
        'longname' => 'Armenia Time',
        'dstlongname' => 'Armenia Summer Time', ],
    'NST' => [
        'offset' => 43200000,
        'shortname' => 'NZST',
        'dstshortname' => 'NZDT',
        'longname' => 'New Zealand Standard Time',
        'dstlongname' => 'New Zealand Daylight Time', ],
    'Nauru Time' => [
        'offset' => 43200000,
        'shortname' => 'NRT',
        'longname' => 'Nauru Time', ],
    'Nepal Time' => [
        'offset' => 20700000,
        'shortname' => 'NPT',
        'longname' => 'Nepal Time', ],
    'New Caledonia Time' => [
        'offset' => 39600000,
        'shortname' => 'NCT',
        'longname' => 'New Caledonia Time', ],
    'New Zealand Standard Time' => [
        'offset' => 43200000,
        'shortname' => 'NZST',
        'dstshortname' => 'NZDT',
        'longname' => 'New Zealand Standard Time',
        'dstlongname' => 'New Zealand Daylight Time', ],
    'Newfoundland Standard Time' => [
        'offset' => -12600000,
        'shortname' => 'NST',
        'dstshortname' => 'NDT',
        'longname' => 'Newfoundland Standard Time',
        'dstlongname' => 'Newfoundland Daylight Time', ],
    'Niue Time' => [
        'offset' => -39600000,
        'shortname' => 'NUT',
        'longname' => 'Niue Time', ],
    'Norfolk Time' => [
        'offset' => 41400000,
        'shortname' => 'NFT',
        'longname' => 'Norfolk Time', ],
    'Novosibirsk Time' => [
        'offset' => 21600000,
        'shortname' => 'NOVT',
        'dstshortname' => 'NOVST',
        'longname' => 'Novosibirsk Time',
        'dstlongname' => 'Novosibirsk Summer Time', ],
    'Omsk Time' => [
        'offset' => 21600000,
        'shortname' => 'OMST',
        'dstshortname' => 'OMSST',
        'longname' => 'Omsk Time',
        'dstlongname' => 'Omsk Summer Time', ],
    'PLT' => [
        'offset' => 18000000,
        'shortname' => 'PKT',
        'longname' => 'Pakistan Time', ],
    'PNT' => [
        'offset' => -25200000,
        'shortname' => 'MST',
        'longname' => 'Mountain Standard Time', ],
    'PRT' => [
        'offset' => -14400000,
        'shortname' => 'AST',
        'longname' => 'Atlantic Standard Time', ],
    'PST' => [
        'offset' => -28800000,
        'shortname' => 'PST',
        'dstshortname' => 'PDT',
        'longname' => 'Pacific Standard Time',
        'dstlongname' => 'Pacific Daylight Time', ],
    'Pacific Standard Time' => [
        'offset' => -28800000,
        'shortname' => 'PST',
        'dstshortname' => 'PDT',
        'longname' => 'Pacific Standard Time',
        'dstlongname' => 'Pacific Daylight Time', ],
    'Pakistan Time' => [
        'offset' => 18000000,
        'shortname' => 'PKT',
        'longname' => 'Pakistan Time', ],
    'Palau Time' => [
        'offset' => 32400000,
        'shortname' => 'PWT',
        'longname' => 'Palau Time', ],
    'Papua New Guinea Time' => [
        'offset' => 36000000,
        'shortname' => 'PGT',
        'longname' => 'Papua New Guinea Time', ],
    'Paraguay Time' => [
        'offset' => -14400000,
        'shortname' => 'PYT',
        'dstshortname' => 'PYST',
        'longname' => 'Paraguay Time',
        'dstlongname' => 'Paraguay Summer Time', ],
    'Peru Time' => [
        'offset' => -18000000,
        'shortname' => 'PET',
        'longname' => 'Peru Time', ],
    'Petropavlovsk-Kamchatski Time' => [
        'offset' => 43200000,
        'shortname' => 'PETT',
        'dstshortname' => 'PETST',
        'longname' => 'Petropavlovsk-Kamchatski Time',
        'dstlongname' => 'Petropavlovsk-Kamchatski Summer Time', ],
    'Philippines Time' => [
        'offset' => 28800000,
        'shortname' => 'PHT',
        'longname' => 'Philippines Time', ],
    'Phoenix Is. Time' => [
        'offset' => 46800000,
        'shortname' => 'PHOT',
        'longname' => 'Phoenix Is. Time', ],
    'Pierre & Miquelon Standard Time' => [
        'offset' => -10800000,
        'shortname' => 'PMST',
        'dstshortname' => 'PMDT',
        'longname' => 'Pierre & Miquelon Standard Time',
        'dstlongname' => 'Pierre & Miquelon Daylight Time', ],
    'Pitcairn Standard Time' => [
        'offset' => -28800000,
        'shortname' => 'PST',
        'longname' => 'Pitcairn Standard Time', ],
    'Ponape Time' => [
        'offset' => 39600000,
        'shortname' => 'PONT',
        'longname' => 'Ponape Time', ],
    'Reunion Time' => [
        'offset' => 14400000,
        'shortname' => 'RET',
        'longname' => 'Reunion Time', ],
    'SST' => [
        'offset' => 39600000,
        'shortname' => 'SBT',
        'longname' => 'Solomon Is. Time', ],
    'Sakhalin Time' => [
        'offset' => 36000000,
        'shortname' => 'SAKT',
        'dstshortname' => 'SAKST',
        'longname' => 'Sakhalin Time',
        'dstlongname' => 'Sakhalin Summer Time', ],
    'Samara Time' => [
        'offset' => 14400000,
        'shortname' => 'SAMT',
        'dstshortname' => 'SAMST',
        'longname' => 'Samara Time',
        'dstlongname' => 'Samara Summer Time', ],
    'Samoa Standard Time' => [
        'offset' => -39600000,
        'shortname' => 'SST',
        'longname' => 'Samoa Standard Time', ],
    'Seychelles Time' => [
        'offset' => 14400000,
        'shortname' => 'SCT',
        'longname' => 'Seychelles Time', ],
    'Singapore Time' => [
        'offset' => 28800000,
        'shortname' => 'SGT',
        'longname' => 'Singapore Time', ],
    'Solomon Is. Time' => [
        'offset' => 39600000,
        'shortname' => 'SBT',
        'longname' => 'Solomon Is. Time', ],
    'South Africa Standard Time' => [
        'offset' => 7200000,
        'shortname' => 'SAST',
        'longname' => 'South Africa Standard Time', ],
    'South Georgia Standard Time' => [
        'offset' => -7200000,
        'shortname' => 'GST',
        'longname' => 'South Georgia Standard Time', ],
    'Sri Lanka Time' => [
        'offset' => 21600000,
        'shortname' => 'LKT',
        'longname' => 'Sri Lanka Time', ],
    'Suriname Time' => [
        'offset' => -10800000,
        'shortname' => 'SRT',
        'longname' => 'Suriname Time', ],
    'Syowa Time' => [
        'offset' => 10800000,
        'shortname' => 'SYOT',
        'longname' => 'Syowa Time', ],
    'SystemV/AST4' => [
        'offset' => -14400000,
        'shortname' => 'AST',
        'dstshortname' => '',
        'longname' => 'Atlantic Standard Time', ],
    'SystemV/AST4ADT' => [
        'offset' => -14400000,
        'shortname' => 'AST',
        'dstshortname' => 'ADT',
        'longname' => 'Atlantic Standard Time',
        'dstlongname' => 'Atlantic Daylight Time',
        'summertimeoffset' => 3600000000,
        'summertimestartmonth' => 4,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 21600000000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 18000000000, ],
    'SystemV/CST6' => [
        'offset' => -21600000,
        'shortname' => 'CST',
        'dstshortname' => '',
        'longname' => 'Central Standard Time', ],
    'SystemV/CST6CDT' => [
        'offset' => -21600000,
        'shortname' => 'CST',
        'dstshortname' => 'CDT',
        'longname' => 'Central Standard Time',
        'dstlongname' => 'Central Daylight Time',
        'summertimeoffset' => 3600000000,
        'summertimestartmonth' => 4,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 28800000000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 25200000000, ],
    'SystemV/EST5' => [
        'offset' => -18000000,
        'shortname' => 'EST',
        'dstshortname' => '',
        'longname' => 'Eastern Standard Time', ],
    'SystemV/EST5EDT' => [
        'offset' => -18000000,
        'shortname' => 'EST',
        'dstshortname' => 'EDT',
        'longname' => 'Eastern Standard Time',
        'dstlongname' => 'Eastern Daylight Time',
        'summertimeoffset' => 3600000000,
        'summertimestartmonth' => 4,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 25200000000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 21600000000, ],
    'SystemV/HST10' => [
        'offset' => -36000000,
        'shortname' => 'HST',
        'dstshortname' => '',
        'longname' => 'Hawaii Standard Time', ],
    'SystemV/MST7' => [
        'offset' => -25200000,
        'shortname' => 'MST',
        'dstshortname' => '',
        'longname' => 'Mountain Standard Time', ],
    'SystemV/MST7MDT' => [
        'offset' => -25200000,
        'shortname' => 'MST',
        'dstshortname' => 'MDT',
        'longname' => 'Mountain Standard Time',
        'dstlongname' => 'Mountain Daylight Time',
        'summertimeoffset' => 3600000000,
        'summertimestartmonth' => 4,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 32400000000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 28800000000, ],
    'SystemV/PST8' => [
        'offset' => -28800000,
        'shortname' => 'PST',
        'dstshortname' => '',
        'longname' => 'Pitcairn Standard Time', ],
    'SystemV/PST8PDT' => [
        'offset' => -28800000,
        'shortname' => 'PST',
        'dstshortname' => 'PDT',
        'longname' => 'Pacific Standard Time',
        'dstlongname' => 'Pacific Daylight Time',
        'summertimeoffset' => 3600000000,
        'summertimestartmonth' => 4,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 36000000000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 32400000000, ],
    'SystemV/YST9' => [
        'offset' => -32400000,
        'shortname' => 'YST',
        'dstshortname' => '',
        'longname' => 'Gambier Time', ],
    'SystemV/YST9YDT' => [
        'offset' => -32400000,
        'shortname' => 'YST',
        'dstshortname' => 'YDT',
        'longname' => 'Alaska Standard Time',
        'dstlongname' => 'Alaska Daylight Time',
        'summertimeoffset' => 3600000000,
        'summertimestartmonth' => 4,
        'summertimestartday' => 'lastSun',
        'summertimestarttime' => 39600000000,
        'summertimeendmonth' => 10,
        'summertimeendday' => 'lastSun',
        'summertimeendtime' => 36000000000, ],
    'Tahiti Time' => [
        'offset' => -36000000,
        'shortname' => 'TAHT',
        'longname' => 'Tahiti Time', ],
    'Tajikistan Time' => [
        'offset' => 18000000,
        'shortname' => 'TJT',
        'longname' => 'Tajikistan Time', ],
    'Tokelau Time' => [
        'offset' => -36000000,
        'shortname' => 'TKT',
        'longname' => 'Tokelau Time', ],
    'Tonga Time' => [
        'offset' => 46800000,
        'shortname' => 'TOT',
        'longname' => 'Tonga Time', ],
    'Truk Time' => [
        'offset' => 36000000,
        'shortname' => 'TRUT',
        'longname' => 'Truk Time', ],
    'Turkmenistan Time' => [
        'offset' => 18000000,
        'shortname' => 'TMT',
        'longname' => 'Turkmenistan Time', ],
    'Tuvalu Time' => [
        'offset' => 43200000,
        'shortname' => 'TVT',
        'longname' => 'Tuvalu Time', ],
    'Ulaanbaatar Time' => [
        'offset' => 28800000,
        'shortname' => 'ULAT',
        'longname' => 'Ulaanbaatar Time', ],
    'Uruguay Time' => [
        'offset' => -10800000,
        'shortname' => 'UYT',
        'longname' => 'Uruguay Time', ],
    'Uzbekistan Time' => [
        'offset' => 18000000,
        'shortname' => 'UZT',
        'longname' => 'Uzbekistan Time', ],
    'VST' => [
        'offset' => 25200000,
        'shortname' => 'ICT',
        'longname' => 'Indochina Time', ],
    'Vanuatu Time' => [
        'offset' => 39600000,
        'shortname' => 'VUT',
        'longname' => 'Vanuatu Time', ],
    'Venezuela Time' => [
        'offset' => -14400000,
        'shortname' => 'VET',
        'longname' => 'Venezuela Time', ],
    'Vladivostok Time' => [
        'offset' => 36000000,
        'shortname' => 'VLAT',
        'dstshortname' => 'VLAST',
        'longname' => 'Vladivostok Time',
        'dstlongname' => 'Vladivostok Summer Time', ],
    'Vostok time' => [
        'offset' => 21600000,
        'shortname' => 'VOST',
        'longname' => 'Vostok time', ],
    'Wake Time' => [
        'offset' => 43200000,
        'shortname' => 'WAKT',
        'longname' => 'Wake Time', ],
    'Wallis & Futuna Time' => [
        'offset' => 43200000,
        'shortname' => 'WFT',
        'longname' => 'Wallis & Futuna Time', ],
    'West Indonesia Time' => [
        'offset' => 25200000,
        'shortname' => 'WIT',
        'longname' => 'West Indonesia Time', ],
    'West Samoa Time' => [
        'offset' => -39600000,
        'shortname' => 'WST',
        'longname' => 'West Samoa Time', ],
    'Western African Time' => [
        'offset' => 3600000,
        'shortname' => 'WAT',
        'dstshortname' => 'WAST',
        'longname' => 'Western African Time',
        'dstlongname' => 'Western African Summer Time', ],
    'Western European Time' => [
        'offset' => 0,
        'shortname' => 'WET',
        'dstshortname' => 'WEST',
        'longname' => 'Western European Time',
        'dstlongname' => 'Western European Summer Time', ],
    'Western Greenland Time' => [
        'offset' => -10800000,
        'shortname' => 'WGT',
        'dstshortname' => 'WGST',
        'longname' => 'Western Greenland Time',
        'dstlongname' => 'Western Greenland Summer Time', ],
    'Western Standard Time (Australia)' => [
        'offset' => 28800000,
        'shortname' => 'WST',
        'longname' => 'Western Standard Time (Australia)', ],
    'Yakutsk Time' => [
        'offset' => 32400000,
        'shortname' => 'YAKT',
        'dstshortname' => 'YAKST',
        'longname' => 'Yakutsk Time',
        'dstlongname' => 'Yaktsk Summer Time', ],
    'Yap Time' => [
        'offset' => 36000000,
        'shortname' => 'YAPT',
        'longname' => 'Yap Time', ],
    'Yekaterinburg Time' => [
        'offset' => 18000000,
        'shortname' => 'YEKT',
        'dstshortname' => 'YEKST',
        'longname' => 'Yekaterinburg Time',
        'dstlongname' => 'Yekaterinburg Summer Time', ],
];

/**
 * Initialize default timezone
 *
 * First try php.ini directive, then the value returned by date("e"), then
 * _DATE_TIMEZONE_DEFAULT global, then PHP_TZ environment variable, then TZ
 * environment variable.
 */
if (isset($GLOBALS['_DATE_TIMEZONE_DEFAULT'])
   && Date_TimeZone::isValidID($GLOBALS['_DATE_TIMEZONE_DEFAULT'])) {
    Date_TimeZone::setDefault($GLOBALS['_DATE_TIMEZONE_DEFAULT']);
} elseif (function_exists('version_compare') &&
           version_compare(phpversion(), "5.1.0", ">=") &&
           (
               Date_TimeZone::isValidID($ps_id = date_default_timezone_get()) ||
            Date_TimeZone::isValidID($ps_id = date("e"))
           )
           ) {
    Date_TimeZone::setDefault($ps_id);
} elseif (getenv('PHP_TZ') && Date_TimeZone::isValidID(getenv('PHP_TZ'))) {
    Date_TimeZone::setDefault(getenv('PHP_TZ'));
} elseif (getenv('TZ') && Date_TimeZone::isValidID(getenv('TZ'))) {
    Date_TimeZone::setDefault(getenv('TZ'));
} elseif (Date_TimeZone::isValidID(date('T'))) {
    Date_TimeZone::setDefault(date('T'));
} else {
    Date_TimeZone::setDefault('UTC');
}

/*
 * Local variables:
 * mode: php
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */
