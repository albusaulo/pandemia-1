<?php

namespace Pandemia\Model;

use Pandemia\Model\Model;
use Pandemia\Model\ModelInterface;

class JobModel extends Model implements ModelInterface {

	function __construct() {
		parent::__construct('job', [
			'id' => 'type:int|notEmpty',
			'type_job' => 'type:string|max:10|notEmpty',
			'status' => 'type:string|max:20',
			'id_person' => 'type:int|notEmpty',
			'created_at' => 'type:datetime',
			'updated_at' => 'type:date',
			'dayD' => 'date'
		]);
	}

	function customValidate($data): array {
		return $this->validate($data);
	}

}