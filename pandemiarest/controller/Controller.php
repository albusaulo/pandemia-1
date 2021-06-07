<?php

namespace Pandemia\Controller;

use DateTime;
use Pandemia\Utils\Utils;
use Pandemia\Utils\Session;

class Controller
{

	private $payload = [];
	private $params;
	private $get = [];
	private $post = [];
	private $put = [];
	private $patch = [];
	private $delete = [];

	protected function get($path = null, $funcao): void
	{
		if ($path && $funcao) {
			$this->get[trim($path, '/')] = $funcao;
		}
	}

	protected function post($path = null, $funcao): void
	{
		if ($path && $funcao) {
			$this->post[trim($path, '/')] = $funcao;
		}
	}

	protected function put($path = null, $funcao): void
	{
		if ($path && $funcao) {
			$this->put[trim($path, '/')] = $funcao;
		}
	}

	protected function patch($path = null, $funcao): void
	{
		if ($path && $funcao) {
			$this->patch[trim($path, '/')] = $funcao;
		}
	}

	protected function delete($path = null, $funcao): void
	{
		if ($path && $funcao) {
			$this->delete[trim($path, '/')] = $funcao;
		}
	}

	private function initRequest(): array
	{
		$params['path'] = Utils::getRoutePath();
		$params['query'] = [];
		parse_str($_SERVER['QUERY_STRING'], $params['query']);
		$params['headers'] = apache_request_headers();
		$params['method'] = strtoupper($_SERVER['REQUEST_METHOD']);
		$params['data'] = json_decode(file_get_contents('php://input'), true);

		$this->payload = Session::load($params['headers']['Authorization'] ?? null);

		return $params;
	}

	protected function hasPermission(?string $currentPermission): void
	{
		if (!empty($this->payload['exp']) && (new DateTime() > new DateTime($this->payload['exp']))) {
			$this->response(403, ["error" => "JWT expirado."]);
		}

		if (empty(Session::getPermissions())) {
			$this->response(401);
		} else if (!empty($currentPermission) && !Session::getPermissions()[$currentPermission]) {
			$this->response(403);
		}
	}

	protected function response(int $code = 200, ?array $array = []): void
	{
		if (empty($array)) {
			switch ($code) {
				case 400:
					$array = ["error" => "Por favor, verifique os dados informados."];
					break;
				case 401:
					$array = ["error" => "Dados de acesso incorretos."];
					break;
				case 403:
					$array = ["error" => "Você não tem permissão para acessar este módulo/recurso."];
					break;
				case 404:
					$array = ["error" => "Requisição inválida, não encontrada."];
					break;
				case 500:
					$array = ["error" => "Falha inesperada no sistema, por favor, entre em contato com o suporte."];
					break;
				default:
					break;
			}
		}

		http_response_code($code);
		exit(json_encode($array));
	}

	private function getRequest($key): array
	{
		$request = ['headers' => $this->params['headers'], 'pathParam' => [], 'queryParam' => $this->params['query'], 'data' => $this->params['data']];
		$paths = explode("/", $this->params['path']);

		if (is_array($paths)) {
			$keys = explode("/", $key);
			foreach ($paths as $index => $path) {
				if ($keys[$index] === ":id") {
					if (!isset($request['pathParam']['id'])) {
						$request['pathParam']['id'] = $path;
					} else {
						if (!is_array($request['pathParam']['id'])) {
							$request['pathParam']['id'] = (array) $request['pathParam']['id'];
							$request['pathParam']['id'][] = $path;
						} else {
							$request['pathParam']['id'][] = $path;
						}
					}
				} else if ($keys[$index] === ":code") {
					if (!isset($request['pathParam']['code'])) {
						$request['pathParam']['code'] = $path;
					} else {
						if (!is_array($request['pathParam']['code'])) {
							$request['pathParam']['code'] = (array) $request['pathParam']['code'];
							$request['pathParam']['code'][] = $path;
						} else {
							$request['pathParam']['code'][] = $path;
						}
					}
				}
			}
		}

		return $request;
	}

	protected function run(): void
	{
		$this->params = $this->initRequest();
		$hasPath = false;
		switch ($this->params['method']) {
			case 'POST':
				foreach ($this->post as $key => $value) {
					$keyReplaced = "/^" . str_replace([":id", ":code", "/"], ["([0-9]+)", "([a-zA-Z0-9]+)", "\\/"], $key) . "$/";
					if (preg_match($keyReplaced, $this->params['path'])) {
						$value($this->getRequest($key));
						$hasPath = true;
						break;
					}
				}
				break;
			case 'GET':
				foreach ($this->get as $key => $value) {
					$keyReplaced = "/^" . str_replace([":id", ":code", "/"], ["([0-9]+)", "([a-zA-Z0-9]+)", "\\/"], $key) . "$/";
					if (preg_match($keyReplaced, $this->params['path'])) {
						$value($this->getRequest($key));
						$hasPath = true;
						break;
					}
				}
				break;
			case 'PUT':
				foreach ($this->put as $key => $value) {
					$keyReplaced = "/^" . str_replace([":id", ":code", "/"], ["([0-9]+)", "([a-zA-Z0-9]+)", "\\/"], $key) . "$/";
					if (preg_match($keyReplaced, $this->params['path'])) {
						$value($this->getRequest($key));
						$hasPath = true;
						break;
					}
				}
				break;
			case 'PATCH':
				foreach ($this->patch as $key => $value) {
					$keyReplaced = "/^" . str_replace([":id", ":code", "/"], ["([0-9]+)", "([a-zA-Z0-9]+)", "\\/"], $key) . "$/";
					if (preg_match($keyReplaced, $this->params['path'])) {
						$value($this->getRequest($key));
						$hasPath = true;
						break;
					}
				}
				break;
			case 'DELETE':
				foreach ($this->delete as $key => $value) {
					$keyReplaced = "/^" . str_replace([":id", ":code", "/"], ["([0-9]+)", "([a-zA-Z0-9]+)", "\\/"], $key) . "$/";
					if (preg_match($keyReplaced, $this->params['path'])) {
						$value($this->getRequest($key));
						$hasPath = true;
						break;
					}
				}
				break;
			default:
				$this->response(404);
				break;
		}

		if (!$hasPath) {
			$this->response(404);
		}
	}
}
