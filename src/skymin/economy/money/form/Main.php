<?php
declare(strict_types = 1);

namespace skymin\economy\money\form;

use skymin\economy\money\MoneyManager;

use pocketmine\form\Form;
use pocketmine\player\Player;

final class Main implements Form{
	
	public function __construct(private Player $player){}
	
	public function jsonSerialize() : array{
		$manager = MoneyManager::getInstance();
		$player = $this->player;
		$buttons = [
			['text' => '지불'],
			['text' => '순위'],
		];
		if($player->hasPermission('economy.op')){
			$buttons[] = ['text' => '지급'];
			$buttons[] = ['text' => '뺏기'];
			$buttons[] = ['text' => '설정'];
		}
		return [
			'type' => 'form',
			'title' => MoneyManager::$prefix,
			'content' => '§b내 돈: §r' . $manager->format($manager->getMoney($player)) . "\n§b순위: §r" . $manager->getRank($player) . "\n",
			'buttons' => $buttons
		];
	}
	
	public function handleResponse(Player $player, $data) : void{
		if($data === null) return;
		$form = match($data){
			0 => new PayMoney(),
			1 => new MoneyRank(),
			2 => new AddMoney(),
			3 => new ReduceMoney(),
			4 => new SetMoney()
		};
		$player->sendForm($form);
	}
	
}