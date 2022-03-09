<?php
declare(strict_types = 1);

namespace skymin\economy\check;

use skymin\economy\Loader;
use skymin\economy\money\MoneyManager;
use skymin\economy\check\command\{CheckCmd, SettingCmd};

use pocketmine\utils\SingletonTrait;
use pocketmine\item\{Item, VanillaItems};
use pocketmine\nbt\tag\{IntTag, CompoundTag};
use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerInteractEvent;

use skymin\data\Data;

final class CheckManager{
	use SingletonTrait;
	
	public Data $db;
	
	public function init(Loader $plugin) : void{
		$this->db = new Data(Loader::$datapath . 'Check/Config.json', Data::JSON, [
			'min-amount' => 10000,
			'item' => VanillaItems::PAPER()
		]);
		$plugin->getServer()->getCommandMap()->register('check', new CheckCmd());
		$plugin->getServer()->getCommandMap()->register('check', new SettingCmd());
		$plugin->getServer()->getPluginManager()->registerEvent(PlayerInteractEvent::class, function(PlayerInteractEvent $ev) : void{
			$item = $ev->getItem();
			$amount =  $this->getAmount($item);
			if($amount !== false){
				$player = $ev->getPlayer();
				MoneyManager::getInstance()->addMoney($player, $amount);
				$player->getInventory()->removeItem($item->setCount(1));
				$player->sendMessage('§l§f[§e수표§f]§r 정상 지급되었습니다.');
			}
		}, EventPriority::NORMAL, $plugin);
	}
	
	public function isCheck(Item $item) : bool{
		return ($item->getNamedTag()->getTag('ccamount') instanceof IntTag);
	}
	
	public function getAmount(Item $item) : false|int{
		$amount = $item->getNamedTag()->getTag('ccamount');
		if($amount instanceof IntTag){
			return $amount->getValue();
		}
		return false;
	}
	
	public function create(int $amount) : false|Item{
		if($amount < $this->db->__get('min-amount')) return false;
		$item = Item::jsonDeserialize($this->db->__get('item'));
		$nbt = $item->getCompoundTag();
		$item = $item->setNamedTag($nbt->setInt('ccamount', $amount))
			->setCustomName('§l§f[§d수표§f]§r §e' . MoneyManager::getInstance()->format($amount))
			->setLore(['바닥을 터치하여 수표 금액 만큼 얻을 수 있다.']);
		return $item;
	}
	
}