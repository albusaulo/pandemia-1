<?php

namespace Pandemia;

require_once('vendor/autoload.php');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: HEAD, GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Access-Control-Request-Headers, Authorization');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Max-Age: 3600');
if (in_array($_SERVER['REQUEST_METHOD'], ['HEAD', 'OPTIONS'])) {
	exit();
}

use Pandemia\Utils\Utils;

class Pandemia
{
	function exception_handler($ex)
	{
		error_log($ex->getMessage()); // LOGAR NO BANCO DE DADOS
		http_response_code(500);
		exit(json_encode(['error' => 'Falha inesperada no sistema, por favor, entre em contato com o suporte.']));
	}

	public function start(): void
	{
		set_exception_handler(array($this, 'exception_handler'));
		$path = Utils::getRoutePath(true);
		$routes = (new Routes())->getRoutes();

		if (array_key_exists($path, $routes) && !empty($routes[$path])) {
			$routes[$path]->init();
		} else {
			http_response_code(404);
			exit(json_encode(['error' => "Requisição inválida, não encontrada."]));
		}
	}
}

(new Pandemia())->start();
