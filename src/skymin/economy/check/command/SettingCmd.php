<?php
declare(strict_types = 1);

namespace skymin\economy\check\command;

use skymin\economy\check\form\SettingForm;

use pocketmine\player\Player;
use pocketmine\command\{Command, CommandSender};

final class SettingCmd extends Command{
	
	public function __construct(){
		parent::__construct('수표설정');
		$this->setPermission('economy.op');
	}
	
	public function execute(CommandSender $sender, string $commandLabel,  array $arga) : void{
		if(!$this->testPermission($sender)) return;
		if($sender instanceof Player){
			$sender->sendForm(new SettingForm());
		}
	}
	
}
