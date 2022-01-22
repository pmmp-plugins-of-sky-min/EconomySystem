<?php
declare(strict_types = 1);

namespace skymin\economy\utils;

use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\item\{Item, ItemFactory};

use function trim;
use function explode;
use function strtolower;
use function base64_encode;
use function base64_decode;

final class Utils{
	
	public static function isEmpty(string $str) : bool{
		return (trim($str) === '');
	}
	
	public static function getLowerCaseName(string|Player $player) : string{
		return strtolower($player instanceof Player ? $player->getName() : $player);
	}
	
	public static function getPlayer(string|Player $player) ?Player{
		return $player instanceof Player ? $player : Server::getInstance()->getPlayerExact($player);
	}
	
	public static function itemTohash(Item $item) : string{
		return $item->getId() . ':' . $item->getMeta() . ':' . base64_encode($item->getNamedTag();
	}
	
	public static function hashToitem(string $hash) : Item{
		$data = explode(':', $hash);
		return ItemFactory::getInstance()->get((int) $data[0], (int) $data[1], 1, base64_decode($data[2]));
	}
	
}