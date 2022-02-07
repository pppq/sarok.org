/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;

/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES */;

/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET UNIQUE_CHECKS=0 */;
/*!40014 SET FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET SQL_NOTES=0 */;

/*!40101 SET CHARACTER_SET_CLIENT='utf8' */;
/*!40101 SET NAMES utf8 */;

--
-- Table structure for table `accesslist`
--
-- This table is currently unused.
--

-- DROP TABLE IF EXISTS `accesslist`;
-- CREATE TABLE `accesslist` (
--   `entryID` int(10) unsigned NOT NULL DEFAULT '0',
--   `userID`  int(10) unsigned NOT NULL DEFAULT '0',
--   PRIMARY KEY (`entryID`),
--   KEY `accesslist_idx_1` (`userID`)
-- ) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `accesslog`
--

DROP TABLE IF EXISTS `accesslog`;
CREATE TABLE `accesslog` (
  `datum`      datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
  `micros`     int(3)       NOT NULL DEFAULT '0',
  `sessid`     bigint(15)   NOT NULL DEFAULT '0',
  `action`     varchar(64)  NOT NULL DEFAULT '',
  `referrer`   varchar(120) NOT NULL DEFAULT '',
  `ip`         varchar(16)  NOT NULL DEFAULT '',
  `userCode`   int(11)      NOT NULL DEFAULT '0',
  `runTime`    int(7)       NOT NULL DEFAULT '0',
  `numQueries` int(6)       NOT NULL DEFAULT '0',
  KEY `accesslog_idx_1` (`referrer`),
  KEY `accesslog_idx_2` (`ip`),
  KEY `accesslog_idx_3` (`userCode`)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `bannedip`
--
-- This table is currently unused; banned IP addresses are loaded from a text file.
--

-- DROP TABLE IF EXISTS `bannedip`;
-- CREATE TABLE `bannedip` (
--   `IP`      char(255)        NOT NULL DEFAULT '',
--   `banCode` int(10) unsigned NOT NULL DEFAULT '0',
--   PRIMARY KEY (`IP`),
--   KEY `bannedip_idx_1` (`banCode`)
-- ) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `bans`
--
-- This table is currently unused.
--

-- DROP TABLE IF EXISTS `bans`;
-- CREATE TABLE `bans` (
--   `ID`        int(10) unsigned NOT NULL DEFAULT '0',
--   `userID`    int(10) unsigned NOT NULL DEFAULT '0',
--   `banDate`   datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
--   `unbanDate` datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
--   `banType`   enum('Global','Account','ReadOnly') NOT NULL DEFAULT 'Global',
--   `reason`    varchar(255)     NOT NULL DEFAULT '',
--   PRIMARY KEY (`ID`),
--   KEY `bans_idx_1` (`userID`)
-- ) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `bots`
--
-- This table is currently unused.
--

-- DROP TABLE IF EXISTS `bots`;
-- CREATE TABLE `bots` (
--   `ID`         int(6) unsigned NOT NULL DEFAULT '0',
--   `login`      char(30)        NOT NULL DEFAULT '',
--   `createDate` datetime        NOT NULL DEFAULT '0000-00-00 00:00:00',
--   `loginDate`  datetime        NOT NULL DEFAULT '0000-00-00 00:00:00'
-- ) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `cache_commentlist`
--

DROP TABLE IF EXISTS `cache_commentlist`;
CREATE TABLE `cache_commentlist` (
  `category`   enum('comments','commentsOfEntries','myComments') NOT NULL DEFAULT 'comments',
  `ID`         int(11)          NOT NULL DEFAULT '0',
  `ownerID`    int(10) unsigned NOT NULL DEFAULT '0',
  `userID`     char(30)         NOT NULL DEFAULT '',
  `diaryID`    char(30)         NOT NULL DEFAULT '',
  `entryID`    int(11)          NOT NULL DEFAULT '0',
  `createDate` datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access`     enum('ALL','REGISTERED','FRIENDS','PRIVATE','LIST') NOT NULL DEFAULT 'ALL',
  `body`       char(60)         NOT NULL DEFAULT '',
  `lastUsed`   datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`,`ownerID`,`category`),
  KEY `cache_commentlist_idx_1` (`ownerID`),
  KEY `cache_commentlist_idx_2` (`access`)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `cache_entrylist`
--

DROP TABLE IF EXISTS `cache_entrylist`;
CREATE TABLE `cache_entrylist` (
  `ID`         int(11)          NOT NULL DEFAULT '0',
  `ownerID`    int(10) unsigned NOT NULL DEFAULT '0',
  `userID`     char(30)         NOT NULL DEFAULT '',
  `diaryID`    char(30)         NOT NULL DEFAULT '',
  `createDate` datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access`     enum('ALL','REGISTERED','FRIENDS','PRIVATE','LIST') NOT NULL DEFAULT 'ALL',
  `body`       char(60)         NOT NULL DEFAULT '',
  `lastUsed`   datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`,`ownerID`),
  KEY `cache_entrylist_idx_1` (`ownerID`),
  KEY `cache_entrylist_idx_2` (`access`)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `calendar`
--

DROP TABLE IF EXISTS `calendar`;
CREATE TABLE `calendar` (
  `userID`           int(10) unsigned NOT NULL DEFAULT '0',
  `y`                int(10) unsigned NOT NULL DEFAULT '0',
  `m`                int(10) unsigned NOT NULL DEFAULT '0',
  `d`                int(10) unsigned NOT NULL DEFAULT '0',
  `numPublic`        int(10) unsigned NOT NULL DEFAULT '0',
  `numRegistered`    int(10) unsigned NOT NULL DEFAULT '0',
  `numFriends`       int(10) unsigned NOT NULL DEFAULT '0',
  `numAll`           int(10) unsigned NOT NULL DEFAULT '0',
  `numMailsReceived` int(10) unsigned NOT NULL DEFAULT '0',
  `numMailsSent`     int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`userID`,`y`,`m`,`d`),
  KEY `calendar_idx_1` (`y`,`m`),
  KEY `calendar_idx_2` (`d`,`m`,`y`)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `categories`
--
-- This table is currently unused.
--

-- DROP TABLE IF EXISTS `categories`;
-- CREATE TABLE `categories` (
--   `entryID` int(11)  NOT NULL DEFAULT '0',
--   `Name`    char(30) NOT NULL DEFAULT '',
--   PRIMARY KEY (`entryID`,`Name`),
--   KEY `categories_idx_1` (`Name`),
--   KEY `categories_idx_2` (`entryID`)
-- ) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `commentrates`
--

DROP TABLE IF EXISTS `commentrates`;
CREATE TABLE `commentrates` (
  `userID`     int(11) NOT NULL,
  `commentID`  int(11) NOT NULL,
  `rate`       enum('rulez','sux') NOT NULL DEFAULT 'rulez',
  `createDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`userID`,`commentID`),
  KEY `commentrates_idx_1` (`userID`)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `ID`           int(10) unsigned NOT NULL AUTO_INCREMENT,
  `isTerminated` enum('Y','N')    NOT NULL DEFAULT 'N',
  `parentID`     int(10) unsigned NOT NULL DEFAULT '0',
  `entryID`      int(10) unsigned NOT NULL DEFAULT '0',
  `userID`       int(10) unsigned NOT NULL DEFAULT '0',
  `createDate`   datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
  `body`         longtext         NOT NULL DEFAULT '',
  `IP`           varchar(255)     NOT NULL DEFAULT '',
  `dayDate`      date             NOT NULL DEFAULT '0000-00-00',
  `rate`         int(11)          NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `comments_idx_1` (`entryID`),
  KEY `comments_idx_2` (`userID`),
  KEY `comments_idx_3` (`dayDate`),
  KEY `comments_idx_4` (`isTerminated`),
  KEY `comments_idx_5` (`createDate`),
  KEY `comments_idx_6` (`userID`,`isTerminated`,`createDate`)
) AUTO_INCREMENT=1912242 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `entries`
--

DROP TABLE IF EXISTS `entries`;
CREATE TABLE `entries` (
  `ID`               int(10) unsigned NOT NULL AUTO_INCREMENT,
  `diaryID`          int(10) unsigned NOT NULL DEFAULT '0',
  `userID`           int(10) unsigned NOT NULL DEFAULT '0',
  `createDate`       datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modifyDate`       datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access`           enum('ALL','REGISTERED','FRIENDS','PRIVATE','LIST') NOT NULL DEFAULT 'ALL',
  `comments`         enum('ALL','REGISTERED','FRIENDS','PRIVATE','LIST') NOT NULL DEFAULT 'ALL',
  `title`            varchar(255)     NOT NULL DEFAULT '',
  `body`             longtext         NOT NULL DEFAULT '',
  `body2`            longtext         NOT NULL DEFAULT '',
  `numComments`      int(10) unsigned NOT NULL DEFAULT '0',
  `lastComment`      datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lastVisit`        datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
  `isTerminated`     enum('Y','N')    NOT NULL DEFAULT 'N',
  `moderatorComment` varchar(255)     NOT NULL DEFAULT '',
  `category`         int(10) unsigned NOT NULL DEFAULT '0',
  `dayDate`          date             NOT NULL DEFAULT '0000-00-00',
  `rssURL`           varchar(255)     NOT NULL DEFAULT '',
  `posX`             double                    DEFAULT NULL,
  `posY`             double                    DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `entries_idx_1` (`diaryID`),
  KEY `entries_idx_2` (`userID`),
  KEY `entries_idx_3` (`access`),
  KEY `entries_idx_4` (`dayDate`),
  KEY `entries_idx_5` (`isTerminated`),
  KEY `entries_idx_6` (`diaryID`,`access`,`isTerminated`),
  KEY `entries_idx_7` (`createDate`),
  KEY `entries_idx_8` (`diaryID`,`userID`,`isTerminated`),
  KEY `entries_idx_9` (`posX`,`posY`)
) AUTO_INCREMENT=443165 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `entryaccess`
--

DROP TABLE IF EXISTS `entryaccess`;
CREATE TABLE `entryaccess` (
  `entryID` int(10) unsigned NOT NULL DEFAULT '0',
  `userID`  int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`entryID`,`userID`),
  KEY `entryaccess_idx_1` (`entryID`)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `favourites`
--

DROP TABLE IF EXISTS `favourites`;
CREATE TABLE `favourites` (
  `userID`      int(10) unsigned NOT NULL DEFAULT '0',
  `entryID`     int(10) unsigned NOT NULL DEFAULT '0',
  `lastVisited` datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
  `newComments` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`userID`,`entryID`),
  KEY `favourites_idx_1` (`userID`),
  KEY `favourites_idx_2` (`entryID`),
  KEY `favourites_idx_3` (`lastVisited`)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `feeds`
--

DROP TABLE IF EXISTS `feeds`;
CREATE TABLE `feeds` (
  `ID`           int(10) unsigned NOT NULL AUTO_INCREMENT,
  `feedURL`      varchar(255)     NOT NULL DEFAULT '',
  `blogID`       int(10) unsigned NOT NULL DEFAULT '0',
  `lastUpdate`   datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
  `nextUpdate`   datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lastEntry`    varchar(255)     NOT NULL DEFAULT '',
  `contactEmail` varchar(255)     NOT NULL DEFAULT '',
  `status`       enum('allowed','banned','-') NOT NULL DEFAULT '-',
  `comment`      varchar(255)     NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`),
  KEY `feeds_idx_1` (`feedURL`),
  KEY `feeds_idx_2` (`blogID`),
  KEY `feeds_idx_3` (`nextUpdate`),
  KEY `feeds_idx_4` (`status`)
) AUTO_INCREMENT=97 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `friends`
--

