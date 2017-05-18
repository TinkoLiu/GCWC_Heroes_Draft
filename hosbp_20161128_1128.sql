-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2016-11-28 04:28:17
-- 服务器版本： 10.1.19-MariaDB
-- PHP Version: 5.6.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hosbp`
--

-- --------------------------------------------------------

--
-- 表的结构 `heroes_bplog`
--

CREATE TABLE `heroes_bplog` (
  `id` int(11) NOT NULL,
  `createTime` datetime NOT NULL,
  `lastAction` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `timeSpent` int(11) NOT NULL DEFAULT '0',
  `redTeam` text NOT NULL,
  `blueTeam` text NOT NULL,
  `gameName` text NOT NULL,
  `firstHand` int(11) NOT NULL,
  `bans` int(11) NOT NULL,
  `mapMode` int(11) NOT NULL,
  `map` int(11) NOT NULL,
  `mapPool` int(11) NOT NULL,
  `weekLimit` int(11) NOT NULL,
  `bpdata` text NOT NULL,
  `creator` varchar(255) NOT NULL DEFAULT 'public',
  `status` enum('lobby','draft','completed','') NOT NULL DEFAULT 'completed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `heroes_heroes`
--

CREATE TABLE `heroes_heroes` (
  `id` int(11) NOT NULL,
  `codename` text NOT NULL,
  `imgpath` text,
  `portraitPath` text,
  `iconpath` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `heroes_heroes`
--

INSERT INTO `heroes_heroes` (`id`, `codename`, `imgpath`, `portraitPath`, `iconpath`) VALUES
(1, 'zeratul', NULL, '', NULL),
(2, 'valla', NULL, '', NULL),
(3, 'uther', NULL, '', NULL),
(4, 'tyrande', NULL, '', NULL),
(5, 'tyrael', NULL, '', NULL),
(6, 'tassadar', NULL, '', NULL),
(7, 'stitches', NULL, '', NULL),
(8, 'sonya', NULL, '', NULL),
(9, 'sgt-hammer', NULL, '', NULL),
(10, 'raynor', NULL, '', NULL),
(11, 'nova', NULL, '', NULL),
(12, 'nazeebo', NULL, '', NULL),
(13, 'muradin', NULL, '', NULL),
(14, 'malfurion', NULL, '', NULL),
(15, 'kerrigan', NULL, '', NULL),
(16, 'illidan', NULL, '', NULL),
(17, 'gazlowe', NULL, '', NULL),
(18, 'falstad', NULL, '', NULL),
(19, 'etc', NULL, '', NULL),
(20, 'diablo', NULL, '', NULL),
(21, 'arthas', NULL, '', NULL),
(22, 'abathur', NULL, '', NULL),
(23, 'tychus', NULL, '', NULL),
(24, 'lili', NULL, '', NULL),
(25, 'brightwing', NULL, '', NULL),
(26, 'murky', NULL, '', NULL),
(27, 'zagara', NULL, '', NULL),
(28, 'rehgar', NULL, '', NULL),
(29, 'chen', NULL, '', NULL),
(30, 'azmodan', NULL, '', NULL),
(31, 'anubarak', NULL, '', NULL),
(32, 'jaina', NULL, '', NULL),
(33, 'thrall', NULL, '', NULL),
(34, 'the-lost-vikings', NULL, '', NULL),
(35, 'sylvanas', NULL, '', NULL),
(36, 'kaelthas', NULL, '', NULL),
(37, 'johanna', NULL, '', NULL),
(38, 'the-butcher', NULL, '', NULL),
(39, 'leoric', NULL, '', NULL),
(40, 'kharazim', NULL, '', NULL),
(41, 'rexxar', NULL, '', NULL),
(42, 'lt-morales', NULL, '', NULL),
(43, 'artanis', NULL, '', NULL),
(44, 'chogall', NULL, '', NULL),
(45, 'cho', NULL, '', NULL),
(46, 'gall', NULL, '', NULL),
(47, 'lunara', NULL, '', NULL),
(48, 'greymane', NULL, '', NULL),
(49, 'li-ming', NULL, '', NULL),
(50, 'xul', NULL, '', NULL),
(51, 'dehaka', NULL, '', NULL),
(52, 'tracer', NULL, '', NULL),
(53, 'chromie', NULL, '', NULL),
(54, 'medivh', NULL, '', NULL),
(55, 'guldan', NULL, '', NULL),
(56, 'auriel', NULL, '', NULL),
(57, 'alarak', NULL, '', NULL),
(58, 'zarya', NULL, '', NULL),
(59, 'samuro', NULL, '', NULL),
(60, 'varian', NULL, '', NULL);

-- --------------------------------------------------------

--
-- 表的结构 `heroes_mappool`
--

CREATE TABLE `heroes_mappool` (
  `id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `maps` text NOT NULL,
  `createTime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `heroes_mappool`
--

INSERT INTO `heroes_mappool` (`id`, `comment`, `maps`, `createTime`) VALUES
(1, 'Gold Series Heroes League 2016 Summer', '1,2,3,4,5,6,7,8,10', '2016-07-27 10:10:54');

-- --------------------------------------------------------

--
-- 表的结构 `heroes_maps`
--

CREATE TABLE `heroes_maps` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `previewURL` text NOT NULL,
  `createTime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `heroes_maps`
