<?php
declare(strict_types = 1);

namespace skymin\economy\shop\inventory;

use pocketmine\player\Player;
use pocketmine\world\Position;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\item\{ItemIds, ItemFactory};

use skymin\InventoryLib\{LibInvType, LibInventory};

final class ShopInventory extends LibInventory{
	
	public function __construct(Position $pos, string $title){
		$pos->y += 4;
		parent::__construct(LibInvType::DOUBLE_CHEST(), $pos, $title);
	}
	
	public function onOpen(Player $who) : void{
		parent::onOpen($who);
		$factory = ItemFactory::getInstance();
		$nbt = CompoundTag::create()->setString('shop', 'bgr');
		$bgr = $factory->get(ItemIds::BARRIER, 0, 1, $nbt->setTag('display', CompoundTag::create()->setString('Name','')));
		$content = [];
		for($i = 0; $i < 9; $i++){
			$content[$i] = $bgr;
		}
		for($i = 44; $i < 54; $i++){
			$content[$i] = $bgr;
		}
		$content[49] = $factory->get(ItemIds::SIGN_POST, 0, 1, $nbt->setTag('display', CompoundTag::create()->setString('Name','§l§f판매 전체')));
		$this->setContent($content);
	}
	
}