<?php
/**
 * Surveys table definitions function
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Surveys
 * @author Surveys module development team
 */
/*
 * Short Description [REQUIRED one line description]
 *
 * Long Description [OPTIONAL one or more lines]
 *
 * @author     Jason Judge <jason.judge@academe.co.uk>
 * @author     Another Author <another@example.com>          [REQURIED]
 * @param string $arg1  the string used                      [OPTIONAL A REQURIED]
 * @param int    $arg2  an integer and use description
 *                      Identing long comments               [OPTIONAL A REQURIED]
 *
 * @return int  type and name returned                       [OPTIONAL A REQURIED]
 *
 * @throws      exceptionclass  [description]                [OPTIONAL A REQURIED]
 *
 * @access      public                                       [OPTIONAL A REQURIED]
 * @static                                                   [OPTIONAL]
 * @link       link to a reference                           [OPTIONAL]
 * @see        anothersample(), someotherlinke [reference to other function, class] [OPTIONAL]
 * @since      [Date of first inclusion long date format ]   [REQURIED]
 * @deprecated Deprecated [release version here]             [AS REQUIRED]
 */
/*
 * Get the language suffix used to append to DD columns
 * when utilizing alternative languages.
 */

function surveys_userapi_getlanguagesuffix() {
    // Get the current locale.
    $locale = xarLocaleGetInfo(xarMLSGetCurrentLocale());
    $language = $locale['lang'];
    $country = $locale['country'];

    if (!strcmp($language, 'en')) {
        $suffix ='_'.$language;
    } else {
        $suffix ='';
    }

    //$this->language =
    //$this->country

    // Set the language suffix to '_' + language code. Only do this
    // for non-English languages, to keep processing down a little.
    // TODO: perhaps only do this for non-site default locale languages,
    // rather than hard-coding English here.


    //return ($this->language <> 'en') ? '_' . $this->language : '';
    return $suffix;
}

?>