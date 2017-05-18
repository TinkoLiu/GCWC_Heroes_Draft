<link rel="stylesheet" href="css/create.css" type="text/css" media="screen">
<script src="js/create.js" type="text/javascript" charset="utf-8"></script>
<?php
$teams = $sqlObj->getTeams();
?>
<div class="createContainer">
	<div class="info" data-i18n="create.intro"></div>
	<form method="post" autocomplete="off">
		<div class="FullWidthColumn">
			<input type="text" required="required" name="game" maxlength="40" class="form-control" data-i18n="[placeholder]create.GameName">
		</div>
		<div class="playerNameBlock">
			<div class="QuarterWidthColumn" style="padding-left: 0px;">
				<select name="BlueTeam" id="BlueTeam" oninput="changeBlueTeamLabel()" class="form-control">
				<option value=""></option>
				<?php
foreach ($teams as $a) {
	echo "<option data-lang=\"" . $a["default_lang"] . "\" value=\"" . $a["id"] . "\">" . $a["name"] . "</option>";
}
?>
				</select>
				</div>
			<div class="QuarterWidthColumn">
				<select name="BlueTeamPreferLang" id="BlueTeamPreferLang" class="form-control">
					<option value=""></option>
					<option value="zh-CN">简体中文</option>
					<option value="zh-TW">繁体中文</option>
					<option value="en-US">英文</option>
					<option value="ko-KR">韩文</option>
				</select>
			</div>
			<div class="QuarterWidthColumn">
				<select name="RedTeam" id="RedTeam" oninput="changeRedTeamLabel()" class="form-control">
				<option value=""></option>
				<?php
foreach ($teams as $a) {
	echo "<option data-lang=\"" . $a["default_lang"] . "\" value=\"" . $a["id"] . "\">" . $a["name"] . "</option>";
}
?>
				</select>
			</div>
			<div class="QuarterWidthColumn" style="padding-right: 0px;">
				<select name="RedTeamPreferLang" id="RedTeamPreferLang" class="form-control">
					<option value=""></option>
					<option value="zh-CN">简体中文</option>
					<option value="zh-TW">繁体中文</option>
					<option value="en-US">英文</option>
					<option value="ko-KR">韩文</option>
				</select>
			</div>
		</div>
		<div class="bluePositionBlock">
			<div class="OneFifthWidthColumn" style="padding-left: 0px;">
				<select name="Blue1" id="Blue1" class="form-control">
				</select>
			</div>
			<div class="OneFifthWidthColumn" style="padding-left: 0px;">
				<select name="Blue2" id="Blue2" class="form-control">
				</select>
			</div>
			<div class="OneFifthWidthColumn" style="padding-left: 0px;">
				<select name="Blue3" id="Blue3" class="form-control">
				</select>
			</div>
			<div class="OneFifthWidthColumn" style="padding-left: 0px;">
				<select name="Blue4" id="Blue4" class="form-control">
				</select>
			</div>
			<div class="OneFifthWidthColumn" style="padding-left: 0px;">
				<select name="Blue5" id="Blue5" class="form-control">
				</select>
			</div>
		</div>
		<div class="redPositionBlock">
			<div class="OneFifthWidthColumn" style="padding-left: 0px;">
				<select name="Red1" id="Red1" class="form-control">
				</select>
			</div>
			<div class="OneFifthWidthColumn" style="padding-left: 0px;">
				<select name="Red2" id="Red2" class="form-control">
				</select>
			</div>
			<div class="OneFifthWidthColumn" style="padding-left: 0px;">
				<select name="Red3" id="Red3" class="form-control">
				</select>
			</div>
			<div class="OneFifthWidthColumn" style="padding-left: 0px;">
				<select name="Red4" id="Red4" class="form-control">
				</select>
			</div>
			<div class="OneFifthWidthColumn" style="padding-left: 0px;">
				<select name="Red5" id="Red5" class="form-control">
				</select>
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
