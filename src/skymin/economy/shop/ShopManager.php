<?php
declare(strict_types = 1);

namespace skymin\economy\shop;

use skymin\economy\Loader;
use skymin\economy\shop\entity\ShopNpc;

use pocketmine\utils\SingletonTrait;
use pocketmine\world\World;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\event\EventPriority;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\entity\{EntityFactory, EntityDataHelper};
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;

use skymin\data\Data;

final class ShopManager{
	use SingletonTrait;
	
	public ?Loader $register = null;
	public array $data = [];
	
	public function __construct(){
		self::setInstance($this);
	}
	
	public function init(Loader $plugin) : void{
		if($this->register === null){
			$this->register = $plugin;
		}
		$this->data = Data::call($plugin->getDataFolder() . 'shop/Config.json', Data::JSON, [
			'npc' => [],
			'shop' => []
		]);
		EntityFactory::getInstance()->register(ShopNpc::class, function(World $world, CompoundTag $nbt) : ShopNpc{
			return new ShopNpc(EntityDataHelper::parseLocation($nbt, $world), ShopNpc::parseSkinNBT($nbt), $nbt);
		}, ['ShopNpc']);
		$plugin->getServer()->getPluginManager()->registerEvent(DataPacketReceiveEvent::class, function(DataPacketReceiveEvent$ev) : void{
			$player = $ev->getOrigin()->getPlayer();
			$packet = $ev->getPacket();
			if($player === null) return;
			if($packet instanceof InventoryTransactionPacket && $packet->trData instanceof UseItemOnEntityTransactionData){
				if($player->hasPermission('economy.op') && $player->isSneaking()){
					$entity->close();
					return;
				}
				//openshop
			}
		}, EventPriority::HIGHEST, $plugin);
	}
	
	public function save() : void{
		Data::save($this->register->getDataFolder() . 'shop/Config.json', $this->data, Data::JSON);
	}
	
}