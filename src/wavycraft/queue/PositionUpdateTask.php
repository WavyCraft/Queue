<?php

declare(strict_types=1);

namespace wavycraft\queue;

use pocketmine\scheduler\Task;

use pocketmine\utils\Config;

use pocketmine\Server;

class PositionUpdateTask extends Task {

    public function onRun() : void{
        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            $position = QueueManager::getQueuePosition($player);
            if ($position > 0) {
                $messages = new Config(Loader::getInstance()->getDataFolder() . "messages.yml", Config::YAML);
                $msg = $messages->get("queue_position");
                $msg = str_replace("{position}", (string)$position, $msg);
                $player->sendTip($msg);
            }
        }
        QueueManager::processQueue();
    }
}
