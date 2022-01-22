<?php
declare(strict_types = 1);

namespace skymin\economy\shop\form\shop;

use skymin\economy\shop\Shop;
use skymin\economy\shop\util\ShopUtil;
use skymin\economy\money\MoneyManager;

use pocketmine\item\Item;
use pocketmine\form\Form;
use pocketmine\player\Player;

final class MenuForm implements Form{
	
	public function __construct(
		private Player $player,
		private Shop $shop,
		private Item $item
	){}
	
	public function jsonSerialize() : array{
		$money = MoneyManager::getInstance();
		$player = $this->player;
		$inv = $player->getInventory();
		$item = $this->item;
		return [
			'type' => 'custom_form',
			'title' => $this->shop->getName(),
			'content' => [
				[
					'type' => 'label',
					'text' => "§l§b{$item->getName()}§r(을)를 구매하시겠습니까?\n
					소지 금액: {$money->format($momey->getMoney($player))}\n
					아이템 소유량: {ShopUtil::getItemCount($inv, $item)}\n
					구매 가능 수량: {ShopUtil::getBuyMaximum($inv, $item)}"
				],
				['type' => 'dropdown',  'text' => '원하시는 메뉴를 선택하세요.', 'options' => ['구매하기', '판매하기']],
				['type' => 'input', 'text' => '구매할 수량을 입력해 주세요.']
			]
		];
	}
	
	public function handleResponse(Player $player, $data) : void{
		
	}
	
}