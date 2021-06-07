<?php

namespace Pandemia\Controller;

use Pandemia\Controller\Controller;
use Pandemia\Controller\ControllerInterface;
use Pandemia\Service\PessoaService;

class PessoaController extends Controller implements ControllerInterface
{
	private PessoaService $service;

	public function __construct()
	{
		$this->service = new PessoaService();
	}

	public function init(): void
	{
		$this->post("/", function ($request) {
			if (!empty($this->service->findByCpf($request['queryParam']['data']['cpf']))) {
				$this->response(400, ["error" => "cpf cadastrado"]);
			}

			if (empty($request['queryParam']['data']['name']) || empty($request['queryParam']['data']['cpf'])) {
				$this->response(400, ["error" => "por favor, preenchar todos os campos"]);
			}

			if ($dados = $this->service->save($request['queryParam']['data'])) {
				$this->response(201, ["Mensagem" => $dados]);
			} else {
				$this->response(400);
			};
		});

		$this->post("/id", function ($request) {
			if ($this->service->updateStatusJob($request['queryParam'])) {
				$this->response(201, ["Mensagem" => "Atualizado com sucesso"]);
			} else {
				$this->response(400);
			};
		});


		$this->get("/", function ($request) {
			if (empty($request['queryParam']['data']['cpf'])) {
				$this->response(400, ["error" => "por favor, preenchar todos os campos"]);
			}

			if ($dados = $this->service->listPersonAndJob($request['queryParam']['data']['cpf'])) {
				if (!empty($dados['cpf'])) {
					$this->response(200, $dados);
				} else {
					$this->response(400, ["error" => "cpf inexistente em nosso banco"]);
				}
			}
		});

		$this->get("/relatorios", function ($request) {
			if ($dados = $this->service->relatorio($request['queryParam']['data'])) {
				$this->response(200, $dados);
			} else {
				$this->response(400, ["error" => "cpf inexistente em nosso banco"]);
			}
		});

		$this->run();
	}
}
