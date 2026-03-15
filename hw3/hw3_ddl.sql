-- HW 3 DDL file
--    Create database and table to manage CSUF's basketball team
-- Kshitij Pingle
-- kpingle@csu.fullerton.edu
-- 15 March, 2026


DROP DATABASE IF EXISTS         CSUF_Basketball;
CREATE DATABASE IF NOT EXISTS   CSUF_Basketball;

USE CSUF_Basketball;

DROP USER IF EXISTS 'coach';

-- Use the following indepent CREATE statement for user if MariaDB versions conflict
-- CREATE USER 'coach'@'localhost' IDENTIFIED BY 'coachPassword123';

GRANT SELECT, INSERT, DELETE, UPDATE, EXECUTE 
    ON CSUF_Basketball.* TO 'coach'@'localhost'
    IDENTIFIED BY 'coachPassword123';



CREATE TABLE TeamRoster(
    ID INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    Name_First VARCHAR(100),            -- Prof, you have snake and camel case mixed together, this is exactly how it is in the pdf
    Name_Last VARCHAR(150) NOT NULL,    -- Last Name is a required field
    Street VARCHAR(250),
    City VARCHAR(100),
    State VARCHAR(100),
    Country VARCHAR(100),
    ZipCode VARCHAR(10),

    -- Table level constrint for one column works, not the other way
    CONSTRAINT check_ZipCode
        CHECK (ZipCode REGEXP '(?!0{5})(?!9{5})\\d{5}(-(?!0{4})(?!9{4})\\d{4})?'),      -- Required check from prof's pdf
    
    -- An index to name which is unique. Indexing makes SELECT stmts faster, but UPDATE, DELETE, and INSERT slower
    UNIQUE KEY FullName (Name_Last, Name_First)
);
INSERT INTO TeamRoster VALUES 
    (100, "Donald",              "Duck",    "1313 S Harbour Blvd.",    "Anaheim",          "CA",          "USA",    "92808-3232"),
    (101, "Daisy",               "Duck",    "1180 Seven Seas Dr.",     "Lake Buena Vista", "FL",          "USA",    "32830"),
    (107, "Mickey",              "Mouse",   "1313 S Harbour Blvd.",    "Anaheim",          "CA",          "USA",    "92808-3232"),
    (111, "Pluto",               "Dog",     "1313 S Harbour Blvd.",    "Anaheim",          "CA",          "USA",    "92808-3232"),
    (118, "Scrooge",             "McDuck",  "1180 Seven Seas Dr.",     "Lake Buena Vista", "FL",           "USA",   "32830"),
    (119, "Huebert (Heuy)",      "Duck",    "1180 Seven Seas Dr.",     "Lake Buena Vista", "FL",           "USA",   "32830"),
    (123, "Deuteronomy (Dewey)", "Duck",    "1180 Seven Seas Dr.",     "Lake Buena Vista", "FL",           "USA",   "32830"),
    (128, "Louise",              "Duck",    "1180 Seven Seas Dr.",     "Lake Buena Vista", "FL",           "USA",   "32830"),
    (129, "Phooey",              "Duck",    "1-1 Maihama Urayasu",     "Chiba Prefecture", "Disney Tokyo", "Japan",  NULL),
    (131, "Della",               "Duck",    "77700 Boulevard du Parc", "Coupvray",         "Disney Paris", "France", NULL);



CREATE TABLE Roles (
    ID TINYINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    RoleName VARCHAR(50) NOT NULL UNIQUE
);
INSERT INTO Roles VALUES
    (1, "Visitor"),
    (2, "Player"),
    (3, "Coach"),
    (4, "Manager");


CREATE TABLE Accounts (
    UserID INT UNSIGNED NOT NULL PRIMARY KEY, -- UserID alone is the PK to enforce one role per account
    RoleID TINYINT UNSIGNED NOT NULL,
    PasswordHash VARCHAR(255) NOT NULL,
    
    CONSTRAINT fk_user 
        FOREIGN KEY (UserID) REFERENCES TeamRoster(ID) ON DELETE CASCADE,
    CONSTRAINT fk_role 
        FOREIGN KEY (RoleID) REFERENCES Roles(ID) ON DELETE RESTRICT -- Prevents deleting a role if users are assigned to it
);

