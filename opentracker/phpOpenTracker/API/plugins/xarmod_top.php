<?php
require_once POT_INCLUDE_PATH . 'API/Plugin.php';

class phpOpenTracker_API_xarmod_top extends phpOpenTracker_API_Plugin {
  var $apiCalls = array('xarmod_top');

  var $apiType = 'get';

  /**
  * Runs the phpOpenTracker API call.
  *
  * @param  array $parameters
  * @return mixed
  * @access public
  */
  function run($parameters) {
    list($constraint, $selfJoin) = $this->_constraint(
      $parameters['constraints'],
      true
    );

    if ($selfJoin) {
      $selfJoinConstraint = 'AND accesslog.accesslog_id = accesslog2.accesslog_id';

      $selfJoinTable = sprintf(
        '%s accesslog2,',

        $this->config['accesslog_table']
      );
    } else {
      $selfJoinConstraint = '';
      $selfJoinTable      = '';
    }

    $timerange = $this->_whereTimerange(
      $parameters['start'],
      $parameters['end']
    );

    switch ($parameters['result_format']) {
      case 'csv': {
        $csv = "Rank;Item;Count;Percent\n";
      }
      break;

      case 'xml':
      case 'xml_object': {
        $tree = new XML_Tree;
        $root = &$tree->addRoot('top');
      }
      break;

      case 'separate_result_arrays': {
        $names   = array();
        $values  = array();
        $percent = array();
      }
      break;

      default: {
        $topItems = array();
      }
    }
    $xar_groupby = 'GROUP BY accesslog.accesslog_id';
    $xar_groupby = '';
        $nestedQuery = sprintf(
          "SELECT xar_modname AS item
             FROM %s accesslog,
                  %s
                  %s visitors
            WHERE visitors.client_id    = %d
              AND visitors.accesslog_id = accesslog.accesslog_id
                    %s
                  %s
                  %s ".$xar_groupby,
            //GROUP BY accesslog.accesslog_id, data_table.string",
            //end modification            
          $this->config['accesslog_table'],
          $selfJoinTable,
          $this->config['visitors_table'],
          $parameters['client_id'],
          $selfJoinConstraint,
          $constraint,
          $timerange
        );
      
      
    if ($this->db->supportsNestedQueries()) {
      $queryTotalUnique = sprintf(
        'SELECT COUNT(item)           AS total_items,
                COUNT(DISTINCT(item)) AS unique_items
           FROM (%s) items',

        $nestedQuery
      );

      $queryItems = sprintf(
        'SELECT COUNT(item) AS item_count,
                item
           FROM (%s) items
          GROUP BY item
          ORDER BY item_count %s,
                   item',

        $nestedQuery,
        $parameters['order']
      );
    } else {
      if ($this->config['db_type'] == 'mysql') {
        $dropTemporaryTable = true;

        $this->db->query(
          sprintf(
            'CREATE TEMPORARY TABLE pot_temporary_table %s',

            $nestedQuery
          )
        );

        $queryTotalUnique = sprintf(
          'SELECT COUNT(item)           AS total_items,
                  COUNT(DISTINCT(item)) AS unique_items
             FROM pot_temporary_table',

          $nestedQuery
        );

        $queryItems = sprintf(
          'SELECT COUNT(item) AS item_count,
                  item
             FROM pot_temporary_table
            GROUP BY item
            ORDER BY item_count %s,
                     item',

          $parameters['order']
        );
      } else {
        return phpOpenTracker::handleError(
          'You need a database system capable of nested queries.',
          E_USER_ERROR
        );
      }
    }

    $this->db->query($queryTotalUnique);

    if ($row = $this->db->fetchRow()) {
      $totalItems  = intval($row['total_items']);
      $uniqueItems = intval($row['unique_items']);
    } else {
      return phpOpenTracker::handleError(
        'Database query failed.'
      );
    }

    if ($totalItems > 0) {
      $this->db->query($queryItems, $parameters['limit']);

      $i = 0;

      while ($row = $this->db->fetchRow()) {
        $percentValue = doubleval(
          number_format(
            ((100 * $row['item_count']) / $totalItems),
            2
          )
        );

        switch ($parameters['result_format']) {
          case 'csv': {
            $csv = sprintf(
              "%d;%s;%d;%d\n",

              $i+1,
              $row['item'],
              intval($row['item_count']),
              $percentValue
            );
          }
          break;

          case 'xml':
          case 'xml_object': {
            $itemChild = &$root->addChild('item');

            $itemChild->addChild('rank',    $i+1);
            $itemChild->addChild('string',  $row['item']);
            $itemChild->addChild('count',   intval($row['item_count']));
            $itemChild->addChild('percent', $percentValue);

            if (isset($row['document_url'])) {
              $itemChild->addChild('url',  $row['document_url']);
            }
          }
          break;

          case 'separate_result_arrays': {
            $names[$i]   = $row['item'];
            $values[$i]  = intval($row['item_count']);
            $percent[$i] = $percentValue;
          }
          break;

          default: {
            $topItems[$i]['count'  ] = intval($row['item_count']);
            $topItems[$i]['string' ] = $row['item'];
            $topItems[$i]['percent'] = $percentValue;

            if (isset($row['document_url'])) {
              $topItems[$i]['url' ] = $row['document_url'];
            }
          }
        }

        $i++;
      }
    }

    if (isset($dropTemporaryTable)) {
      $this->db->query('DROP TABLE pot_temporary_table');
    }

    switch ($parameters['result_format']) {
      case 'csv': {
        return $csv;
      }
      break;

      case 'xml':
      case 'xml_object': {
        $root->addChild('total',  $totalItems);
        $root->addChild('unique', $uniqueItems);

        switch ($parameters['result_format']) {
          case 'xml': {
            return $root->get();
          }
          break;

          case 'xml_object': {
            return $root;
          }
          break;
        }
      }
      break;

      case 'separate_result_arrays': {
        return array(
          $names,
          $values,
          $percent,
          $uniqueItems
        );
      }
      break;

      default: {
        return array(
          'top_items'    => $topItems,
          'unique_items' => $uniqueItems
        );
      }
    }
  }
}

//
// "phpOpenTracker essenya, gul meletya;
//  Sebastian carneron PHP."
//
?>
