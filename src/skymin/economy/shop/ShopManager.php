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
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\entity\{Location, EntityFactory, EntityDataHelper};
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;

use skymin\data\Data;
use skymin\skin\{ImageTool, ModelTool};

use function array_keys;

final class ShopManager{
	use SingletonTrait;
	
	public ?Loader $register = null;
	public array $data = [];
	/** @var Shop[] */
	public array $shops = [];
	
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
		$shops = [];
		foreach($this->data['shop'] as $name => $items){
			$shops[$name] = new Shop($name, $items);
		}
		$this->shops = $shops;
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
		$shops = [];
		foreach($this->shops as $name => $shop){
			$shop[$name] = $shop->getAll();
		}
		$this->data['shop'] = $shops;
		Data::save($this->register->getDataFolder() . 'shop/Config.json', $this->data, Data::JSON);
	}
	
	public function getShop(string $name) : ?Shop{
		return $this->shops[$name] ?? null;
	}
	
	public function addShop(Shop $shop) : void{
		if(!isset($shop->getName())){
			$this->shops[$shop->getName()] = $shop;
		}
	}
	
	public function deleteShop(string $name) : void{
		if(isset($this->shops[$name])){
			unset($this->shops[$name]);
		}
	}
	
	public function getNpcNames() : array{
		return array_keys($this->data['npc']);
	}
	
	public function getNpcData(string $npcName) : ?array{
		return $this->data['npc'][$npcName] ?? null;
	}
	
	public function setNpcData(string $shopName, tring $npcName, string $closemsg, string $buymsg) : void{
		if(!isset($this->shops[$shopName])) return;
		$this->data['npc'][$npcName] = [
			'shop' => $shopName,
			'close' => $closemsg,
			'buy' => $buymsg
		];
	}
	
	public function deleteNpcData(string $name) : void{
		if(isset($this->data['npc'][$name])){
			unset($this->data['npc'][$name]);
		}
	}
	
	public function spawnNpc(string $npcName, Location $pos, SkinTool $image, string|ModelTool $model = '') : void{
		
	}
	
}