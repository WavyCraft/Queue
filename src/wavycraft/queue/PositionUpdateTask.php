<?php

declare(strict_types=1);

namespace wavycraft\queue;

use pocketmine\scheduler\Task;
use pocketmine\Server;

class PositionUpdateTask extends Task {

    public function onRun() : void{
        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            $position = QueueManager::getQueuePosition($player);
            if ($position > 0) {
                $player->sendTip("You are #$position in the queue...");
            }
        }
        QueueManager::processQueue();
    }
}