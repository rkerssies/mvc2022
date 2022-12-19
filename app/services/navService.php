<?php
	/**
	 * Project: MVC2022.
	 * Author:  InCubics
	 * Date:    03/07/2022
	 * File:    navService.php
	 */
	
	namespace services;
	class navService
	{
		public function call()
		{
			//TODO get array with nav-values from DB (!). Add a check on permissions to make nav-item visible
			$arrayItems = [
				[   'href' => 'articles',
					'label' => 'articles index'
				],
				[   'href' => 'article/1',
					'label' => 'articles show id=1'
				],
				[   'href' => 'gallery',
					'label' => 'photo\'s'
				],
				[   'href' => 'fruits',
					'label' => 'fruit index'
				],
				[   'href' => 'fruity',
					'label' => 'fruity index'
				],
				[   'href' => 'login',
					'label' => 'login'
				],
			];
			
			$response = '';
			foreach($arrayItems as $item)
			{
				if(isset(session_get('login')->username) && $item['label'] == 'login') {
					$hasSession	 = 'text-success';
				}
				elseif( $item['label'] == 'login')
				{
					$hasSession	 = 'text-danger';
				}
				$response .= '<a class="navbar-item '.$hasSession.'" href="'.url($item['href']).'">'.$item['label'].'</a> |';
			}
			return $response;
		}
	}
