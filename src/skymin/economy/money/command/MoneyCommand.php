<?php
declare(strict_types = 1);

namespace skymin\economy\money\command;

use skymin\economy\money\MoneyManager;
use skymin\economy\money\form\{Main, MoneyRank};

use pocketmine\command\CommandSender;

use skymin\CommandLib\{BaseCommand, EnumFactory, EnumType};

use function count;
use function in_array;
use function array_keys;
use function is_numeric;

final class MoneyCommand extends BaseCommand{
	
	public const CMDS = ['지급', '뺏기', '설정', '지불', '순위'];
	
	public function __construct(private MoneyManager $manager){
		parent::__construct('돈');
		$this->setOverloadPermission(0, 'economy.op');
		$this->setOverloadPermission(1, 'economy.op');
		$this->setOverloadPermission(2, 'economy.op');
		$this->addParameter(EnumFactory::create('돈', '지급', ['지급']), 0);
		$this->addParameter(EnumFactory::create('플레이어', EnumType::STRING()), 0);
		$this->addParameter(EnumFactory::create('돈', EnumType::INT()), 0);
		$this->addParameter(EnumFactory::create('돈', '뺏기', ['뺏기']), 1);
		$this->addParameter(EnumFactory::create('플레이어', EnumType::STRING()), 1);
		$this->addParameter(EnumFactory::create('돈', EnumType::INT()), 1);
		$this->addParameter(EnumFactory::create('돈', '설정', ['설정']), 2);
		$this->addParameter(EnumFactory::create('플레이어', EnumType::STRING()), 2);
		$this->addParameter(EnumFactory::create('돈', EnumType::INT()), 2);
		$this->addParameter(EnumFactory::create('돈', '지불', ['지불']), 3);
		$this->addParameter(EnumFactory::create('플레이어', EnumType::STRING()), 3);
		$this->addParameter(EnumFactory::create('돈', EnumType::INT()), 3);
		$this->addParameter(EnumFactory::create('돈', '순위', ['순위']), 4);
		$this->addParameter(EnumFactory::create('돈', '돈', ['']), 3);
		$this->update();
	}
	
	public function update() : void{
		$players = [];
		foreach(array_keys($this->manager->data['players']) as $player){
			$players[] = '"' . $player . '"';
		}
		if(count($players) > 1){
			$this->setParameter(EnumFactory::create('플레이어', 'string', $players), 1, 0);
			$this->setParameter(EnumFactory::create('플레이어', 'string', $players), 1, 1);
			$this->setParameter(EnumFactory::create('플레이어', 'string', $players), 1, 2);
			$this->setParameter(EnumFactory::create('플레이어', 'string', $players), 1, 3);
		}
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args) : void{
		$manager = $this->manager;
		$manager->updateRank();
		if(!isset($args[0]) || !in_array($args[0], self::CMDS)){
			if($sender->getName() !== 'CONSOLE'){
				$sender->sendForm(new Main($sender));
			}else{
				$msg = "돈 지불 - 플레이어에게 돈을 지불합니다. \n돈 순위 - 돈 순위를 확인합니다.\n";
				if($sender->hasPermission('economy.op')){
					$msg .= "돈 지급 - 플레이어에게 돈을 지급합니다. \n돈 뺏기 - 플레이어의 돈을 뺏습니다. \n돈 설정 - 플레이어의 돈을 설정합니다. \n";
				}
				$sender->sendMessage($msg);
			}
			return;
		}
		$subcmd = $args[0];
		if($subcmd === self::CMDS[0]){
			if(!isset($args[1]) || !isset($args[2]) || !is_numeric($args[2])){
				$sender->sendMessage('/돈 지급 [플레이어] [돈]');
				return;
			}
			if(!$manager->isData($args[1])){
				$sender->sendMessage('접속한적 없는 플레이어 입니다.');
				return;
			}
			if((int) $args[2] < 0){
				$sender->sendMessage('양수만 가능합니다.');
			}
			$manager->addMoney($args[1], (int) $args[2], true);
			$sender->sendMessage('성공적으로 ' . $manager->format((int) $args[2]) . '(을)를 지급하였습니다.');
			return;
		}
		if($subcmd === self::CMDS[1]){
			if(!isset($args[1]) || !isset($args[2]) || !is_numeric($args[2])){
				$sender->sendMessage('/돈 뺏기 [플레이어] [돈]');
				return;
			}
			if(!$manager->isData($args[1])){
				$sender->sendMessage('접속한적 없는 플레이어 입니다.');
				return;
			}
			if($manager->getMoney($args[1]) < (int) $args[2]){
				$sender->sendMessage('플레이어 소지금액보다 많습니다.');
			}
			$manager->reduceMoney($args[1], (int) $args[2], true);
			$sender->sendMessage('성공적으로 ' . $manager->format((int) $args[2]) . '(을)를 뺏었습니다.');
			return;
		}
		if($subcmd === self::CMDS[2]){
			if(!isset($args[1]) || !isset($args[2]) || !is_numeric($args[2])){
				$sender->sendMessage('/돈 설정 [플레이어] [돈]');
				return;
			}
			if(!$manager->isData($args[1])){
				$sender->sendMessage('접속한적 없는 플레이어 입니다.');
				return;
			}
			if((int) $args[2] < 0){
				$sender->sendMessage('음수로 설정 할 수 없습니다.');
				return;
			}
			$manager->setMoney($args[1], (int) $args[2], true);
			$sender->sendMessage('성공적으로 ' . $manager->format((int) $args[2]) . '(으)로 설정 하였습니다.');
			return;
		}
		if($subcmd === self::CMDS[3]){
			if($sender->getName() === 'CONSOLE') return;
			if(!isset($args[1]) || !isset($args[2]) || !is_numeric($args[2])){
				$sender->sendMessage('/돈 지불 [플레이어] [돈]');
				return;
			}
			if(!$manager->isData($args[1])){
				$sender->sendMessage('접속한적 없는 플레이어 입니다.');
				return;
			}
			$name = $sender->getName();
			if(strtolower($name) === strtolower($args[1])){
				$sender->sendMessage('자기 자신에게 지불 할 수 없습니다.');
				return;
			}
			$money = (int) $args[2];
			if($money < 1){
				$sender->sendMessage('양수로 된 금액으로만 지불 하실 수 있습니다.');
			}
			if($manager->getMoney($name) < $money){
				$sender->sendMessage('소지금액보다 많은 금액을 지불 할 수 없습니다.');
				return;
			}
			$manager->addMoney($args[1], $money);
			$manager->reduceMoney($name, $money);
			$format = $manager->format($money);
			$manager->msg($args[1], $name . '님께 ' . $format . '(을)를 받았습니다.');
			$manager->msg($sender, $args[1] . '님께 ' . $format . '(을)를 지불하였습니다.');
			return;
		}
		if($subcmd === self::CMDS[4]){
			if($sender->getName() !== 'CONSOLE'){
				$sender->sendForm(new MoneyRank());
			}
		}
	}
	
}