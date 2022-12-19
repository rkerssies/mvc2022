<?php
	/**
	 * Project: MVC2022.
	 * Author:  InCubics
	 * Date:    02/07/2022
	 * File:    cssService.php
	 */

	namespace services;

	use core\Request;
	use core\Response;
	
	class metaService
	{
		public function call($controllerObj)
		{
			// create object with html-header meta-values for SEO-support
			
			$meta = (object) [];
			
			if($controllerObj->meta->keywords) {
				$meta->keywords = CONFIG['keywords'].', '.$controllerObj->meta->keywords;
			}
			else {
				$meta->keywords = CONFIG['keywords'];
			}
			
			if($controllerObj->meta->description) {
				$meta->description = CONFIG['description'].'. '.$controllerObj->meta->description;
			}
			else {
				$meta->description = CONFIG['description'];
			}
			
			if($controllerObj->meta->html_lang) {
				$meta->html_lang = $controllerObj->meta->html_lang;
			}
			else {
				$meta->html_lang = CONFIG['html_lang'];
			}
			
			if($controllerObj->meta->language) {
				$meta->language = $controllerObj->meta->language;
			}
			else {
				$meta->language = CONFIG['language'];
			}
			
			if(!empty($controllerObj->meta->title)) { // to append title-tag with text, eq: controller and action || custom text
				$meta->title = $controllerObj->meta->title;
			}
			else    {
				$meta->title = ucfirst(response()->route->controllerRoute) .'-'.ucfirst(response()->route->action);
			}
			
			$meta->robot    = CONFIG['robot'];         //follow, nofollow, noindex, index  -->   content="index,follow"
			$meta->author   = CONFIG['author'];
			$meta->char_set = CONFIG['charset'];
			
			return (object) $meta;
		}
	}
