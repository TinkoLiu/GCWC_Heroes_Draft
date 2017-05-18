var wsPrefix = "6147_";
var wsUri ="ws://ws.smartgslb.com:11233/"
var websocket;
var socketSettings = {
    enabled: false,
    client: null,
    channel: null,
    lastMessage: null
};
var infoFromUrl = {
    draftSessionId: null,
    accessKey: null,
    language: null,
	isjudge : false
};

var serverTimeInfo = {
    offset: null, // The "real" delay (to the precision possible)
    margin: null, // Faked margin to reduce all timers by
    lastTimeSync: null,
    fetches: []
};

var state = {
    $holder: null,
    roundIndexOverride: null,
    preliminaryPickId: null,
    lastFetch: 0,
    latestServerDraftSessionVersion: null,
    runningActions: [],
    draftSession: null,
    clientData: null,
    timers: {
        lobby: '...',
        teamOne: {
            turn: '...',
            pool: '...'
        },
        teamTwo: {
            turn: '...',
            pool: '...'
        }
    },

    isFetching: function() {
        // Todo: look over this function
        return this.runningActions.length > 0;
    },

    onActionStart: function(xhr) {
        this.runningActions.push(xhr);
    },

    onActionStop: function(xhr) {
        var deleteIndex;
        $.each(this.runningActions, function (index, el) {
            if (el === xhr) {
                deleteIndex = index;
                return false;
            }
        });
        if (deleteIndex !== undefined) {
            this.runningActions.splice(deleteIndex, 1);
        }
    },

    updateDraftSession: function (newDraftSession) {
        var oldDraftSession = this.draftSession;
        if (oldDraftSession && newDraftSession.version <= oldDraftSession.version) {
            return;
        }

        this.draftSession = newDraftSession;

        updateViewerClasses();
        updateStateClasses();

        if (!oldDraftSession) {
            window.dispatchEvent(new Event('init'));
        } else {
            var draftSessionUpdatedEvent = new CustomEvent('draftSessionUpdated', {
                detail: {
                    oldDraftSession: oldDraftSession,
                    newDraftSession: newDraftSession
                }
            });

            if ( (this.getRound().status !== oldDraftSession.currentRound.status ||
                this.getDraft().index !== oldDraftSession.currentRound.currentDraft.index)
            ) {
                //getStateHTMLAction();
            }

            window.dispatchEvent(draftSessionUpdatedEvent);
        }
    },

    updateFromServerPayload: function (payload) {
        if (payload.clientData) {
            this.clientData = payload.clientData;
        }

        if (payload.draftSession) {
            this.updateDraftSession(payload.draftSession);
        }

        if (payload.html) {
            state.$holder.html(payload.html);
            window.dispatchEvent(new Event('stateUpdated'));
        }
    },

    isViewerTeamsTurn: function() {
        if (this.getRound().status === 'LOBBY') {
            return true;
        }

        if (this.getDraft().status !== 'IN_PROGRESS') {
            return false;
        }

        return this.getCurrentTurnTeamNumber() === this.getViewerTeamNumber();
    },

    viewerCanPerformAction: function() {
        return this.isViewerTeamsTurn();
    },

    getCurrentTurnTeamNumber: function () {
        if (!this.getPick()) {
            return null;
        }

        return this.getPick().team.number;
    },

    getViewerTeamNumber: function () {
        if (!this.clientData.team) {
            return null;
        }

        return this.clientData.team.number;
    },

    getRound: function() {
        return this.draftSession.currentRound;
    },

    getLobby: function() {
        return this.getRound().lobby;
    },

    getDraft: function() {
        return this.getRound().currentDraft;
    },

    getPick: function() {
        return this.getDraft().currentPick;
    },

    getTeamsLastPick: function(teamNumber) {
        var actions = this.getDraft().actions;
        for (var i = actions.length - 1; i >= 0; i--) {
            var action = actions[i];
            if (action.team.number === teamNumber && action.startedAt) {
                return action;
            }
        }

        return null;
    }
};

var logger = {
    level: 3,

    log: function(message) {
        if (this.level < 3) {
            return;
        }

        console.log(message);
    },

    warn: function(message) {
        if (this.level < 2) {
            return;
        }

        console.warn(message);
    },


    error: function(message) {
        if (this.level < 1) {
            return;
        }

        console.error(message);
    }
};

var tickIntervalRef;

