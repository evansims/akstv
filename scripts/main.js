var supportsHTML5Audio = !!(document.createElement('audio').canPlayType);
var lockChannelSwitching = false;
var staticAudio = null;

$(function() {
	staticAudio = $("audio#static")[0];

	$("#channel-selection li a").on("click", function(e) {
		e.preventDefault;
		if(lockChannelSwitching) return false;
		lockChannelSwitching = true;

		if(channel = $(this).attr('data-twitch')) {
			twitter = ($(this).attr('data-twitter') ? $(this).attr('data-twitter') : false);
			name    = ($(this).attr('data-name') ? $(this).attr('data-name') : false);
			topic   = ($(this).attr('data-topic') ? $(this).attr('data-topic') : false);
			game    = ($(this).attr('data-game') ? $(this).attr('data-game') : false);

			if(window.history.replaceState) {
				window.history.replaceState(null, $("body").attr('data-title-template').replace("%s", name), '/' + channel);
			}

			loadStream(500, channel, twitter, name, topic, game);
		} else {
			lockChannelSwitching = false;
		}

		return false;
	});
});

function loadStream(wait, twitch, twitter, displayName, description, game) {
	if(!wait) wait = 500;
	if(!displayName) displayName = twitch;
	if(!description) description = 'Streaming from their twitch.tv account.';

	if(wait > 0) {
		if(supportsHTML5Audio) {
			staticAudio.volume = 0.1;
			staticAudio.play();
		}

		$("#live-channel").css({'background-image': "url('/images/static.gif')"}).html('');
	}

	setTimeout(function() {
		$("#stream-title").html($("#stream-title").attr("data-template").replace("%s", displayName));
		$("#stream-description").html($("#stream-description").attr("data-template").replace("%s", description));

		var embedTemplate = '<object type="application/x-shockwave-flash" height="378" width="620" id="live_embed_player_flash" data="http://www.twitch.tv/widgets/live_embed_player.swf?channel=' + twitch + '" bgcolor="#000000"><param name="allowFullScreen" value="true" /><param name="allowScriptAccess" value="always" /><param name="allowNetworking" value="all" /><param name="movie" value="http://www.twitch.tv/widgets/live_embed_player.swf" /><param name="flashvars" value="hostname=www.twitch.tv&channel=' + twitch + '&auto_play=true&start_volume=25" /></object>';

		if(twitter) {
			embedTemplate += '<p style="float: right"><a href="https://twitter.com/intent/tweet?screen_name=' + twitter + '" class="twitter-mention-button" data-related="aureusknights,' + twitter + '">Tweet to @' + twitter + '</a><script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script></p>';
		}

		if(game) {
			embedTemplate += '<p>' + displayName + ' is playing <strong>' + game + '</strong>.</p>';
		}

		embedTemplate += '</p>';

		$("#live-channel").html(embedTemplate);

		if(supportsHTML5Audio) {
			setTimeout(function() {
					lockChannelSwitching = false;
					staticAudio.pause();
			}, 500);
		} else {
			lockChannelSwitching = false;
		}
	}, wait);

}
