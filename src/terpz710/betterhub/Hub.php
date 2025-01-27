<?php

declare(strict_types=1);

namespace terpz710\betterhub;

use pocketmine\plugin\PluginBase;

use pocketmine\utils\Config;

use terpz710\betterhub\commands\{HubCommand, SetHubCommand, DeleteHubCommand};

final class Hub extends PluginBase {

    protected static self $instance;

    protected HubManager $manager;

    public Config $messages;

    protected function onLoad() : void{
        self::$instance = $this;
    }

    protected function onEnable() : void{
        $this->saveDefaultConfig();
        $this->saveResource("messages.yml");
        $this->getServer()->getCommandMap()->registerAll("BetterHub", [
            new HubCommand(),
            new SetHubCommand(),
            new DeleteHubCommand()
        ]);

        $this->manager = new HubManager($this);

        $this->messages = new Config($this->getDataFolder() . "messages.yml");
    }

    protected function onDisable() : void{
        $this->manager->close();
    }

    public static function getInstance() : self{
        return self::$instance;
    }

    public function getHubManager() : HubManager{ 
        return $this->manager;
    }
}
