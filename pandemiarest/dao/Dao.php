<?php

namespace Pandemia\Dao;

use DateTime;
use PDO;
use Exception;
use Pandemia\Utils\Persistence;

abstract class Dao {

	private $table;

	public function __construct($model) {
		$this->table = $model->getTable();
	}

	/**
	 *	Se quiser receber os dados como objetos basta usar PDO::FETCH_OBJ na variavel $fetch
	*/
	public function readByKeys($keys = [], $columns = [], $fetch = PDO::FETCH_ASSOC): array {
		$sql = 'select ';
		if ($columns) {
			$sql .= implode(', ', $columns);
		} else {
			$sql .= '*';
		}
		$sql .= ' from ' . $this->table;
		$sql .= ' where 0 = 0';

		$paramsValues = [];
		foreach ($keys as $key => $value) {
			$sql .= ' and ' . $key . ' = ?';
			array_push($paramsValues, $value);
		}
		$sql .= ';';

		$stmt = Persistence::execute($sql, $paramsValues);
		return Persistence::fetchData($stmt, $fetch);
	}

	public function create($data, $created_at = true, $updated_at = true): int {
		unset($data['id']);

		$createdData = (new DateTime())->format('Y-m-d H:i:s');
		if ($created_at) {
			$data['created_at'] = $createdData;
		}

		if ($updated_at) {
			$data['updated_at'] = $createdData;
		}

		$keys = array_keys($data);
		$values = array_values($data);

		$sql = 'insert into ';
		$sql .= $this->table;
		$sql .= ' (';
		$sql .= implode(',', $keys);
		$sql .= ') values (';

		$params = '';
		for ($i=0; $i < count($values); $i++) {
			$params .= '?,';
			if (is_bool($values[$i])) {
				$values[$i] = $values[$i] ? 1 : 0;
			}
		}
		$sql .= substr($params, 0, -1);
		$sql .= ');';

		$stmt = Persistence::getConnection()->prepare($sql);
		$result = $stmt->execute($values);

		if ($result) {
			return Persistence::getConnection()->lastInsertId();
		}

		return 0;
	}

	public function update($data, $updated_at = true): int {
		if (empty($data['id'])) {
			throw new Exception('Atualização de dados sem a chave primária.');
			return 0;
		}

		$ids = $data['id'];
		unset($data['id']);

		$sql = 'update ';
		$sql .= $this->table;
		$sql .= ' set ';
		$values = [];
		$params = '';

		if ($updated_at) {
			$data['updated_at'] = (new DateTime())->format('Y-m-d H:i:s');
		}
		foreach ($data as $key => $value) {
			$params .= $key . '=?,';
			if (is_bool($value)) {
				$value = $value ? 1 : 0;
			}
			array_push($values, $value);
		}
		$sql .= substr($params, 0, -1);
		$sql .= ' where 0 = 0';

		if (!is_array($ids)) {
			$sql .= ' and id = ?;';
			array_push($values, $ids);
		} else {
			$sql .= ' and id in (?);';
			array_push($values, implode(',', $ids));
		}

		$stmt = Persistence::getConnection()->prepare($sql);
		$result = $stmt->execute($values);

		if ($result) {
			return $stmt->rowCount();
		}

		return 0;
	}

	public function delete($ids): int {
		if (empty($ids)) {
			throw new Exception('Exclusão de dados sem a chave primária.');
			return 0;
		}

		$sql = 'delete from ';
		$sql .= $this->table;
		$sql .= ' where 0 = 0';

		$values = [];
		if (!is_array($ids)) {
			$sql .= ' and id = ?;';
			array_push($values, $ids);
		} else {
			$sql .= ' and id in (?);';
			array_push($values, implode(',', $ids));
		}

		$stmt = Persistence::getConnection()->prepare($sql);
		$result = $stmt->execute($values);

		if ($result) {
			return $stmt->rowCount();
		}

		return 0;
	}
}