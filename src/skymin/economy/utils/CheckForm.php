<?php
declare(strict_types = 1);

namespace skymin\economy\utils;

use pocketmine\form\Form;
use pocketmine\player\Player;

final class CheckForm implements Form{
	
	public function __construct(private Form $beforeForm, private string $msg){}
	
	public function jsonSerialize() : array{
		return [
			'type' => 'modal',
			'title' => '',
			'content' => $this->msg,
			'button1' => '다시작성',
			'button2' => '취소'
		];
	}
	
	public function handleResponse(Player $player, $data) : void{
		if($data === true){
			$player->sendForm($this->beforeForm);
		}
	}
	
}