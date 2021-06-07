<?php

namespace Pandemia\Dao;

use Pandemia\Model\JobModel;
use Pandemia\Utils\Persistence;

class JobDao extends Dao
{

	function __construct()
	{
		parent::__construct(new JobModel());
	}


	public function listJobDaoPendentes($type): array
	{
		$sql = "select count(*) from job where type_job = :type and status = :status;";
		$stmt = Persistence::execute($sql, [':type' => $type, ':status' => "pendente"]);

		return Persistence::fetchRow($stmt);
	}

	public function listJobDaoRecebido($type) {
		$sql = "select count(*) from job where type_job = :type and status = :status;";
		$stmt = Persistence::execute($sql, [':type' => $type, ':status' => "recebido"]);

		return Persistence::fetchRow($stmt);
	}

	public function listJobDaoCancelado($type) {
		$sql = "select count(*) from job where type_job = :type and status = :status;";
		$stmt = Persistence::execute($sql, [':type' => $type, ':status' => "cancelado"]);

		return Persistence::fetchRow($stmt);
	}

}
