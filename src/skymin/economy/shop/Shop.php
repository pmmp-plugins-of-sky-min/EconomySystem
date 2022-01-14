<?php
declare(strict_types = 1);

namespace skymin\economy\shop;

use pocketmine\item\{Item, ItemFactory};

use function explode;
use function base64_encode;
use function base64_decode;

final class Shop{
	
	private static function itemTohash(Item $item) : string{
		return $item->getId() . ':' . $item->getMeta() . ':' . base64_encode($item->getCompoundTag());
	}
	
	private static function hashToitem(string $hash) : Item{
		$data = explode(':', $hash);
		return ItemFactory::getInstance()->get((int) $data[0], (int) $data[1], 1, base64_decode($data[2]));
	}
	
	private array $data = [
		'items' = [],
		'prices' = []
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
			$result[$slot] = self::hashToitem($item);
		}
		return $result;
	}
	
	public function getItem(int $page, int $slot) : ?Item{
		if(isset($this->data['items'][$page][$slot])){
			return self::hashToitem($this->data['items'][$page][$slot]));
		}
		return null;
	}
	
	public function setItem(int $page, int $slot, Item $item) : void{
		$this->data['items'][$page][$slot] = self::itemTohash($item);
	}
	
	public function setItemPrice(Item $item, int $buy = -1, int $sale = -1) : void{
		$this->data['prices'][self::itemTohash($item)] = [
			'buy' => $buy,
			'sale' => $sale
		];
	}
	
	public function getBuyPrice(Item $item) : int{
		return $this->data['prices'][self::hashToitem($item)]['buy'] ?? -1;
	}
	
	public function getSalePrice(Item $item) : int{
		return $this->data['prices'][self::hashToitem($item)]['sale'] ?? -1;
	}
	
}