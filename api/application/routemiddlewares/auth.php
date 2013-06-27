<?php
namespace Sigmarest\RouteMiddlewares;

/**
 * Slim Route Middleware que valida login do usuário
 *
 * Exemplo de uso:
 *
 * ----- index.php -----
 * -- $app->get('/foo', 
 * --     \Sigmarest\RouteMiddlewares::get('Auth'), // <=== Route Middleware
 * --     function() use ($app) { ... });
 *
 * @class \Sigmarest\RouteMiddlewares\Auth
 */
class Auth
{
	static public function run_test()
	{
		//$app = \Slim\Slim::getInstance();
		if(!isset($_GET['user'],$_GET['password'])) {
			die('login required');
		}
		
		$login = array('user' => $_GET['user'], 'pass' => $_GET['password']);
		
		if($login['user']=='admin' && $login['pass']=='admin') {
			return true;
		} else {
			die('login error');
		}
	}
	
	static public function run()
	{
		$username = null;
		$password = null;
		
		// Preenche usuário e password
		if(isset($_SERVER['PHP_AUTH_USER'])) {
			$username = $_SERVER['PHP_AUTH_USER'];
			$password = $_SERVER['PHP_AUTH_PW'];
		} elseif(isset($_SERVER['HTTP_AUTHENTICATION'])) {
			if (strpos(strtolower($_SERVER['HTTP_AUTHENTICATION']),'basic')===0)
			list($username,$password) = explode(':',base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
		}
		
		// Debug pra mostrar que usuário e senha está sendo usado
		if(isset($_GET['auth_debug'])) {
			print_r(array(
				array(isset($_SERVER['PHP_AUTH_USER'])?$_SERVER['PHP_AUTH_USER']:'',isset($_SERVER['PHP_AUTH_PW'])?$_SERVER['PHP_AUTH_PW']:''),
				isset($_SERVER['HTTP_AUTHENTICATION'])?$_SERVER['HTTP_AUTHENTICATION']:'NO_HTTP_AUTHENTICATION'
			));die();
		}
		
		// Login não foi preenchido corretamente ou não existe
		if (is_null($username)) {
			header('WWW-Authenticate: Basic realm="Sigma Rest API"');
			header('HTTP/1.0 401 Unauthorized');
			echo 'Falha na autenticação';
			die();
		}
		
		// Preenchido corretamente...
		
		// Verificar se usuário e senha estão certos
		if($username=='admin' && $password=='admin') {
			return true;
		}
		
		// Senha errada
		die('Usuário e/ou senha inválidos');
	}
}
