<?php
namespace Sigmarest;

class RouteMiddlewareDoesNotExist extends \Exception { }

/**
 * Classe que inclui um route middleware
 *
 * Exemplo de uso:
 *
 * ----- index.php -----
 * -- $app->get('/foo', 
 * --     \Sigmarest\RouteMiddlewares::get('Foo'), // <=== Route Middleware Foo
 * --     function() use ($app) { ... });
 *
 * ----- application/routemiddlewares/foo.php -----
 * -- namespace \Sigmarest\RouteMiddlewares;
 * --
 * -- class Foo {
 * --     static public function run() { [> «Código Aqui» <] }
 * -- }
 *
 * @class \Sigmarest\RouteMiddlewares
 */
class RouteMiddlewares
{
	private function __construct() { }
	
	static public function get($middleware)
	{
		$class_name = '\\Sigmarest\\RouteMiddlewares\\'.$middleware;
		if(!class_exists($class_name,false)) {
			$path = realpath(__DIR__ . '/routemiddlewares/' . strtolower($middleware) . '.php');
			
			if(!$path) {
				throw new RouteMiddlewareDoesNotExist('O Route Middleware \"'.$middleware.'\" não existe.');
			}
			
			require $path;
		}
		
		return $class_name.'::run';
	}
}
