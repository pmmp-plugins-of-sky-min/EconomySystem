<?php
declare(strict_types = 1);

namespace skymin\economy\check\form;

use skymin\economy\check\CheckManager;

use pocketmine\form\Form;
use pocketmine\player\Player;

final class SettingForm implements Form{
	
	public function jsonSerialize() : array{
		return [
			'type' => 'form',
			'title' => '설정',
			'content' => '원하시는 것을 선택하세요.',
			'buttons' => [
				['text' => '최소 금액 설정'], 
				['text' => '수표 아이템 설정']
			]
		];
	}
	
	public function handleResponse(Player $player, $data) : void{
		if($data === 0){
			$player->sendForm(new InputForm());
			return;
		}
		if($data === 1){
			$item = $player->getInventory()->getItemInHand();
			if($item->isNull()){
				$player->sendMessage('공기로 설정 할 수 없습니다.');
				return;
			}
			CheckManager::getInstance()->db->__set('item', $item);
			$player->sendMessage('수표 아이템이 변경 되었습니다. ')
		}
	}
	
}