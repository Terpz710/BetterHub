<?php

declare(strict_types=1);

namespace terpz710\betterhub;

use pocketmine\world\Position;

use poggit\libasynql\libasynql;
use poggit\libasynql\DataConnector;

final class HubManager {

    private DataConnector $database;

    public function __construct(protected Hub $plugin) {
        $this->plugin = $plugin;

        $this->database = libasynql::create($this->plugin, $plugin->getConfig()->get("database"), [
            "sqlite" => "sqlite.sql",
            "mysql" => "mysql.sql"
        ]);

        $this->initializeDatabase();
    }

    private function initializeDatabase() : void{
        $this->database->executeGeneric("hub.init", []);
    }

    public function setHub(Position $position) : void{
        $this->database->executeChange("hub.delete", []);
        $this->database->executeChange("hub.insert", [
            "x" => $position->getX(),
            "y" => $position->getY(),
            "z" => $position->getZ(),
            "world" => $position->getWorld()->getFolderName()
        ]);
    }

    public function getHub(callable $callback) : void{
        $this->database->executeSelect("hub.select", [], function (array $rows) use ($callback): void {
            if (count($rows) === 0) {
                $callback(null);
                return;
            }

            $data = $rows[0];
            $worldManager = Hub::getInstance()->getServer()->getWorldManager();
            $worldName = $data["world"];

            if (!$worldManager->isWorldLoaded($worldName)) {
                if (!$worldManager->loadWorld($worldName)) {
                    $callback(null);
                    return;
                }
            }

            $world = $worldManager->getWorldByName($worldName);
            if ($world === null) {
                $callback(null);
                return;
            }

            $position = new Position((float)$data["x"], (float)$data["y"], (float)$data["z"], $world);
            $callback($position);
        });
    }

    public function deleteHub() : void{
        $this->database->executeChange("hub.delete", []);
    }

    public function close() : void{
        $this->database->close();
    }
}
