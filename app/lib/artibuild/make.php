<?php
	/**
	 * Project: MVC2022.
	 * Author:  InCubics
	 * Date:    19/12/2022
	 * File:    make.php
	 */

	function makeController($argsArray) {

		$myfile = fopen('app/controllers/'.$argsArray['args'][0].'.php', "w") or die("Unable to open file!");
		$txt = "<?php";
		$txt .= "\r\t/**\r\t* Project: MVC2022.\r\t* Author:\tSome other programmer\r\t* Date:\t\t".date('d-m-Y')."\r\t* File:\t\t".$argsArray['args'][0].".php\r\t*/\n";
		$txt .= "\tnamespace controllers;\n\r\tclass ".$argsArray['args'][0]."\t\r{\r";
			foreach(['index', 'show', 'add','store','edit','update','delete'] as $action){
				$txt .= "\t\tpublic function ".$action."()\t{\r\t\t\t// some code for the controller-action\n\t\t}\r";
			}
		$txt .= "\t}";
		$txt .= "\n?>";
		fwrite($myfile, $txt);
		fclose($myfile);
		echo "\t \e[32m".'created controller: '.$argsArray['args'][0] ."\033[0m  \n\r";
	}
	
	function makeModel($argsArray)  {

		$myfile = fopen('app/models/'.$argsArray['args'][0].'.php', "w") or die("Unable to open file!");
		$txt = "<?php";
		$txt .= "\r\t/**\r\t* Project: MVC2022.\r\t* Author:\tSome other programmer\r\t* Date:\t\t".date('d-m-Y')."\r\t* File:\t\t".$argsArray['args'][0].".php\r\t*/\n";
		$txt .= "\tnamespace models;\n\r\tclass ".$argsArray['args'][0]."\t\r\t{\r";
		$txt .= "\t\t// protected ".'$table'."\t\t= '".strtolower($argsArray['args'][0])."s';\r";
		$txt .= "\t\t// protected ".'$fillables'."\t= [];\r";
		$txt .= "\t\t// protected ".'$hidden'."\t\t= [];\r";
		$txt .= "\t\t\n";
		$txt .= "\t}";
		$txt .= "\n?>";
		fwrite($myfile, $txt);
		fclose($myfile);
		echo "\t \e[32m".'created model: '.$argsArray['args'][0] ."\033[0m  \n\r";
	}
	
	function makeMiddleware($argsArray) {
		
		if(empty($argsArray['options'][0])) {
			$type ='call';
		}
		elseif(empty($argsArray['options'][0]) || $argsArray['options'][0] == 'auto'){
			$type = 'auto';
		}
		
		$myfile = fopen('app/middleware/'.$type.'/'.$argsArray['args'][0].'.php', "w") or die("Unable to open file!");
		$txt = "<?php";
		$txt .= "\r\t/**\r\t* Project: MVC2022.\r\t* Author:\tSome other programmer\r\t* Date:\t\t".date('d-m-Y')."\r\t* File:\t\tmiddleware/".$type.'/'.$argsArray['args'][0].".php\r\t*/\n";
		$txt .= "\tnamespace middleware\\".$type.";\n\r\tclass ".$argsArray['args'][0]."\t\r\t{\n";
		foreach(['up', 'down'] as $method){
			$txt .= "\t\tpublic function ".$method."()\t{\r\t\t\t// some code for the ".$method."-method\n\t\t}\n";
		}
		$txt .= "\t}";
		$txt .= "\n?>";
		fwrite($myfile, $txt);
		fclose($myfile);
		echo "\t \e[32m".'created middleware: '.$argsArray['args'][0] ."\033[0m  \n\r";
	}
	
	function makeRequest($argsArray) {

		$myfile = fopen('app/validation/'.$argsArray['args'][0].'.php', "w") or die("Unable to open file!");
		$txt = "<?php";
		$txt .= "\r\t/**\r\t* Project: MVC2022.\r\t* Author:\tSome other programmer\r\t* Date:\t\t".date('d-m-Y')."\r\t* File:\t\tvalidation/".$argsArray['args'][0].".php\r\t*/\n";
		$txt .= "\tnamespace validation;\n";
		$txt .= "\tuse core\FormRequests;\n\r\tclass ".$argsArray['args'][0]." extends FormRequests\t\r\t{\n";
		foreach(['rules'] as $method){
			$txt .= "\t\tpublic function ".$method."()\t{\r\t\t\treturn [ ]; // array with validation-rules\n\t\t}\n";
		}
		$txt .= "\t}";
		$txt .= "\n?>";
		fwrite($myfile, $txt);
		fclose($myfile);
		echo "\t \e[32m".'created request: '.$argsArray['args'][0] ."\033[0m  \n\r";
	}
	
	
	function makeService($argsArray) {
	
//		print_r($argsArray);
//		die;
		
		$myfile = fopen('app/services/'.$argsArray['args'][0].'.php', "w") or die("Unable to open file!");
		$txt = "<?php";
		$txt .= "\r\t/**\r\t* Project: MVC2022.\r\t* Author:\tSome other programmer\r\t* Date:\t\t".date('d-m-Y')."\r\t* File:\t\tservices/".$argsArray['args'][0].".php\r\t*/\n";
		$txt .= "\tnamespace services;\n\r";
		$txt .= "\tclass ".$argsArray['args'][0]." \t\r\t{\n";
		foreach(['call'] as $method){
			$txt .= "\t\tpublic function ".$method."()\t{\r\t\t\treturn 'some value'; // value that supports eq: views or layouts\n\t\t}\n";
		}
		$txt .= "\t}";
		$txt .= "\n?>";
		fwrite($myfile, $txt);
		fclose($myfile);
		echo "\t \e[32m".'created services: '.$argsArray['args'][0] ."\033[0m  \n\r";
	}
	
	
	function makeView($argsArray) {
		
		if(strpos($argsArray['args'][0], '.'))
		{
			$parts = explode('.',$argsArray['args'][0]);
			$file = $parts[count($parts)-1].'.phtml';
			unset($parts[count($parts)-1]);
			$path = '';
			foreach($parts as $subfolder){
				$path .= 'app/views/'.$subfolder.'/';
			}
			$fullpath = $path.$file;
		}
		if(!is_dir($path)){
			mkdir($path, 0755, true);
		}
		
		$myfile = fopen($fullpath, "w") or die("Unable to open file!");
		$txt = "<?php";
		$txt .= "\r\t/**\r\t* Project: MVC2022.\r\t* Author:\tSome other programmer\r\t* Date:\t\t".date('d-m-Y')."\r\t* File:\t\t".$fullpath."\r\t*/\n?>\n";
		$txt .= "<h2>Some title</h2>\n<hr>\n";
		$txt .= "<div>\n\t<?= date('d-m-Y') ?>\n</div>\n";

		fwrite($myfile, $txt);
		fclose($myfile);
		echo "\t \e[32m".'created view-file: '.$fullpath."\033[0m  \n\r";
	}
	
	function makeLayout($argsArray) {
		
		if(strpos($argsArray['args'][0], '.'))
		{
			$parts = explode('.',$argsArray['args'][0]);
			$layoutName = $parts[count($parts)-1];

			unset($parts[count($parts)-1]);

			$path = 'app/layouts/';
			foreach($parts as $subfolder){
				$path .= $subfolder.'/';
			}
			$path .= $layoutName;
			$fullpath = $path.'/layout.phtml';
		}
		
		if(!is_dir($path)){
			mkdir($path, 0755, true);
		}
		$myfile = fopen($fullpath, "w") or die("Unable to open file!");
		$txt = "<?php";
		$txt .= "\r\t/**\r\t* Project: MVC2022.\r\t* Author:\tSome other programmer\r\t* Date:\t\t".date('d-m-Y')."\r\t* File:\t\t".$fullpath."\r\t*/\n?>\n";
		$txt .=
'<!DOCTYPE html>
 <html>
 	<head>
 		<title>Site-title</title>
 			<meta charset="<?= $meta->char_set ?>">
			<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
			<meta http-equiv="Content-Type" content="text/html; charset=<?= $meta->char_set ?>">
			<meta name="description" content="some description ">
			<meta name="keywords" content="MVC, app, teaching, learning">
			<meta http-equiv=”content-language” content="nl">
			<meta name="author" content="InCubics">
			<meta name="copyright" content="(c) <?= date(\'Y\').\'-\'.(date(\'Y\')+1)?>">
			<meta name="robots" content="noindex,nofollow">
 		<link src="../css/style.css" >
 		<style>body:{ background-color: silver;}</style>
	</head>
	<body>
	    <header>
	        <h1>SiteName</h1>
		</header>
		<nav>
		    <a href="#">Home</a>
		    <a href="#">Item1</a>
		    <a href="#">Item2</a>
		</nav>
		
		<article>
			<?= $this->view() ?>
		</article>
		<section id="sidebar">
		</section>
		<aside>
		</aside>
		
		<footer>
			<p class="copy">(c) <?= date(\'Y\').\'-\'.(date(\'Y\')+1)?> by Incubics </p>
		</footer>
	</body>
	<script></script>
</html>';

		fwrite($myfile, $txt);
		fclose($myfile);
		echo "\t \e[32m".'created layout-file: '.$fullpath."\033[0m  \n\r";
	}
