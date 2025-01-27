<?php

declare(strict_types=1);

namespace terpz710\betterhub\task;

use pocketmine\scheduler\Task;

use pocketmine\world\Position;

use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\effect\EffectInstance;

use pocketmine\player\Player;

use pocketmine\Server;

use terpz710\betterhub\Hub;

use terpz710\betterhub\utils\{Message, Error};

class TeleportationTask extends Task {

    private Player $player;

    private Position $startPosition;

    private int $timer;

    public function __construct(Player $player) {
        $this->player = $player;
        $this->hubManager = Hub::getInstance()->getHubManager();
        $this->startPosition = $player->getPosition();
        $this->timer = Hub::getInstance()->getConfig()->get("timer");

        $player->getEffects()->add(new EffectInstance(VanillaEffects::BLINDNESS(), 20 * $this->timer));
    }

    public function onRun() : void{
        $player = $this->player;

        if (!$player->isOnline()) {
            $this->getHandler()->cancel();
            return;
        }

        $hubPosition = $this->hubManager->getHub();

        if ($hubPosition === null) {
            $player->sendMessage((string) new Message("hub-not-set"));
            $player->getEffects()->remove(VanillaEffects::BLINDNESS());
            $this->getHandler()->cancel();
            return;
        }

        $worldManager = Server::getInstance()->getWorldManager();
        $hubWorldName = $hubPosition->getWorld()->getFolderName();

        if (!$worldManager->isWorldLoaded($hubWorldName)) {
            if (!$worldManager->loadWorld($hubWorldName)) {
                $player->sendMessage(Error::TYPE_WORLD_CANNOT_BE_LOADED);
                $player->getEffects()->remove(VanillaEffects::BLINDNESS());
                $this->getHandler()->cancel();
                return;
            }
        }

        $hubWorld = $worldManager->getWorldByName($hubWorldName);

        if ($hubWorld === null) {
            $player->sendMessage(Error::TYPE_WORLD_CANNOT_BE_LOADED);
            $player->getEffects()->remove(VanillaEffects::BLINDNESS());
            $this->getHandler()->cancel();
            return;
        }

        if ($player->getPosition()->equals($this->startPosition)) {
            $player->sendTip((string) new Message("countdown-tip", ["{timer}"], [$this->timer]));
            $this->timer--;
        } else {
            $player->sendMessage((string) new Message("teleportation-cancelled"));
            $player->getEffects()->remove(VanillaEffects::BLINDNESS());
            $this->getHandler()->cancel();
            return;
        }

        if ($this->timer === 0) {
            $player->getEffects()->remove(VanillaEffects::BLINDNESS());
            $player->teleport($hubPosition);
            $player->sendMessage((string) new Message("successfully-teleported"));
            $this->getHandler()->cancel();
        }
    }
}
