<?php

namespace Pandemia\Service;

use Pandemia\Utils\Session;

class Service
{
	public function __construct()
	{
	}

	protected function hasPermission(?string $currentPermission): bool
	{
		return !empty(Session::getPermissions()) && (!empty($currentPermission) && Session::getPermissions()[$currentPermission]);
	}
}
