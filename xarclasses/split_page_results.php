<?php
// ----------------------------------------------------------------------
// Copyright (C) 2004: Marc Lutolf (marcinmilan@xaraya.com)
// Purpose of file:  Configuration functions for commerce
// ----------------------------------------------------------------------
//  based on:
//  (c) 2003 XT-Commerce
//  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
//  (c) 2002-2003 osCommerce (oscommerce.sql,v 1.83); www.oscommerce.com
//  (c) 2003  nextcommerce (nextcommerce.sql,v 1.76 2003/08/25); www.nextcommerce.org
// ----------------------------------------------------------------------

  class splitPageResults
  {
    var $rowsperpage;
    var $queryrows;
    var $currentpage;
    var $url;

    function splitPageResults($current_page_number=1, $query_num_rows=0, $url, $max_rows_per_page=20)
    {
        $this->rowsperpage = $max_rows_per_page;
        $this->queryrows = $query_num_rows;
        $this->currentpage = $current_page_number;
        $this->url = $url;
        $num_pages = ceil($query_num_rows / $this->rowsperpage);
        if ($this->currentpage > $num_pages) {
            $this->currentpage = $num_pages;
        }
    }

    function display_links($max_page_links=10)
    {
        // calculate number of pages needing links
        $num_pages = ceil($this->queryrows / $this->rowsperpage);

        $pages_array = array();
        for ($i=1; $i<=$num_pages; $i++) {
            $pages_array[] = array('id' => $i, 'text' => $i);
        }

        $string = 'Page #(1) of #(2)';
        $previouspage = $this->currentpage - 1;
        $nextpage = $this->currentpage + 1;

        if ($num_pages > 1) {
            $display_links = '<FORM name= "pages" action="' . $this->url . '" method="POST">';

            $previoustext = '[&lt;&lt;&#160;' . xarML('previous') . ']';
            $nexttext = '[' . xarML('next') . '&#160;&gt;&gt;]';
            if ($this->currentpage > 1) {
                $display_links .= '<a href="' . $this->url .
                '&page=' . $previouspage .
                '" class="splitPageLink">' . $previoustext . '</a>&#160;&#160;';
            } else {
                $display_links .= $previoustext . '&#160;&#160;';
            }

            $args = array(xarModAPIFunc('commerce','user','draw_pull_down_menu',array(
                                        'name' => 'page',
                                        'values' => $pages_array,
                                        'default' => $this->currentpage,
                                        'parameters' => 'onChange="this.form.submit();"')),
                                        $num_pages);
            $i = 1;
            foreach($args as $var) {
                $search = "#($i)";
                $string = str_replace($search, $var, $string);
                $i++;
            }
            $display_links .= $string;

            if (($this->currentpage < $num_pages) && ($num_pages != 1)) {
                $display_links .= '&#160;&#160;<a href="' . $this->url .
                '&page=' . $nextpage .
                '" class="splitPageLink">' . $nexttext . '</a>';
            } else {
                $display_links .= '&#160;&#160;' . $nexttext;
            }

//            if (SID) $display_links .= xtc_draw_hidden_field(session_name(), session_id());

            $display_links .= '</FORM>';
        } else {
            $args = array($num_pages,$num_pages);
            $i = 1;
            foreach($args as $var) {
                $search = "#($i)";
                $string = str_replace($search, $var, $string);
                $i++;
            }
            $display_links = $string;
        }
        return $display_links;
    }

    function display_count($string) {
      $to_num = ($this->rowsperpage * $this->currentpage);
      if ($to_num > $this->queryrows) $to_num = $this->queryrows;
      $from_num = ($this->rowsperpage * ($this->currentpage - 1));
      if ($to_num == 0) {
        $from_num = 0;
      } else {
        $from_num++;
      }
        $args = array('<b>' . $from_num . '</b>',
                    '<b>' . $to_num . '</b>',
                    '<b>' . $this->queryrows . '</b>');
        $i = 1;
        foreach($args as $var) {
            $search = "#($i)";
            $string = str_replace($search, $var, $string);
            $i++;
        }
        return $string;
    }
  }
?>