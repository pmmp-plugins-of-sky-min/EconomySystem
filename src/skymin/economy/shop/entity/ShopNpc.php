<?php
declare(strict_types = 1);

namespace skymin\economy\shop\entity;

use pocketmine\entity\Human;
use pocketmine\player\Player;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\event\entity\EntityDamageEvent;

final class ShopNpc extends Human{
	
	private string $name;
	private string $msg;
	
	protected function init(CompoundTag $nbt) : void{
		parent::initEntity($nbt);
		$this->name = $name = $nbt->getString('shop');
		$this->name = $name = $nbt->getString('msg');
		$this->setNameTagVisible(true);
		$this->setNameTagAlwaysVisible(true);
	}
	
	public function saveNBT() : CompoundTag{
		$nbt = parent::saveNBT();
		$nbt->setString('shop', $this->name);
		$nbt->setString('msg', $this->msg);
		return $nbt;
	}
	
	public function hasMovementUpdate(): bool{
		return false;
	}
	
	public function attack(EntityDamageEvent $source): void{
		$source->cancel();
	}
	
}
