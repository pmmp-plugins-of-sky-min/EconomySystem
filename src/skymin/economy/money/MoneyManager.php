<?php
declare(strict_types = 1);

namespace skymin\economy\money;

use skymin\economy\Loader;
use skymin\economy\utils\Utils;
use skymin\economy\money\command\MoneyCommand;
use skymin\economy\money\task\AsyncUpdateRank;

use pocketmine\plugin\Plugin;
use pocketmine\utils\SingletonTrait;

use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerJoinEvent;

use skymin\data\Data;
use skymin\asyncqueue\AsyncQueue;
use skymin\CommandLib\CmdManager;

final class MoneyManager{
	use SingletonTrait;
	
	public static string $prefix = '';
	
	private Data $db;
	
	public array $data = [];
	public array $rank = [];
	
	public function __construct(){
		self::setInstance($this);
	}
	
	public function init(Loader $plugin) : void{
		$this->db = new Data(Loader::$datapath . 'Money/Config.json', Data::JSON, [
			'settings' => [
				'unit' => '원',
				'default' => 1000,
				'prefix' => '§l§2[§b돈§2]',
				'oprank' => true
			],
			'players' => []
		]);
		$this->data = $this->db->data;
		$this->updateRank();
		self::$prefix = $this->data['settings']['prefix'] . '§r';
		$server = $plugin->getServer();
		$server->getCommandMap()->register('moeny', new MoneyCommand($this));
		$server->getPluginManager()->registerEvent(PlayerJoinEvent::class, function(PlayerJoinEvent $ev) use($server): void{
			$player = $ev->getPlayer();
			$name = $player->getName();
			if(!$this->isData($name)){
				$this->setMoney($name, $this->data['settings']['default']);
				$cmd = $server->getCommandMap()->getCommand('돈');
				if($cmd instanceof MoneyCommand){
					$cmd->update();
				}else{
					$server->getLogger()->debug('돈 명령어 업데이트에 실패하였습니다.');
				}
			}
			$this->msg($player, '현재 소지금은 ' . $this->format($this->getMoney($name)) . ' 입니다');
		}, EventPriority::MONITOR, $plugin);
	}
	
	public function save() : void{
		$this->db->data = $this->data;
		$this->db->save();
	}
	
	public function msg(string|Player $player, string $msg) : void{
		$player = Utils::getPlayer($player);
		if($player !== null){
			$player->sendMessage(self::$prefix . ' ' . $msg);
		}
	}
	
	public function isData(string|Player $player) : bool{
		$name = Utils::getLowerCaseName($player);
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
		return implode(' ', $elements) . ' ' . $this->data['settings']['unit'];
	}
	
	public function getMoney(string|Player $player) : int{
		$name = Utils::getLowerCaseName($player);
		return $this->data['players'][$name] ?? 0;
	}
	
	public function setMoney(string|Player $player, int $money, bool $msg = false) : void{
		$name = Utils::getLowerCaseName($player);
		$this->data['players'][$name] = $money;
		if($msg){
			$this->msg($player, $this->format($money) . '(으)로 설정 되었습니다.');
		}
	}
	
	public function addMoney(string|Player $player, int $money, bool $msg = false) : void{
		$name = Utils::getLowerCaseName($player);
		if(!$this->isData($name)) return;
		$this->data['players'][$name] += $money;
		if($msg){
			$this->msg($player, $this->format($money) . ' 지급되었습니다. ');
		}
	}
	
	public function reduceMoney(string|Player $player, int $money, bool $msg = false) : void{
		$name = Utils::getLowerCaseName($player);
		if(!$this->isData($name)) return;
		$this->data['players'][$name] -= $money;
		if($msg){
			$this->msg($player, $this->format($money) . ' 감소되었습니다.');
		}
	}
	
	public function updateRank(?\Closure $callBack = null) : void{
		$server = Server::getInstance();
		AsyncQueue::submit(new AsyncUpdateRank($this->data['players'], $this->data['settings']['oprank'] ? [] : $server->getOps()->getAll()), $callBack === null ? null : $callBack);
	}
	
	public function getRank(string|Player $player) : int{
		$name = Utils::getLowerCaseName($player);
		return $this->rank['player'][$name] ?? 0;
	}
	
	public function getPlayerByRank(int $rank) : ?string{
		return $this->rank['rank'][$rank] ?? null;
	}
	
}