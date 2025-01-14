<?php

declare(strict_types=1);

namespace terpz710\betterhub;

use pocketmine\player\Player;

use pocketmine\utils\SingletonTrait;

use pocketmine\world\Position;

use SQLite3;

final class HubManager {
    use SingletonTrait;

    private $plugin;
    private $db;

    public function __construct() {
        $this->plugin = Hub::getInstance();

        $this->db = new SQLite3($this->plugin->getDataFolder() . "hub.db");
        $this->initializeDatabase();
    }

    private function initializeDatabase() : void{
        $this->db->exec("CREATE TABLE IF NOT EXISTS hub (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            x REAL,
            y REAL,
            z REAL,
            world TEXT
        )");
    }

    public function setHub(Position $position) : void{
        $this->db->exec("DELETE FROM hub");

        $stmt = $this->db->prepare("INSERT INTO hub (x, y, z, world) VALUES (:x, :y, :z, :world)");
        $stmt->bindValue(":x", $position->getX(), SQLITE3_FLOAT);
        $stmt->bindValue(":y", $position->getY(), SQLITE3_FLOAT);
        $stmt->bindValue(":z", $position->getZ(), SQLITE3_FLOAT);
        $stmt->bindValue(":world", $position->getWorld()->getFolderName(), SQLITE3_TEXT);
        $stmt->execute();
        $stmt->close();
    }

    public function getHub() : ?Position{
        $result = $this->db->query("SELECT x, y, z, world FROM hub LIMIT 1");
        $data = $result->fetchArray(SQLITE3_ASSOC);

        if ($data === false) {
            return null;
        }

        $worldManager = $this->plugin->getServer()->getWorldManager();
        $worldName = $data["world"];

        if (!$worldManager->isWorldLoaded($worldName)) {
            if (!$worldManager->loadWorld($worldName)) {
                return null;
            }
        }

        $world = $worldManager->getWorldByName($worldName);
        if ($world === null) {
            return null;
        }

        return new Position($data["x"], $data["y"], $data["z"], $world);
    }

    public function deleteHub() : void{
        $this->db->exec("DELETE FROM hub");
    }
}