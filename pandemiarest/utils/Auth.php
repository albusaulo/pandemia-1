<?php

namespace Pandemia\Utils;

use DateInterval;
use DateTime;

class Auth {
	public static function authentication(?string $authorization) {
		$basic = explode(' ', $authorization);

		if (empty($basic[0]) || count($basic) != 2 || $basic[0] != 'Basic') {
			return [];
		}

		$basicUser = base64_decode($basic[1]);
		if (empty($basicUser)) {
			return [];
		}

		$basicUser = explode(":", $basicUser);
		if (count($basicUser) != 2) {
			return [];
		}

		return $basicUser;
	}

	public static function generate(array $user) {
		$header = [
			'alg' => 'HS256',
			'typ' => 'JWT'
		];
		$header = json_encode($header);
		$header = base64_encode($header);

		$currentDate = new DateTime();
		$payload = [
			'user' => $user,
			'iat' => $currentDate->format('Y-m-d H:i:s'),
			'exp' => $currentDate->add(new DateInterval('P1D'))->format('Y-m-d H:i:s')
		];
		$payload = json_encode($payload);
		$payload = base64_encode($payload);

		$signature = hash_hmac('sha256', $header . '.' . $payload, Utils::getEnv('sha256'), true);
		$signature = base64_encode($signature);

		return $header . '.' . $payload . '.' . $signature;
	}

	public static function checkAndGetPayload(?string $authorization): array {
		if (empty($authorization)) {
			return [];
		}

		$jwt = explode(' ', $authorization);

		if (empty($jwt[0]) || count($jwt) != 2 || $jwt[0] != 'Bearer') {
			return [];
		}

		$jwtArray = explode('.', $jwt[1]);
		if (empty($jwtArray) || count($jwtArray) != 3) {
			return [];
		}

		$header = $jwtArray[0];
		$payload = $jwtArray[1];
		$signature = $jwtArray[2];

		$signatureValid = hash_hmac('sha256', $header . '.' . $payload, Utils::getEnv('sha256'), true);
		$signatureValid = base64_encode($signatureValid);

		if ($signatureValid == $signature) {
			$payload = base64_decode($jwtArray[1]);
			$payload = json_decode($payload, true);

			if (!empty($payload)) {
				return $payload;
			}
		}

		return [];
	}
}
