<?php
	/**
	 * Project: MVC2022.
	 * Author:  InCubics
	 * Date:    19/12/2022
	 * File:    options.php
	 */

	function options($arg= null)
	{   // directly called options on Artibuild, eq: php artibuild --version
		
		if($arg == '--version' || $arg == '--v')
		{
			echo "\t \e[36m".'MVC2022 version: 2.1    Artibuild-version: 1.0'."\033[0m \n";
		}
		elseif($arg == '--help' || $arg == '--h')
		{
			$help = "\t\e[36m".'Some help with the usage of MVC2022-artibuild'."\033[0m \n\r" ;
			$help .= "\t".'======================================================================================================'."\n\r" ;
			$help .= "\t".'php artibuild --help                                >> shows all possibilities  '."\r";
			$help .= "\t".'php artibuild --h                                   >> shows all possibilities  '."\n\r";
			$help .= "\t".'php artibuild --v                                   >> shows versien MVC2022 and Artibuild'."\n\r";
			$help .= "\t".'-----------------------------------------------------------------------------------------------------'."\n\r";
			$help .= "\t".'php artibuild --version                             >> shows versien MVC2022 and Artibuild'."\n\r";
			$help .= "\t".'php artibuild appkey:generate                       >> creates a private appkey '."\n\r";
			$help .= "\t".'php artibuild db:refresh                            >> drops database and imports sql-file in the root '."\n\r";
			$help .= "\t".'-----------------------------------------------------------------------------------------------------'."\n\r";
			$help .= "\t".'php artibuild make:controller <Name>Controller      >> creates a controller '."\n\r";
			$help .= "\t".'php artibuild make:model <Name>          	    >> creates a model '."\n\r";
			$help .= "\t".'php artibuild make:view <subFolder.view>            >> creates a view-folder with a viewfile '."\n\r";
			$help .= "\t".'php artibuild make:view <sub1.sub2.view>            >> creates a view-folder with a viewfile '."\n\r";
			$help .= "\t".'php artibuild make:layout <subFolder.layoutName>    >> creates a layout-folder with a layout-file '."\n\r";
			$help .= "\t".'php artibuild make:layout <sub1.sub2.layoutName>    >> creates a layout-folder with a layout-file '."\n\r";
			$help .= "\t".'php artibuild make:request <modelname>Request       >> creates a Request-file '."\n\r";
			$help .= "\t".'php artibuild make:request <name>Service            >> creates a Request-file '."\n\r";
			$help .= "\t".'php artibuild make:request <name>Middleware         >> creates a call Middleware-file'."\n\r";
			$help .= "\t".'php artibuild make:request <name>Middleware --call  >> creates a call Middleware-file'."\n\r";
			$help .= "\t".'php artibuild make:request <name>Middleware --auto  >> creates a auto Middleware-file'."\n\r";
			echo $help;
		}
		elseif($arg == '--empty'){
			echo "\t \e[36m".'Artibuild didn\'t receive any parameter(s)'."\033[0m \n";
			echo "\t \e[36m".'Please enter parameters, or see suggestions on: php artibuild --help '."\033[0m \n";
		}
	}
	

