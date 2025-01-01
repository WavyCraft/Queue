<?php

declare(strict_types=1);

namespace wavycraft\queue;

use pocketmine\plugin\PluginBase;

final class Loader extends PluginBase {

    protected static $instance;

    protected function onLoad() : void{
        self::$instance = $this;
    }

    protected function onEnable() : void{
        $this->saveDefaultConfig();
        $this->saveResource("messages.yml");
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        $this->getScheduler()->scheduleRepeatingTask(new PositionUpdateTask(), 20);
    }

    public static function getInstance() : Loader{
        return self::$instance;
    }
}
