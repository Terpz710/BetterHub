<?php

declare(strict_types=1);

namespace terpz710\betterhub;

use pocketmine\world\Position;
use poggit\libasynql\libasynql;
use poggit\libasynql\DataConnector;

final class HubManager {

    private DataConnector $database;

    public function __construct(protected Hub $plugin) {
        $this->database = libasynql::create($this->plugin, $plugin->getConfig()->get("database"), [
            "sqlite" => "sqlite.sql",
            "mysql" => "mysql.sql"
        ]);

        $this->initializeDatabase();
    }

    private function initializeDatabase() : void{
        $this->database->executeGeneric("hub.init", [], function (): void {
            $this->plugin->getLogger()->info("Hub table initialized successfully.");
        }, function (\Exception $e): void {
            $this->plugin->getLogger()->error("Failed to initialize hub table: " . $e->getMessage());
        });
    }

    public function setHub(Position $position) : void{
        $this->database->executeChange("hub.delete", [], null, function (\Exception $e): void {
            $this->plugin->getLogger()->error("Failed to delete existing hub: " . $e->getMessage());
        });

        $this->database->executeChange("hub.insert", [
            "x" => $position->getX(),
            "y" => $position->getY(),
            "z" => $position->getZ(),
            "world" => $position->getWorld()->getFolderName()
        ], function (int $affectedRows): void {
            if ($affectedRows > 0) {
                $this->plugin->getLogger()->info("Hub position set successfully.");
            }
        }, function (\Exception $e): void {
            $this->plugin->getLogger()->error("Failed to set hub position: " . $e->getMessage());
        });
    }

    public function getHub(callable $callback): void {
        $this->database->executeSelect("hub.select", [], function (array $rows) use ($callback): void {
            if (empty($rows)) {
                $callback(null);
                return;
            }

            $data = $rows[0];
            $worldManager = $this->plugin->getServer()->getWorldManager();
            $worldName = $data["world"];

            if (!$worldManager->isWorldLoaded($worldName) && !$worldManager->loadWorld($worldName)) {
                $callback(null);
                return;
            }

            $world = $worldManager->getWorldByName($worldName);
            if ($world === null) {
                $callback(null);
                return;
            }

            $position = new Position((float)$data["x"], (float)$data["y"], (float)$data["z"], $world);
            $callback($position);
        }, function (\Exception $e) use ($callback): void {
            $this->plugin->getLogger()->error("Failed to fetch hub position: " . $e->getMessage());
        $callback(null);
        });
    }

    public function deleteHub() : void{
        $this->database->executeChange("hub.delete", [], function (int $affectedRows): void {
            if ($affectedRows > 0) {
                $this->plugin->getLogger()->info("Hub position deleted successfully.");
            }
        }, function (\Exception $e): void {
            $this->plugin->getLogger()->error("Failed to delete hub position: " . $e->getMessage());
        });
    }

    public function close() : void{
        $this->database->close();
    }
}
