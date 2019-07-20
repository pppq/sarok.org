/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `accesslist`
--

DROP TABLE IF EXISTS `accesslist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accesslist` (
  `entryID` int(10) unsigned NOT NULL DEFAULT '0',
  `userID` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`entryID`),
  KEY `Index_2` (`userID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `accesslog`
--

DROP TABLE IF EXISTS `accesslog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accesslog` (
  `datum` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `micros` int(3) NOT NULL DEFAULT '0',
  `sessid` bigint(15) NOT NULL DEFAULT '0',
  `action` varchar(64) NOT NULL DEFAULT '',
  `referrer` varchar(120) NOT NULL DEFAULT '',
  `ip` varchar(16) NOT NULL DEFAULT '',
  `userCode` int(11) NOT NULL DEFAULT '0',
  `runTime` int(7) NOT NULL DEFAULT '0',
  `numQueries` int(6) NOT NULL DEFAULT '0',
  KEY `referrer` (`referrer`),
  KEY `ip` (`ip`),
  KEY `userCode` (`userCode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bannedip`
--

DROP TABLE IF EXISTS `bannedip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bannedip` (
  `IP` char(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `banCode` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`IP`),
  KEY `bannedIP_banCode` (`banCode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bans`
--

DROP TABLE IF EXISTS `bans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bans` (
  `ID` int(10) unsigned NOT NULL DEFAULT '0',
  `userID` int(10) unsigned NOT NULL DEFAULT '0',
  `banDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `unbanDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `banType` enum('Global','Account','ReadOnly') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Global',
  `reason` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`),
  KEY `Bans_userID` (`userID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bots`
--

DROP TABLE IF EXISTS `bots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bots` (
  `ID` int(6) unsigned NOT NULL DEFAULT '0',
  `login` char(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `createDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `loginDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cache_commentlist`
--

DROP TABLE IF EXISTS `cache_commentlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_commentlist` (
  `category` enum('comments','commentsOfEntries','myComments') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'comments',
  `ID` int(11) NOT NULL DEFAULT '0',
  `ownerID` int(10) unsigned NOT NULL DEFAULT '0',
  `userID` char(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `diaryID` char(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `entryID` int(11) NOT NULL DEFAULT '0',
  `createDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access` enum('ALL','REGISTERED','FRIENDS','PRIVATE','LIST') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'ALL',
  `body` char(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastUsed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`,`ownerID`,`category`),
  KEY `ownerID` (`ownerID`),
  KEY `access` (`access`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cache_entrylist`
--

DROP TABLE IF EXISTS `cache_entrylist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_entrylist` (
  `ID` int(11) NOT NULL DEFAULT '0',
  `ownerID` int(10) unsigned NOT NULL DEFAULT '0',
  `userID` char(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `diaryID` char(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `createDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access` enum('ALL','REGISTERED','FRIENDS','PRIVATE','LIST') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'ALL',
  `body` char(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastUsed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`,`ownerID`),
  KEY `ownerID` (`ownerID`),
  KEY `access` (`access`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `calendar`
--

DROP TABLE IF EXISTS `calendar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `calendar` (
  `userID` int(10) unsigned NOT NULL DEFAULT '0',
  `y` int(10) unsigned NOT NULL DEFAULT '0',
  `m` int(10) unsigned NOT NULL DEFAULT '0',
  `d` int(10) unsigned NOT NULL DEFAULT '0',
  `numPublic` int(10) unsigned NOT NULL DEFAULT '0',
  `numRegistered` int(10) unsigned NOT NULL DEFAULT '0',
  `numFriends` int(10) unsigned NOT NULL DEFAULT '0',
  `numAll` int(10) unsigned NOT NULL DEFAULT '0',
  `numMailsReceived` int(10) unsigned NOT NULL DEFAULT '0',
  `numMailsSent` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`userID`,`y`,`m`,`d`),
  KEY `Calendar_Year` (`y`,`m`),
  KEY `Calendar_Day` (`d`,`m`,`y`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `entryID` int(11) NOT NULL DEFAULT '0',
  `Name` char(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`entryID`,`Name`),
  KEY `Name` (`Name`),
  KEY `entryID` (`entryID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `commentrates`
--

DROP TABLE IF EXISTS `commentrates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commentrates` (
  `userID` int(11) NOT NULL,
  `commentID` int(11) NOT NULL,
  `rate` enum('rulez','sux') NOT NULL DEFAULT 'rulez',
  `createDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`userID`,`commentID`),
  KEY `userID` (`userID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comments` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `isTerminated` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `parentID` int(10) unsigned NOT NULL DEFAULT '0',
  `entryID` int(10) unsigned NOT NULL DEFAULT '0',
  `userID` int(10) unsigned NOT NULL DEFAULT '0',
  `createDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `body` longtext COLLATE utf8_unicode_ci,
  `IP` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `dayDate` date NOT NULL DEFAULT '0000-00-00',
  `rate` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `comments_entryID` (`entryID`),
  KEY `comments_userID` (`userID`),
  KEY `dayDate` (`dayDate`),
  KEY `isTerminated` (`isTerminated`),
  KEY `createDate` (`createDate`),
  KEY `isTerminated_2` (`userID`,`isTerminated`,`createDate`)
) ENGINE=MyISAM AUTO_INCREMENT=1912242 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `entries`
--

DROP TABLE IF EXISTS `entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entries` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `diaryID` int(10) unsigned NOT NULL DEFAULT '0',
  `userID` int(10) unsigned NOT NULL DEFAULT '0',
  `createDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modifyDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access` enum('ALL','REGISTERED','FRIENDS','PRIVATE','LIST') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'ALL',
  `comments` enum('ALL','REGISTERED','FRIENDS','PRIVATE','LIST') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'ALL',
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `body` longtext COLLATE utf8_unicode_ci NOT NULL,
  `body2` longtext COLLATE utf8_unicode_ci NOT NULL,
  `numComments` int(10) unsigned NOT NULL DEFAULT '0',
  `lastComment` datetime DEFAULT '0000-00-00 00:00:00',
  `lastVisit` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `isTerminated` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `moderatorComment` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `category` int(10) unsigned NOT NULL DEFAULT '0',
  `dayDate` date NOT NULL DEFAULT '0000-00-00',
  `rssURL` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `posX` double DEFAULT NULL,
  `posY` double NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `Entires_diaryID` (`diaryID`),
  KEY `Entires_userID` (`userID`),
  KEY `Entires_access` (`access`),
  KEY `dayDate` (`dayDate`),
  KEY `isTerminated` (`isTerminated`),
  KEY `diaryID` (`diaryID`,`access`,`isTerminated`),
  KEY `dayDate_2` (`createDate`),
  KEY `diaryID_2` (`diaryID`,`userID`,`isTerminated`),
  KEY `posX` (`posX`,`posY`)
) ENGINE=MyISAM AUTO_INCREMENT=443165 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `entryaccess`
--

DROP TABLE IF EXISTS `entryaccess`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entryaccess` (
  `entryID` int(10) unsigned NOT NULL DEFAULT '0',
  `userID` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`entryID`,`userID`),
  KEY `entryID` (`entryID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `favourites`
--

DROP TABLE IF EXISTS `favourites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `favourites` (
  `userID` int(10) unsigned NOT NULL DEFAULT '0',
  `entryID` int(10) unsigned NOT NULL DEFAULT '0',
  `lastVisited` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `newComments` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`userID`,`entryID`),
  KEY `Favourites_User` (`userID`),
  KEY `Favourites_entries` (`entryID`),
  KEY `lastVisited` (`lastVisited`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `feeds`
--

DROP TABLE IF EXISTS `feeds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `feeds` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `feedURL` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `blogID` int(10) unsigned NOT NULL DEFAULT '0',
  `lastUpdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `nextUpdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lastEntry` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `contactEmail` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `status` enum('allowed','banned','-') COLLATE utf8_unicode_ci NOT NULL DEFAULT '-',
  `comment` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`),
  KEY `Feeds_URL` (`feedURL`),
  KEY `Feeds_blogID` (`blogID`),
  KEY `nextUpdate` (`nextUpdate`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=97 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `friends`
--

DROP TABLE IF EXISTS `friends`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `friends` (
  `friendOf` int(10) unsigned NOT NULL DEFAULT '0',
  `userID` int(10) unsigned NOT NULL DEFAULT '0',
  `friendType` enum('friend','banned','read') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'friend',
  PRIMARY KEY (`friendOf`,`userID`),
  KEY `Friends_userID` (`userID`),
  KEY `friendOf` (`friendOf`),
  KEY `friendType` (`friendType`,`friendOf`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lastvisits`
--

DROP TABLE IF EXISTS `lastvisits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lastvisits` (
  `userID` int(10) unsigned NOT NULL DEFAULT '0',
  `entryID` int(10) unsigned NOT NULL DEFAULT '0',
  `visitDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`userID`),
  KEY `lastVisits_entryID` (`entryID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mail`
--

DROP TABLE IF EXISTS `mail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mail` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Recipient` int(10) unsigned NOT NULL DEFAULT '0',
  `Sender` int(10) unsigned NOT NULL DEFAULT '0',
  `Date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `Body` longblob NOT NULL,
  `isRead` enum('Y','N') COLLATE utf8_unicode_ci DEFAULT 'N',
  `isDeletedByRecipient` enum('Y','N') COLLATE utf8_unicode_ci DEFAULT 'N',
  `isDeletedBySender` enum('Y','N') COLLATE utf8_unicode_ci DEFAULT 'N',
  `replyOn` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `Mail_Recipient` (`Recipient`),
  KEY `Mail_Sender` (`Sender`),
  KEY `Mail_replyOn` (`replyOn`),
  KEY `Recipient` (`Recipient`,`isRead`),
  KEY `Date` (`Date`),
  KEY `isDeletedBySender` (`Sender`,`isDeletedBySender`),
  KEY `Sender` (`Sender`,`Recipient`),
  KEY `isDeletedByRecipient` (`Recipient`,`isDeletedByRecipient`,`Date`)
) ENGINE=MyISAM AUTO_INCREMENT=660948 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pollanswers`
--

DROP TABLE IF EXISTS `pollanswers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pollanswers` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pollID` int(10) unsigned NOT NULL DEFAULT '0',
  `answer` char(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `numVotes` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `PollAnswers_pollID` (`pollID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `polls`
--

DROP TABLE IF EXISTS `polls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `polls` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userID` int(10) unsigned NOT NULL DEFAULT '0',
  `entryID` int(10) unsigned NOT NULL DEFAULT '0',
  `question` char(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `numAnswers` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `Polls_userID` (`userID`),
  KEY `Polls_entryID` (`entryID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pollvotes`
--

DROP TABLE IF EXISTS `pollvotes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pollvotes` (
  `AnswerID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `UserID` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`AnswerID`,`UserID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `ID` bigint(15) NOT NULL DEFAULT '0',
  `userID` int(10) unsigned NOT NULL DEFAULT '0',
  `createDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `loginDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `activationDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `IP` char(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`),
  KEY `Sessions_userID` (`userID`),
  KEY `activationDate` (`activationDate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `statistics`
--

DROP TABLE IF EXISTS `statistics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `statistics` (
  `userID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `visitors` int(10) unsigned NOT NULL DEFAULT '0',
  `reloads` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`userID`),
  KEY `Statistics_date` (`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `statisticspages`
--

DROP TABLE IF EXISTS `statisticspages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `statisticspages` (
  `Action` char(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `Date` date NOT NULL DEFAULT '0000-00-00',
  `visitors` int(10) unsigned NOT NULL DEFAULT '0',
  `unigueVisitors` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`Action`),
  KEY `StatisticsPages_date` (`Date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `userdata`
--

DROP TABLE IF EXISTS `userdata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userdata` (
  `userID` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `value` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`userID`,`name`),
  KEY `Index_1` (`userID`),
  KEY `Index_2` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `ID` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `login` char(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `pass` char(42) COLLATE utf8_unicode_ci NOT NULL,
  `createDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `loginDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `activationDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `isTerminated` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `login` (`login`),
  KEY `Index_2` (`login`),
  KEY `isTerminated` (`isTerminated`),
  KEY `activationDate` (`activationDate`)
) ENGINE=MyISAM AUTO_INCREMENT=9902 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usertrack`
--

DROP TABLE IF EXISTS `usertrack`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usertrack` (
  `sessionID` char(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `userID` char(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `Date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `IP` char(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `Action` char(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`sessionID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
