CREATE TABLE `requestForgotPassword` (
  `id` int(11) NOT NULL,
  `requestUniqId` varchar(350) NOT NULL,
  `userUniqId` varchar(300) NOT NULL,
  `creationTime` int(20) NOT NULL,
  `isValid` int(1) NOT NULL
);

CREATE TABLE `session` (
  `id` int(11) NOT NULL,
  `jwtUniqId` text NOT NULL,
  `sessionUniqId` varchar(255) NOT NULL,
  `userUniqId` varchar(255) NOT NULL,
  `loginTime` bigint(20) NOT NULL,
  `expireTime` varchar(50) NOT NULL,
  `isValid` tinyint(1) NOT NULL,
  `userAgent` varchar(255) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `countryName` varchar(255) DEFAULT NULL,
  `countryCode` varchar(255) DEFAULT NULL,
  `regionName` varchar(255) DEFAULT NULL,
  `regionCode` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `timezone` varchar(255) DEFAULT NULL,
  `zipCode` varchar(255) DEFAULT NULL
);

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `surname` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `userUniqId` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `tfaActive` tinyint(1) NOT NULL,
  `accountActive` tinyint(1) NOT NULL,
  `flaggedTo` int(11) NOT NULL,
  `confirmCode` int(11) NOT NULL,
  `lastLogin` int(11) NOT NULL,
  `lastPasswordChange` int(11) NOT NULL,
  `loginAttempt` int(11) NOT NULL,
  `registrationDate` int(11) NOT NULL
)

ALTER TABLE `requestForgotPassword`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `requestUniqId` (`requestUniqId`);

ALTER TABLE `session`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `userUniqId` (`userUniqId`);

ALTER TABLE `requestForgotPassword`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `session`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