DROP TABLE IF EXISTS `friends`;
CREATE TABLE `friends` (
  `friendOf`   int(10) unsigned NOT NULL DEFAULT '0',
  `userID`     int(10) unsigned NOT NULL DEFAULT '0',
  `friendType` enum('friend','banned','read') NOT NULL DEFAULT 'friend',
  PRIMARY KEY (`friendOf`,`userID`),
  KEY `friends_idx_1` (`userID`),
  KEY `friends_idx_2` (`friendOf`),
  KEY `friends_idx_3` (`friendType`,`friendOf`)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `lastvisits`
--
-- This table is currently unused.
--

-- DROP TABLE IF EXISTS `lastvisits`;
-- CREATE TABLE `lastvisits` (
--   `userID`    int(10) unsigned NOT NULL DEFAULT '0',
--   `entryID`   int(10) unsigned NOT NULL DEFAULT '0',
--   `visitDate` datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
--   PRIMARY KEY (`userID`),
--   KEY `lastvisits_idx_1` (`entryID`)
-- ) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `mail`
--

DROP TABLE IF EXISTS `mail`;
CREATE TABLE `mail` (
  `ID`        int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Recipient` int(10) unsigned NOT NULL DEFAULT '0',
  `Sender`    int(10) unsigned NOT NULL DEFAULT '0',
  `Date`      datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Title`     varchar(255)     NOT NULL DEFAULT '',
  `Body`      longtext         NOT NULL DEFAULT '',
  `isRead`    enum('Y','N')    NOT NULL DEFAULT 'N',
  `isDeletedByRecipient` enum('Y','N') NOT NULL DEFAULT 'N',
  `isDeletedBySender`    enum('Y','N') NOT NULL DEFAULT 'N',
  `replyOn`   int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `mail_idx_1` (`Recipient`),
  KEY `mail_idx_2` (`Sender`),
  KEY `mail_idx_3` (`replyOn`),
  KEY `mail_idx_4` (`Recipient`,`isRead`),
  KEY `mail_idx_5` (`Date`),
  KEY `mail_idx_6` (`Sender`,`isDeletedBySender`),
  KEY `mail_idx_7` (`Sender`,`Recipient`),
  KEY `mail_idx_8` (`Recipient`,`isDeletedByRecipient`,`Date`)
) AUTO_INCREMENT=660948 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `pollanswers`
--
-- This table is currently unused.
--

-- DROP TABLE IF EXISTS `pollanswers`;
-- CREATE TABLE `pollanswers` (
--   `ID`       int(10) unsigned NOT NULL AUTO_INCREMENT,
--   `pollID`   int(10) unsigned NOT NULL DEFAULT '0',
--   `answer`   char(255)        NOT NULL DEFAULT '',
--   `numVotes` int(10) unsigned NOT NULL DEFAULT '0',
--   PRIMARY KEY (`ID`),
--   KEY `pollanswers_idx_1` (`pollID`)
-- ) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `polls`
--
-- This table is currently unused.
--

-- DROP TABLE IF EXISTS `polls`;
-- CREATE TABLE `polls` (
--   `ID`         int(10) unsigned NOT NULL AUTO_INCREMENT,
--   `userID`     int(10) unsigned NOT NULL DEFAULT '0',
--   `entryID`    int(10) unsigned NOT NULL DEFAULT '0',
--   `question`   char(255)        NOT NULL DEFAULT '',
--   `numAnswers` int(10) unsigned NOT NULL DEFAULT '0',
--   PRIMARY KEY (`ID`),
--   KEY `polls_idx_1` (`userID`),
--   KEY `polls_idx_2` (`entryID`)
-- ) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `pollvotes`
--
-- This table is currently unused.
--

-- DROP TABLE IF EXISTS `pollvotes`;
-- CREATE TABLE `pollvotes` (
--   `AnswerID` int(10) unsigned NOT NULL AUTO_INCREMENT,
--   `UserID`   int(10) unsigned NOT NULL DEFAULT '0',
--   PRIMARY KEY (`AnswerID`,`UserID`)
-- ) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `ID`             bigint(15)       NOT NULL DEFAULT '0',
  `userID`         int(10) unsigned NOT NULL DEFAULT '0',
  `createDate`     datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
  `loginDate`      datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
  `activationDate` datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
  `IP`             char(100)        NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`),
  KEY `sessions_idx_1` (`userID`),
  KEY `sessions_idx_2` (`activationDate`)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `statistics`
--
-- This table is currently unused.
--

-- DROP TABLE IF EXISTS `statistics`;
-- CREATE TABLE `statistics` (
--   `userID`   int(10) unsigned NOT NULL AUTO_INCREMENT,
--   `date`     date             NOT NULL DEFAULT '0000-00-00',
--   `visitors` int(10) unsigned NOT NULL DEFAULT '0',
--   `reloads`  int(10) unsigned NOT NULL DEFAULT '0',
--   PRIMARY KEY (`userID`),
--   KEY `statistics_idx_1` (`date`)
-- ) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `statisticspages`
--
-- This table is currently unused.
--

-- DROP TABLE IF EXISTS `statisticspages`;
-- CREATE TABLE `statisticspages` (
--   `Action`         char(255)        NOT NULL DEFAULT '',
--   `Date`           date             NOT NULL DEFAULT '0000-00-00',
--   `visitors`       int(10) unsigned NOT NULL DEFAULT '0',
--   `unigueVisitors` int(10) unsigned NOT NULL DEFAULT '0',
--   PRIMARY KEY (`Action`),
--   KEY `statisticspages_idx_1` (`Date`)
-- ) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `userdata`
--

DROP TABLE IF EXISTS `userdata`;
CREATE TABLE `userdata` (
  `userID` int(10) unsigned NOT NULL DEFAULT '0',
  `name`   varchar(60)      NOT NULL DEFAULT '',
  `value`  longtext         NOT NULL DEFAULT '',
  PRIMARY KEY (`userID`,`name`),
  KEY `userdata_idx_1` (`userID`),
  KEY `userdata_idx_2` (`name`)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `ID`             int(6) unsigned NOT NULL AUTO_INCREMENT,
  `login`          char(30)        NOT NULL DEFAULT '',
  `pass`           char(42)        NOT NULL DEFAULT 'this-is-not-a-valid-password-hash',
  `createDate`     datetime        NOT NULL DEFAULT '0000-00-00 00:00:00',
  `loginDate`      datetime        NOT NULL DEFAULT '0000-00-00 00:00:00',
  `activationDate` datetime        NOT NULL DEFAULT '0000-00-00 00:00:00',
  `isTerminated`   enum('Y','N')   NOT NULL DEFAULT 'N',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `users_idx_1` (`login`),
  KEY `users_idx_2` (`login`),
  KEY `users_idx_3` (`isTerminated`),
  KEY `users_idx_4` (`activationDate`)
) AUTO_INCREMENT=9902 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `usertrack`
--
-- This table is currently unused.
--

-- DROP TABLE IF EXISTS `usertrack`;
-- CREATE TABLE `usertrack` (
--   `sessionID` char(30)  NOT NULL DEFAULT '',
--   `userID`    char(30)  NOT NULL DEFAULT '',
--   `Date`      datetime  NOT NULL DEFAULT '0000-00-00 00:00:00',
--   `IP`        char(255) NOT NULL DEFAULT '',
--   `Action`    char(255) NOT NULL DEFAULT '',
--   PRIMARY KEY (`sessionID`)
-- ) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/* Restore saved variables in reverse order */

/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
