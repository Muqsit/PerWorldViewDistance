<?php

declare(strict_types=1);

namespace muqsit\perworldviewdistance;

use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

final class Main extends PluginBase implements Listener{

	/** @var PerWorldViewDistanceConfig */
	private $view_distance_config;

	protected function onEnable() : void{
		$this->view_distance_config = new PerWorldViewDistanceConfig((int) $this->getConfig()->get("default-view-distance"));
		foreach($this->getConfig()->get("view-distances") as $world => $view_distance){
			$this->view_distance_config->setViewDistance($world, $view_distance);
		}

		if(!$this->view_distance_config->isUnnecessary()){
			$this->getServer()->getPluginManager()->registerEvents($this, $this);
		}
	}

	public function getViewDistanceConfig() : PerWorldViewDistanceConfig{
		return $this->view_distance_config;
	}

	/**
	 * @param PlayerJoinEvent $event
	 * @priority MONITOR
	 */
	public function onPlayerJoin(PlayerJoinEvent $event) : void{
		$player = $event->getPlayer();
		$player->setViewDistance($this->view_distance_config->getViewDistance($player->getWorld()->getFolderName()));
	}

	/**
	 * @param EntityTeleportEvent $event
	 * @priority MONITOR
	 */
	public function onEntityTeleport(EntityTeleportEvent $event) : void{
		$from_world = $event->getFrom()->getWorld();
		$to_world = $event->getTo()->getWorld();
		if($to_world !== null && $from_world !== $to_world){
			$player = $event->getEntity();
			if($player instanceof Player){
				$player->setViewDistance($this->view_distance_config->getViewDistance($to_world->getFolderName()));
			}
		}
	}
}