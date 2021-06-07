<?php

namespace Pandemia\Model;

use Pandemia\Model\Model;
use Pandemia\Model\ModelInterface;

class PessoaModel extends Model implements ModelInterface {

	function __construct() {
		parent::__construct('person', [
			'id' => 'type:int|notEmpty',
			'name' => 'type:string|max:255',
			'cpf' => 'type:cpfCnpj|max:14'
		]);
	}

	function customValidate($data): array {
		return $this->validate($data);
	}

}