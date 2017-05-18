window.addEventListener('init', function() {
    initDraftToolUI();
    updateUI();
});

window.addEventListener('draftSessionUpdated', function() {
    updateUI();
});

window.addEventListener('stateUpdated', function() {
    updateUI();
});

var positionInitalized = false;

function initDraftToolUI() {
	setTeamName(state.draftSession.teams[0].name,0);
	setTeamName(state.draftSession.teams[1].name,1);
    setMapName(state.draftSession.currentRound.map.name,1);
    $("#gameName").text(state.draftSession.gameName);
	setBlockIndex(state.draftSession.currentRound.currentDraft.firstPickTeam.number);
    if (state.clientData.type === 'team') {
        var $body = $('body');

        // Ready buttons
        $("#readyButton").on("click", function(){draftSessionTeamReadyAction()});
        $("#confirmBigButton").on("click", function(){confirmPickAction()});
        $("#positionConfirm").on("click", function(){positionConfirm()});
        // Map picks+bans
        // $body.on('click', '.draft-map .maps-pool .pickable', function () {
            // if (!state.isViewerTeamsTurn()) {
                // return;
            // }
// 
            // var $this = $(this);
            // $('.preliminary').removeClass('preliminary');
            // $this.addClass('preliminary');
// 
            // xxx: optimistically display ban
            // var id = $(this).attr('data-id');
            // preliminaryPickAction(id);
        // });
// 
        // $body.on('click', '.draft-map .button-holder-state-confirm button.confirm', function (e) {
            // if (!state.isViewerTeamsTurn()) {
                // return;
            // }
// 
            // var $preliminary = $('.preliminary', $body);
            // $preliminary.removeClass('preliminary');
            // var $banButton = $('.button-holder.button-holder-type-ban');
            // removeClassesWithPrefix($banButton, 'button-holder-state-');
            // $banButton.addClass('button-holder-state-waiting');
// 
            // confirmPickAction();
            // e.stopPropagation();
        // });

        /** Hero Draft */
        // $body.on('click', '.draft-hero .pickable-available', function () {
            // if (!state.isViewerTeamsTurn()) {
                // return;
            // }
// 
            // var $button = $('.button-holder-confirm');
            // removeClassesWithPrefix($button, 'button-holder-state-');
            // $button.addClass('button-holder-state-confirm');
// 
            // var id = $(this).attr('data-id');
            // preliminaryPickAction(id);
        // });
        $body.on('click', "#confirmBigButton", function (e) {
            if (!state.isViewerTeamsTurn()) {
                return;
            }

            // xxx: optimistically display pick
            var $buttonHolder = $(this).closest('.button-holder');
            removeClassesWithPrefix($buttonHolder, 'button-holder-state-');

            confirmPickAction();
            e.stopPropagation();
        });
    }
}

/**
 */
function onTimersUpdate() {
    if(state.getRound().status === 'LOBBY'){
    	setMainCounter(state.timers.lobby, "white");
    }
    setTimePoolLabel(state.timers.teamOne.pool, 1);
    setTimePoolLabel(state.timers.teamTwo.pool, 2);
}

// Todo: function onTurnChange() {}

/**
 * Handle UI updates after fresh data has been fetched
 */
function updateUI() {
    var $confirmButton = $("#confirmBigButton");

    switch (state.getRound().status) {
        case 'LOBBY':
            var lobby = state.getLobby();

            break;

        case 'DRAFT':
            var draft = state.getDraft();
            var currentPick = state.getPick();
            if(currentPick.allowChogall){
            	if ($(".heroButton[hero-code=cho]").attr('status') == 'disabled') {
            		$(".heroButton[hero-code=cho]").removeAttr('status');
            	}
            }else{
            	if (!$(".heroButton[hero-code=cho]").attr('status')) {
            		$(".heroButton[hero-code=cho]").attr('status','disabled');
            	}
            }
            updateConfirmButton();

            //updatePickablesPoolDisplay();
            //updateMapPicksDisplay();
            updateHeroPicksDisplay();
			logClientLastReport();

            break;

        case 'POSITION':
            updateHeroPicksDisplay();
            updatePositionDisplay();
			logClientLastReport();
            break;

        case 'COMPLETED':
            //updatePickablesPoolDisplay();
            updateHeroPicksDisplay();
            updatePositionDisplay();
            updateResultDisplay();
			logClientLastReport();


            break;
    }
}

function logClientLastReport(){
	if(infoFromUrl.isjudge){
		console.log("Blue Last Report: " + state.draftSession.teams[0].lastUpdate);
		console.log("Red Last Report: " + state.draftSession.teams[1].lastUpdate);
		console.log("Current Timestamp: " + (Date.now() / 1000));
	}
}

