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
		public $meta = [];
		public function call($controllerObj)
		{
			// create object with html-header meta-values for SEO-support
			$meta = (object) [];
			
			$meta->keywords = env('app')->keywords;
			if($controllerObj->meta->keywords) {
				$meta->keywords .= ', '.$controllerObj->meta->keywords;
			}
			
			$meta->description = env('app')->description;
			if($controllerObj->meta->description) {
				$meta->description .= '. '.$controllerObj->meta->description;
			}
			
			if($controllerObj->meta->html_lang) {
				$meta->html_lang = $controllerObj->meta->html_lang;
			}
			else {
				$meta->html_lang =env('app')->htmllang;
			}
			
			if($controllerObj->meta->language) {
				$meta->language = $controllerObj->meta->language;
			}
			else {
				$meta->language = env('app')->language;
			}
			
			if(!empty($controllerObj->meta->title)) { // to append title-tag with text, eq: controller and action || custom text
				$meta->title = $controllerObj->meta->title;
			}
			else    {
				$meta->title = ucfirst(response()->route->controllerRoute) .'-'.ucfirst(response()->route->action);
			}
			
			$meta->robot    = env('app')->robot;         //follow, nofollow, noindex, index  -->   content="index,follow"
			$meta->author   = env('app')->author;
			$meta->char_set = env('app')->charset;
			
			return (object) $meta;
		}
	}
