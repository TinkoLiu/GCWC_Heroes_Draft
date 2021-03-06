<link rel="stylesheet" href="css/create.css" type="text/css" media="screen">
<script src="js/create.js" type="text/javascript" charset="utf-8"></script>

<div class="createContainer">
	<div class="info" data-i18n="create.intro"></div>
	<form method="post" autocomplete="off">
		<div class="FullWidthColumn">
			<input type="text" name="game" maxlength="40" class="form-control" data-i18n="[placeholder]create.GameName">
		</div>
		<div class="playerNameBlock">
			<div class="HalfWidthColumn" style="padding-left: 0px;">
				<input type="text" name="BlueTeam" id="BlueTeam" oninput="changeBlueTeamLabel()" class="form-control" maxlength="20" data-i18n="[placeholder]create.BlueTeam">
			</div>
			<div class="HalfWidthColumn" style="padding-right: 0px;">
				<input type="text" name="RedTeam" id="RedTeam" oninput="changeRedTeamLabel()" class="form-control" maxlength="20" data-i18n="[placeholder]create.RedTeam">
			</div>
		</div>
		<div class="start-team">
			<span class="start-team" data-i18n="create.mapSelectionTip"></span>
			<span class="start-team">
				<label>
					<input type="radio" name="mapSelectType" onclick="checkMapSelection($(this))" value="0">
					<span data-i18n="create.BANPICK"></span>
				</label>
			</span>
			<span class="start-team">
				<label>
					<input type="radio" name="mapSelectType" onclick="checkMapSelection($(this))" value="1">
					<span  data-i18n="create.TeamSelect"></span>
				</label>
			</span>
			<span class="start-team">
				<label>
					<input type="radio" name="mapSelectType" onclick="checkMapSelection($(this))" value="2" checked="checked">
					<span data-i18n="create.JudgeSelect"></span>
				</label>
			</span>
		</div>
		<div class="form-group" style="margin-top: -20px;">
			<select class="form-control" name="mapId">
				<?php
$tmp = $sqlObj->getAvaliableMaps();
foreach ($tmp as $a) {
	echo "<option value=\"" . $a["id"] . "\" data-i18n=\"map." . $a["name"] . "\"></option>";
}
?>
			</select>
			<select style="display:none;" class="form-control" name="mapPools">
				<!-- <option value="8" data-i18n="create.MapEternity"></option> -->
				<!-- Fill this after got the map pools by ajax -->
				<?php
$tmp = $sqlObj->getAvaliableMapPools();
foreach ($tmp as $a) {
	echo "<option value=\"" . $a["id"] . "\" data-i18n=\"mapPoolComment." . $a["comment"] . "\"></option>";
}
?>
			</select>
			<span name="TeamSelectNotice" style="display:none;" data-i18n="create.TeamSelectNotice"></span>
			<span name="MapBanPickNotice" style="display:none;" data-i18n="create.MapBanPickNotice"></span>
		</div>
		<br>

		<div class="form-group num-bans">
			<span data-i18n="create.BanNumber"></span>
			<span class="num-bans">
				<label><input type="radio" name="numBans" value="0"> 0</label>
			</span>
			<span class="num-bans">
				<label><input type="radio" name="numBans" value="1"> 1</label>
			</span>
			<span class="num-bans">
				<label><input type="radio" name="numBans" value="2" checked=""> 2</label>
			</span>
			<span class="num-bans">
				<label><input type="radio" name="numBans" value="3"> 3</label>
			</span>
			<span class="num-bans">
				<label><input type="radio" name="numBans" value="4"> 4</label>
			</span>
			<span class="num-bans">
				<label><input type="radio" name="numBans" value="5"> 5</label>
			</span>
		</div>
		<div class="form-group start-player">
			<span data-i18n="create.FirstHand"></span>
			<span class="start-player">
				<label id="BlueTeamLabel"><input type="radio" name="FirstHand" value="0" checked=""><span data-i18n="create.BlueTeam"></span></label>
			</span>
			<span class="start-player">
				<label id="RedTeamLabel"><input type="radio" name="FirstHand" value="1"><span data-i18n="create.RedTeam"></span></label>
			</span>
		</div>
		<div>
			<label><input type="checkbox" name="weekLimit" id="weekLimit"><span data-i18n="create.newHeroWeekLimit"></span></label>
		</div>
		<div>
			<label><input type="checkbox" name="createRequest" id="createRequest" onchange="changeState()"><span data-i18n="create.confirmCheck"></span></label>
		</div>
		<div class="create-button">
			<button class="in-confirmation" id="submitBtn" disabled="disabled" data-i18n="create.StartButton"></button>
		</div>
	</form>
</div>
