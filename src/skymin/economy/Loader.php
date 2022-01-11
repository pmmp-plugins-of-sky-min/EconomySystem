<?php
declare(strict_types = 1);

namespace skymin\economy;

use skymin\economy\money\MoneyManager;

use pocketmine\plugin\PluginBase;

use skymin\InventoryLib\InvLibManager;
use skymin\CommandLib\CmdManager;

final class Loader extends PluginBase{
	
	public function onEnable() : void{
		CmdManager::register($this);
		InvLibManager::register($this);
		MoneyManager::getInstance()->init($this);
	}
	
	public function onDisable() : void{
		MoneyManager::getInstance()->save();
	}
	
}