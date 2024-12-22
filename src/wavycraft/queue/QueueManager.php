<?php

declare(strict_types=1);

namespace wavycraft\queue;

use pocketmine\player\Player;

use pocketmine\Server;

use pocketmine\world\Position;

final class QueueManager {

    private static array $queue = [];

    public static function addToQueue(Player $player) : void{
        $config = Loader::getInstance()->getConfig();
        $queueWorldName = $config->get("queue_world");
        $queueX = $config->get("queue_x");
        $queueY = $config->get("queue_y");
        $queueZ = $config->get("queue_z");

        $worldManager = Server::getInstance()->getWorldManager();

        $world = $worldManager->getWorldByName($queueWorldName);
        if ($world === null) {
            if (!$worldManager->loadWorld($queueWorldName)) {
                $player->kick("Queue world not found...");
                return;
            }
            $world = $worldManager->getWorldByName($queueWorldName);
        }

        self::$queue[] = $player->getName();
        $player->teleport(new Position((float)$queueX, (float)$queueY, (float)$queueZ, $world));
        $player->setNoClientPredictions(true);
    }

    public static function processQueue() : void{
        $server = Server::getInstance();
        $config = Loader::getInstance()->getConfig();
        $worldName = $config->get("world");
        $worldX = $config->get("world_x");
        $worldY = $config->get("world_y");
        $worldZ = $config->get("world_z");

        $worldManager = $server->getWorldManager();

        $world = $worldManager->getWorldByName($worldName);
        if ($world === null && !$worldManager->loadWorld($worldName)) {
            foreach (self::$queue as $playerName) {
                $player = $server->getPlayerExact($playerName);
                if ($player !== null) {
                    $player->kick("Destination world not found...");
                }
            }
            return;
        }

        $slotsAvailable = $server->getMaxPlayers() - count($server->getOnlinePlayers());

        while ($slotsAvailable > 0 && !empty(self::$queue)) {
            $playerName = array_shift(self::$queue);
            $player = $server->getPlayerExact($playerName);

            if ($player !== null) {
                $player->teleport(new Position((float)$worldX, (float)$worldY, (float)$worldZ, $world));
                $player->setNoClientPredictions(false);
            }

            $slotsAvailable--;
        }
    }

    public static function getQueuePosition(Player $player) : int{
        return array_search($player->getName(), self::$queue) + 1;
    }

    public static function removeFromQueue(Player $player) : void{
        $playerName = $player->getName();
        $key = array_search($playerName, self::$queue, true);

        if ($key !== false) {
            unset(self::$queue[$key]);
            self::$queue = array_values(self::$queue);
        }
    }
}