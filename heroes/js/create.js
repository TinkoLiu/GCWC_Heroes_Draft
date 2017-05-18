function changeState(){
	$("#submitBtn").prop('disabled', !$('#createRequest').prop('checked'));
}

function getBluePlayers(){
	var teamID = $("#BlueTeam").val();
	$.get('./ajax.php?action=get-player&team='+teamID, function(data) {
		$(".bluePositionBlock select").empty();
		$(data).each(function(index, el) {;
			$(".bluePositionBlock select").append('<option value="'+el.id+'">'+el.gameID+'</option>');
		});
	});
}
function getRedPlayers(){
	var teamID = $("#RedTeam").val();
	$.get('./ajax.php?action=get-player&team='+teamID, function(data) {
		$(".redPositionBlock select").empty();
		$(data).each(function(index, el) {;
			$(".redPositionBlock select").append('<option value="'+el.id+'">'+el.gameID+'</option>');
		});
	});
}
function changeBlueTeamLabel(){
	if($("#BlueTeam").val() != ""){
		$("#BlueTeamLabel>span").text($("#BlueTeam option:selected").text());
		$("#BlueTeamPreferLang").val($("#BlueTeam option:selected").attr("data-lang"));
		getBluePlayers();
	}else{
		$("#BlueTeamLabel>span").text($("#BlueTeam").prop('placeholder'));
		$(".bluePositionBlock select").empty();
	}
}
function changeRedTeamLabel(){
	if($("#RedTeam").val() != ""){
		$("#RedTeamLabel>span").text($("#RedTeam option:selected").text());
		$("#RedTeamPreferLang").val($("#RedTeam option:selected").attr("data-lang"));
		getRedPlayers();
	}else{
		$("#RedTeamLabel>span").text($("#RedTeam").prop('placeholder'));
		$(".redPositionBlock select").empty();
	}
}
function checkMapSelection(ele) {
    var val = ele.val();
    if (parseInt(val) == 2) {
        $('select[name="mapId"]').show();
        $('span[name="TeamSelectNotice"]').hide();
        $('span[name="MapBanPickNotice"]').hide();
        $('select[name="mapPools"]').hide();
    } else if(parseInt(val) == 1) {
        $('select[name="mapPools"]').show();
        $('span[name="TeamSelectNotice"]').show();
        $('select[name="mapId"]').hide();
        $('span[name="MapBanPickNotice"]').hide();
    }
	else{
        $('select[name="mapPools"]').show();
        $('span[name="MapBanPickNotice"]').show();
        $('select[name="mapId"]').hide();
        $('span[name="TeamSelectNotice"]').hide();
    }
}

function uncompletedWorks(){
	$('input[name="mapSelectType"]:not([value=2])').attr("disabled","disabled");
	$('input[name="numBans"]:not([value=2])').attr("disabled","disabled");
	$("#weekLimit").attr("checked","true");
	$("#weekLimit").attr("disabled","disabled");
	$("#weekLimit").bind('click', function() {
		return false;
	});
}
$(function() {
	uncompletedWorks();
});