$(document).ready(function() {
    extractUrlInfo();

    detectLanguage();

    fetchServerTimeInfo(5);

    //connectWebsocket();


    // Get update pack from server
    var xhr = fetchAction();
    if (xhr) {
        xhr.done(function() {
            // Set up the tick
            tickIntervalRef = setInterval(tick, 100);
        });
    }
    $("#heroesDragArea").sortable();
});

function detectLanguage(){
	if(typeof(infoFromUrl.language)=='undefined'){
    	infoFromUrl.language = Cookies.get("i18next");
    }
    Cookies.set("i18next", infoFromUrl.language);
    $.get('./resources/localization/'+infoFromUrl.language+'/translation.json', function(data) {
    	window.i18nJSON = data;
    });
    translate();
}

function connectWebsocket(){
	websocket = new WebSocket(wsUri+wsPrefix+infoFromUrl.draftSessionId+infoFromUrl.accessKey);
	websocket.onopen = function(evt) {
		data = {};
		data.channelName = infoFromUrl.draftSessionId;
        socketSettings.enabled = true;
        socketSettings.client = websocket;
        socketSettings.channel = data.channelName;
		actionExecute('draft-session.join', data);
	};
	websocket.onclose = function(evt) {
        socketSettings.enabled = false;
	};
	websocket.onmessage = function(evt) {
		console.log("Websocket Receive: " + evt.data);
		var data = $.parseJSON(evt.data);
		console.log(data);
		handleStdAct(data);
        socketSettings.lastMessage = Math.floor(Date.now() / 1000);
	};
	websocket.onerror = function(evt) {
		console.log("ERROR: " + evt.data);
        socketSettings.enabled = false;
	};
}

// Engine functions
function extractUrlInfo() {
    infoFromUrl.draftSessionId  = url('?id');
    infoFromUrl.language        = url('?lang');
    infoFromUrl.isjudge        = url('?isjudge');
    if (typeof(url('?key')) == "undefined") {
    	infoFromUrl.accessKey   = "observer" + new Date().getTime() + parseInt(Math.random()*100000000);
    }else{
    	infoFromUrl.accessKey   = url('?key');
    }
    
}

/**
 * Measure and update information related to the server time
 */
function fetchServerTimeInfo(timesToRun) {
    var clientStartMilliseconds = Date.now();
    $.get('/time.php').done(function (data)
    {
        var serverTimeMilliseconds = parseInt(data);
        var clientCurrentMilliseconds = Date.now();
        var roundTripMS = clientCurrentMilliseconds - clientStartMilliseconds;
        var offset = (serverTimeMilliseconds - clientStartMilliseconds) - (roundTripMS / 2);
        serverTimeInfo.fetches.push({
            offset: offset,
            margin: roundTripMS // Todo: Evaluate this behavior
        });

        // Calculate averages
        var totalOffset = 0, totalMargin = 0;
        $.each(serverTimeInfo.fetches, function(index, el) {
            totalOffset += el.offset;
            totalMargin += el.margin;
        });

        var numFetches = serverTimeInfo.fetches.length;
        serverTimeInfo.offset = totalOffset / numFetches;
        serverTimeInfo.margin = totalMargin / numFetches;
        serverTimeInfo.lastTimeSync = Date.now();

        // Keep running until we have a decent average
        if (numFetches < timesToRun) {
            fetchServerTimeInfo(timesToRun);
        } else {
            serverTimeInfo.fetches = [];
        }

        logger.log('--- Calculating server time offset ---');
        logger.log('Round trip time: '             + roundTripMS);
        logger.log('Server time: '                 + serverTimeMilliseconds);
        logger.log('Client time before: '          + clientStartMilliseconds);
        logger.log('Client time after: '           + clientCurrentMilliseconds);
        logger.log('Calculated server offset: '    + serverTimeInfo.offset);
        logger.log('------');
    });
}

function tick() {

    updateTimers();

    if (checkShouldFetch()) {
        fetchAction();
    }
}

