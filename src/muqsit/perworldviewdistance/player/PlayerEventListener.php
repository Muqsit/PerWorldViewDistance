<?php

declare(strict_types=1);

namespace muqsit\perworldviewdistance\player;

use muqsit\perworldviewdistance\Main;
use muqsit\perworldviewdistance\PerWorldViewDistanceConfig;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\RequestChunkRadiusPacket;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\world\World;

final class PlayerEventListener implements Listener{

	/** @var Main */
	private $plugin;

	/** @var PerWorldViewDistanceConfig */
	private $config;

	public function __construct(Main $plugin, PerWorldViewDistanceConfig $config){
		$this->plugin = $plugin;
		$this->config = $config;
	}

	private function checkViewDistance(Player $player, PlayerInstance $instance, ?World $world = null) : void{
		$max_view_distance = $this->config->getViewDistance(($world ?? $player->getWorld())->getFolderName());
		$requested_view_distance = $instance->getRequestedViewDistance();
		$current_view_distance = $player->getViewDistance();
		if($requested_view_distance > $max_view_distance){
			$player->setViewDistance($max_view_distance);
			$this->plugin->getLogger()->debug("Overriding player's view distance to " . $max_view_distance . " (actually requested " . $requested_view_distance . ")");
		}elseif($current_view_distance < $max_view_distance && $current_view_distance < $requested_view_distance){
			$player->setViewDistance($requested_view_distance);
			$this->plugin->getLogger()->debug("Overriding player's view distance to " . $requested_view_distance . " (same as requested)");
		}
	}

	/**
	 * @param PlayerLoginEvent $event
	 * @priority MONITOR
	 */
	public function onPlayerLogin(PlayerLoginEvent $event) : void{
		PlayerManager::add($event->getPlayer());
	}

	/**
	 * @param PlayerJoinEvent $event
	 * @priority MONITOR
	 */
	public function onPlayerJoin(PlayerJoinEvent $event) : void{
		$player = $event->getPlayer();
		$instance = PlayerManager::get($player);
		$instance->onJoin();
		$this->checkViewDistance($player, $instance);
	}

	/**
	 * @param PlayerQuitEvent $event
	 * @priority MONITOR
	 */
	public function onPlayerQuit(PlayerQuitEvent $event) : void{
		PlayerManager::remove($event->getPlayer());
	}

	/**
	 * @param DataPacketReceiveEvent $event
	 * @priority MONITOR
	 */
	public function onDataPacketReceive(DataPacketReceiveEvent $event) : void{
		$packet = $event->getPacket();
		if($packet instanceof RequestChunkRadiusPacket){
			$player = $event->getOrigin()->getPlayer();
			if($player !== null && $player->isOnline()){
				$instance = PlayerManager::get($player);
				$instance->onRequestChunkRadius($packet->radius);
				$this->plugin->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use($player, $instance) : void{
					if($player->isOnline()){
						$this->checkViewDistance($player, $instance);
					}
				}), 1);
			}
		}
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
				$this->checkViewDistance($player, PlayerManager::get($player), $to_world);
			}
		}
	}
}