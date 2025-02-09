-- #!sqlite

-- #{ table
    -- #{ hub
        CREATE TABLE IF NOT EXISTS hub (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            x REAL NOT NULL,
            y REAL NOT NULL,
            z REAL NOT NULL,
            world TEXT NOT NULL
        );
    -- #}
-- #}

-- #{ hub
    -- #{ delete
        DELETE FROM hub;
    -- #}

    -- #{ insert
        -- # :x float
        -- # :y float
        -- # :z float
        -- # :world string
        INSERT INTO hub (x, y, z, world)
        VALUES (:x, :y, :z, :world);
    -- #}

    -- #{ select
        SELECT x, y, z, world FROM hub LIMIT 1;
    -- #}
-- #}
