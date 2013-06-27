<?php
namespace Sigmarest\Config;


class Database
{
	static private $dsn  = 'sqlsrv:Server=187.0.218.34;Database=NEWITALIAN';
	static private $user = 'sa';
	static private $pass = 'Sigma2012';
	static private $debug = true;
	
	static private $_conexao = false;
	
	static public function get()
	{
		return \Sigma\Configs::instance()->db();
		
		
		// Código antigo, ser retirado
		if(!self::$_conexao) {
			self::$_conexao = new \PDO(self::$dsn, self::$user, self::$pass,array(
				\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
				\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
			));
			self::$_conexao->debug = self::$debug;
		}
		
		return self::$_conexao;
	}
}

