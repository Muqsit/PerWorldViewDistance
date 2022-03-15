<?php

declare(strict_types=1);

namespace muqsit\perworldviewdistance\player;

use muqsit\perworldviewdistance\Loader;
use pocketmine\player\Player;

final class PlayerManager{

	/** @var PlayerInstance[] */
	private $instances = [];
	
	public function __construct(Loader $plugin){
		$plugin->getServer()->getPluginManager()->registerEvents(new PlayerEventListener($plugin, $plugin->getViewDistanceConfig(), $this), $plugin);
	}

	public function add(Player $player) : void{
		$this->instances[$player->getId()] = new PlayerInstance($player);
	}

	public function remove(Player $player) : void{
		unset($this->instances[$player->getId()]);
	}

	public function get(Player $player) : PlayerInstance{
		return $this->instances[$player->getId()];
	}
}