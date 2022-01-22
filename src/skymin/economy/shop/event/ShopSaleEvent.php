<?php
declare(strict_types = 1);

namespace skymin\economy\shop\event;

use skymin\economy\shop\Shop;

use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\event\{Cancellable CancellableTrait}; 

final class ShopSaleEvent extends ShopEvent implements Cancellable{
	use CancellableTrait;
	
	public function __construct(Player $player, Shop $shop, protected int $price, protected Item $item){
		parent::__construct($player, $shop);
	}
	
	public function getPrice() : int{
		return $this->price;
	}
	
	public function setPrice(int $money) : void{
		$this->price = $money;
	}
	
	public function getItem() : Item{
		return $this->item;
	}
	
}