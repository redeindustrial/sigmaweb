<?php
define('SIGMAREST','NomeDaEmpresa');

require '_config.php';
require 'vendor/autoload.php';
require 'application/autoload.php';
//require 'application/config/tables/SS.php';


$conn = \Sigmarest\Config\Database::get();

/*
//print_r(PDO::getAvailableDrivers());die();
echo 'conn: ';
$db = new PDO('sqlsrv:Server=187.0.218.34;Database=NEWITALIAN','sa','Sigma2012');
$db->debug = true;
$qry = $db->prepare('select * from OS');
$qry->execute();

var_dump($qry->fetchAll());
die();
*/


// Configurações gerais
$app = new \Slim\Slim(array(
	'mode'  => 'development',
	'templates.path' => './templates',
	'log.enabled' => true
));

// Configurações de produção
$app->configureMode('production', function() use ($app) {
	$app->config(array(
		'debug' => false,
		'log.level' => \Slim\Log::WARN
	));
});

// Configurações de desenvolvimento
$app->configureMode('development', function() use ($app) {
	$app->config(array(
		'debug' => true,
		'log.level' => \Slim\Log::DEBUG
	));
});


$app->get('/hello/:name', function ($name) {
    echo "Hello, $name";
});

$app->get('/haut', function() use ($app) {
	$app->halt(403);
});

$app->get('/teste(/:mensagem)', function($mensagem = '') use ($app) {
	$app->render('template_teste.php',array(
		'mensagem' => $mensagem?$mensagem:'Sem mensagem'
	),200);
});

//$app->get('/os(/)', \Sigmarest\RouteMiddlewares::get('Auth'), function() use ($app,$conn) {
$app->get('/os(/)', function() use ($app,$conn) {
	$qryos = $conn->query('select * from OS');
	
	$app->render('default.php',array(
		'data' => $qryos->fetchAll()
	),200);
});

$app->get('/login', \Sigmarest\RouteMiddlewares::get('Auth'), function() use ($app) {
//$app->get('/login', function() use ($app) {
	$app->halt(200,'OK');
});










$app->get('/me/modulos', function() use ($app,$conn) {
	echo json_encode(array(
		'modulos' => array(
			'ss','css'
		)
	));
});


$app->get('/ss', function() use ($app,$conn) {
	
	/*
	die(json_encode(array(
		'data' => array(
			array('SS_CODIGO' => 1, 'OBSERVACAO' => 'Descri SS 1', 'APROVADO' => 'P'),
			array('SS_CODIGO' => 2, 'OBSERVACAO' => 'Descri SS 2', 'APROVADO' => 'S'),
			array('SS_CODIGO' => 3, 'OBSERVACAO' => 'Descri SS 3', 'APROVADO' => 'N'),
			array('SS_CODIGO' => 4, 'OBSERVACAO' => 'Descri SS 4', 'APROVADO' => ''),
			array('SS_CODIGO' => 5, 'OBSERVACAO' => 'Descri SS 5', 'APROVADO' => 'P'),
		)
	)));
	*/

	// Instanciar a tabela
	$SS = \Sigmarest\Config\Tables\SS::instance();
	// Verifica se os campos requisitados são válidos
	$fields = isset($_GET['fields'])?$_GET['fields']:'*';
	$fields_validation = $SS->validate_fields_names($fields);
	// Houve erro nos campos?
	if(is_array($fields_validation)) {
		die(json_encode($fields_validation));
	}
	
	//die(json_encode(array('validation'=>$fields_validation)));
	// Pega as SS's de acordo com os campos
	$qry1 = $conn->query('select '.$fields.' from SS');
	// Imprime os valores na tela
	$app->render('default.php',array(
		'data' => array(
			'data' => $qry1->fetchAll()
		)
	),200);
});

$app->get('/ss/forms/cadastro', function() use ($app,$conn) {
	$SS = \Sigmarest\Config\Tables\SS::instance();
	
	// Mostrar uma listinha bonitinha caso seja para um humano
	if(isset($_GET['human'])) {
		$campos = $SS->getFormConfig('FORMCADSS');
		$chtml = '';
		foreach($campos as $campo) {
			$req = ($campo['requerido']=='S')?'requerido':'';
			$vis = ($campo['visivel']=='S')?'visivel':'';
			$chtml .= $campo['tab_order'].' - '.$campo['name'].': '. $req . ' ' . $vis .'<br>';
		}
		die($chtml);
	}
	
	// Mostrar apenas 3 campos
	$tcampos = $SS->getFormConfig('FORMCADSS');
	while(count($tcampos)>3) {
		array_shift($tcampos);
	}
	
	$app->render('default.php',array(
		'data' => array(
			'data' => $tcampos
		)
	),200);die();
	
	$app->render('default.php',array(
		'data' => array(
			'data' => $SS->getFormConfig('FORMCADSS')
		)
	),200);
});

$app->post('/ss', function() use ($app,$conn) {
	$campos = array();
	
	foreach($_POST as $campo_nome => $campo_valor) {
		$campos[$campo_nome] = $campo_valor;
	}
	
	$SS = \Sigmarest\Config\Tables\SS::instance();
	$SS->validate($campos);
});

$app->get('/maquinas', function() use ($app,$conn) {
	$qry1 = $conn->query('select MAQ_CODIGO, MAQ_DESCRI from MAQUINA');
	/*
	$app->render('default.php',array(
		'data' => $qry1->fetchAll()
	),200);
	*/
	
	die(json_encode(array('data'=>$qry1->fetchAll())));
	
	die(json_encode(array(
		'data' => array(
			array('MAQ_CODIGO' => 1, 'MAQ_DESCRI' => 'Descri maquina 1'),
			array('MAQ_CODIGO' => 2, 'MAQ_DESCRI' => 'Descri maquina 2'),
			array('MAQ_CODIGO' => 3, 'MAQ_DESCRI' => 'Descri maquina 3'),
			array('MAQ_CODIGO' => 4, 'MAQ_DESCRI' => 'Descri maquina 4'),
			array('MAQ_CODIGO' => 5, 'MAQ_DESCRI' => 'Descri maquina 5'),
		)
	)));
});

// Rodar aplicação
$app->run();
