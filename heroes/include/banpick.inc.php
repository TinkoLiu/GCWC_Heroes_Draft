<?php
include_once './include/mysql.inc.php';
$sqlObj = new sqlHandle();
function createHeroPool() {
	global $sqlObj;
	$heroes = $sqlObj->getHeroes();
	$page = 1;
	echo "<div class=\"groupContainer\" page-id=\"" . $page . "\"><div class=\"heroButtonGroup\">";
	foreach ($heroes as $hero) {
		if ($hero["codename"] == "chogall" || $hero["codename"] == "gall") {
			continue;
		}
		echo "<span class=\"heroButton\" hero-id=\"" . $hero["id"] . "\" hero-code=\"" . $hero["codename"] . "\"></span>";
		if ($hero["id"] == 10 || $hero["id"] == 19 || $hero["id"] == 29 || $hero["id"] == 38 || $hero["id"] == 50 || $hero["id"] == 59) {
			echo "</div><div class=\"heroButtonGroup\">";
		}
	}
	echo "</div></div>";
}
?>
<script src="./js/banpick.js"></script>
<link rel="stylesheet" href="./css/banpick.public.css" type="text/css" media="screen">
<link rel="stylesheet" href="./css/banpick.public.anim.css" type="text/css" media="screen">
<link rel="stylesheet" href="./css/banpick.public.heroes.css" type="text/css" media="screen">
<script src="./js/banpick.ui.js"></script>
<script src="./js/banpick.strategy.js"></script>
<div id="Topbar">
	<div id="headMainBG">
		<img src="resources/gcwc-ui/topbar/bg.png">
	</div>
	<div id="mapBG">
		<img src="resources/gcwc-ui/topbar/mapbg.png">
	</div>
	<div id="mapImg">
		<img src="">
	</div>
	<div id="gameName" class="HeaderInfo"></div>
	<div id="centerTimer" class="HeaderInfo heroesBlueFont">88</div>
	<div id="blueName" class="HeaderInfo BlueTeam HeadbarName">Blue Team</div>
	<div id="redName" class="HeaderInfo RedTeam HeadbarName">Red Team</div>
	<div id="blueTimePool" class="HeaderInfo BlueTeam HeadbarTimePool">99</div>
	<div id="redTimePool" class="HeaderInfo RedTeam HeadbarTimePool">99</div>
	<div id="mapName" class="HeaderInfo mapName">Map Holder</div>
</div>
<div id="centerContainer">
	<div data-id="pick" class="centerAlignFixer" id="selectedArea">
		<div data-id="Team1" class="TeamSelectedContainer">
			<div data-id="Team1Pick1" class="hero">
				<span class="icon-wrap">
					<img>
					<span class="statusImg"></span>
					<span class="heroName"></span>
				</span>
			</div>
			<div data-id="Team1Pick2" class="hero">
				<span class="icon-wrap">
					<img>
					<span class="statusImg"></span>
					<span class="heroName"></span>
				</span>
			</div>
			<div data-id="Team1Pick3" class="hero">
				<span class="icon-wrap">
					<img>
					<span class="statusImg"></span>
					<span class="heroName"></span>
				</span>
			</div>
			<div data-id="Team1Pick4" class="hero">
				<span class="icon-wrap">
					<img>
					<span class="statusImg"></span>
					<span class="heroName"></span>
				</span>
			</div>
			<div data-id="Team1Pick5" class="hero">
				<span class="icon-wrap">
					<img>
					<span class="statusImg"></span>
					<span class="heroName"></span>
				</span>
			</div>
		</div><div data-id="Team2" class="TeamSelectedContainer">
			<div data-id="Team2Pick1" class="hero">
				<span class="icon-wrap">
					<img>
					<span class="statusImg"></span>
					<span class="heroName"></span>
				</span>
			</div>
			<div data-id="Team2Pick2" class="hero">
				<span class="icon-wrap">
					<img>
					<span class="statusImg"></span>
					<span class="heroName"></span>
				</span>
			</div>
			<div data-id="Team2Pick3" class="hero">
				<span class="icon-wrap">
					<img>
					<span class="statusImg"></span>
					<span class="heroName"></span>
				</span>
			</div>
			<div data-id="Team2Pick4" class="hero">
				<span class="icon-wrap">
					<img>
					<span class="statusImg"></span>
					<span class="heroName"></span>
				</span>
			</div>
			<div data-id="Team2Pick5" class="hero">
				<span class="icon-wrap">
					<img>
					<span class="statusImg"></span>
					<span class="heroName"></span>
				</span>
			</div>
		</div>
	</div>
	<div data-id="ban" class="centerAlignFixer" id="bannedArea">
		<div data-id="Team1" class="TeamBannedContainer">
			<div data-id="Team1Ban1" class="hero">
				<span class="icon-wrap">
					<img>
					<span class="statusImg"></span>
					<span class="heroName"></span>
			</div>
			<div data-id="Team1Ban2" class="hero">
				<span class="icon-wrap">
					<img>
					<span class="statusImg"></span>
					<span class="heroName"></span>
				</span>
			</div>
		</div>
		<div data-id="Team2" class="TeamBannedContainer">
			<div data-id="Team2Ban1" class="hero">
				<span class="icon-wrap">
					<img>
					<span class="statusImg"></span>
					<span class="heroName"></span>
				</span>
			</div>
			<div data-id="Team2Ban2" class="hero">
				<span class="icon-wrap">
					<img>
					<span class="statusImg"></span>
					<span class="heroName"></span>
				</span>
			</div>
		</div>
	</div>
