<?php
class memcacheHandle {
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
	function set($name, $val) {
		$stmt = $this->dbh->prepare('INSERT INTO `nocache_support` (`name`, `value`) VALUES (:name,:value) ON DUPLICATE KEY update value=values(value)');
		$stmt->bindValue(':name', $name, PDO::PARAM_STR);
		$stmt->bindValue(':value', $val, PDO::PARAM_STR);
		$stmt->execute();
		return $this->dbh->errorCode();
	}
	function get($name) {
		$stmt = $this->dbh->prepare('SELECT `value` FROM `nocache_support` WHERE `name` = :name');
		$stmt->bindValue(':name', $name, PDO::PARAM_STR);
		$stmt->execute();
		$ret = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $ret[0]['value'];
	}
	function setClientHeartbeatTime($gameID, $client, $clientTime) {
		$this->set("heartbeat" . $gameID . $client, $clientTime);
	}
	function getClientHeartbeatTime($gameID, $client) {
		return $this->get("heartbeat" . $gameID . $client);
	}
	function setClientKey($gameID, $client, $connKey) {
		$key = "heroes_" . $gameID . "_" . $client;
		$connKey = $connKey;
		$this->set($key, $connKey);
	}
	function getClientKey($gameID) {
		$keys = array(
			$this->get("heroes_" . $gameID . "_blue"),
			$this->get("heroes_" . $gameID . "_red"),
		);
		return $keys;
	}
	function createChannel($draftIDAsChannelName) {
		$this->set("heroes_" . $draftIDAsChannelName, "");
	}
	function getChannelInfo($channelName) {
		return $this->get("heroes_" . $channelName);
	}
	function saveClientAddress($draftID, $client, $addr) {
		$this->set("url" . $draftID . $client, $addr);
	}
	function getClientAddress($draftID, $client) {
		return $this->get("url" . $draftID . $client);
	}
	function saveObAddress($draftID, $client, $addr) {
		$this->set("oburl" . $draftID . $client, $addr);
	}
	function getObAddress($draftID, $client) {
		return $this->get("oburl" . $draftID . $client);
	}
	function addKeyToChannel($channelName, $accessKey) {
		$tmp = $this->get("heroes_" . $channelName);
		if (!(substr($accessKey, 0, strlen($channelName)) === $channelName)) {
			$accessKey = $channelName . $accessKey;
		}
		$accessKey = "6147_" . $accessKey; //For hostker prefix.
		if (!stripos($tmp, $accessKey)) {
			$tmp = $tmp . " " . $accessKey;
			$this->set("heroes_" . $channelName, $tmp);
		}
		return json_encode(["status" => 1]);
	}
}
if (isset($_REQUEST["getValue"])) {
	$memcObj = new memcacheHandle();
	echo $memcObj->get($_REQUEST["getValue"]);
}
?>