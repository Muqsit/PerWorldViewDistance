<?php

declare(strict_types=1);

namespace muqsit\perworldviewdistance\player;

use muqsit\perworldviewdistance\Main;
use pocketmine\player\Player;

final class PlayerManager{

	/** @var PlayerInstance[] */
	private static $instances = [];

	public static function init(Main $plugin) : void{
		$plugin->getServer()->getPluginManager()->registerEvents(new PlayerEventListener($plugin, $plugin->getViewDistanceConfig()), $plugin);
	}

	public static function add(Player $player) : void{
		self::$instances[$player->getId()] = new PlayerInstance($player);
	}

	public static function remove(Player $player) : void{
		unset(self::$instances[$player->getId()]);
	}

	public static function get(Player $player) : PlayerInstance{
		return self::$instances[$player->getId()];
	}
}