function updateResultDisplay(){
    $(state.draftSession.teams[0].players).each(function(index, el) {
        $($("#Team1playerNameArea .playerNameHolder")[index]).text(el.gameID);
    });
    $(state.draftSession.teams[1].players).each(function(index, el) {
        $($("#Team2playerNameArea .playerNameHolder")[index]).text(el.gameID);
    });
    $(state.draftSession.teams[0].picked).each(function(index, el) {
        $($("#Team1DisplayArea .hero")[index]).attr('hero-code', el);
        $($("#Team1DisplayArea .hero")[index]).find(".heroName").text(i18nJSON["Heroes"][el]);
    });
    $(state.draftSession.teams[1].picked).each(function(index, el) {
        $($("#Team2DisplayArea .hero")[index]).attr('hero-code', el);
        $($("#Team2DisplayArea .hero")[index]).find(".heroName").text(i18nJSON["Heroes"][el]);
    });
}

function updatePositionDisplay(){
	if(state.getViewerTeamNumber() != null){
		currTeam = state.getViewerTeamNumber()-1;
		if(state.draftSession.teams[currTeam].positionSet){
			$("#positionConfirm").hide();
			$("#heroesDragArea").sortable('disable');
			$(state.draftSession.teams[currTeam].players).each(function(index, el) {
				$($("#operatingArea .playerNameHolder")[index]).text(el.gameID);
			});
			$(state.draftSession.teams[currTeam].picked).each(function(index, el) {
				$($("#heroesDragArea .hero")[index]).attr('hero-code', el);
				$($("#heroesDragArea .hero")[index]).find(".heroName").text(i18nJSON["Heroes"][el]);
			});
			positionInitalized = true;
		}
	}
	
    if (positionInitalized) {
        return;
    }
    if(state.getViewerTeamNumber()==null){
        return;
    }
    if (state.getRound().status == 'POSITION' && !(state.draftSession.teams[0].positionSet && state.draftSession.teams[1].positionSet)) {
        switch(state.getViewerTeamNumber()){
            case 1:
                $(state.draftSession.teams[0].players).each(function(index, el) {
                    $($("#operatingArea .playerNameHolder")[index]).text(el.gameID);
                });
                $("[data-id=pick] [data-id=Team1] .hero").each(function(index, el) {
                    $($("#heroesDragArea .hero")[index]).attr('hero-code', $(el).attr("hero-code"));
                    $($("#heroesDragArea .hero")[index]).find(".heroName").text($(el).find(".heroName").text());
                });
                positionInitalized = true;
                break;
            case 2:
                $(state.draftSession.teams[1].players).each(function(index, el) {
                    $($("#operatingArea .playerNameHolder")[index]).text(el.gameID);
                });
                $("[data-id=pick] [data-id=Team2] .hero").each(function(index, el) {
                    $($("#heroesDragArea .hero")[index]).attr('hero-code', $(el).attr("hero-code"));
                    $($("#heroesDragArea .hero")[index]).find(".heroName").text($(el).find(".heroName").text());
                });
                positionInitalized = true;
                break;
            default:
                break;
        }
    }
    if (positionInitalized) {
        $("body").attr('client-status', 'position');
    }
}

function updateConfirmButton(){
    if(typeof(state.getDraft()) == "undefined")return;
    var draft = state.getDraft();
    var currentPick = state.getPick();
    btnConfirmState(0, "none");
    if (state.isViewerTeamsTurn()) {
        if ($('.heroButton[status="selected"]').length <= 0 && typeof(currentPick.picked["hero-code"]) != "undefined" && !currentPick.isConfirmed) {
            $(".heroButton[hero-code="+currentPick.picked["hero-code"]+"]").attr('status', 'selected');
            setConfirmBtnLabel(i18nJSON.Heroes[currentPick.picked["hero-code"]]);
        }
        if (typeof(currentPick.picked["hero-code"]) != "undefined" || $('.heroButton[status="selected"]').length > 0) {
            btnConfirmState(1,currentPick.type);
            setConfirmBtnLabel(i18nJSON.Heroes[$('.heroButton[status="selected"]').attr("hero-code")]);
        } else {
            btnConfirmState(0,currentPick.type);
        }
    } else {
        btnConfirmState(0, "none");
        setConfirmBtnLabel("");
    }
}

