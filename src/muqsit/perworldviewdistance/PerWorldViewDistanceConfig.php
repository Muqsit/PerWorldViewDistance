<?php

declare(strict_types=1);

namespace muqsit\perworldviewdistance;

use InvalidArgumentException;
use pocketmine\Server;

final class PerWorldViewDistanceConfig{

	/** @var int */
	private $default;

	/** @var int[] */
	private $view_distances = [];

	public function __construct(int $default){
		$this->default = $default;
	}

	public function isUnnecessary() : bool{
		return $this->default === Server::getInstance()->getViewDistance() && count($this->view_distances) === 0;
	}

	public function setViewDistance(string $world, int $view_distance) : void{
		if($view_distance > Server::getInstance()->getViewDistance()){
			throw new InvalidArgumentException("View distance cannot be greater than server's max view distance");
		}

		$this->view_distances[$world] = $view_distance;
	}

	public function getViewDistance(string $world) : int{
		return $this->view_distances[$world] ?? $this->default;
	}
}