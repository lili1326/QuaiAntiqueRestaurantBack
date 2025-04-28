CREATE TABLE `User` (
  `id` int PRIMARY KEY,
  `uuid` char(36),
  `firstName` varchar(32),
  `lastName` varchar(65),
  `email` varchar(64),
  `password` varchar(255),
  `roles` json,
  `guestNumber` smallint,
  `allergy` varchar(255),
  `createAt` datetime,
  `updateAt` datetime
);

CREATE TABLE `Restaurant` (
  `id` int PRIMARY KEY,
  `uuid` char(36),
  `name` varchar(32),
  `description` longtext,
  `amOpenTime` json,
  `apOpenTime` json,
  `maxGuest` smallint,
  `createAt` datetime,
  `updateAt` datetime,
  `owner` int
);

CREATE TABLE `Picture` (
  `id` int PRIMARY KEY,
  `title` char(128),
  `slug` char(128),
  `createAt` datetime,
  `updateAt` datetime,
  `restaurant` int
);

ALTER TABLE `Restaurant` ADD FOREIGN KEY (`owner`) REFERENCES `User` (`id`);

ALTER TABLE `Picture` ADD FOREIGN KEY (`restaurant`) REFERENCES `Restaurant` (`id`);
