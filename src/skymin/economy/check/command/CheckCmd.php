<?php
declare(strict_types = 1);

namespace skymin\economy\check\command;

use skymin\economy\money\MoneyManager;
use skymin\economy\check\CheckManager;

use pocketmine\player\Player;
use pocketmine\command\CommandSender;

use skymin\CommandLib\{BaseCommand, EnumFactory, EnumType};

use function is_numeric;

final class CheckCmd extends BaseCommand{
	
	public function __construct(){
		parent::__construct('수표');
		$this->addParameter(EnumFactory::create('금액', EnumType::INT()));
	}
	
	public function execute(CommandSender $sender, string $commandLabel,  array $args) : void{
		if(!$sender instanceof Player) return;
		if((!isset($args[0])) || (!is_numeric($args[0]))){
			$sender->sendMessage('/수표 <금액>');
			return;
		}
		$amount = (int) $args[0];
		$money = MoneyManager::getInstance();
		if($money->getMoney($sender) < $amount){
			$sender->sendMessage('소지 금액보다 더 높은 수표를 만드실 수 없습니다.');
			return;
		}
		$manager = CheckManager::getInstance();
		$check = $manager->create($amount);
		if($check === false){
			$sender->sendMessage('최소 금액보다 높아야 합니다. 최소 금액: ' . $money->format($manager->db->__get('min-amount')));
			return;
		}
		$money->reduceMoney($sender, $amount);
		$sender->getInventory()->addItem($check);
		$sender->sendMessage('지급 되었습니다.');
	}
	
}