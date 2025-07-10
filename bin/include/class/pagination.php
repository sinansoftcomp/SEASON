<?php
/*
Codeigniter Pagination.

// 로드
include_once($_SERVER[DOCUMENT_ROOT].'/V2/_lib/class/Pagination.php');

// 설정
$pagination = new Pagination(array(
	'base_url' => '/V2/dateCourse/areaList.php?',
	'per_page' => 5,
	'total_rows' => 100,
	'cur_page' => $page,

	'first_link' => "<img src='' />",
	'cur_tag_open' => "<span class='current'>",
	'cur_tag_close' => '</span>',
	'full_tag_open' => "<div id='pagination'>",
	'full_tag_close' => '</div>',

	'onclick' => "get_list('%n');"
));

OR

$config['base_url'] = 'url';

OR

$config = array('base_url' => 'url');

$pagination = new Pagination($config);

// 출력
echo $pagination->create_links();
*/

class Pagination {

	var $base_url			= ''; // The page we are linking to
	var $prefix				= ''; // A custom prefix added to the path.
	var $suffix				= ''; // A custom suffix added to the path.
	var $display_onepage	= FALSE;
	var $total_rows			=  0; // Total number of items (database results)
	var $per_page			= 10; // contents 리스트수
	var $print_pages		= 10; // num_liks가 0일때 페이징수
	var $num_links			=  4; // 좌우측값 대칭 페이징때 사용
	var $cur_page			=  1; // The current page being viewed
	var $first_link			= FALSE;
	var $next_link			= '&gt;&gt;';
	var $prev_link			= '&lt;&lt;';
	var $last_link			= FALSE;
	var $full_tag_open		= '';
	var $full_tag_close		= '';
	var $first_tag_open		= '';
	var $first_tag_close	= '';
	var $last_tag_open		= '';
	var $last_tag_close		= '';
	var $first_url			= ''; // Alternative URL for the First Page.
	var $cur_tag_open		= '<li class="active"><a href="#">';
	var $cur_tag_close		= '</a></li>';
	var $next_tag_open		= '<li>';
	var $next_tag_close		= '</li>';
	var $prev_tag_open		= '<li>';
	var $prev_tag_close		= '</li>';
	var $num_tag_open		= '<li>';
	var $num_tag_close		= '</li>';
	var $query_string_segment = 'page';
	var $display_pages		= TRUE;
	var $anchor_class		= '';

	var $num_pages	= "";
/*
	<div class="text-center">
			<nav>
			  <ul class="pagination">
				<li>
				  <a href="#" aria-label="Previous">
					<span aria-hidden="true">&laquo;</span>
				  </a>
				</li>
				<li class="active"><a href="#">1</a></li>
				<li><a href="#">2</a></li>
				<li><a href="#">3</a></li>
				<li><a href="#">4</a></li>
				<li><a href="#">5</a></li>
				<li>
				  <a href="#" aria-label="Next">
					<span aria-hidden="true">&raquo;</span>
				  </a>
				</li>
			  </ul>
			</nav>
		  </div>
*/
	// CUSTOM
	var $onclick			= '';

	public function __construct($params = array()) {
		if (count($params) > 0) {
			$this->initialize($params);
		}

		if ($this->anchor_class != '') {
			$this->anchor_class = 'class="'.$this->anchor_class.'" ';
		}

		if ($this->onclick != '') {
			$this->onclick = ' onclick="'.$this->onclick.' return false;"';
		}

		$this->num_pages = ceil($this->total_rows / $this->per_page);
	}
	
	// 세팅된 변수 반환
	function GetData($str){
		return $this->$str;
	}
	
	function initialize($params = array()) {
		if (count($params) > 0) {
			foreach ($params as $key => $val) {
				if (isset($this->$key)) {
					$this->$key = $val;
				}
			}
		}
	}

	private function n_rep($str, $i=1) {
		return str_replace('%n', $i, $str);
	}

