<?php

declare(strict_types=1);

namespace terpz710\betterhub\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;

use pocketmine\player\Player;

use terpz710\betterhub\Hub;

use terpz710\betterhub\utils\{Message, Error, Permission};

class SetHubCommand extends Command implements PluginOwned {

    private $plugin;

    public function __construct() {
        parent::__construct("sethub");
        $this->setDescription("Set the hub location");
        $this->setPermission(Permission::PERM_SETHUB);

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

        $this->plugin->getHubManager()->setHub($sender->getPosition());
        $sender->sendMessage((string) new Message("set-hub"));
        return true;
    }

    public function getOwningPlugin() : Plugin{
        return $this->plugin;
    }
}