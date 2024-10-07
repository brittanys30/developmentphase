<!DOCTYPE html>
<html lang="en" data-cast-api-enabled="true">
<head>
	<title> Youtube Downloader</title>
	<meta charset="utf-8">
	<link href="https://vjs.zencdn.net/7.0.3/video-js.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/videojs-seek-buttons/dist/videojs-seek-buttons.css">
	<script src="https://vjs.zencdn.net/7.0.3/video.js"></script>
	<script src="http://code.jquery.com/jquery-1.12.4.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/videojs-seek-buttons/dist/videojs-seek-buttons.min.js"></script>
	<script src="https://unpkg.com/videojs-sprite-thumbnails@0.5.2/dist/videojs-sprite-thumbnails.min.js"></script>

	<style>
	* {
		margin: 0px;
		padding: 0px;
	}

	div.content-image {
		float: left;
		margin-right: 20px;
	}
	div.content-wrapper {
		margin-bottom: 35px;
		font-size: 15px;
	}
	section#rightbar {
		background-color: #99A6B0;
		width: 30%;
		float:right;
	}

	section#mainbar {
		background-color: #99A6B0;
	}
	</style>
</head>
<body style="background: linear-gradient(to right bottom, #648880, #293f50);">
<div id="container">

	<section id="rightbar">
	<br /><p style="font-size: 20px;"> &nbspSuggested Videos<br /></p><br /><br />
	<?php
