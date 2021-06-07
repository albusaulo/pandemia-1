<?php

namespace Pandemia\Utils;

use DateTime;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Utils
{
	public static function getRoutePath(bool $main = false): string
	{
		$pathInfo = isset($_SERVER['PATH_INFO']) ? trim($_SERVER['PATH_INFO'], "/") : '';
		$pathInfo = explode("/", $pathInfo);

		if (!empty($pathInfo)) {
			if ($main) {
				return $pathInfo[0];
			} else {
				unset($pathInfo[0]);
				$pathInfo = implode("/", $pathInfo);
			}
		}

		return $pathInfo;
	}

	public static function getEnv(?string $varEnv = '')
	{
		$config = parse_ini_file(__DIR__ . '/../.config.ini');
		if (empty($varEnv)) {
			return $config;
		}
		return $config[$varEnv];
	}

	public static function dateTime($dataHora, $formatoEntrada = "d/m/Y H:i:s", $formatoSaida = "Y-m-d H:i:s")
	{
		$dataHoraDB = DateTime::createFromFormat($formatoSaida, $dataHora);
		if ($dataHoraDB) {
			if ($dataHoraDB->format($formatoSaida) !== $dataHora) {
				return false;
			}
			return $dataHoraDB->format($formatoSaida);
		}

		$dataHoraDB = DateTime::createFromFormat($formatoEntrada, $dataHora);

		if (!$dataHoraDB) {
			return false;
		}

		if ($dataHoraDB->format($formatoEntrada) !== $dataHora) {
			return false;
		}

		return $dataHoraDB->format($formatoSaida);
	}

	public static function validateEmail($email = null)
	{
		if (!$email) {
			return false;
		}
		if (strlen($email) > 255) {
			return false;
		}
		return preg_match('/^(?:[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+\.)*[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+@(?:(?:(?:[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!\.)){0,61}[a-zA-Z0-9_-]?\.)+[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!$)){0,61}[a-zA-Z0-9_]?)|(?:\[(?:(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\]))$/', $email);
	}
}