function updateTimers() {
    if (serverTimeInfo.lastTimeSync === null) {
        //logger.log('Server time has not been synced yet');
        return;
    }

    state.timers.expired = false;

    var timeStamp = Math.floor(Date.now() / 1000);
    var timeLeft;
    if (state.getRound().status === 'LOBBY') {
        if (state.getLobby().expiresAt) {
            var timeLeftRaw = parseInt(state.getLobby().expiresAt) - timeStamp;
            timeLeft = timeLeftRaw - (serverTimeInfo.offset / 1000) + (serverTimeInfo.margin / 1000);
            timeLeft = timeLeft > 0 ? Math.floor(timeLeft) : 0;

            state.timers.lobby = timeLeft;
            state.timers.expired = timeLeft === 0;
        }
    } else if (state.getRound().status === 'DRAFT') {
        updateTimersForTeam(1);
        updateTimersForTeam(2);

        var currentTimers = state.getCurrentTurnTeamNumber() === 1 ? state.timers.teamOne : state.timers.teamTwo;
        state.timers.expired = (currentTimers.turn === 0 && currentTimers.pool === 0);
        setMainCounter(currentTimers.turn, state.getCurrentTurnTeamNumber());
        if (currentTimers.turn == 10) {
            timeUp();
        }
    }

    // Todo: Trigger timer update event instead
    onTimersUpdate();
}

function checkShouldFetch() {
    if (state.isFetching()) {
        return false;
    }

    var millisecondsSinceLastFetch = (Date.now() - state.lastFetch);
    var fetchInterval = 1000;

    // If using sockets, only perform fetches when timers are expired
    if (socketSettings.enabled) {
        return state.timers.expired && millisecondsSinceLastFetch > fetchInterval;
    }

    // Don't do any more fetches if the round is complete and we are viewing a specific round
    if (state.draftSession && !(state.getRound().status === 'COMPLETED')) {
        return millisecondsSinceLastFetch > fetchInterval;
    }

    return false;
}

function updateTimersForTeam(teamNumber) {
    var timersPreset = state.getDraft().ruleSet.timersPreset;
    var lastTeamPick = state.getTeamsLastPick(teamNumber);
    var thisPick = state.getPick();
    var teamTimers = teamNumber === 1 ? state.timers.teamOne : state.timers.teamTwo;

    var timePoolLeft = state.draftSession.teams[teamNumber - 1].availableTimePool;

    // This is the current pick
    if (thisPick.team.number != teamNumber) {
        teamTimers.pool = timePoolLeft;
        return;
    }
    var timeStamp = Math.floor(Date.now() / 1000);
    var timeElapsed = timeStamp - parseInt(thisPick.startedAt) + (serverTimeInfo.offset / 1000) - (serverTimeInfo.margin / 1000); // Todo: Double check timers logic
    var timeLeft = timersPreset.pickTime - timeElapsed;


    if (timeLeft < 0) {
        timePoolLeft += timeLeft;
    }

    teamTimers.turn = timeLeft > 0 ? Math.floor(timeLeft) : 0;
    teamTimers.pool = timePoolLeft > 0 ? Math.floor(timePoolLeft) : 0;
}

function actionExecute(action, data) {
    // Build URL
    var actionBaseUrl = './ajax.php?id='+ infoFromUrl.draftSessionId;
    var url = actionBaseUrl + '&action=' + action;
    console.log(url);
    // Compile post data
    if (data === undefined) {
        data = { };
    }

    data.accessKey = infoFromUrl.accessKey;

    if (state.draftSession) {
        data.lastUpdate = state.draftSession.version;
        data.roundIndex = state.getRound().index;
    }

    logger.log('Sending action: ' + action);

    var xhr = $.ajax({
        url: url,
        method: 'post',
        data: data
    }).fail(function(xhr, textStatus, errorThrown) {
        // Todo: Handle errors better
        if (textStatus !== "abort") {
            console.log('Fail: ' + textStatus);
            fetchAction();
        }
    }).always(function(data, textStatus, xhr) {
        state.onActionStop(xhr);

        if (textStatus !== "abort") {
            var payload = data.payload;
            if (data.status < 0) {
                if (!payload || payload.error) {
                    console.warn('Error return from server: ' + payload.error)
                } else {
                    alert('An internal error occurred.');
                }
            } else if (payload) {
                state.updateFromServerPayload(payload);
            }
        }
    });

    state.onActionStart(xhr);
    return xhr;
}

