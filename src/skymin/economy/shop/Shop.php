<?php
declare(strict_types = 1);

namespace skymin\economy\shop;

use skymin\economy\utils\Utils;

use pocketmine\item\Item;

final class Shop{
	
	private array $data = [
		'items' => [],
		'prices' => []
	];
	
	public function __construct(private string $name){}
	
	public function getName() : string{
		return $this->name;
	}
	
	public function getAll() : array{
		return $this->data;
	}
	
	public function getItems() : array{
		return $this->data['items'];
	}
	
	public function getPriceData() : array{
		return $this->data['prices'];
	}
	
	public function getPageItems(int $page) : ?array{
		$result = [];
		foreach($this->data['items'][$page] as $slot => $item){
			$result[$slot] = Utils::hashToitem($item);
		}
		return $result;
	}
	
	public function getItem(int $page, int $slot) : ?Item{
		if(isset($this->data['items'][$page][$slot])){
			return Utils::hashToitem($this->data['items'][$page][$slot]);
		}
		return null;
	}
	
	public function setItem(int $page, int $slot, Item $item) : void{
		$this->data['items'][$page][$slot] = Utils::itemTohash($item);
	}
	
	public function setItemPrice(Item $item, int $buy = -1, int $sale = -1) : void{
		$this->data['prices'][Utils::itemTohash($item)] = [
			'buy' => $buy,
			'sale' => $sale
		];
	}
	
	public function getBuyPrice(Item $item) : int{
		return $this->data['prices'][Utils::hashToitem($item)]['buy'] ?? -1;
	}
	
	public function getSalePrice(Item $item) : int{
		return $this->data['prices'][Utils::hashToitem($item)]['sale'] ?? -1;
	}
	
}