-- Initialize the hub table
hub.init:
CREATE TABLE IF NOT EXISTS hub (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    x REAL,
    y REAL,
    z REAL,
    world TEXT
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