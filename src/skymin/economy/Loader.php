<?php
declare(strict_types = 1);

namespace skymin\economy;

use skymin\economy\shop\ShopManager;
use skymin\economy\money\MoneyManager;

use pocketmine\plugin\PluginBase;

use skymin\InventoryLib\InvLibManager;
use skymin\CommandLib\CmdManager;

use function rmdir;
use function date_default_timezone_set;

final class Loader extends PluginBase{
	
	public static string $datapath = '';
	
	protected function onLoad() : void{
		date_default_timezone_set('Asia/Seoul');
		rmdir($this->getDataFolder());
		Loader::$datapath = $this->getServer()->getDataPath() . 'plugin_data/Economy';
	}
	
	protected function onEnable() : void{
		CmdManager::register($this);
		InvLibManager::register($this);
		MoneyManager::getInstance()->init($this);
	}
	
	protected function onDisable() : void{
		MoneyManager::getInstance()->save();
	}
	
}