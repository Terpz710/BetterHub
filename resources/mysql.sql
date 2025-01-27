-- Initialize the hub table
hub.init:
CREATE TABLE IF NOT EXISTS hub (
    id INT AUTO_INCREMENT PRIMARY KEY,
    x DOUBLE NOT NULL,
    y DOUBLE NOT NULL,
    z DOUBLE NOT NULL,
    world VARCHAR(255) NOT NULL
);

-- Delete all hub entries
hub.delete:
DELETE FROM hub;

-- Insert a hub position
hub.insert:
INSERT INTO hub (x, y, z, world) VALUES (:x, :y, :z, :world);

-- Select the hub position
hub.select:
SELECT x, y, z, world FROM hub LIMIT 1;