//		&& !file_exists('temp/video' . $_GET['v'] . '.mp4')
		if(isset($_GET['v']) && !empty($_GET['v'])) {
			$name = $_GET['v'];
//			$name = 'Q0jeohWnmAQ';
			$html = file_get_contents('https://youtube.com/watch?v=' . $name);

			preg_match_all('/i.ytimg.com\/sb\/.*?"/', $html, $matches);
			if ( sizeof($matches[0]) > 0 ) {
			        $spec_parts = explode( '|', stripslashes( substr($matches[0][0], 0, -1) ) );
			        $base_urla = explode('$', $spec_parts[0]);
			        $base_url = $base_urla[0] . '2/M';
				$base_sqp = explode('?', $base_urla[2])[1];
				$sigha = explode('#', $spec_parts[3]);
				$sigh = array_pop($sigha);
				$width = $sigha[0];
				$height = $sigha[1];
				$cnt = $sigha[2];
//				$cnt2 = $cnt % 25;
				$count = floor($sigha[2] / 25);
				for($i = 0; $i <= $count; $i += 1){
			                $urlstory = 'https://' . $base_url . $i . '.jpg?' . $base_sqp . '&sigh=' . $sigh;
			                copy($urlstory, 'temp/' . $name . $i . '.jpg');
			        }
			        if ( $sigha[2] % 25 ) {
			                $cms1 = 'ffmpeg -loglevel panic -y -i temp/'  . $name . $count . '.jpg -vf "pad=' . (string)(5*$width+1) . ':' . (string)(5*$height+1) . '" temp/z' . $name . (string)($count+1) . '.jpg';
			                exec($cms1);
					$cms3 = 'ffmpeg -loglevel panic -y -i temp/z'  . $name . (string)($count+1) . '.jpg -vf "scale=' . (string)(5*$width) . ':' . (string)(5*$height) . '" temp/' . $name . $count . '.jpg';
					exec($cms3);
//			                $cms2 = 'mv temp/' . $name . (string)($count+1) . '.jpg temp/' . $name . $count . '.jpg';
//			                exec($cms2);
			        }
				$cstich = 'ffmpeg -loglevel panic -i temp/' .$name . '%01d.jpg -filter_complex tile=1x' . (string)($count+1) . ' temp/2' . $name . '.jpg';
				exec($cstich);
			}

			$str_array = explode ('"compactVideoRenderer":{"videoId":"', $html);
			array_splice($str_array, 0, 1);
			foreach ($str_array as $sub_str) {
				$urls = substr($sub_str, 0, 11);
				copy('https://i.ytimg.com/vi/' . $urls . '/hqdefault.jpg', 'temp/' . $urls . '.jpg');
				$tmp = explode('"simpleText":"', $sub_str);
				$title = explode('"},', $tmp[1])[0];
				$yearp = explode('"},', $tmp[2])[0];
				$viewp = explode('"},', $tmp[3])[0];
				$timep = explode('"},', $tmp[4])[0];
				
				$authorbyurl = explode('"text":"', $tmp[1])[1];
				$author = explode('","', $authorbyurl)[0];
				$urlauthor = explode('"url":"', $authorbyurl)[1];
				$urlauth = explode('","', $urlauthor)[0];
				
				$thumbnail = '<div class="content-image"><a href="/watch?v=' . $urls . '"><img src="/temp/' . $urls . '.jpg" style="width:168px;height:94px;border:0;"></a></div>';
				
				$titter = '<div class="content-wrapper"><p><b>' . $title . '</b></p><p><a href="' . $urlauth . '">' . $author . '</a></p><p>' . $viewp . ' &nbsp&nbsp&nbsp ' . $yearp . ' &nbsp&nbsp&nbsp ' . $timep . '</p></div><br /><br />'; 
				
				echo $thumbnail . $titter;
			}
		} else {
			echo '<br />';
			echo '<br />';
			echo '<center><h2 style="color: #9f9f9f;"> The Page Does Not Exist!!! </h2></center>';
		}
	?>
	</section><!-- end of rightbar -->


	<section id="mainbar">
	<?php
		if(isset($_GET['v']) && !empty($_GET['v'])) {
			if(isset($_GET['q']) && !empty($_GET['q'])){
				$quality = $_GET['q'];
			} else {
				$quality = '400';
			}
			$name = $_GET['v'];
			$vvv = "video" . $name; // uniqid('video');
			$subp = "/temp/" . $vvv . ".en.vtt";
			if (!file_exists('temp/video' . $_GET['v'] . '.mp4')) {
				$cmd = 'yt-dlp --proxy socks5://127.0.0.1:10808 --write-info-json --write-sub --write-auto-sub --sub-lang en -f "mp4[height<=' .$quality .']" -o "temp/' .$vvv .'.%(ext)s" --skip-download ' . escapeshellarg($name);
				exec($cmd);
			}
			$vidfile = "temp/" . $vvv . ".mp4";

			$json = file_get_contents("temp/" . $vvv . ".info.json");
			$yummy = json_decode($json,true);	
			$cdur = $yummy['duration'];
			$output_1 = $yummy['url'];
			$dl_urla = explode('https://', $output_1);
			$dl_url = 'https://tiny-scene-3a04.stanleyhaftarot37904.workers.dev/https/' . $dl_urla[1];


			$dur = (int)($cdur);
			$tshot = (string)ceil($dur/$cnt);

			$imsp = 'temp/2' . $name . '.jpg';

			echo '<br /><center><h2 style="color: #FF0000;">Youtube Video </h2></center>';
			echo '<br />';
			echo '<div style="padding-left:50px;">';
			echo '<video id="vid1" class="video-js vjs-default-skin" controls preload="auto" width="640" height="400" data-setup=\'{"customControlsOnMobile": true, "playbackRates": [1.2, 1.1, 1, 0.9, 0.8]}\' >';
			
			$hvvidmp = $dl_url;
//			$hvvidmp = "/temp/" . $vvv . ".mp4";
			echo '<source src="'.$hvvidmp.'" type="video/mp4" />';
			echo '<track kind="captions" src="'.$subp.'" srclang="en" label="English">';
			echo '</video>';
			echo '</div>';
			echo '<br /><center>&nbsp&nbsp<a href="'.$hvvidmp.'" download>Download link for MP4</a></center>';
			echo '<br />';

			$des = $yummy['description'];
			echo '<div style="border: 3px solid #5C0202; margin-left:7px; margin-right: 31%;">';
			$udate = $yummy['upload_date'];
			$udate = substr($udate,0,4).'/'.substr($udate,4,2).'/'.substr($udate,6,2);
			$nameu = $yummy['uploader'];
			$urlu = $yummy['uploader_url'];
			$view = $yummy['view_count'];
			$like = $yummy['like_count'];
			$dislike = $yummy['dislike_count'];
			$title = $yummy['title'];
			echo '<br />&nbsp&nbsp<a href="'.$urlu.'">' . $nameu . '</a>';
			echo '<br />&nbsp&nbsp Views: ' . $view . '&nbsp&nbsp Likes: ' . $like . '&nbsp&nbsp DisLikes: ' . $dislike;
			echo '<br /><br />&nbsp&nbsp&nbsp<strong style="color: #FF2222; font-size: 20px; margin-left: 10px;">' . $title  . '</strong>';
			echo '<br /><p style="margin-left: 20px;">' . $des . '</p>';
			echo '<br /><br /><p style="color: #480BA9;">&nbsp&nbsp Published Date: ' . $udate . '</p><br /><br /><br />';
			echo '</div>';
//			echo '<br />&nbsp&nbsp<a href="'.$hvvidmp.'" download>Download link for MP4</a>';
		} else {
			echo '<br />';
			echo '<br />';
			echo '<center><h2 style="color: #9f9f9f;"> This Page Does Not Exist!!! </h2></center>';
		}
	?>
	</section><!-- end of mainbar -->



</div><!-- end of container -->
<script>

var options = {controlBar: {volumePanel: {inline: false}},};
videojs('vid1', options);

var vid = videojs('vid1');
vid.seekButtons({forward: 15, back: 15});

vid.spriteThumbnails({url: '<?php echo $imsp; ?>', interval: '<?php echo $tshot; ?>', width: '<?php echo $width; ?>', height: '<?php echo $height; ?>'});

    var player = videojs('vid1', {
      html5: {
        nativeTextTracks: false
      }
    });

var player = videojs('myvideo', {
  textTrackSettings: true
});

</script>

</body>
</html>
