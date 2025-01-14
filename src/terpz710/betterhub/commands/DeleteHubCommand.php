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

class DeleteHubCommand extends Command implements PluginOwned {

    private $plugin;

    public function __construct() {
        parent::__construct(Hub::getInstance()->getConfig()->get("deletehub-command-label"));
        $this->setDescription(Hub::getInstance()->getConfig()->get("deletehub-command-description"));
        $this->setAliases(Hub::getInstance()->getConfig()->get("deletehub-command-aliases", []));
        $this->setPermission(Permission::PERM_DELHUB);

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

        $this->plugin->getHubManager()->deleteHub();
        $sender->sendMessage((string) new Message("hub-deleted"));
        return true;
    }

    public function getOwningPlugin() : Plugin{
        return $this->plugin;
    }
}
