<?php

declare(strict_types=1);

namespace wavycraft\queue;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;

use pocketmine\Server;

class EventListener implements Listener {

    public function onPlayerJoin(PlayerJoinEvent $event) : void{
        $server = Server::getInstance();
        $config = Loader::getInstance()->getConfig();

        $queueSlot = $config->get("queue_slot");
        $player = $event->getPlayer();

        if (count($server->getOnlinePlayers()) > $queueSlot) {
            QueueManager::addToQueue($player);
        }
    }

    public function onPlayerQuit(PlayerQuitEvent $event) : void{
        QueueManager::removeFromQueue($event->getPlayer());
    }
}
