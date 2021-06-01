<?php 

	/**
	 * 
	 */
	class Pagination
	{
		private $start_pagination;
		private $end_pagination;
		private $previous_page 		= null;
		private $next_page 			= null;
		private $page 				= 1;
		private $noresults			= true;
		
		function __construct($page, $total_pages, $numOfPagination=5)
		{	
			$page = intval($page);
			$page = ($page == 0)? 1: $page;

			if($total_pages==0){
				return;
			}
			$this->hasResults();

			if($page > $total_pages){
				$page = $total_pages;
			}
			$this->setPage($page);

			$numOfPagination--;

			$firstPage = $page;
			$lastPage = $page;

			while($firstPage != 1 || $lastPage != $total_pages){
				if ($numOfPagination==0) {
					break;
				}
				if($lastPage < $total_pages){
					$lastPage++;
					$numOfPagination--;
				}
				if($firstPage > 1){
					$firstPage--;
					$numOfPagination--;
				}
			}
			$this->setStartPage($firstPage);
			$this->setEndPage($lastPage);

			if($page > 1){
				$this->setPrevPage($page-1);
			}
			if($page < $total_pages){
				$this->setNextPage($page+1);
			}
		}

		function noPages(){
			return $this->noresults;
		}

		function hasPrev(){
			return $this->previous_page == null;
		}

		function hasNext(){
			return $this->next_page == null;
		}

		function setStartPage($page){
			$this->start_pagination = $page;
		}

		function setEndPage($page){
			$this->end_pagination = $page;
		}

		function setPrevPage($page){
			$this->previous_page = $page;
		}

		function setNextPage($page){
			$this->next_page = $page;
		}

		function setPage($page){
			$this->page = $page;
		}

		function hasResults(){
			$this->noresults = false;
		}

		function getStartPage(){
			return $this->start_pagination;
		}

		function getEndPage(){
			return $this->end_pagination;
		}

		function getPrevPage(){
			return $this->previous_page;
		}

		function getNextPage(){
			return $this->next_page;
		}

		function getPage(){
			return $this->page;
		}

	}	

?>