<?php
declare(strict_types = 1);

namespace skymin\economy\money\task;

use skymin\economy\money\MoneyManager;

use pocketmine\scheduler\AsyncTask;

use skymin\asyncqueue\AsyncQueue;

final class AsyncUpdateRank extends AsyncTask{
	
	public function __construct(private array $players, private array $ops){}
	
	public function onRun() : void{
		$players = (array) $this->players;
		arsort($players);
		$i = 0;
		$result = [];
		foreach($players as $player => $money){
			if(in_array($player, (array) $this->ops, true)) continue;
			$i++;
			$result['player'][$player] = $i;
			$result['rank'][$i] = $player;
		}
		$this->setResult($result);
	}
	
	public function onCompletion() : void{
		MoneyManager::getInstance()->rank = $this->getResult();
		AsyncQueue::callBack($this);
	}
	
}