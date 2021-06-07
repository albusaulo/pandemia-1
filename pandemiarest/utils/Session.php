<?php

namespace Pandemia\Utils;

class Session {
	private function __construct() {}

	private static $userId = null;
	private static $permissions = [];

	public static function getUserId(): ?int {
		return SELF::$userId;
	}

	public static function getPermissions(): ?array {
		return SELF::$permissions;
	}

    public static function load(?string $authorizationToken): array {
		$payload = Auth::checkAndGetPayload($authorizationToken);

		if (!empty($payload)) {
			SELF::$userId = $payload['user']['id'];
			SELF::$permissions = $payload['user']['permissions'];
		};

		return $payload;
	}
}