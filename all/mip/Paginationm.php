<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace mip;
use think\Request;
class Paginationm
{
// Copyright http://blog.csdn.net/hjt321658/article/details/40678431
 
    var $base_url = ''; // The page we are linking to
    var $page_break = '';
    var $total_rows = ''; // Total number of items (database results)
    var $per_page = 10; // Max number of items you want shown per page
    var $num_links = 2; // Number of "digit" links to show before/after the currently viewed page
    var $cur_page = 0; // The current page being viewed
    var $first_link = '‹ First';

    var $next_link = '>';

    var $prev_link = '<';

    var $last_link = 'Last ›';

    var $uri_segment = 3;

    var $full_tag_open = '';

    var $full_tag_close = '';

    var $first_tag_open = '';

    var $first_tag_close = ' ';

    var $last_tag_open = ' ';

    var $last_tag_close = '';

    var $cur_tag_open = ' <strong>';

    var $cur_tag_close = '</strong>';

    var $next_tag_open = ' ';

    var $next_tag_close = ' ';

    var $prev_tag_open = ' ';

    var $prev_tag_close = '';

    var $num_tag_open = ' ';

    var $num_tag_close = '';

    var $page_query_string = FALSE;

    var $query_string_segment = 'index';

    var $use_page_numbers = FALSE; // Use page number for segment instead of offset
    var $prefix = ''; // A custom prefix added to the path.
    var $suffix = ''; // A custom suffix added to the path.
    var $anchor_class = '';

    var $display_pages = TRUE;

    var $first_url = ''; // Alternative URL for the First Page.

    /**
     * Constructor
     *
     * @access public
     * @param
     *          array   initialization parameters
     */
    function __construct($params = array())
    {

        $config = array(
            'first_link' => '&lt;&lt;',
            'next_link' => '&gt;',
            'prev_link' => '&lt;',
            'last_link' => '&gt;&gt;',
            'uri_segment' => 3,
            'full_tag_open' => '<div class="page-control"><ul class="pagination pull-right">',
            'full_tag_close' => '</ul></div>',
            'first_tag_open' => '<li>',
            'first_tag_close' => '</li>',
            'last_tag_open' => '<li>',
            'last_tag_close' => '</li>',
            'first_url' => '', // Alternative URL for the First Page.
            'cur_tag_open' => '<li class="active"><span>',
            'cur_tag_close' => '</span></li>',
            'next_tag_open' => '<li>',
            'next_tag_close' => '</li>',
            'prev_tag_open' => '<li>',
            'prev_tag_close' => '</li>',
            'num_tag_open' => '<li>',
            'num_tag_close' => '</li>',
            'display_pages' => TRUE,
            'anchor_class' => '',
            'num_links' => 1
        );
        $this->initialize($config);
        if(count($params) > 0)
        {
            $this->initialize($params);
        }

    }

    // --------------------------------------------------------------------

    /**
     * Initialize Preferences
     *
     * @access public
     * @param
     *          array   initialization parameters
     * @return void
     */
    function initialize($params = array())
    {
        if(count($params) > 0)
        {
            foreach($params as $key => $val)
            {
                if(isset($this->$key))
                {
                    $this->$key = $val;
                }
            }
        }
    }

    // --------------------------------------------------------------------

