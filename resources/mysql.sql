-- #!mysql

-- #{ table
    -- #{ hub
        CREATE TABLE IF NOT EXISTS hub (
            id INTEGER PRIMARY KEY AUTO_INCREMENT,
            x REAL NOT NULL,
            y REAL NOT NULL,
            z REAL NOT NULL,
            world TEXT NOT NULL,
            UNIQUE(world)
        );
    -- #}
-- #}

-- #{ hub
    -- #{ update
        -- # :x float
        -- # :y float
        -- # :z float
        -- # :world string
        UPDATE hub SET x = :x, y = :y, z = :z WHERE world = :world;
    -- #}

    -- #{ insert
        -- # :x float
        -- # :y float
        -- # :z float
        -- # :world string
        INSERT INTO hub (x, y, z, world)
        VALUES (:x, :y, :z, :world)
        ON DUPLICATE KEY UPDATE x = VALUES(x), y = VALUES(y), z = VALUES(z);
    -- #}

    -- #{ select
        SELECT x, y, z, world FROM hub LIMIT 1;
    -- #}
-- #}
