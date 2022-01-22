<?php
declare(strict_types = 1);

namespace skymin\economy\shop\util;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\inventory\Inventory;
use pocketmine\item\{Item, ItemIds, ItemFactory};

final class ShopUtil{
	
	public static function getBackGround() : array{
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
		return $content;
	}
	
	public static function getItemCount(Inventory $inv, Item $item) : int{
		$count = 0;
		foreach($inv->all($item) as $slot => $i){
			$count => $i->getCount();
		}
		return $count;
	}
	
	public static function  getBuyMaximum(Inventory $inv, Item $item) : int{
		$emptySlot = 0;
		foreach($inv->getContents(true) as $slot => $i){
			if($i->isNull()){
				$emptySlot++;
			}
		}
		return $item->getMaxStackSize() * $emptySlot;
	}
	
}