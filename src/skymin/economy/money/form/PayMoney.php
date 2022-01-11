<?php
declare(strict_types = 1);

namespace skymin\economy\money\form;

use skymin\economy\money\MoneyManager;

use pocketmine\form\Form;
use pocketmine\player\Player;

final class PayMoney implements Form{
	use PlayersTrait;
	
	public function jsonSerialize() : array{
		return [
			'type' => 'custom_form',
			'title' => '돈 지불',
			'content' => [
				['type' => 'dropdown',  'text' => '플레이어를 선택하세요.', 'options' => $this->players],
				['type' => 'input', '금액을 입력하세요.']
			]
		];
	}
	
	public function handleResponse(Player $player, $data) : void{
		if($data === null) return;
		if(!isset($data[0]) || !isset($data[1]) || !is_numeric($data[1])){
			$this->msg($player, '정확히 입력해 주세요.');
			return;
		}
		$data[0] = $this->players[$data[0]];
		$money = (int) $data[1];
		if($money < 1){
			$this->msg($player, '금액은 양수로 입력해주세요.')
			return;
		}
		$manager = MoneyManager::getInstance();
		if(!$manager->isData($data[0])){
			$this->msg($player, '접속한적 없는 플레이어 입니다.');
			return;
		}
		if($manager->getMoney($player) < $money){
			$this->msg($player, '소지금액보다 많은 금액을 지불 할 수 없습니다');
			return;
		}
		$manager->addMoney($data[0], $money);
		$manager->reduceMoney($player, $money);
		$manager->msg($data[0], $player->getName() . '님께' . $format . '(을)를 받았습니다.'])
		$this->msg($player, $data[0] . '님 께' $manager->format($money) . '(을)를 지불하였습니다.');
	}
	
}