    /**
     * Generate the pagination links
     *
     * @access public
     * @return string
     */
    function create_links()
    {
        // If our item count or per-page total is zero there is no need to continue.
        if($this->total_rows == 0 or $this->per_page == 0)
        {
            return '';
        }

        // Calculate the total number of pages
        $num_pages = ceil($this->total_rows / $this->per_page);

        // Is there only one page? Hm... nothing more to do here then.
        if($num_pages == 1)
        {
            return '';
        }

        // Determine the current page number.

        if (input('param.page') != 0)
        {
            $this->cur_page = input('param.page');
            // Prep the current page - no funny business!
            $this->cur_page = (int) $this->cur_page;
        }
        else
        {
            $this->cur_page = 1;
        }
        $this->num_links = (int)$this->num_links;

        if($this->num_links < 1)
        {
            show_error('Your number of links must be a positive number.');
        }

        if(!is_numeric($this->cur_page))
        {
            $this->cur_page = 0;
        }

        // Is the page number beyond the result range?
        // If so we show the last page
        if($this->cur_page > $this->total_rows)
        {
            $this->cur_page = ($num_pages - 1) * $this->per_page;
        }

        $uri_page_number = $this->cur_page;
//        $this->cur_page = floor(($this->cur_page / $this->per_page) + 1);

        // Calculate the start and end numbers. These determine
        // which number to start and end the digit links with
        $start = (($this->cur_page - $this->num_links) > 0)? $this->cur_page - ($this->num_links - 1) :1;
        $end = (($this->cur_page + $this->num_links) < $num_pages)? $this->cur_page + $this->num_links :$num_pages;

        // Is pagination being used over GET or POST? If get, add a per_page query
        // string. If post, add a trailing slash to the base URL if needed
        if (substr($this->base_url, -1, 1) == '/')
        {
            $this->page_base_url = rtrim($this->base_url).$this->query_string_segment.$this->page_break;
        }
        else
        {
            $this->page_base_url = rtrim($this->base_url).'/'.$this->query_string_segment.$this->page_break;
        }


        // And here we go...
        $output = '';

        // Render the "First" link
        if($this->first_link !== FALSE AND $this->cur_page > ($this->num_links + 1))
        {
            $first_url = ($this->first_url == '') ? $this->base_url : $this->first_url;

            $output .= $this->first_tag_open.'<a data-type="mip" '.$this->anchor_class.'href="'.$first_url.'">'.$this->first_link.'</a>'.$this->first_tag_close;

        }

        // Render the "previous" link
        if($this->prev_link !== FALSE AND $this->cur_page != 1)
        {
            $i = $uri_page_number - 1;

            if ($i == 1)
            {
                $output .= $this->prev_tag_open.'<a data-type="mip" '.$this->anchor_class.'href="'.$this->base_url.'/">'.$this->prev_link.'</a>'.$this->prev_tag_close;
            }
            else
            {
                $i = ($i == 0) ? '' : $this->prefix.$i.$this->suffix;

                $output .= $this->prev_tag_open.'<a data-type="mip" '.$this->anchor_class.'href="'.$this->page_base_url.$i.'.html">'.$this->prev_link . '</a>'.$this->prev_tag_close;
            }

        }

        // Write the digit links
        for($loop = $start - 1; $loop <= $end; $loop ++)
        {
            $i = ($loop * $this->per_page) - $this->per_page;

            if($i >= 0)
            {
                if($this->cur_page == $loop)
                {
                    $output .= $this->cur_tag_open . $loop . $this->cur_tag_close; // Current page
                }
                else
                {
                    $n = ($i == 0)? '1' :($i/ $this->per_page + 1);
                    if ($n == 1)
                    {
                        $output .= $this->num_tag_open.'<a data-type="mip" '.$this->anchor_class.'href="'.$this->base_url.'/">'.$loop.'</a>'.$this->num_tag_close;
                    }
                    else
                    {
                        $n = ($n == '') ? '' : $this->prefix.$n.$this->suffix;

                        $output .= $this->num_tag_open.'<a data-type="mip" '.$this->anchor_class.'href="'.$this->page_base_url.$n.'.html">'.$loop.'</a>'.$this->num_tag_close;
                    }
                }
            }
        }

        // Render the "next" link
        if($this->next_link !== FALSE AND $this->cur_page < $num_pages)
        {
            $output .= $this->next_tag_open.'<a data-type="mip" '.$this->anchor_class.'href="'.$this->page_base_url.$this->prefix.($this->cur_page + 1).$this->suffix.'.html">'.$this->next_link.'</a>'.$this->next_tag_close;
        }

        // Render the "Last" link
        if($this->last_link !== FALSE AND ($this->cur_page + $this->num_links) < $num_pages)
        {
            $i = $num_pages;
            $output .= $this->last_tag_open.'<a data-type="mip" '.$this->anchor_class.'href="'.$this->page_base_url.$this->prefix.$i.$this->suffix.'.html">'.$this->last_link.'</a>'.$this->last_tag_close;
        }

        // Kill double slashes. Note: Sometimes we can end up with a double slash
        // in the penultimate link so we'll kill all double slashes.
        $output = preg_replace("#([^:])//+#", "\\1/", $output);

        // Add the wrapper HTML if exists
        $output = $this->full_tag_open . $output . $this->full_tag_close;

        return $output;
    }
}