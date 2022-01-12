<?php
declare(strict_types = 1);

namespace skymin\economy\shop;

final class Shop{
	
	public function __construct(private string $name, private array $items = []){}
	
	public function getName() : string{
		return $this->name;
	}
	
	public function getAll() : array{
		return $this->items;
	}
	
}