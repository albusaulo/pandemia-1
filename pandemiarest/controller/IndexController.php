<?php

namespace Pandemia\Controller;

use Pandemia\Controller\Controller;
use Pandemia\Controller\ControllerInterface;

class IndexController extends Controller implements ControllerInterface
{
	public function init(): void
	{
		$this->get("/", function ($request) {
			$this->response(200, ['message' => 'Successful request. Welcome!']);
		});

		$this->run();
	}
}
