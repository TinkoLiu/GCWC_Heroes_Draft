<link rel="stylesheet" href="./css/go.css" type="text/css" media="screen">
<?php
$games = $sqlObj->getAvaliableSession();
foreach ($games as $key => $value) {
	$detail = json_decode($value["bpdata"], true);
	echo '<div class="session">
	<span class="blue"><a href="' . $memcObj->getClientAddress($value["id"], "blue") . '">' . $value["blueTeam"] . '</a></span>
	<span class="red"><a href="' . $memcObj->getClientAddress($value["id"], "red") . '">' . $value["redTeam"] . '</a></span>
	<span class="blueob"><a href="' . $memcObj->getObAddress($value["id"], "blue") . '">Observer Link</a></span>
	<span class="redob"><a href="' . $memcObj->getObAddress($value["id"], "red") . '">Observer Link</a></span>
	<span class="game">' . $detail["payload"]["draftSession"]["gameName"] . '</span>
</div>';
}

?>