function updatePickablesPoolDisplay() {
    var draft = state.getDraft();
    if (!draft) {
        return;
    }

    var currentPick = state.getPick();
    var pickablesWithStatus = draft.pickablesWithStatus;
    $('.pickable[data-id]').each(function(index, element) {
        var $pickable = $(element);
        var pickableId = $pickable.attr('data-id');
        var pickableData = pickablesWithStatus[pickableId];

        if (!pickableData) {
            return;
        }

        removeClassesWithPrefix($pickable, 'pickable-');
        if (pickableData.available) {
            $pickable.addClass('pickable-available');

            if (currentPick.pickable && currentPick.pickable.id === pickableId) {
                $pickable.addClass('pickable-preliminary');
            }
        } else {
            $pickable.addClass('pickable-banned');
        }
    });
}

function updateMapPicksDisplay() {
    var draft = state.getDraft();
    if (!draft || draft.type !== 'MAP') {
        return;
    }

    var $picksWrapper = $('.picks-wrapper');
    var $teamOnePicks = $('.team-1 ul', $picksWrapper).html('');
    var $teamTwoPicks = $('.team-2 ul', $picksWrapper).html('');
    var picksArr = draft.picks;
    $.each(picksArr, function(index, pick) {
        if (!pick.isConfirmed) {
            return;
        }

        var element = $('<li>' + pick.pickable.name + '</li>');
        if (pick.team.number === 1) {
            $teamOnePicks.append(element);
        } else {
            $teamTwoPicks.append(element);
        }
    });
}

function updateHeroPicksDisplay() {
    var draft = state.getDraft();
    if (!draft || draft.type !== 'HERO') {
        return;
    }

    var currentPick = state.getPick();
    if (currentPick.startedAt && !currentPick.isConfirmed && state.getDraft().status !== 'COMPLETED') {
        $('.hero[data-index="' + currentPick.index + '"]').attr("status",'working');
        updateConfirmButton();
    }
    var actionsArr = draft.actions;
    $.each(actionsArr, function(index, currAct) {
        var $heroDispHolder = $('.hero[data-index="' + currAct.index + '"]');
        if(currAct.startedAt && currAct.isConfirmed){
            $('.hero[data-index="' + currAct.index + '"]').attr("status",'confirmed');
        }
        var pickedHero = currAct.picked;
        if (!pickedHero) {
            return;
        }
        var herocode = pickedHero["hero-code"];
        if (herocode == "chogall") {
        	herocode = "cho";
        }
        if (currAct.isConfirmed) {
            if (currAct.type == "ban") {
                $('.heroButton[hero-code="'+herocode+'"]').attr('status', 'locked');
            }else{
                $('.heroButton[hero-code="'+herocode+'"]').attr('status', 'Team'+currAct.team.number+'Pick');
            }
        }
		if (currAct.isConfirmed || state.isViewerTeamsTurn() || state.clientData.type === 'observer') {
            $heroDispHolder.attr("hero-code",pickedHero["hero-code"]);
            $heroDispHolder.find(".heroName").text(i18nJSON.Heroes[pickedHero["hero-code"]]);
        }
    });

}



