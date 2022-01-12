<?php
declare(strict_types = 1);

namespace skymin\economy\money\form;

use skymin\economy\money\MoneyManager;

use pocketmine\player\Player;

use function array_keys;

trait PlayersTrait{
	
	private array $players;
	
	public function __construct(){
		$this->players = array_keys(MoneyManager::getInstance()->data['players']);
	}
	
	private function msg(Player $player, string $msg) : void{
		$player->sendMessage(MoneyManager::$prefix . '§r ' . $msg);
	}
	
}