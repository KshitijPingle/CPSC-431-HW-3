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
    PasswordHash VARCHAR(255) NOT NULL,
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
    (100, "Donald",              "Duck",     "1313 S Harbour Blvd.",    "Anaheim",          "CA",          "USA",    "92808-3232"),
    (101, "Daisy",               "Duck",     "1180 Seven Seas Dr.",     "Lake Buena Vista", "FL",          "USA",    "32830"),
    (107, "Mickey",              "Mouse",    "1313 S Harbour Blvd.",    "Anaheim",          "CA",          "USA",    "92808-3232"),
    (111, "Pluto",               "Dog",      "1313 S Harbour Blvd.",    "Anaheim",          "CA",          "USA",    "92808-3232"),
    (118, "Scrooge",             "McDuck",   "1180 Seven Seas Dr.",     "Lake Buena Vista", "FL",           "USA",   "32830"),
    (119, "Huebert (Heuy)",      "Duck",     "1180 Seven Seas Dr.",     "Lake Buena Vista", "FL",           "USA",   "32830"),
    (123, "Deuteronomy (Dewey)", "Duck",     "1180 Seven Seas Dr.",     "Lake Buena Vista", "FL",           "USA",   "32830"),
    (128, "Louise",              "Duck",     "1180 Seven Seas Dr.",     "Lake Buena Vista", "FL",           "USA",   "32830"),
    (129, "Phooey",              "Duck",     "1-1 Maihama Urayasu",     "Chiba Prefecture", "Disney Tokyo", "Japan",  NULL),
    (131, "Della",               "Duck",     "77700 Boulevard du Parc", "Coupvray",         "Disney Paris", "France", NULL);



CREATE TABLE Roles (
    ID TINYINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    RoleName VARCHAR(50) NOT NULL UNIQUE
);
INSERT INTO Roles VALUES
    (0, "Visitor"),
    (1, "Player"),
    (2, "Coach"),
    (3, "Manager");


CREATE TABLE Accounts (
    UserID INT UNSIGNED NOT NULL,
    RoleID TINYINT UNSIGNED NOT NULL,
    PRIMARY KEY (UserID, RoleID),
    CONSTRAINT fk_user 
        FOREIGN KEY (UserID) REFERENCES TeamRoster(ID) ON DELETE CASCADE,
    CONSTRAINT fk_role 
        FOREIGN KEY (RoleID) REFERENCES Roles(ID) ON DELETE CASCADE
);


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


