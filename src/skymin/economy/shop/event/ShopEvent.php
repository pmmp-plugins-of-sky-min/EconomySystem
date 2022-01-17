<?php
declare(strict_types = 1);

namespace skymin\economy\shop\event;

use skymin\economy\shop\Shop;

use pocketmine\event\Event;
use pocketmine\player\Player;

abstract class ShopEvent extends Event{
	
	public function __construct(protected Player $player, protected Shop $shop){}
	
	public function getPlayer() : Player{
		return $this->player;
	}
	
	public function getShop() : Shop{
		return $this->shop;
	}
	
}