<?php

declare(strict_types=1);

namespace muqsit\perworldviewdistance\player;

use Closure;
use pocketmine\player\Player;

final class PlayerInstance{

	private int $requested_view_distance;
	private bool $joined = false;

	/** @var Closure[] */
	private array $join_callbacks = [];

	public function __construct(Player $player){
		$this->requested_view_distance = $player->getViewDistance();
	}

	public function getRequestedViewDistance() : int{
		return $this->requested_view_distance;
	}

	public function onRequestChunkRadius(int $radius) : void{
		$this->requested_view_distance = $radius;
	}

	public function onJoin() : void{
		$this->joined = true;
		foreach($this->join_callbacks as $cb){
			$cb();
		}
		$this->join_callbacks = [];
	}

	public function onJoinCallback(Closure $callback) : void{
		if($this->joined){
			$callback();
		}else{
			$this->join_callbacks[] = $callback;
		}
	}
}