<?php

namespace Pandemia\Utils;

use PDO;
use Exception;
use PDOException;
use PDOStatement;

class Persistence
{
	private function __construct()
	{
	}

	private static $instanceConnection;

	public static function getConnection()
	{
		if (!isset(SELF::$instanceConnection)) {
			try {
				SELF::$instanceConnection = new PDO(
					'mysql:host=' . Utils::getEnv('main_host') . ';port=' . Utils::getEnv('main_port') . ';dbname=' . Utils::getEnv('main_dbname'),
					Utils::getEnv('main_username'),
					Utils::getEnv('main_passwd'),
					[
						PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
					]
				);
			} catch (PDOException $e) {
				error_log($e->getMessage());
				throw new Exception('Falha ao tentar se conectar com o banco de dados, por favor, contate o suporte.');
			}
		}

		return SELF::$instanceConnection;
	}

	public static function transactionBegin(): void
	{
		SELF::getConnection()->prepare('begin;')->execute();
	}

	public static function transactionCommit(): void
	{
		SELF::getConnection()->prepare('commit;')->execute();
	}

	public static function transactionRollback(): void
	{
		SELF::getConnection()->prepare('rollback;')->execute();
	}

	public static function execute(string $sql, array $params = []): PDOStatement
	{
		$stmt = SELF::getConnection()->prepare($sql);
		$stmt->execute($params);

		return $stmt;
	}

	public static function fetchData(PDOStatement $stmt, $fetch = PDO::FETCH_ASSOC): array
	{
		$result = [];
		while ($row = $stmt->fetch($fetch)) {
			$result[] = $row;
		}

		return $result;
	}

	public static function fetchRow(PDOStatement $stmt, $fetch = PDO::FETCH_ASSOC): array
	{
		if ($result = $stmt->fetch($fetch)) {
			return $result;
		}

		return [];
	}

	public static function criteria($column, $operator, $value): string
	{
		if (isset($value)) {
			$value = is_string($value) ? "'" . pg_escape_string(null, $value) . "'" : $value;
			return " unaccent(lower({$column})) {$operator} unaccent(lower({$value})) ";
		} else {
			return " {$column} is null ";
		}
	}
}
