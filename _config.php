<?php
namespace Sigma;



class Configs
{
	static private $_instance = false;
	static private $_db = false;
	
	public $database = array(
		'driver' => 'sqlsrv',
		'host' => '187.0.218.34',
		'user' => 'sa',
		'pass' => 'Sigma2012',
		'debug' => true,
		'name' => 'NEWITALIAN',
		'dsn' => '{driver}:Server={host};Database={name};',
	);
	
	private function __construct() { }
	
	/**
	 * Singleton
	 */
	public static function instance() {
		if(!self::$_instance) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * Pega informações do banco de dados e atualiza dinamicamente o DSN
	 * Obs.: pega da variável $this->database que precisa ter os seguintes
	 *       campos: [driver,host,user,pass,debug,name,dsn]
	 *
	 * @method getDatabaseInfo
	 * @return (array)
	 */
	private function getDatabaseInfo() {
		$dbinfo = $this->database;
		
		foreach($dbinfo as $kinfo => &$info) {
			if($kinfo=='dsn') continue;
			$dbinfo['dsn'] = str_replace('{'.$kinfo.'}',$info,$dbinfo['dsn']);
		}
		
		return $dbinfo;
	}
	
	/**
	 * Retorna uma conexão PDO com o banco de dados
	 *
	 * @method db
	 * @return (\PDO)
	 */
	public function db() {
		if(!self::$_db) {
			$dbinfo = $this->getDatabaseInfo();
			self::$_db = new \PDO($dbinfo['dsn'], $dbinfo['user'], $dbinfo['pass'],array(
				\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
				\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
			));
			self::$_db->debug = $dbinfo['debug'];
		}
		return self::$_db;
	}
}
