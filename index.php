<?php
	require('logic.php');
?><!DOCTYPE html>
<html>

	<head>

		<meta charset="utf-8">
		<title>Aureus Knights TV - The Stream Network of the Aureus Knights Community</title>

		<meta name='description' content='Streaming network for the Aureus Knights Community. Online gaming streamed throughout the day.'/>
		<meta name='keywords' content='MMORPG, Streaming, Twitch.TV, Guild Wars 2, Planetside 2, World of Warcraft'/>

		<link rel="stylesheet" type="text/css" href="styles/desktop.css" />
		<link rel="stylesheet" type="text/css" href="styles/mobile.css" />
		<script data-main="scripts/main.js" src="scripts/require.js"></script>

		<script type="text/javascript" src="http://use.typekit.net/idc1fhm.js"></script>
		<script type="text/javascript">try{Typekit.load();}catch(e){}</script>

		<script type="text/javascript">
			  var _gaq = _gaq || [];
			  _gaq.push(['_setAccount', 'UA-1375457-6']);
			  _gaq.push(['_trackPageview']);

			  (function() {
			    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			  })();
		</script>

	</head>

	<body data-title-template="%s - Aureus Knights TV">
		<div id="fb-root"></div>
		<script>(function(d, s, id) {
		  var js, fjs = d.getElementsByTagName(s)[0];
		  if (d.getElementById(id)) return;
		  js = d.createElement(s); js.id = id;
		  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=110646772300148";
		  fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));</script>

		<header id="site">
			<div class="layout">
				<h1><a rel="author" href="http://aureusknights.com/"><span class="guild">Aureus Knights</span> <span class="banner">TV</span></a></h1>
				<p id="intro-text">AKStv is a web channel for watching the often concurrent live streams of our guild members at any time.</p>

				<div id="social">
					<div class="twitter button">
						<a href="https://twitter.com/share" class="twitter-share-button" data-via="aureusknights" data-related="aureusknights">Tweet</a><script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
					</div>
					<div class="facebook button">
						<div class="fb-like" data-send="false" data-layout="button_count" data-width="100" data-show-faces="false" data-colorscheme="dark" data-font="arial"></div>
					</div>
					<div class="googleplus button">
<!-- Place this tag where you want the +1 button to render. -->
<div class="g-plusone" data-size="tall" data-annotation="none" data-href="http://www.aureusknights.tv/"></div>

<!-- Place this tag after the last +1 button tag. -->
<script type="text/javascript">
  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/plusone.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>
					</div>
				</div>

				<nav>
					<ul>
						<li><a rel="author" href="http://aureusknights.com/">About the Knights</a></li>
						<li><a href="http://aureusknights.com/events">Upcoming Events</a></li>
						<li><a href="http://aureusknights.com/recruitment">Recruitment</a></li>
					</ul>
				</nav>
			</div>
		</header>

		<div id="top">

			<article>
				<header id="page">
					<h3 id="stream-title" data-template="You're Watching: %s"></h3>
					<p id="stream-description" data-template="%s"></p>
				</header>

				<section id="twitter-badge">
					<a class="twitter-timeline" href="https://twitter.com/evansims/guildies" data-widget-id="264565412358930432">Tweets from @evansims/guildies</a><script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
				</section>

				<section id="live-channel"></section>

				<div class="clearfix">&nbsp;</div>

				<div id="irc-chat">
					<h2>Guild Chat</h2>

					<iframe width="620" height="350" scrolling="no" src="http://widget.mibbit.com/?settings=c42689e88dfd82eb23d8ecd568fcd485&server=irc.aureusknights.com&channel=%23knights"></iframe>
				</div>

				<nav id="channel-selection">
					<h2>Member Channels</h2>

					<ul>
<?php
					$offline = true;

					foreach($broadcasters as $broadcaster):
						$offline = !$offline;
?>
						<li <?php if($broadcaster->online) { echo('class="online"'); } else { echo('class="offline"'); } ?>><a data-twitch="<?php echo($broadcaster->twitch); ?>" data-twitter="<?php echo($broadcaster->twitter); ?>" data-name="<?php echo($broadcaster->name); ?>" data-topic="<?php echo(htmlentities($broadcaster->topic, ENT_QUOTES | ENT_IGNORE, "UTF-8")); ?>" data-game="<?php echo(htmlentities($broadcaster->game, ENT_QUOTES | ENT_IGNORE, "UTF-8")); ?>" style="background-image: url('<?php echo($broadcaster->thumbnail); ?>')" href="http://www.twitch.tv/<?php echo($broadcaster->twitch); ?>"><?php echo($broadcaster->name); ?></a></li>
<?php
					endforeach;
?>
					</ul>
				</nav>

				<div class="clearfix">&nbsp;</div>
			</article>

		</div>

		<audio id="static" src="audio/static.ogg" preload="auto" autobuffer></audio>

		<img src="images/static.gif" style="position: absolute; top: 0; left: 0; width: 2px; height: 2px;" />

		<script>
			require(['jquery', 'main'], function() {
				// Load default or requested channel.
				loadStream(50, '<?php echo($startupChannel->twitch); ?>', '<?php echo($startupChannel->twitter); ?>', '<?php echo($startupChannel->name); ?>', '<?php echo(htmlentities($startupChannel->topic, ENT_QUOTES | ENT_IGNORE, "UTF-8")); ?>', '<?php echo(htmlentities($startupChannel->game, ENT_QUOTES | ENT_IGNORE, "UTF-8")); ?>');
			});
		</script>

	</body>

</html>