	function create_links() {
		
		// If our item count or per-page total is zero there is no need to continue.
		if ($this->total_rows == 0 OR $this->per_page == 0) {
			return false;
		}

		// Calculate the total number of pages
		$this->num_pages = ceil($this->total_rows / $this->per_page);

		// Is there only one page? Hm... nothing more to do here then.
		if ($this->num_pages == 1 && !$this->display_onepage) {
			return false;
		}

		// Set the base page index for starting page number
		$base_page = 1;

		if ((int)$this->cur_page == 0) {
			$this->cur_page = $base_page;
		}

		$this->num_links = (int)$this->num_links;

//		if ($this->num_links < 1) {
//			return false;
//		}

		if (!is_numeric($this->cur_page)) {
			$this->cur_page = $base_page;
		}

		// Is the page number beyond the result range?
		// If so we show the last page
		if ($this->cur_page > $this->num_pages) {
			$this->cur_page = $this->num_pages;
		}

		$uri_page_number = $this->cur_page;

		// Calculate the start and end numbers. These determine
		// which number to start and end the digit links with
		if($this->num_links==0){
			$start = (int)(($this->cur_page-1)/$this->print_pages)*$this->print_pages+2;
			$end = (int)(($this->cur_page-1)/$this->print_pages)*$this->print_pages+$this->print_pages;
			if($end>$this->num_pages) $end = $this->num_pages;
		}else{
			$start = (($this->cur_page - $this->num_links) > 0) ? $this->cur_page - ($this->num_links - 1) : 1;
			$end   = (($this->cur_page + $this->num_links) < $this->num_pages) ? $this->cur_page + $this->num_links : $this->num_pages;
		}

		// Is pagination being used over GET or POST?  If get, add a per_page query
		// string. If post, add a trailing slash to the base URL if needed
		$this->base_url = rtrim($this->base_url).'&amp;'.$this->query_string_segment.'=';

		// And here we go...
		$output = '';

		// Render the "First" link
		if ($this->first_link !== FALSE AND $this->cur_page > ($this->num_links + 1)) {
			$first_url = ($this->first_url == '') ? $this->base_url : $this->first_url;
			$output .= $this->first_tag_open.'<a '.$this->anchor_class.'href="'.$first_url.'"'.$this->n_rep($this->onclick, $i).'>'.$this->first_link.'</a>'.$this->first_tag_close;
		}

		// Render the "previous" link
		if ($this->prev_link !== FALSE AND $this->cur_page != 1) {
			$i = $uri_page_number - 1;

			if ($i == 0 && $this->first_url != '') {
				$output .= $this->prev_tag_open.'<a class="prev" '.$this->anchor_class.'href="'.$this->first_url.'">'.$this->prev_link.'</a>'.$this->prev_tag_close;
			}
			else {
				$i = ($i == 0) ? '' : $this->prefix.$i.$this->suffix;
				$output .= $this->prev_tag_open.'<a class="prev" '.$this->anchor_class.'href="'.$this->base_url.$i.'"'.$this->n_rep($this->onclick, $i).'>'.$this->prev_link.'</a>'.$this->prev_tag_close;
			}
		}

		// Render the pages
		if ($this->display_pages !== FALSE) {
			// Write the digit links
			for ($loop = $start -1; $loop <= $end; $loop++) {
				$i = $loop;

				if ($i >= $base_page) {
					if ($this->cur_page == $loop) {
						$output .= $this->cur_tag_open.$loop.$this->cur_tag_close; // Current page
					}
					else {
						$n = $i; // $n = ($i == $base_page) ? '' : $i;

						if ($n == '' && $this->first_url != '') {
							$output .= $this->num_tag_open.'<a '.$this->anchor_class.'href="'.$this->first_url.'">'.$loop.'</a>'.$this->num_tag_close;
						}
						else {
							$n = ($n == '') ? '' : $this->prefix.$n.$this->suffix;

							$output .= $this->num_tag_open.'<a '.$this->anchor_class.'href="'.$this->base_url.$n.'"'.$this->n_rep($this->onclick, $n).'>'.$loop.'</a>'.$this->num_tag_close;
						}
					}
				}
			}
		}

		// Render the "next" link
		if ($this->next_link !== FALSE AND $this->cur_page < $this->num_pages) {
			$i = $this->cur_page + 1;

			$output .= $this->next_tag_open.'<a class="next" '.$this->anchor_class.'href="'.$this->base_url.$this->prefix.$i.$this->suffix.'"'.$this->n_rep($this->onclick, $i).'>'.$this->next_link.'</a>'.$this->next_tag_close;
		}

		// Render the "Last" link
		if ($this->last_link !== FALSE AND ($this->cur_page + $this->num_links) < $this->num_pages) {
			$i = $this->num_pages;

			$output .= $this->last_tag_open.'<a '.$this->anchor_class.'href="'.$this->base_url.$this->prefix.$i.$this->suffix.'"'.$this->n_rep($this->onclick, $i).'>'.$this->last_link.'</a>'.$this->last_tag_close;
		}

		// Kill double slashes.  Note: Sometimes we can end up with a double slash
		// in the penultimate link so we'll kill all double slashes.
		$output = preg_replace("#([^:])//+#", "\\1/", $output);

		// Add the wrapper HTML if exists
		$output = $this->full_tag_open.$output.$this->full_tag_close;

		return $output;
	}
}

?>