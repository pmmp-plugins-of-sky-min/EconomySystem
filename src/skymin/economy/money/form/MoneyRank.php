<?php
declare(strict_types = 1);

namespace skymin\economy\money\form;

use skymin\economy\money\MoneyManager;

use pocketmine\form\Form;
use pocketmine\player\Player;

final class MoneyRank implements Form{
	
	public function jsonSerialize() : array{
		$rank = [];
		$manager = MoneyManager::getInstance();
		for($i = 1; $i < PHP_INT_MAX; $i++){
			$player = $manager->getPlayerByRank($i);
			if($player === null) break;
			$rank[] = ['type' => 'label', 'text' => '§l§e' . $i . '위§r ' . $player . ' §l§e:§r ' . $manager->format($manager->getMoney($player))];
		}
		return [
			'type' => 'custom_form',
			'title' => '돈 순위',
			'content' => $rank
		];
	}
	
	public function handleResponse(Player $player, $data) : void{
		return;
	}
	
}