-- The passwords are just '!' followed by the first name and last name exactly as they appear
-- Ex. Username: Phooey Duck;   Password: '!Phooey Duck'

INSERT INTO Accounts (UserID, PasswordHash) VALUES 
    (100, 2, '$2y$10$Qkvc.ByOtqvFSek5knN5ketjZB3auM4hNPo6HyvV7N7JsHQkGH/cy'),
    (101, 2, '$2y$10$NIjb8cQaocIDQnCbs4ixhus6HIlCDVC.6980DDejU4Xzju9Tv3dQO'),
    (107, 2, '$2y$10$QXy3aLZ.BLnYapGOqTqn3e1gzDYrQvNFJkql2BWtiC9zGGn0PttOC'),
    (111, 2, '$2y$10$qpjtowr5ozEIxBKx3zL2OehYhgjT4St53ggwlaHMBeCS9WSupWioq'),
    (118, 3, '$2y$10$AqB7QcTPbo61GkglHgQmseOl2qQKb0llfE1w2d1n1CBzh97Pxc0ui'),  -- Scrooge is a coach
    (119, 2, '$2y$10$I73jkb7ZL5O52KwdNp93GuBLteOB9jkPI6iLSkBVPTt/GBgA0rlJC'),
    (123, 3, '$2y$10$sZKYDHgXivAeD29rjoU6dO3p7Xp6TFk58cjl9wqdXP11o8nzMi1De'),  -- Deuteronomy is a coach
    (128, 4, '$2y$10$XfT3l2poy7RmBARiI3s6m.3gwkSIulIt7WMCn99FuMCjForgc8lFO'),  -- Louise is a manager
    (129, 4, '$2y$10$fy7ugZ85exAKra05wRrZSeL2G4zXKeSYGfi9BZexizv4PfQr.cuoK'),  -- Phooey is a manager
    (131, 2, '$2y$10$9itj9u0rhg1qqVJKD7FEueTPFpMmsRBqID44bAP16KSxhOgx5sjRy');


CREATE TABLE Statistics (
    ID INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    Player INT UNSIGNED NOT NULL,              -- Foreign Key Defined first, and is required (i.e. NOT NULL)
    PlayingTimeMin TINYINT(2) UNSIGNED DEFAULT 0,
    PlayingTimeSec TINYINT(2) UNSIGNED DEFAULT 0,
    Points TINYINT(3) UNSIGNED DEFAULT 0,
    Assists TINYINT(3) UNSIGNED DEFAULT 0,
    Rebounds TINYINT(3) UNSIGNED DEFAULT 0,

    -- Table Constraint to connect tables
    CONSTRAINT fk_player 
        Foreign KEY (Player) 
        REFERENCES TeamRoster(ID)
        ON DELETE CASCADE,             -- Stat will be deleted when player is deleted

    -- Table Constraint to have time between the range of "00:01" and "40:00" inclusive
    CONSTRAINT check_PlayingTimeRange CHECK (
        (PlayingTimeMin = 0  AND PlayingTimeSec >= 1  AND PlayingTimeSec < 60) OR -- Lowest is "00:01", and seconds < 60
        (PlayingTimeMin > 0  AND PlayingTimeMin <= 39 AND PlayingTimeSec < 60) OR     
        (PlayingTimeMin = 40 AND PlayingTimeSec = 0)                              -- Max value of "40:00"
    )
);
INSERT INTO Statistics VALUES
    (17, 100, 35, 12, 47, 11, 21),
    (18, 107, 13, 22, 13, 1,  3),
    (19, 111, 10, 0,  18, 2,  4),
    (20, 128, 2,  45, 9,  1,  2),
    (21, 107, 15, 39, 26, 3,  7),
    (22, 100, 29, 47, 27, 9,  8);


