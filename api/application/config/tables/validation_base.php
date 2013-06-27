<?php
namespace Sigmarest\Config\Tables;


class Validation_Base
{
	static protected $instances = array();
	
	/**
	 * Nome da tabela
	 */
	protected $table_name;
	
	/**
	 * Campos da tabela PRECISAM estar cadastrados aqui
	 */
	protected $fields;
	
	public function __construct() {
		$this->fields = $this->getFields();
	}
	
	
	/**
	 * Singleton
	 */
	static public function instance() {
		$c = get_called_class();
		
		if(!isset(self::$instances[$c])) {
			$args = func_get_args();
			$reflection_object = new \ReflectionClass($c);
			self::$instances[$c] = $reflection_object->newInstanceArgs($args);
		}
		return self::$instances[$c];
	}
	
	public function __clone() {
		throw new \Exception('You can not clone a singleton.');
	}
	
	/**
	 * Verifica se o campo existe ($this->fields). Por padrão, pesquisa pelo
	 * nome do campo.
	 *
	 * @method getField
	 * @param (string) $field_name  Nome do campo que você quer pegar
	 *                              ou verificar se existe
	 * @param (string) $field_key   Você quer pesquisar pelo NOME do campo?
	 * @return (boolean|array)      false ou array contendo as configurações do campo
	 */
	protected function getField($field_name, $field_key = 'name') {
		foreach($this->fields as $field) {
			//echo '<br>comp:'.strtolower($field[$field_key]) . ' === ' . strtolower($field_name);
			if(strtolower($field[$field_key]) === strtolower($field_name))
				return $field;
		}
		
		return false;
	}
	
	/**
	 * Recebe um array associativo (campo => valor) e verifica se os
	 * dados estão prontos para serem inseridos no banco de dados
	 *
	 * @method validate
	 * @param (array) $campos  Campos e valores para validar
	 * @return (bool|array)    True ou um array associativo com descrição do erro
	 */
	public function validate($campos) {
		if($campos=='*') return true;
		
		foreach($campos as $ckey => $cval) {
			$field = $this->getField($ckey);
			//echo 'aqui '.$ckey.': ';var_dump($field);die();
			// Verificar se o campo existe
			if(!$field) {
				return array(
					'valid' => false,
					'field' => $ckey,
					'error' => 'field_doesnot_exist',
					'error_description' => 'O campo \''.$ckey.'\' não existe na tabela ' . $this->table_name,
				);
			}
			
			// Efetuar validações necessárias
			if(isset($field['validate'])) {
				foreach($field['validate'] as $validate) {
					$validate_method = 'validate_'.$validate;
					
					// Validação não existe
					if(!method_exists($this,$validate_method)) {
						return array(
							'valid' => false,
							'field' => $ckey,
							'error' => 'invalid_validation_method',
							'error_description' => 'A validação \''.$validate.'\' não existe na classe ' . $this->table_name . ' // Campo: ' . $ckey,
						);
					}
					
					$validada = $this->$validate_method($field,$cval);
					// Se for array significa que deu algo errado
					// Deu certo: true
					// Deu errado: array(error_name, error_description)
					if(is_array($validada)) {
						return array(
							'valid' => false,
							'field' => $ckey,
							'error' => $validada[0],
							'error_description' => $validada[1]
						);
					}
				}
			}
		}
		
		return true;
	}
	
	/**
	 * [Validação] Verifica se um campo está vazio
	 *
	 * @method validate_not_empty
	 * @param (array) $field    Array com as configurações do campo
	 * @param (string) $val     Valor do campo
	 * @return (boolean|array)  true ou array com dados sobre o erro
	 *                          array(error_name, error_description)
	 */
	protected function validate_not_empty($field, $val) {
		if(!$val || !trim($val)) return array('not_empty', 'O campo '.$field['name'].' não pode ficar em branco');
		return true;
	}
	
	/**
	 * [Validação] Verifica se um campo foi preenchido apenas com números
	 *
	 * @method validate_integer
	 * @param (array) $field    Array com as configurações do campo
	 * @param (string) $val     Valor do campo
	 * @return (boolean|array)  true ou array com dados sobre o erro
	 *                          array(error_name, error_description)
	 */
	protected function validate_integer($field, $val) {
		if(preg_match('/[^\d].+/',$val)) return array('integer', 'O campo '.$field['name'].' não contém apenas números');
		return true;
	}
	
