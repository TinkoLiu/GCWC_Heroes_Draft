<?php
if (!defined("MYSQL_USERNAME")) {
	define('MYSQL_USERNAME', '');
	define('MYSQL_PASSWORD', '');
	define('CONNECT_STR', 'mysql:dbname=hosbp;host=localhost');
}
class sqlHandle {
	var $dsn = CONNECT_STR;
	var $sqlUsername = MYSQL_USERNAME;
	var $sqlPassword = MYSQL_PASSWORD;
	var $dbh;
	function __construct() {
		try {
			$this->dbh = new PDO($this->dsn, $this->sqlUsername, $this->sqlPassword);
			$this->dbh->exec("set names 'utf8';");
		} catch (PDOException $e) {
			echo 'Connection failed: ' . $e->getMessage();
		}
	}
	function disconnect() {
		$this->dbh = null;
	}
	function getHeroes() {
		$stmt = $this->dbh->prepare('SELECT * FROM `heroes_heroes`');
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	function getMaps($poolID) {
		$stmt = $this->dbh->prepare('SELECT * FROM `heroes_maps` WHERE FIND_IN_SET(`maps`.`id`, (SELECT `maps` FROM `mapPool` WHERE `mapPool`.`id` = :mappoolid))');
		$stmt->bindValue(':mappoolid', $poolID, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	function getMapInfo($mapID) {
		$stmt = $this->dbh->prepare('SELECT * FROM `heroes_maps` WHERE `id`=:mapID');
		$stmt->bindValue(':mapID', $mapID, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	function getDraftInfo($draftID) {
		$stmt = $this->dbh->prepare('SELECT *,UNIX_TIMESTAMP(createTime) as createStamp, UNIX_TIMESTAMP(lastAction) as lastStamp FROM `heroes_bplog` WHERE `id`=:draftID');
		$stmt->bindValue(':draftID', $draftID, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	function getBPData($draftID) {
		$stmt = $this->dbh->prepare('SELECT `bpdata` FROM `heroes_bplog` WHERE `id`=:draftID');
		$stmt->bindValue(':draftID', $draftID, PDO::PARAM_INT);
		$stmt->execute();
		$tmp = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return json_decode($tmp[0]["bpdata"], true);
	}
	function setBPData($draftID, $bpdata) {
		if (is_array($bpdata)) {
			$bpdata = json_encode($bpdata);
		}
		$stmt = $this->dbh->prepare('UPDATE `heroes_bplog` SET `bpdata`=:bpdata WHERE `id`=:draftID');
		$stmt->bindValue(':draftID', $draftID, PDO::PARAM_INT);
		$stmt->bindValue(':bpdata', $bpdata, PDO::PARAM_STR);
		$stmt->execute();
		return $this->dbh->errorCode();
	}
	function createNewSession($blueTeam, $redTeam, $gameName, $firstHand, $bans, $mapMode, $map, $mapPool, $weekLimit) {
		$stmt = $this->dbh->prepare('INSERT INTO `heroes_bplog`(`createTime`, `redTeam`, `blueTeam`, `gameName`, `firstHand`, `bans`, `mapMode`, `map`, `mapPool`, `weekLimit`, `bpdata`, `status`) VALUES (now(),:redTeam,:blueTeam,:gameName,:firstHand,:bans,:mapMode,:map,:mapPool,:weekLimit,"[]","lobby")');
		$stmt->bindValue(':redTeam', $redTeam, PDO::PARAM_STR);
		$stmt->bindValue(':blueTeam', $blueTeam, PDO::PARAM_STR);
		$stmt->bindValue(':gameName', $gameName, PDO::PARAM_STR);
		$stmt->bindValue(':firstHand', $firstHand, PDO::PARAM_INT);
		$stmt->bindValue(':bans', $bans, PDO::PARAM_INT);
		$stmt->bindValue(':mapMode', $mapMode, PDO::PARAM_INT);
		$stmt->bindValue(':map', $map, PDO::PARAM_INT);
		$stmt->bindValue(':mapPool', $mapPool, PDO::PARAM_INT);
		$stmt->bindValue(':weekLimit', $weekLimit, PDO::PARAM_INT);
		$stmt->execute();
		$draftID = $this->dbh->lastInsertId();
		$stmt = $this->dbh->prepare('SELECT UNIX_TIMESTAMP(createTime) as createStamp FROM `heroes_bplog` WHERE `id`=:draftID');
		$stmt->bindValue(':draftID', $draftID, PDO::PARAM_INT);
		$stmt->execute();
		$createStamp = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$createStamp = $createStamp[0]['createStamp'];
		//print_r($this->dbh->errorInfo());
		return array('id' => $draftID, 'createStamp' => $createStamp);
	}
	function getAvaliableMapPools() {
		$stmt = $this->dbh->prepare('SELECT `id`,`comment` FROM `heroes_mapPool`');
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	function getAvaliableMaps() {
		$stmt = $this->dbh->prepare('SELECT `id`,`name` FROM `heroes_maps`');
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	function getTeamsByID($teamID) {
		$stmt = $this->dbh->prepare('SELECT * FROM `gcwc_teams` WHERE `id`=:teamID');
		$stmt->bindValue(':teamID', $teamID, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	function getTeamNameByID($teamID) {
		$stmt = $this->dbh->prepare('SELECT `name` FROM `gcwc_teams` WHERE `id`=:teamID');
		$stmt->bindValue(':teamID', $teamID, PDO::PARAM_INT);
		$stmt->execute();
		$ret = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $ret[0]["name"];
	}
	function getPlayersByTeam($teamID) {
		$stmt = $this->dbh->prepare('SELECT * FROM `gcwc_players` WHERE `teamID`=:teamID');
		$stmt->bindValue(':teamID', $teamID, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	function getTeams() {
		$stmt = $this->dbh->prepare('SELECT * FROM `gcwc_teams` WHERE `enabled`');
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	function getGameIDByID($playerID) {
		$stmt = $this->dbh->prepare('SELECT `gameID` FROM `gcwc_players` WHERE `id`=:playerID');
		$stmt->bindValue(':playerID', $playerID, PDO::PARAM_INT);
		$stmt->execute();
		$ret = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $ret[0]["gameID"];
	}
	function getAllSession() {
		$stmt = $this->dbh->prepare('SELECT * FROM `heroes_bplog`');
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	function getAvaliableSession() {
		$stmt = $this->dbh->prepare('SELECT * FROM `heroes_bplog` WHERE `status` != "completed"');
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	function setSessionStatus($draftID, $status) {
		$stmt = $this->dbh->prepare('UPDATE `heroes_bplog` SET `status`=:status WHERE `id`=:draftID');
		$stmt->bindValue(':draftID', $draftID, PDO::PARAM_INT);
		$stmt->bindValue(':status', $status, PDO::PARAM_STR);
		$stmt->execute();
		return $this->dbh->errorCode();
	}
}
?>