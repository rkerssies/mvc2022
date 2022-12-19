<?php
	/**
	 * Project: MVC2022
	 * Author:  InCubics
	 * Date:    20/12/2022
	 */
	
	use lib\files\getFiles;
	
	class GalleryController
	{
		public function photos(getFiles $oFile)
		{
//			$oFile = new getFiles();  // same as initiated in params of this method
			$this->data = $oFile->files('img', 'jpg');

			$this->title = 'Gallery !';
			
			$this->useView = 'photos.show';
		}
	
	}
