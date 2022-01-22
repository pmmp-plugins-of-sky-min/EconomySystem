<?php
declare(strict_types = 1);

namespace skymin\economy\shop\command;

use pocketmine\player\Player;
use pocketmine\command\{Command, CommandSender};

final class ShopCommand extends Command{
	
	public function __construct(){
		parent::__construct('상점설정');
		$this->setPermission('economy.op');
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args) : void{
		if($sender instanceof Player) return;
		if(!$this->testPermission($sender)){
			//$sender->sendForm()
		}
	}
	
}