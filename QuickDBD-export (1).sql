-- Exported from QuickDBD: https://www.quickdatabasediagrams.com/
-- NOTE! If you have used non-SQL datatypes in your design, you will have to change these here.


CREATE TABLE `User` (
    `id` int  NOT NULL ,
    `uuid` CHAR(36)  NOT NULL ,
    `firstName` VARCHAR(32)  NOT NULL ,
    `lastName` VARCHER(65)  NOT NULL ,
    `email` VARCHAR(64)  NOT NULL ,
    `password` VARCHAR(255)  NOT NULL ,
    `roles` JSON  NOT NULL ,
    `guetNumber` SMALLINT?  NOT NULL ,
    `allergy` VARCHAR(255)?  NOT NULL ,
    `createAt` DATETIME  NOT NULL ,
    `updateAt` DATETIME?  NOT NULL ,
    PRIMARY KEY (
        `id`
    )
);

CREATE TABLE `Restaurant` (
    `id` int  NOT NULL ,
    `uuid` CHAR(36)  NOT NULL ,
    `name` VARCHAR(32)  NOT NULL ,
    `description` LONGTEXT  NOT NULL ,
    `amOpenTile` JSON  NOT NULL ,
    `apOpenTime` JSON  NOT NULL ,
    `maxGuest` SMALLINT  NOT NULL ,
    `createAt` DATETIME  NOT NULL ,
    `updateAt` DATETIME?  NOT NULL ,
    `owner` ONE_TO_ONE  NOT NULL ,
    PRIMARY KEY (
        `id`
    )
);

-- 1 Restarant a 1 owner
CREATE TABLE `Booking` (
    `id` int  NOT NULL ,
    `uuid` CHAR(36)  NOT NULL ,
    `guestNumber` SMALLINT  NOT NULL ,
    `orderDate` DATE  NOT NULL ,
    `orderHour` DATETIME  NOT NULL ,
    `allergy` VARCHAR(255)?  NOT NULL ,
    `createAt` DATETIME  NOT NULL ,
    `updateAt` DATETIME?  NOT NULL ,
    `Restaurant` MANY_TO_ONE  NOT NULL ,
    `Client` MANY_TO_ONE  NOT NULL ,
    PRIMARY KEY (
        `id`
    )
);

CREATE TABLE `Picture` (
    `id` int  NOT NULL ,
    `title` CHAR(128)  NOT NULL ,
    `slug` CHAR(128)  NOT NULL ,
    `createAt` DATETIME  NOT NULL ,
    `updateAt` DATETIME?  NOT NULL ,
    `restaurant` MANY_TO_ONE  NOT NULL ,
    PRIMARY KEY (
        `id`
    )
);

-- plusieurs Picture pour 1 restaurant
CREATE TABLE `Category` (
    `id` int  NOT NULL ,
    `uuid` CHAR(36)  NOT NULL ,
    `title` CHAR(128)  NOT NULL ,
    `createAt` DATETIME  NOT NULL ,
    `updateAt` DATETIME?  NOT NULL ,
    PRIMARY KEY (
        `id`
    )
);

CREATE TABLE `Menu` (
    `id` int  NOT NULL ,
    `uuid` CHAR(36)  NOT NULL ,
    `title` CHAR(128)  NOT NULL ,
    `description` LONGTEXT  NOT NULL ,
    `price` SMALLINT  NOT NULL ,
    `createAt` DATETIME  NOT NULL ,
    `updateAt` DATETIME?  NOT NULL ,
    `restaurant` MANY_TO_ONE  NOT NULL ,
    PRIMARY KEY (
        `id`
    )
);

CREATE TABLE `Food` (
    `id` int  NOT NULL ,
    `uuid` CHAR(36)  NOT NULL ,
    `title` CHAR(128)  NOT NULL ,
    `description` LONGTEXT  NOT NULL ,
    `price` SMALLINT  NOT NULL ,
    `createAt` DATETIME  NOT NULL ,
    `updateAt` DATETIME?  NOT NULL ,
    PRIMARY KEY (
        `id`
    )
);

CREATE TABLE `Menu_Category` (
    `id` int  NOT NULL ,
    `MenuId` MANY_TO_ONE  NOT NULL ,
    -- plusieurs menu-category pour 1  menu
    `CategoryId` MANY_TO_ONE  NOT NULL ,
    PRIMARY KEY (
        `id`
    )
);

-- plusieurs menu-category pour 1 category
CREATE TABLE `Food_Category` (
    `id` int  NOT NULL ,
    `FoodId` MANY_TO_ONE  NOT NULL ,
    `CategoryId` MANY_TO_ONE  NOT NULL ,
    PRIMARY KEY (
        `id`
    )
);

ALTER TABLE `Restaurant` ADD CONSTRAINT `fk_Restaurant_owner` FOREIGN KEY(`owner`)
REFERENCES `User` (`id`);

ALTER TABLE `Booking` ADD CONSTRAINT `fk_Booking_Restaurant` FOREIGN KEY(`Restaurant`)
REFERENCES `Restaurant` (`id`);

ALTER TABLE `Booking` ADD CONSTRAINT `fk_Booking_Client` FOREIGN KEY(`Client`)
REFERENCES `User` (`id`);

ALTER TABLE `Picture` ADD CONSTRAINT `fk_Picture_restaurant` FOREIGN KEY(`restaurant`)
REFERENCES `Restaurant` (`id`);

ALTER TABLE `Menu` ADD CONSTRAINT `fk_Menu_restaurant` FOREIGN KEY(`restaurant`)
REFERENCES `Restaurant` (`id`);

ALTER TABLE `Menu_Category` ADD CONSTRAINT `fk_Menu_Category_MenuId` FOREIGN KEY(`MenuId`)
REFERENCES `Menu` (`id`);

ALTER TABLE `Menu_Category` ADD CONSTRAINT `fk_Menu_Category_CategoryId` FOREIGN KEY(`CategoryId`)
REFERENCES `Category` (`id`);

ALTER TABLE `Food_Category` ADD CONSTRAINT `fk_Food_Category_FoodId` FOREIGN KEY(`FoodId`)
REFERENCES `Food` (`id`);

ALTER TABLE `Food_Category` ADD CONSTRAINT `fk_Food_Category_CategoryId` FOREIGN KEY(`CategoryId`)
REFERENCES `Category` (`id`);