var animLock = 0;
//Topbar Animation
//Function: Red blink when only 10 seconds to go.
//Argument(s):none
function timeUp(){
    if (animLock) {return;}
    animLock = 1;
	$('#headHighlight').css({'visibility': 'hidden'});
	$('#headTimeUp').css({'visibility': 'visible'});
	$('#headTimeUp').animate({opacity:'1'},200);
	$('#headTimeUp').animate({opacity:'1'},300);
	$('#headTimeUp').animate({opacity:'0'},1000);
	setTimeout("showHighlightAnim()",1500);
}
//Function: Start highlight animation
//Argument(s):none
function showHighlightAnim(){
	$('#headTimeUp').css({'visibility': 'hidden'});
	$('#headHighlight').css({'visibility': 'visible'});
    animLock = 0;
}
//Function: change center timer number
//Argument(s):
//			time:string
//				Will change the timer label directly
function setMainCounter(time, style){
	$('#centerTimer').text(time);
	switch(style){
		case 1:
			$("#centerTimer").removeClass('heroesRedFont');
			$("#centerTimer").addClass('heroesBlueFont');
			break;
		case 2:
			$("#centerTimer").removeClass('heroesBlueFont');
			$("#centerTimer").addClass('heroesRedFont');
			break;
		default:
			$("#centerTimer").removeClass('heroesBlueFont');
			$("#centerTimer").removeClass('heroesRedFont');
			break;

	}
}
//Function: set team name
//Argument(s):
//			name:string
//				Will change the name label directly
//			team:int
//				0=>blue
//				1=>red
function setTeamName(name,team){
	if(team){
		$('#redName').text(name);
	}else{
		$('#blueName').text(name);
	}
}
//Function: set map name
//Argument(s):
//			name:string
//				Will change the name label directly
//			team:int
//				0=>blue
//				1=>red
function setMapName(name,i18n){
	if(i18n){
		$('#mapName').attr("data-i18n", "map." + name);
		translate();
	}else{
		$('#mapName').text(name);
	}
    mapImg = $("#mapImg img");
    switch(name){
        case "TowersOfDoom":
            mapImg.attr("src","resources/gcwc-ui/topbar/maps/"+name+".png");
            break;
        case "BlackheartsBay":
            mapImg.attr("src","resources/gcwc-ui/topbar/maps/"+name+".png");
            break;
        case "CursedHollow":
            mapImg.attr("src","resources/gcwc-ui/topbar/maps/"+name+".png");
            break;
        case "DragonShire":
            mapImg.attr("src","resources/gcwc-ui/topbar/maps/"+name+".png");
            break;
        case "GardenOfTerror":
            mapImg.attr("src","resources/gcwc-ui/topbar/maps/"+name+".png");
            break;
        case "HauntedMines":
            mapImg.attr("src","resources/gcwc-ui/topbar/maps/"+name+".png");
            break;
        case "SkyTemple":
            mapImg.attr("src","resources/gcwc-ui/topbar/maps/"+name+".png");
            break;
        case "TombOfTheSpiderQueen":
            mapImg.attr("src","resources/gcwc-ui/topbar/maps/"+name+".png");
            break;
        case "BattlefieldOfEternity":
            mapImg.attr("src","resources/gcwc-ui/topbar/maps/"+name+".png");
            break;
        case "InfernalShrines":
            mapImg.attr("src","resources/gcwc-ui/topbar/maps/"+name+".png");
            break;
        case "BraxisHoldout":
            mapImg.attr("src","resources/gcwc-ui/topbar/maps/"+name+".png");
            break;
        case "WarheadJunction":
            mapImg.attr("src","resources/gcwc-ui/topbar/maps/"+name+".png");
            break;
        default:
            break;
    }
}
//Function: change ready button state
//Argument(s):
//	enabled:BOOL
//			enabled: 1
//			disabled:0
function btnReadyState(enabled){
	if(enabled){
		$("#readyButton").prop('disabled', '');
	}else{
		$("#readyButton").prop('disabled', 'disabled');
	}
}
//Function: change player state
//Argument(s):
//	team:BOOL
//			red: 1
//			blue:0
function teamReady(team){
	if(team){
		$("#redName").addClass('ready');
	}else{
		$("#blueName").addClass('ready');
	}
}

//Function: change confirm button state
//Argument(s):
//	enabled:BOOL
//			enabled: 1
//			disabled:0
//	type:string
//			"ban"	for ban =>add class ban and remove class pick
//			"pick"	for pick=>add class pick and remove class ban
function btnConfirmState(enabled, type){
    if(type==="ban" || type==="pick"){
        $("#confirmBigButton").attr("status", type);
    }else if(type==="keep"){
        $("#confirmBigButton").attr("status");
    }else{
        $("#confirmBigButton").attr("status", "");
    }
	if(enabled){
        if (confirmBtnLock) {return;}
		$("#confirmBigButton").prop('disabled', '');
	}else{
		$("#confirmBigButton").prop('disabled', 'disabled');
	}
}

//Function: change confirm button label
//Argument(s):
//	hero:string
function setConfirmBtnLabel(hero){
	$("#confirmBigButton").text(hero);
}

//Function: set hero button status
//Argument(s):
//	heroes:string
//  status:active
//		   selected
//		   locked
//		   Team1Pick
//		   Team2Pick
function setHeroBtnStatus(heroes, status){
	$(".heroButton[hero-code="+heroes+"]").attr('status', status);
}
//Function: set hero button status
//Argument(s):
//	firstTeam:first hand team id
//	   1 blue
//	   2 red
function setBlockIndex(firstTeam){
	var bprule = $(state.draftSession.currentRound.currentDraft.ruleSet.pickBanPattern.pattern);
	if (firstTeam == 1) {
		secondTeam = 2;
	}else{
		secondTeam = 1;
	}
	var dataIndex = 0;
	bprule.each(function(index, el) {
		type = el[1];
		if (el[0] == "first") {
			cur = firstTeam;
		}else if(el[0] == "second"){
			cur = secondTeam;
		}
		console.log($("div[data-id="+type+"]>div[data-id=Team"+cur+"]>.hero:not([data-index])"));
		$("div[data-id="+type+"]>div[data-id=Team"+cur+"]>.hero:not([data-index]):first").attr('data-index', dataIndex);
		dataIndex = dataIndex + 1;
	});
}
//Function: set time pool text
//Argument(s):
//    time: int
//    team: int
//       1:blue
//       2:red
function setTimePoolLabel(time, team){
	team = team - 1;
	if(team){
		$("#redTimePool").text(time);
	}else{
		$("#blueTimePool").text(time);
	}
}