	/**
	 * Valida o nome dos campos
	 *
	 * @method validate_fields_names
	 * @param (string|array) $fields  Campos a seres validados
	 * @return (true|array)           true ou Array com a descrição do erro
	 */
	public function validate_fields_names($fields) {
		if($fields=='*') return true;
		$fields_arr = array();
		
		// Formar uma array com os campos
		if(is_string($fields)) {
			$fields_arr = explode(",",$fields);
		} elseif(is_array($fields)) {
			$fields_arr = $fields;
		}
		
		// Verifica se há pelomenos um campo
		if(!count($fields_arr)) {
			return array(
				'error' => 'invalid_fields_names',
				'description' => 'Os campos passados como argumento são inválidos. | '.$fields,
			);
		}
		
		// Verifica se o campo é uma string válida
		foreach($fields_arr as $field) {
			if(!is_string($field) || !preg_match('/^[\w]+$/',$field) || !strlen(trim($field))) {
				return array(
					'error'   => 'invalid_field',
					'field'   => $field,
					'description' => 'O campo `'.$field.'` é inválido'
				);
			}
			
			if(!$this->getField($field)) {
				return array(
					'error'   => 'field_not_exists',
					'field'   => $field,
					'description' => 'O campo `'.$field.'` não existe nesta tabela'
				);
			}
		}
		
		// Retorna true
		return true;
	}
	
	/**
	 * Pega os campos da tabela ($this->table_name) e verifica se ele é visível ou requerido
	 *
	 * @method getFields
	 * @return (array)
	 */
	private function getFields() {
		// Pegar nome dos campos
		$conn = \Sigmarest\Config\Database::get();
		$query = $conn->query("SELECT c.NAME FROM sys.columns c WHERE c.object_id = OBJECT_ID('{$this->table_name}')");
		$campos = $query->fetchAll(\PDO::FETCH_ASSOC);
		
		return $campos;
		
		// Pegar dados de cada campo
		$query2 = $conn->prepare('select top 1 nomelabel, visivel, requerido from camposreq where tela = :tela and nomelabel = :nome order by TAB_ORDER asc');
		foreach($campos as &$campo) {
			//$campo = $campo['NAME'];
			$campo = array('name' => $campo['NAME']);
			
			$query2->execute(array(':tela' => 'FORMCADSS', ':nome' => $campo['name']));
			$campo_db = $query2->fetchAll();
			
			if(count($campo_db)) {
				$campo = array_merge($campo,array(
					'requerido' => $campo_db[0]['requerido'],
					'visivel'   => $campo_db[0]['visivel'],
				));
			} else {
				$campo = array_merge($campo,array(
					'requerido' => 'N',
					'visivel' => 'N',
				));
			}
		}
		
		return $campos;
	}
	
	/**
	 * Pega uma lista com os campos da tabela que podem ser visíveis e/ou requeridos
	 *
	 * @method getFormConfig
	 * @param (string) $form_name  Nome da "TELA" usado na tabela CAMPOSREQ
	 * @return (array)             Array com nome e configurações dos campos
	 */
	public function getFormConfig($form_name) {
		$campos = $this->fields;
		// Pegar dados de cada campo
		$query2 = \Sigmarest\Config\Database::get()->prepare(
			'select top 1 nomelabel, visivel, requerido, tab_order from camposreq where tela = :tela and nomelabel = :nome order by TAB_ORDER asc'
		);
		foreach($campos as $ckey => &$campo) {
			//$campo = $campo['NAME'];
			$campo = array('name' => $campo['NAME']);
			
			$query2->execute(array(':tela' => $form_name, ':nome' => $campo['name']));
			$campo_db = $query2->fetchAll();
			
			// Configurações dos campos
			if(count($campo_db)) {
				$campo = array_merge($campo,array(
					'requerido' => $campo_db[0]['requerido'],
					'visivel'   => $campo_db[0]['visivel'],
					//'value'     => '', // Implementação futura: Valor padrão dos campos (uma função pega o padrão)
					//'label'     => '', // Implementação futura: Texto para a label do campo
					'tab_order' => (int) $campo_db[0]['tab_order'],
				));
			} else {
				$campo = array_merge($campo,array(
					'requerido' => 'N',
					'visivel'   => 'N',
					//'value'     => '', // Implementação futura: Valor padrão dos campos (uma função pega o padrão)
					//'label'     => '', // Implementação futura: Texto para a label do campo
					'tab_order' => 999,
				));
			}
			
			// Retirar os campos que não precisam ser visíveis
			if($campo['requerido']==='N' && $campo['visivel']==='N') {
				unset($campos[$ckey]);
			}
		}
		$campos = array_values($campos);
		$this->organizarCamposTabOrdem($campos);
		return $campos;
	}
	
	/**
	 * Organiza a array de campos da tabela
	 *
	 * @method organizarCamposTabOrdem
	 * @param (array) &$campos
	 */
	private function organizarCamposTabOrdem(&$campos) {
		usort($campos,function($first,$second) {
			return $first['tab_order'] - $second['tab_order'];
		});
	}
}
