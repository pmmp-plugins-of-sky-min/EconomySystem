<?php
declare(strict_types = 1);

namespace skymin\economy\money;

use skymin\economy\Loader;
use skymin\economy\money\command\MoneyCommand;
use skymin\economy\money\task\AsyncUpdateRank;

use pocketmine\plugin\Plugin;
use pocketmine\utils\SingletonTrait;

use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerJoinEvent;

use skymin\data\Data;
use skymin\CommandLib\CmdManager;

final class MoneyManager{
	use SingletonTrait;
	
	public static string $prefix = '';
	public ?Loader $register = null;
	public array $data = [];
	public array $rank = [];
	
	public function __construct(){
		self::setInstance($this);
	}
	
	public function init(Loader $plugin) : void{
		if($this->register === null){
			$this->register = $plugin;
		}
		$this->data = Data::call($plugin->getDataFolder() . 'money/Config.json', Data::JSON, [
			'settings' => [
				'unit' => '원',
				'default' => 1000,
				'prefix' => '§l§2[§b돈§2]',
				'oprank' => true
			],
			'players' => []
		]);
		$this->updateRank();
		self::$prefix = $this->data['settings']['prefix'];
		$plugin->getServer()->getCommandMap()->register('moeny', new MoneyCommand($this));
		$plugin->getServer()->getPluginManager()->registerEvent(PlayerJoinEvent::class, function(PlayerJoinEvent $ev) : void{
			$player = $ev->getPlayer();
			$name = $player->getName();
			if(!$this->isData($name)){
				$this->setMoney($name, $this->data['settings']['default']);
				Server::getInstance()->getCommandMap()->getCommand('돈')->update();
			}
			$this->msg($player, '현재 소지금은 ' . $this->format($this->getMoney($name)) . ' 입니다');
		}, EventPriority::MONITOR, $plugin);
	}
	
	public function save() : void{
		Data::save($this->register->getDataFolder() . 'money/Config.json', $this->data, Data::JSON);
	}
	
	public function getLowerCaseName(string|Player $player) : string{
		return $player instanceof Player ? strtolower($player->getName()) : strtolower($player);
	}
	
	public function msg(string|Player $player, string $msg) : void{
		if($player instanceof Player){
			$player->sendMessage(self::$prefix . '§r ' . $msg);
			return;
		}
		$player = Server::getInstance()->getPlayerExact($player);
		if($player !== null){
			$player->sendMessage(self::$prefix . ' ' . $msg);
		}
	}
	
	public function isData(string|Player $player) : bool{
		$name = $this->getLowerCaseName($player);
		return (isset($this->data['players'][$name]));
	}
	
	public function format(int $money) :string{
		$elements = [];
		if ($money >= 1000000000000) {
			$elements[] = floor($money / 1000000000000) . '조';
			$money %= 1000000000000;
		}
		if ($money >= 100000000) {
			$elements[] = floor($money / 100000000) . '억';
			$money %= 100000000;
		}
		if ($money >= 10000) {
			$elements[] = floor($money / 10000) . '만';
			$money %= 10000;
		}
		if (count($elements) == 0 || $money > 0) {
			$elements[] = $money;
		}
		return implode(' ', $elements) . $this->data['settings']['unit'];
	}
	
	public function getMoney(string|Player $player) : int{
		$name = $this->getLowerCaseName($player);
		return $this->data['players'][$name] ?? 0;
	}
	
	public function setMoney(string|Player $player, int $money, bool $msg = false) : void{
		$name = $this->getLowerCaseName($player);
		$this->data['players'][$name] = $money;
		if($msg){
			$this->msg($player, $this->format($money) . '(으)로 설정 되었습니다.');
		}
	}
	
	public function addMoney(string|Player $player, int $money, bool $msg = false) : void{
		$name = $this->getLowerCaseName($player);
		if(!$this->isData($name)) return;
		$this->data['players'][$name] += $money;
		if($msg){
			$this->msg($player, $this->format($money), '지급되었습니다. ');
		}
	}
	
	public function reduceMoney(string|Player $player, int $money, bool $msg = false) : void{
		$name = $this->getLowerCaseName($player);
		if(!$this->isData($name)) return;
		$this->data['players'][$name] -= $money;
		if($msg){
			$this->msg($player, $this->format($money), '감소되었습니다.');
		}
	}
	
	public function updateRank() : void{
		$server = Server::getInstance();
		$server->getAsyncPool()->submitTask(new AsyncUpdateRank($this->data['players'], $this->data['settings']['oprank'] ? [] : $server->getOps()->getAll()));
	}
	
	public function getRank(string|Player $player) : int{
		$name = $this->getLowerCaseName($player);
		return $this->rank['player'][$name] ?? 0;
	}
	
	public function getPlayerByRank(int $rank) : ?string{
		return $this->rank['rank'][$rank] ?? null;
	}
	
}