--

INSERT INTO `heroes_maps` (`id`, `name`, `previewURL`, `createTime`) VALUES
(1, 'TowersOfDoom', 'http://heroes.nos.netease.com/1/images/battlegrounds/index/bg_towers-of-doom.jpg', '2016-07-27 09:58:26'),
(2, 'InfernalShrines', 'http://heroes.nos.netease.com/1/images/battlegrounds/index/bg_infernal-shrines.jpg', '2016-07-27 09:59:29'),
(3, 'BattlefieldOfEternity', 'http://heroes.nos.netease.com/1/images/battlegrounds/index/bg_battlefield-of-eternity.jpg', '2016-07-27 10:00:21'),
(4, 'TombOfTheSpiderQueen', 'http://heroes.nos.netease.com/1/images/battlegrounds/index/bg_tomb-of-the-spider-queen.jpg', '2016-07-27 10:01:20'),
(5, 'SkyTemple', 'http://heroes.nos.netease.com/1/images/battlegrounds/index/bg_sky-temple.jpg', '2016-07-27 10:01:58'),
(6, 'GardenOfTerror', 'http://heroes.nos.netease.com/1/images/battlegrounds/index/bg_garden-of-terror.jpg', '2016-07-27 10:03:03'),
(7, 'BlackheartsBay', 'http://heroes.nos.netease.com/1/images/battlegrounds/index/bg_blackhearts-bay.jpg', '2016-07-27 10:03:03'),
(8, 'DragonShire', 'http://heroes.nos.netease.com/1/images/battlegrounds/index/bg_dragon-shire.jpg', '2016-07-27 10:04:12'),
(9, 'HauntedMines', 'http://heroes.nos.netease.com/1/images/battlegrounds/index/bg_haunted-mines.jpg', '2016-07-27 10:04:12'),
(10, 'CursedHollow', 'http://heroes.nos.netease.com/1/images/battlegrounds/index/bg_cursed-hollow.jpg', '2016-07-27 10:05:08'),
(11, 'LostCavern', 'http://n.sinaimg.cn/games/transform/20160320/TqMP-fxqnski7757665.jpg', '2016-07-27 10:09:44'),
(12, 'BraxisHoldout', 'http://heroes.nos.netease.com/1/images/battlegrounds/index/bg_braxis_holdout.jpg', '2016-09-14 10:30:55'),
(13, 'WarheadJunction', 'http://heroes.nos.netease.com/1/images/battlegrounds/index/bg_warhead_junction.jpg', '2016-09-14 10:32:17');

-- --------------------------------------------------------

--
-- 表的结构 `gcwc_players`
--

CREATE TABLE `gcwc_players` (
  `id` int(11) NOT NULL,
  `teamID` int(11) NOT NULL,
  `gameID` text CHARACTER SET utf8 NOT NULL,
  `name` text CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `gcwc_players`
--

-- --------------------------------------------------------

--
-- 表的结构 `gcwc_teams`
--

CREATE TABLE `gcwc_teams` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `default_lang` enum('zh-CN','zh-TW','en-US','ko-KR') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `gcwc_teams`
--

INSERT INTO `gcwc_teams` (`id`, `name`, `default_lang`) VALUES
(3, 'SPT', 'zh-CN'),
(4, 'Zero', 'zh-CN'),
(5, 'Ballistix', 'ko-KR'),
(6, 'MVP.Black', 'ko-KR'),
(7, 'MVP.Miracle', 'ko-KR'),
(8, 'Dignitas', 'en-US'),
(9, 'Astral Authority', 'en-US'),
(10, 'eStar', 'zh-CN');

-- --------------------------------------------------------

--
-- 表的结构 `nocache_support`
--

CREATE TABLE `nocache_support` (
  `name` varchar(255) NOT NULL,
  `value` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `heroes_bplog`
--
ALTER TABLE `heroes_bplog`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `heroes_heroes`
--
ALTER TABLE `heroes_heroes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `heroes_mappool`
--
ALTER TABLE `heroes_mappool`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `heroes_maps`
--
ALTER TABLE `heroes_maps`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gcwc_players`
--
ALTER TABLE `gcwc_players`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gcwc_teams`
--
ALTER TABLE `gcwc_teams`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nocache_support`
--
ALTER TABLE `nocache_support`
  ADD PRIMARY KEY (`name`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `name_2` (`name`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `heroes_bplog`
--
ALTER TABLE `heroes_bplog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;
--
-- 使用表AUTO_INCREMENT `heroes_heroes`
--
ALTER TABLE `heroes_heroes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;
--
-- 使用表AUTO_INCREMENT `heroes_mappool`
--
ALTER TABLE `heroes_mappool`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- 使用表AUTO_INCREMENT `heroes_maps`
--
ALTER TABLE `heroes_maps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- 使用表AUTO_INCREMENT `gcwc_players`
--
ALTER TABLE `gcwc_players`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;
--
-- 使用表AUTO_INCREMENT `gcwc_teams`
--
ALTER TABLE `gcwc_teams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
