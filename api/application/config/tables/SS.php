<?php
namespace Sigmarest\Config\Tables;


class SS extends Validation_Base
{
	protected $table_name = 'SS';
	
	/*public function __construct() {
		$this->table_name = 'SS';
		
		$this->fields = array(
			array(
				'name'     => 'SS_CODIGO',
				'primary'  => true,
				'validate' => array('not_empty','integer')
			),
			
			array('name' => 'TAG_CODIGO'),
			array('name' => 'EQUI_CODIG'),
			array('name' => 'MAQ_CODIGO'),
			array('name' => 'SINT_CODIG'),
			array('name' => 'OS_CODIGO'),
			array('name' => 'OBSERVACAO'),
			array('name' => 'DATA'),
			array('name' => 'SOLICITANT'),
			array('name' => 'APROVADO'),
			array('name' => 'SS_HORAEMI'),
			array('name' => 'SERV_CODIG'),
			array('name' => 'AFETA_PROD'),
			array('name' => 'PRI_CODIGO'),
			array('name' => 'TIP_OS_COD'),
			array('name' => 'NEGOCIO'),
			array('name' => 'FILIAL_EXE'),
			array('name' => 'MOTIVO'),
			array('name' => 'AREA_CODIG'),
			array('name' => 'FUNCIONARIO'),
			array('name' => 'FUNCI_CODI'),
			array('name' => 'SET_CODIGO'),
			array('name' => 'PEND_CODIG'),
			array('name' => 'ATUALIZADO'),
			array('name' => 'DATA_EQU_DISP'),
			array('name' => 'HORA_EQU_DISP'),
			array('name' => 'VISUALIZADO_ALERTA'),
			array('name' => 'PRI_APROVACAO'),
			array('name' => 'PRIORIDADE'),
			array('name' => 'SS_DESCRIC'),
			array('name' => 'RETRABALHO'),
			array('name' => 'SER_P_CODI'),
			array('name' => 'SUB_TAG'),
			array('name' => 'DEP_CODIGO'),
			array('name' => 'CC_CODIGO'),
			array('name' => 'PROC_CODIG'),
			array('name' => 'CEL_CODIGO'),
			array('name' => 'AVALIADORSS'),
			array('name' => 'SERVICO_RISCO'),
			array('name' => 'PARADA_PARCIAL'),
		);
		
		die(var_dump($this->getvfields()));
	}*/
}



/*
$ss = SS::instance();
$ss2 = SS::instance();
die(var_dump($ss));
var_dump($ss->getFields());die();
var_dump($ss->validate(array('SS_CODIGO' => '', 'OMEUDEUS' => 'Campo invalido kkk')));
die();
*/
