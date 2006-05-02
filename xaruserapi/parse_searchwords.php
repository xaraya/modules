<?php

/**
 * Cleans and parses '$q' keywords passed into a search page.
 * returns and array with the following elements:
 * - 'q': cleaned up string that can be placed back into the next search page
 * - 'q_array': split into array of search terms
 * - 'where': where-clause text, provided only when 'columns' is set
 * Returns NULL if there is no query string, either before or after cleaning.
 * @param q string The query string
 * @param case boolean Case sensitity - false (default) forces lower-case
 * @param columns array List of columns to search on.
 * @param rule string Default rule, values 'and|or', default 'and'
 * @todo: could this be of any use as a more core function?
 * @todo: look for 'and' and 'or' keywords, process parenthesis, and finally parse into a proper tree.
 * @todo: provide a means of preparing SQL where-clauses here too.
 * @todo: this is the point that words can be put through a spell checker, and alternatives offered.
 * @todo: support min and max sizes for each word, and a maximum number of words/terms in total.
 */

function xarbb_userapi_parse_searchwords($args)
{
    extract($args);

    if (empty($q)) return;

    // Put text on one line.
    $q = trim(preg_replace('/\s+/', ' ', $q));

    // Remove any unwanted characters.
    // Leave just letters, numbers, and quotes.
    // TODO: allow through quotes, and parse them for multi-word matches.
    // TODO: ditto for parenthesis.
    $q = trim(preg_replace('/[^\w\d ]/i', '', $q));

    // More white-space trimming, now we have potentially removed some characters.
    $q = trim(preg_replace('/\s+/', ' ', $q));

    // Case-sensitive?
    if (empty($case)) {
        // Not case-sensitive - normalise to lower case.
        $q = strtolower($q);
    }

    // The array is used in queries, along with 'AND' or 'OR'.
    $q_array = explode(' ', $q);

    if (!empty($columns) && is_array($columns)) {
        // The rule defaults to 'and'.
        if (empty($rule) || $rule != 'and' || $rule != 'or') $rule = 'and';

        // The where-clause string parts.
        $where_arr = array();

        // The bind variables for the string.
        $bind = array();

        // Loop for each word.
        foreach($q_array as $word) {
            $where_cols = array();

            // Loop for each column
            foreach($columns as $column) {
                $where_cols[] = $column . " LIKE ?";
                // TODO: use adodb to fetch the database wildcard character.
                $bind[] = '%' . $word . '%';
            }
            $where_arr[] = implode(' OR ', $where_cols);
        }

        if ($rule == 'and') {
            $where = '( ' . implode(' ) ' . strtoupper($rule) . ' ( ', $where_arr) . ' )';
        } else {
            $where = '( ' . implode(' ' . strtoupper($rule) . ' ', $where_arr) . ' )';
        }
    } else {
        $where = '';
        $bind = array();
    }

    return array(
        'q_array' => $q_array,
        'q' => $q,
        'where' => $where,
        'bind' => $bind,
    );
}

?>