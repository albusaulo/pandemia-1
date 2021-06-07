<?php

namespace Pandemia\Model;

use PDO;
use Pandemia\Utils\Persistence;
use Pandemia\Utils\Utils;

abstract class Model {
	private $table;
	private $columns;

	public function __construct(string $table, array $columns) {
		$this->table = $table;
		$this->columns = $columns;
	}

	/**
	 * Validação genérica do modelo de dados
	 *
	 * type: bool|int|float|string|time|date|datetime|email|cpfCnpj|cep
	 * validation: notEmpty|min=NUMBER|max=NUMBER|unique|uniqueCompany
	*/
	public function validate($data): array {
		$errors = [];

		if (!is_array($data)) {
			$errors['dataError'] = 'Os dados da requisição não foram informados, ou estão incorretos.';
			return $errors;
		}

		$keysNotFound = array_diff(array_keys($data), array_keys($this->columns));
		if (!empty($keysNotFound)) {
			$errors['keysNotFound'] = array_values($keysNotFound);
			return $errors;
		}

		foreach ($this->columns as $key => $value) {
			$typeValidation = $this->getTypeValidation($value);

			if (array_key_exists($key, $data)) {
				if (!$this->validateType($typeValidation['type'], $data[$key])) {
					$errors[$key][] = 'O tipo do registro deve ser ' . $typeValidation['type'] . '.';
					return $errors;
				}

				if (!$this->validateMin($typeValidation['type'], $typeValidation['min'] ?? null, $data[$key])) {
					$errors[$key][] = 'O valor mínimo deve ser ' . $typeValidation['min'] . '.';
				}

				if (!$this->validateMax($typeValidation['type'], $typeValidation['max'] ?? null, $data[$key])) {
					$errors[$key][] = 'O valor máximo deve ser ' . $typeValidation['max'] . '.';
				}

				if (!$this->validateNotEmpty($typeValidation['notEmpty'] ?? null, $data[$key])) {
					$errors[$key][] = 'O valor deve ser informado.';
				}

				if (!$this->validateUnique($typeValidation['unique'] ?? null, (isset($data['id']) ? intval($data['id']) : 0), $key, $data[$key])) {
					$errors[$key][] = 'O valor deve ser único, já existe este registro cadastrado.';
				}
			} else if (isset($typeValidation['notEmpty'])) {
				$errors[$key][] = 'O campo é obrigatório.';
			}
		}

		return $errors;
	}

	private function getTypeValidation($typeValidation): array {
		$result = [];

		$typeValidation = explode('|', $typeValidation);
		foreach ($typeValidation as $value) {
			if (in_array(trim($value), ['notEmpty', 'unique', 'uniqueCompany'])) {
				$result[trim($value)] = 'true';
			} else {
				$value = explode(':', $value);
				$result[trim($value[0])] = $value[1];
			}
		}

		return $result;
	}

	private function validateType($typeValue, $dataValue): bool {
		if (is_null($dataValue)) {
			return true;
		}

		switch (trim($typeValue)) {
			case 'bool':
				return is_bool($dataValue);
				break;
			case 'int':
				return is_int($dataValue);
				break;
			case 'float':
				return is_float($dataValue);
				break;
			case 'string':
				return is_string($dataValue);
				break;
			case 'time':
				return is_string($dataValue) && Utils::dateTime($dataValue, "H:i:s", "H:i:s");
				break;
			case 'date':
				return is_string($dataValue) && Utils::dateTime($dataValue, "Y-m-d", "Y-m-d");
				break;
			case 'datetime':
				return is_string($dataValue) && Utils::dateTime($dataValue, "Y-m-d H:i:s");
				break;
			case 'email':
				return is_string($dataValue) && (empty($dataValue) || Utils::validateEmail($dataValue));
				break;
			case 'cpfCnpj':
				return is_string($dataValue) && (in_array(strlen($dataValue), [11, 14]));
				break;
			case 'cep':
				return is_string($dataValue) && (strlen($dataValue) == 8);
				break;
			default:
				return false;
				break;
		}
	}

	private function validateMin($typeValue, $minValue, $dataValue): bool {
		if (is_null($minValue) || empty($dataValue)) {
			return true;
		} else if (trim($typeValue) == 'int') {
			$minValue = (int) $minValue;
			return $minValue <= $dataValue;
		} else if (trim($typeValue) == 'float') {
			$minValue = (float) $minValue;
			return $minValue <= $dataValue;
		} else if (trim($typeValue) == 'string') {
			$minValue = (int) $minValue;
			return $minValue <= strlen($dataValue);
		} else {
			return true;
		}
	}

	private function validateMax($typeValue, $maxValue, $dataValue): bool {
		if (is_null($maxValue) || empty($dataValue)) {
			return true;
		} if (trim($typeValue) == 'int') {
			$maxValue = (int) $maxValue;
			return $maxValue >= $dataValue;
		} else if (trim($typeValue) == 'float') {
			$maxValue = (float) $maxValue;
			return $maxValue >= $dataValue;
		} else if (trim($typeValue) == 'string') {
			$maxValue = (int) $maxValue;
			return $maxValue >= strlen($dataValue);
		} else {
			return true;
		}
	}

	private function validateNotEmpty($notEmptyValue, $dataValue): bool {
		if (is_null($notEmptyValue) || (trim($notEmptyValue) == 'false')) {
			return true;
		} else {
			return !empty($dataValue);
		}
	}

	private function validateUnique($uniqueValue, $id, $dataKey, $dataValue, bool $uniqueCompany = false): bool {
		if (is_null($uniqueValue) || (trim($uniqueValue) == 'false')) {
			return true;
		} else {
			$sql = 'select ' . $dataKey . ' from ' . $this->getTable() .
					' where ' . Persistence::criteria($dataKey, '=', $dataValue) .
					' and id <> ' . $id;

			$stmt = Persistence::execute($sql);
			$result = $stmt->fetch(PDO::FETCH_ASSOC);

			return empty($result);
		}
	}

	public function getTable(): string {
		return $this->table;
	}

	public function getColumns(): array {
		return $this->columns;
	}
}