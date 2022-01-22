<?php
declare(strict_types = 1);

namespace skymin\economy\shop\inventory;

use pocketmine\player\Player;
use pocketmine\world\Position;

use skymin\InventoryLib\{LibInvType, LibInventory};

final class ShopInventory extends LibInventory{
	
	public function __construct(Position $pos, string $title){
		$pos->y += 4;
		parent::__construct(LibInvType::DOUBLE_CHEST(), $pos, $title);
	}
	
}