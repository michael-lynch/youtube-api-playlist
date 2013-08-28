<!doctype html>

<html>

<head>

	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<meta charset="utf-8">

	<title>Youtube API Playlist</title>

	<meta name="description" content="A simple PHP script that returns videos and meta data from a Youtube playlist.">

	<meta name="author" content="http://michaelynch.com">

	<style>

	/* for presentation only */

	body {
		font: 14px Helvetica, Arial, sans-serif;
		line-height: 24px;
		margin: 0;
	}

	.content-wrapper {
		margin: 5% auto;
		max-width: 600px;
		width: 90%;
	}

	.post-wrapper {
		margin: 0 0 40px 0;
		float: left;
		width: 100%;
	}

	.post-wrapper iframe {
		margin: 0 0 20px 0;
		float: left;
		width: 100%;
	}

	</style>

</head>

<body class="no-js en" lang="en">

	<div class="content-wrapper">

		<?php

		//define playlist ID
		$playlistID = '8BCDD04DE8F771B2';

	    //set feed URL
	    $feedURL = 'https://gdata.youtube.com/feeds/api/playlists/'.$playlistID.'?v=2';

	    //turn feed into simpleXML object
	    $sxml = simplexml_load_file($feedURL);

	    /*

	    //If simplexml_load_file() isn't supported by your server,
	    //you can alternatively use PHP cURL to parse through the XML

		function load_file_from_url($url) {
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_REFERER, 'http://www.yourdomain.com');
			$str = curl_exec($curl);
			curl_close($curl);
			return $str;
		}

		function load_xml_from_url($url) {
			return simplexml_load_string(load_file_from_url($url));
		}

		$sxml = load_xml_from_url($feedURL);

		*/

		?>

		<?php foreach($sxml -> entry as $entry) : ?>

			<?php

	        //get author
	        $author = $entry->author->name;

			//get namespaces in entry node
			$namespaces = $entry->getNameSpaces(true);

			//get children nodes in media namespace
			$media = $entry->children($namespaces['media']);

			//get title
			$title = $media->group->title;

			//get description
			$description = $media->group->description;

			//get video player URL
			$player_attrs = $media->group->player->attributes();
			$player_url = $player_attrs['url'];

			//get video thumbnail
			$thumb_attrs = $media->group->thumbnail[0]->attributes();

			//get children nodes of yt namespace in media namespace
			$yt = $media->children($namespaces['yt']);

			//get duration
			$duration_attrs = $yt->duration->attributes();
			$length = floor($duration_attrs['seconds'] / 60).':'.$duration_attrs['seconds'] % 60;

			//get children nodes of yt namespace in entry
			$yt = $entry->children($namespaces['yt']);

			//get view count
			$stats_attrs = $yt->statistics->attributes();
			$viewCount = $stats_attrs['viewCount'];

			//get children nodes of gd namespace
			$gd = $entry->children($namespaces['gd']);

			//if rating exists
			if ($gd->rating) {
				//get and set rating
				$attrs = $gd->rating->attributes();
				$rating = $attrs['average'];
			} else {
				//otherwise set rating to 0
				$rating = 0;
			}

			//get video link
			$link = $entry->link[0]->attributes();
			$embedLink = $link['href'];

			//use video link to format embed link
			$embedLink = str_replace("&feature=youtube_gdata", "", $embedLink);
			$embedLink = str_replace("/watch?v=", "/embed/", $embedLink);

			?>

			<div class="post-wrapper">

				<iframe width="400" height="315" src="<?= $embedLink; ?>" frameborder="0" allowfullscreen></iframe>

				<h2><?= $title; ?></h2>

				<p><strong>Description</strong>
				<br /><?= $description; ?></p>

				<p><strong>Author</strong>
				<br /><?= $author; ?></p>

				<p><strong>Length</strong>
				<br /><?= $length; ?></p>

				<p><strong>Views</strong>
				<br /><?= $viewCount; ?></p>

				<p><strong>Thumbnail</strong>
				<br /><img src="<?= $thumb_attrs; ?>" alt="Video Thumbnail" /></p>

			</div>

		<?php endforeach; ?>

	</div>

</body>

</html>