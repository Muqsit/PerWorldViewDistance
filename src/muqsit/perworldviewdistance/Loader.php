<?php

declare(strict_types=1);

namespace muqsit\perworldviewdistance;

use muqsit\perworldviewdistance\player\PlayerManager;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;

final class Loader extends PluginBase{

	/** @var PerWorldViewDistanceConfig */
	private $view_distance_config;

	/** @var PlayerManager|null */
	private $player_manager = null;

	protected function onEnable() : void{
		$this->view_distance_config = new PerWorldViewDistanceConfig((int) $this->getConfig()->get("default-view-distance"));
		foreach($this->getConfig()->get("view-distances") as $world => $view_distance){
			$this->view_distance_config->setViewDistance($world, $view_distance);
		}
		if(!$this->view_distance_config->isUnnecessary()){
			$this->player_manager = new PlayerManager($this);
		}
	}

	public function getViewDistanceConfig() : PerWorldViewDistanceConfig{
		return $this->view_distance_config;
	}

	public function getPlayerManager() : ?PlayerManager{
		return $this->player_manager;
	}

	/**
	 * @param PlayerJoinEvent $event
	 * @priority MONITOR
	 */
	public function onPlayerJoin(PlayerJoinEvent $event) : void{
		$player = $event->getPlayer();
		$player->setViewDistance($this->view_distance_config->getViewDistance($player->getWorld()->getFolderName()));
	}
}