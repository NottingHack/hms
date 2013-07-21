$(document).ready(function() {
	$('a.mvVote').click(sendVote);
});

var mvIdea;
var mvVote;

function sendVote() {
	/* What idea to vote on? */
	mvIdea = $(this).attr('href').slice($(this).attr('href').lastIndexOf('/'));

	/* Have we already voted?  Obviously server will check too */
	if ($(this).hasClass('mvVoted')) {
		/* If this goes through, vote will be removes */
		mvVote = 0;
		/* Do we want to remove vote? */
		sTitle = 'Remove Vote?';
		sMsg = 'As you have already voted, this will remove your vote, are you sure?';
		confirmDialog(sTitle, sMsg, 'Yes, remove my vote', 'No!', _sendVote, clear);
		
	}
	else {
		/* What vote? */
		if ($(this).hasClass('mvUp')) {
			mvVote = 1;
		}
		if ($(this).hasClass('mvDown')) {
			mvVote = -1;
		}

		/* vote */
		if (mvVote == 1 || mvVote == -1) {
			_sendVote();
		}
	}


	return false;
}

function _sendVote() {
	if (mvIdea.search(/^\d+$/) == 0) {
		$.ajax({
			url: mvVoteUrl + '/' + mvIdea + '.json',
			data: {vote: mvVote},
			dataType: 'json',
			type: 'POST',
		})
		.done(showVote)
		.fail();
	}
}

function clear() {
	mvIdea = '';
	mvVote = '';
}

function showVote(data, textSuccess) {
	if (data.responseid == 1) {
		var idea = $('#mvIdea' + data.id);
		$('div.mvVoteCount strong', idea).html(data.votes);
		if (data.votes  == 1 || data.votes == -1) {
			text = 'vote';
		}
		else {
			text = 'votes';
		}
		$('div.mvVoteCount span', idea).html(text);
		if (data.voted == 1) {
			$('a.mvUp', idea).addClass('mvVoted');
			$('a.mvDown', idea).removeClass('mvVoted');
		}
		else if (data.voted == -1) {
			$('a.mvUp', idea).removeClass('mvVoted');
			$('a.mvDown', idea).addClass('mvVoted');
		}
		else if (data.voted == 0) {
			$('a.mvUp', idea).removeClass('mvVoted');
			$('a.mvDown', idea).removeClass('mvVoted');
		}
	}
	else {
		errorDialog("Vote Failed", "Error: " + data.responseid + "<br />" + data.response);
		//alert(data.responseid);
	}
}


function confirmDialog(sTitle, sMsg, sButtonTrue, sButtonFalse, fTrueFunction, fFalseFunction) {
	var sHTML;

	sHTML = '<div id="dialog-confirm" title="' + sTitle + '">';
	sHTML += '<p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>';
	sHTML += sMsg;
	sHTML += '</p></div>';

	oButtons = new Object;
	oButtons[sButtonTrue] = function() {
								$( this ).dialog( "close" );
								fTrueFunction();
								};
	oButtons[sButtonFalse] = function() {
								$( this ).dialog( "close" );
								fFalseFunction();
								}

	$('body').append(sHTML);

	$( "#dialog-confirm" ).dialog({
		resizable: false,
		modal: true,
		buttons: oButtons
	});
}

function errorDialog(sTitle, sMsg) {
	var sHTML;
	
	sHTML = '<div id="dialog-error" title="' + sTitle + '">';
	sHTML += '<p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>';
	sHTML += sMsg;
	sHTML += '</p></div>';

	$('body').append(sHTML);

	$( "#dialog-error" ).dialog({
		resizable: false,
		modal: true
	});
}