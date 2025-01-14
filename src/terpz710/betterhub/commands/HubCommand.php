<?php

declare(strict_types=1);

namespace terpz710\betterhub\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;

use pocketmine\player\Player;

use terpz710\betterhub\Hub;

use terpz710\betterhub\task\TeleportationTask;

use terpz710\betterhub\utils\{Message, Error, Permission};

class HubCommand extends Command implements PluginOwned {

    private $plugin;

    public function __construct() {
        parent::__construct(Hub::getInstance()->getConfig()->get("hub-command-label"));
        $this->setDescription(Hub::getInstance()->getConfig()->get("hub-command-description"));
        $this->setAliases(Hub::getInstance()->getConfig()->get("hub-command-aliases", []));
        $this->setPermission(Permission::PERM_HUB);

        $this->plugin = Hub::getInstance();
    }

    public function execute(CommandSender $sender, string $label, array $args) : bool{
        if (!$sender instanceof Player) {
            $sender->sendMessage(Error::TYPE_USE_COMMAND_INGAME_ONLY);
            return false;
        }

        if (!$this->testPermission($sender)) {
            return false;
        }

        $hubManager = $this->plugin->getHubManager();
        if ($hubManager->getHub() === null) {
            $sender->sendMessage((string) new Message("hub-not-set"));
            return false;
        }

        $sender->sendMessage((string) new Message("preparing-to-teleport"));
        $this->plugin->getScheduler()->scheduleRepeatingTask(new TeleportationTask($sender), 20);
        return true;
    }

    public function getOwningPlugin() : Plugin{
        return $this->plugin;
    }
}
