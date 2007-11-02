<?php

/**
 * Archive list of issues.
 * This page just lists the issues for a single magazine.
 * If a valid magazine is not selected, then an error will be raised.
 *
 * @param mid integer Magazine ID;
 * @param mag string Magazine reference
 * @param year integer Just select for a given year
 * @param month integer Just select for a given month number; requires the year also
 * 
 */

function mag_user_archive($args)
{
    $return = array();
    extract($args);

    // Get module parameters
    extract(xarModAPIfunc('mag', 'user', 'params',
        array(
            'knames' => 'module,default_numitems_issues,max_numitems_issues,month_names,pager_template_name'
        )
    ));

    // Pager parameters
    xarVarFetch('startnum', 'int:1', $startnum, 1, XARVAR_NOT_REQUIRED);
    xarVarFetch('numitems', 'int:1:' . $max_numitems_issues, $numitems, $default_numitems_issues, XARVAR_NOT_REQUIRED);

    // Year and month parameters.
    xarVarFetch('year', 'int:1900:2100', $year, 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('month', 'int:1:12', $month, 0, XARVAR_NOT_REQUIRED);

    // Get the current selected magazine details.
    $current_mag = xarModAPIfunc($module, 'user', 'currentmag', $args);
   
    if (!empty($current_mag)) {
        extract($current_mag);

        if (xarSecurityCheck('OverviewMag', 0, 'Mag', "$mid")) {
            $return['mid'] = $mid;
            $return['mag'] = $mag;

            // Now we can fetch a list of issues for this magazine.
            $issue_select = array();

            // Date ranges.
            if (!empty($year) && empty($month)) {
                // Span a whole year.
                $issue_select['startdate'] = strtotime("${year}-01-01");
                $issue_select['enddate'] = strtotime("${year}-01-01 +1 year -1 second");
            } elseif (!empty($year) && !empty($month)) {
                // Span a single month.
                $issue_select['startdate'] = strtotime("${year}-${month}-01");
                $issue_select['enddate'] = strtotime("${year}-${month}-01 +1 month -1 second");
            }

            // startnum/numitems
            if (!empty($startnum)) $issue_select['startnum'] = $startnum;
            if (!empty($numitems)) $issue_select['numitems'] = $numitems;

            // Only published issues.
            $issue_select['status'] = 'PUBLISHED';

            // Only for the selected magazine.
            $issue_select['mid'] = $mid;

            // Fetch the issues.
            $issues = xarModAPIfunc($module, 'user', 'getissues', $issue_select);

            // Count the issues.
            // Don't bother doing a database count if we already have all the items.
            if ($startnum > 1 || count($issues) >= $numitems) {
                $count = xarModAPIfunc($module, 'user', 'getissues', array_merge($issue_select, array('docount'=>true)));
            } else {
                $count = count($issues);
            }
            
            // Pass the issues (which could be empty) into the template.
            $return['issues'] = $issues;

            // Group the issues into years and months.
            if (!empty($issues)) {
                $years = array();

                // Total count of years and months in this data set.
                $year_count = 0;
                $month_count = 0;

                // Maximum number of issues in any month.
                $max_month_count = 0;

                foreach($issues as $issue) {
                    // If no publication date given, then we can't group this one.
                    if (empty($issue['pubdate'])) continue;

                    // Get the year and month of the issue.
                    list($issue_year, $issue_month) = explode('-', date('Y-n-d', $issue['pubdate']));

                    // Start a new year if necessary.
                    if (!isset($years[$issue_year])) {
                        $years[$issue_year] = array();
                        $year_count += 1;
                    }

                    // Start a new month if necessary.
                    if (!isset($years[$issue_year][$issue_month])) {
                        $years[$issue_year][$issue_month] = array();
                        $month_count += 1;
                    }

                    // Add this issue to the given month.
                    $years[$issue_year][$issue_month][] = $issue['iid'];

                    // Increment the maximum month count if required.
                    // If we have multiple issues per month, then they can be grouped into months,
                    // oherwise just grouped into years.
                    if (count($years[$issue_year][$issue_month]) > $max_month_count) {
                        $max_month_count = count($years[$issue_year][$issue_month]);
                    }
                }

                // If we are grouping by month, then reverse the order of issues within 
                // each month, so they are in ascending order.
                if ($max_month_count > 1) {
                    foreach($years as $y_key => $y_value) {
                        foreach($y_value as $m_key => $m_value) {
                            if (count($m_value) > 1) $years[$y_key][$m_key] = array_reverse($years[$y_key][$m_key]);
                        }
                    }
                }

                $return['years'] = $years;
                $return['year_count'] = $year_count;
                $return['month_count'] = $month_count;
                $return['max_month_count'] = $max_month_count;

                // Pass ina list of month names.
                $return['month_names'] = $month_names;

                // Do the pager
                $pager_params = array(
                    'func' => 'archive',
                    'mag' => $mag['ref'],
                );
                if ($numitems != $default_numitems_issues) $pager_params['numitems'] = $numitems;
                if (!empty($year)) $pager_params['year'] = $year;
                if (!empty($month)) $pager_params['month'] = $month;
                $pager_url = xarModAPIfunc($module, 'user', 'url', array_merge($pager_params, array('startnum' => '%%')));

                $pager = xarTplGetPager($startnum, $count, $pager_url, $numitems, array(), $pager_template_name);
                $return['pager'] = $pager;
            }
        }
    } else {
        // Raise an error - either too many or too few magazines (not exactly one).
        // The error is displayed in the template, on detecting a lack of a magazine.
    }

    // Set context information for custom templates and blocks.
    $return['function'] = 'archive';
    xarModAPIfunc($module, 'user', 'cachevalues', $return);

    return $return;
}

?>