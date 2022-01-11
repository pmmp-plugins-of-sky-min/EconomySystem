<?php
declare(strict_types = 1);

namespace skymin\economy\money\form;

use skymin\economy\money\MoneyManager;

use pocketmine\form\Form;
use pocketmine\player\Player;

final class ReduceMoney implements Form{
	use PlayersTrait;
	
	public function jsonSerialize() : array{
		return [
			'type' => 'custom_form',
			'title' => '돈 뺏기',
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
		$manager = MoneyManager::getInstance();
		if(!$manager->isData($data[0])){
			$this->msg($player, '접속한적 없는 플레이어 입니다.');
			return;
		}
		if($manager->getMoney($data[0]) < $money){
			$this->msg($playere, '플레이어 소지 금액보다 많습니다.');
		}
		$manager->reduceMoney($data[0], $money, true);
		$this->msg($player, '성공적으로 ' . $manager->format($money) . '(을)를 뺏었습니다.');
	}
	
}