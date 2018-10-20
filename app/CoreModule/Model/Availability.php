<?php

declare(strict_types=1);

namespace App\CoreModule\Model;

final class Availability
{
	use \Nette\StaticClass;

	public const AVAILABILITY_AVAILABLE = 'Na skladě',
		AVAILABILITY_UNAVAILABLE = 'Nedostupné';

	public const AVAILABILITIES = [
		self::AVAILABILITY_AVAILABLE => self::AVAILABILITY_AVAILABLE,
		self::AVAILABILITY_UNAVAILABLE => self::AVAILABILITY_UNAVAILABLE,
	];
}