function updateStateClasses() {
    var $body = $('body');

    var round = state.getRound();
    var roundStatus = round.status.toLowerCase();
    $body.attr('client-status', roundStatus);

    if (roundStatus === 'draft') {
        $body.attr('client-status', round.currentDraft.type.toLowerCase());
    }
    if (roundStatus === 'completed' && !state.getLobby().bothTeamsAreReady) {
        $("#expiredContainer").show();
        $("#heroesButtonContainer").hide();
        $("#confirmContainer").hide();
        $("#readyBtnContainer").hide();
        $("#centerContainer").hide();
        $("#playerPositionContainer").hide();
        setMainCounter("...", "white");
    }
    if (state.getLobby().team1Ready) {
        teamReady(0);
        $("#readyButton.left").parent().hide();
    }
    if (state.getLobby().team2Ready) {
        teamReady(1);
        $("#readyButton.right").parent().hide();
    }
}

function updateViewerClasses() {
    var classesToSet = [
        state.clientData.type
    ];

    if (state.clientData.type === 'team') {
        var teamNumber = state.getViewerTeamNumber();
        if (teamNumber === 1) {
            state.clientData.className = 'client-team-one';
            $("#readyButton").removeClass("right");
            $("#readyButton").addClass("left");
        } else if (teamNumber === 2) {
            state.clientData.className = 'client-team-two';
            $("#readyButton").removeClass("left");
            $("#readyButton").addClass("right");
        } else {
            logger.log('Invalid team number!');
            $("#readyButton").prop('disabled', 'disabled');
        }

        classesToSet.push(state.clientData.className);
    }

    // Todo: Set on stateholder instead, if possible
    $('body').addClass(classesToSet.join(' '));
}


function fetchAction() {
    var lastUpdate = 0;
    if (state.draftSession) {
        lastUpdate = state.draftSession.version;
    }

    var requestData = {
        lastUpdate: lastUpdate
    };

    var xhr = actionExecute('fetch', requestData);
    if (xhr) {
        state.lastFetch = Date.now();
    }

    return xhr;
}

function getStateHTMLAction() {
    actionExecute('view-state');
}

function draftSessionTeamReadyAction() {
    actionExecute('ready');
}
var confirmBtnLock = 0;
function confirmPickAction() {
    if ($(".heroButton[status=selected]").size() <= 0) {
        console.error("Double Click Detected!");
        updateConfirmButton();
        return;
    }
    actionExecute('confirm-pick', {
        confirmID: $(".heroButton[status=selected]").attr('hero-id'),
        confirmCode: $(".heroButton[status=selected]").attr('hero-code')
    });
    setConfirmBtnLabel("");
    btnConfirmState(0, "keep");
    $(".heroButton[status=selected]").attr('status', '');
    confirmBtnLock = 1;
    setTimeout("unlockConfirmBtn()",1000);
}

function positionConfirm(){
    var posHero = [];
    $("#heroesDragArea .hero").each(function(index, el) {
        console.log($(el).attr('hero-code'));
        posHero[index] = $(el).attr('hero-code');
    });
    actionExecute('confirm-position', {
        playerPos: JSON.stringify(posHero)
    });
	$("#heroesDragArea").sortable('disable');
}
//Function: Start highlight animation
//Argument(s):none
function unlockConfirmBtn(){
    confirmBtnLock = 0;
    updateConfirmButton();
    fetchAction();
}

function preSelectAction() {
    actionExecute('pre-select', {
        preSeleceID: $(".heroButton[status=selected]").attr('hero-id'),
        preSeleceCode: $(".heroButton[status=selected]").attr('hero-code')
    });
}

function handleStdAct(data){
    if (data.fetch == 1) {
        fetchAction();
    }
}


function setupObserver(){
	$("#readyButton").hide();
	$("#confirmBigButton").hide();
}

function disablePage(){
	setMainCounter("N/A", "white");
	setPlayerName("N/A",0);
	setPlayerName("N/A",1);
	btnConfirmState(0, "none");
	btnReadyState(0, "");
}



//Handling a lot of mess once finished loading the page.
$(document).ready(function(){
	btnConfirmState(0, "none");
	$("body").attr({
		onselect: 'document.selection.empty()',
		onselectstart: 'return false'
	});
	$(".heroBtnNavBtn").bind('click', function() {
		$(".groupContainer").toggle(0);
	});
	$(".heroButton").bind('click', function() {
        if($(this).attr('status')){return;}
        if (state.isViewerTeamsTurn()) {
            $(".heroButton[status=selected]").attr('status', '');
            $(this).attr('status', 'selected');
            setConfirmBtnLabel(i18nJSON.Heroes[$(this).attr("hero-code")]);
            window.dispatchEvent(new Event('stateUpdated'));
            preSelectAction();
        }
	});
})