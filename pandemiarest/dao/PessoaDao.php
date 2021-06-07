<?php

namespace Pandemia\Dao;

use Pandemia\Model\PessoaModel;
use Pandemia\Utils\Persistence;

class PessoaDao extends Dao
{

	function __construct()
	{
		parent::__construct(new PessoaModel());
	}

	public function listPersonAndJobDao($cpf): array
	{
		$sql = "select j.id, p.name, p.cpf, j.type_job, j.status, j.created_at, j.dayD from person p
		inner join job as j on p.id = j.id_person
		where p.cpf = :cpf;";
		$stmt = Persistence::execute($sql, [':cpf' => $cpf]);

		return Persistence::fetchRow($stmt);
	}
}
