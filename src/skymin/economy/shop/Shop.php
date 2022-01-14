<?php
declare(strict_types = 1);

namespace skymin\economy\shop;

use pocketmine\item\Item;

final class Shop{
	
	public function __construct(private string $name, private array $items = []){}
	
	public function getName() : string{
		return $this->name;
	}
	
	public function getAll() : array{
		return $this->items;
	}
	
	public function getPageItems(int $page) : ?array{
		return $this->items[$page] ?? null;
	}
	
	public function setItem(int $page, int $slot, Item $item) : void{
		$this->items[$page][$slot] = $item;
	}
	
}