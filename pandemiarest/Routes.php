<?php

namespace Pandemia;

use Pandemia\Controller\IndexController;
use Pandemia\Controller\PessoaController;

class Routes
{
	private array $routes;

	public function __construct()
	{
		$this->routes = [
			'' => new IndexController(),
			'pessoa' => new PessoaController()
		];
	}

	public function getRoutes(): array
	{
		return $this->routes;
	}
}