</div>
<div id="readyBtnContainer">
	<button id="readyButton" class="btnHeroes blue" data-i18n="banpickPublic.readyBtn"></button>
</div>
<div id="confirmContainer">
	<button id="confirmBigButton" class="btnMain"></button>
</div>
<div id="heroesButtonContainer">
		<?php
createHeroPool()
?>
</div>
<div id="expiredContainer">
	<span data-i18n="banpickPublic.sessionExpired"></span>
</div>
<div id="waitingPositionHolder" data-i18n="banpickPublic.waitingPosition"></div>
<div id="playerPositionContainer">
	<div id="operatingArea">
		<span id="exchangeTops" data-i18n="banpickPublic.exchangeHint"></span>
		<div id="heroesDragArea">
			<div data-id="slot1" class="hero">
				<span class="icon-wrap">
					<img>
					<span class="heroName"></span>
				</span>
			</div>
			<div data-id="slot2" class="hero">
				<span class="icon-wrap">
					<img>
					<span class="heroName"></span>
				</span>
			</div>
			<div data-id="slot3" class="hero">
				<span class="icon-wrap">
					<img>
					<span class="heroName"></span>
				</span>
			</div>
			<div data-id="slot4" class="hero">
				<span class="icon-wrap">
					<img>
					<span class="heroName"></span>
				</span>
			</div>
			<div data-id="slot5" class="hero">
				<span class="icon-wrap">
					<img>
					<span class="heroName"></span>
				</span>
			</div>
		</div>
		<div id="playerNameArea">
			<span class="playerNameHolder">1</span>
			<span class="playerNameHolder">2</span>
			<span class="playerNameHolder">3</span>
			<span class="playerNameHolder">4</span>
			<span class="playerNameHolder">5</span>
		</div>
		<button id="positionConfirm"></button>
	</div>
	<div id="resultArea">
		<div class="heroArea" id="Team1DisplayArea">
			<div data-id="slot1" class="hero">
				<span class="icon-wrap">
					<img>
					<span class="heroName"></span>
				</span>
			</div>
			<div data-id="slot2" class="hero">
				<span class="icon-wrap">
					<img>
					<span class="heroName"></span>
				</span>
			</div>
			<div data-id="slot3" class="hero">
				<span class="icon-wrap">
					<img>
					<span class="heroName"></span>
				</span>
			</div>
			<div data-id="slot4" class="hero">
				<span class="icon-wrap">
					<img>
					<span class="heroName"></span>
				</span>
			</div>
			<div data-id="slot5" class="hero">
				<span class="icon-wrap">
					<img>
					<span class="heroName"></span>
				</span>
			</div>
		</div>
		<div class="nameArea" id="Team1playerNameArea">
			<span class="playerNameHolder"></span>
			<span class="playerNameHolder"></span>
			<span class="playerNameHolder"></span>
			<span class="playerNameHolder"></span>
			<span class="playerNameHolder"></span>
		</div>
		<div class="heroArea" id="Team2DisplayArea">
			<div data-id="slot1" class="hero">
				<span class="icon-wrap">
					<img>
					<span class="heroName"></span>
				</span>
			</div>
			<div data-id="slot2" class="hero">
				<span class="icon-wrap">
					<img>
					<span class="heroName"></span>
				</span>
			</div>
			<div data-id="slot3" class="hero">
				<span class="icon-wrap">
					<img>
					<span class="heroName"></span>
				</span>
			</div>
			<div data-id="slot4" class="hero">
				<span class="icon-wrap">
					<img>
					<span class="heroName"></span>
				</span>
			</div>
			<div data-id="slot5" class="hero">
				<span class="icon-wrap">
					<img>
					<span class="heroName"></span>
				</span>
			</div>
		</div>
		<div class="nameArea" id="Team2playerNameArea">
			<span class="playerNameHolder"></span>
			<span class="playerNameHolder"></span>
			<span class="playerNameHolder"></span>
			<span class="playerNameHolder"></span>
			<span class="playerNameHolder"></span>
		</div>
	</div>
</div>