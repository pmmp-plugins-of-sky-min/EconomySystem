<?php
declare(strict_types = 1);

namespace skymin\economy\utils;

use pocketmine\Server;
use pocketmine\player\Player;

use function trim;
use function explode;
use function strtolower;

final class Utils{
	
	public static function isEmpty(string $str) : bool{
		return (trim($str) === '');
	}
	
	public static function getLowerCaseName(string|Player $player) : string{
		return strtolower($player instanceof Player ? $player->getName() : $player);
	}
	
	public static function getPlayer(string|Player $player) : ?Player{
		return $player instanceof Player ? $player : Server::getInstance()->getPlayerExact($player);
	}
	
}