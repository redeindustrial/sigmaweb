<?php

function sigmarest_autoload($classe) {
	//die($classe);
	$classe = strtolower(ltrim($classe,'\\'));
	$dirs   = explode('\\',$classe);
	
	if(array_shift($dirs) == 'sigmarest') {
		$caminho = '';
		$caminho = realpath(__DIR__ . '/' . implode('/',$dirs) . '.php');
		
		if(isset($_GET['autoload_debug'])) {
			echo $caminho.'<br><br>';
		}
		
		if($caminho) {
			require($caminho);
		}
	}
}

spl_autoload_register('sigmarest_autoload');
// "99designs/pheasant-adodb": "dev-master"