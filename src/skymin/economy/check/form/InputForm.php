<?php
declare(strict_types = 1);

namespace skymin\economy\check\form;

use skymin\economy\check\CheckManager;

use pocketmine\form\Form;
use pocketmine\player\Player;

final class InputForm implements Form{
	
	public function jsonSerialize() : array{
		return[
			'type' => 'custom_form',
			'title' => '최소 금액 설정',
			'content' => [
				['type' => 'input',  'text' => '최소 금액을 입력하세요.']
			]
		];
	}
	
	public function handleResponse(Player $player, $data) : void{
		if($data === null) return;
		if(!is_numeric($data[0])){
			$player->sendMessage('숫자만 입력가능합니다.');
			return;
		}
		$a = (int) $data[0];
		if($a < 0){
			$player->sendMessage('양수만 입력가능합니다.');
			return;
		}
		CheckManager::getInstance()->db->__set('min-amount', $a);
		$player->sendMessage('최소 금액이 변경 되었습니다.');
	}
	
}