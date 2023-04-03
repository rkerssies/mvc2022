<?php
	/**
	 * Project: PhpStorm.
	 * Author:  InCubics
	 * Date:    02/04/2023
	 * File:    paginationService.phtml
	 */
	
	namespace services;
	
	class paginationService
	{
		public function call()
		{
			$pagination = '';
			if(!empty(response()->query->total) )
			{
				$total = response()->query->total;
				$amount = response()->query->amount;
				$from = response('query')->from;
				
				$amountPages = ceil($total / $amount);
				
				if(strpos(currentPath(), '?'))  {
					$path = explode('?',currentPath())[0];
				}
				else    {
					$path = currentPath();
				}
				
				$pagination =
					'<div class="center">
						<div class="pagination">';
				$pagination .= '<a href="'. url($path.'?page=0').'" class="bggrey paggyLeft">1 &laquo;</a>';
//				if( request()->get->page + 1  > 2   ) {        // show below page 2
//					$pagination .= '<a href="'. url($path.'?page=0').'" class="grey bgrey">...</a>';
//				}
				
				
				if(request()->get->page < 1) { $pathMin = '#'; $label = '-';} else {$pathMin = url($path.'?page='.(request()->get->page -1)); $label = '-';}
				$pagination .= '<a href="'.$pathMin.'" class="';
				if(request()->get->page < 1 ) {
					$pagination .= 'grey';
				}
				$pagination .= '">'.$label.'</a>';
				
				$pagination .= '<a href="'. url($path.'?page='.(request()->get->page)).'" class="currentPage">'.(request()->get->page+1).'</a>';
				
				if(request()->get->page >= ($amountPages-1)) { $pathPlus = '#'; $label = '+';} else {$pathPlus = url($path.'?page='.(request()->get->page +1)); $label = '+';}
				$pagination .= '<a href="'.$pathPlus.'" class="';
				if(request()->get->page >= ($amountPages-1)) {
					$pagination .= 'grey';
				}
				$pagination .= '">'.$label.'</a>';
			//	dd($path);
				$pagination .= '<a href="'. url($path.'?page='.($amountPages-1)).'" class="bggrey paggyRight">&raquo; '.$amountPages.'</a>';
			
				
				$pagination .= '
						<label class="paginatorComments">'.$amount.' records / page, '.$total.' in total</label>
						</div>
						</div>';
			}
			return $pagination;
		}
	}
