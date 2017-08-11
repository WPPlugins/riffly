<?php
/*
Plugin Name: Riffly Video/Audio Comments
Plugin URI: http://riffly.com/
Description: Mixed video and audio comments integrated with the standard comment system.
Version: 2.1
Author: Riffly.com
Author URI: http://riffly.com/
*/

$riffly_image_webcam = '<img src="' . get_bloginfo('wpurl') . '/wp-content/plugins/riffly/riffly-webcam.png" border="0" width="16" height="16">';
$riffly_image_mic = '<img src="' . get_bloginfo('wpurl') . '/wp-content/plugins/riffly/riffly-mic.png" border="0" width="16" height="16">';

$riffly_comment_showrecorder = <<<OUTPUT
<span style="font-weight: bold;font-size: 14px;"><a style="font-weight: bold;font-size: 14px;" href="javascript:void(0);" onclick="rifflyShowRecorder('riffly_recorder_window', 'comment', 'video');">$riffly_image_webcam Add Webcam</a> or <br><a style="font-weight: bold;font-size: 14px;" href="javascript:void(0);" onclick="rifflyShowRecorder('riffly_recorder_window', 'comment', 'audio');">$riffly_image_mic Audio-only Comment</a></span>
<div id="riffly_recorder_window" style="display: none;z-index: 1000;">
</div>
OUTPUT;
global $riffly_comment_showrecorder;

$riffly_comment_video = <<<OUTPUT
<div id="riffly_video_player_%s">
</div>
<div id="riffly_video_player_%s_link">

<table width="400" align="center">
<tr>
	<td align="left" valign="middle" width="150">
		&nbsp;
	</td>
	<td align="left" valign="middle" width="130">
		<a href="javascript:void(0);" onclick="rifflyShowPlayer('riffly_video_player_%s', '%s', 'video');return false;" style="font-weight: bold;font-size: 18px;text-align: center;text-decoration: none;">Play Video<br>Comment &raquo;</a>
	</td>

	<td align="left" valign="top" width="120">
		<div class="video_thumbnail">
		<a href="javascript:void(0);" onclick="rifflyShowPlayer('riffly_video_player_%s', '%s', 'video');return false;" style="text-decoration: none;"><img src="http://free-video-comments-at.riffly.com/static/flv/%s/%s.tiny.jpg" width="80" height="60" style="margin-top: 10px;margin-bottom: 0px;margin-left: 45px;border: 1px #000 solid;"><img src="http://free-video-comments-at.riffly.com/static/images/play_button.gif" alt="Play Video Comment" border="0" class="overlay_button_video"></a>
		</div>
	</td>
</tr>
</table>
</div>
OUTPUT;

# sprintf's out of order here

$riffly_comment_audio = <<<OUTPUT
<div id="riffly_audio_player_%s">
</div>
<div id="riffly_audio_player_%s_link">

<table width="400" align="center">
<tr>
	<td align="left" valign="middle" width="150">
		&nbsp;
	</td>
	<td align="left" valign="middle" width="130">
		<a href="javascript:void(0);" onclick="rifflyShowPlayer('riffly_audio_player_%s', '%s', 'audio');return false;" style="font-weight: bold;font-size: 18px;text-align: center;text-decoration: none;">Play Audio Comment &raquo;</a>
	</td>
	<td align="left" valign="top" width="120">
		<div class="video_thumbnail">
		<a href="javascript:void(0);" onclick="rifflyShowPlayer('riffly_audio_player_%s', '%s', 'audio');return false;" style="text-decoration: none;"><img src="http://free-video-comments-at.riffly.com/static/images/audio_comment.gif" width="80" height="60" style="border: 1px #000 solid;margin-top: 10px;margin-bottom: 0;margin-left: 50px;text-decoration: none;"><img src="http://free-video-comments-at.riffly.com/static/images/play_button.gif" alt="Play Video Comment" border="0" class="overlay_button_audio"></a>
		</div>
	</td>
</tr>
</table>
</div>
OUTPUT;


global $riffly_player;

function riffly_head() {
    echo '<link href="http://free-video-comments-at.riffly.com/static/css/api/1/blog.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="http://free-video-comments-at.riffly.com/static/js/api/1/blog.js"></script>
';
}

function riffly_comment_form($comment_id) {
    global $riffly_comment_showrecorder;
    printf($riffly_comment_showrecorder);
}

$riffly_video_comment_num = 1;
$riffly_audio_comment_num = 1;

global $riffly_video_comment_num, $riffly_audio_comment_num;

function riffly_comment_text($comment = '') {
    global $riffly_comment_video, $riffly_comment_audio, $riffly_video_comment_num, $riffly_audio_comment_num;

    if ($comment != '') {
        $pattern = '/\[riffly_video\](.*)\[\/riffly_video\]/';
        preg_match_all($pattern, $comment, $matches);

        foreach ($matches[1] as $riffly_id) {

			# XXX
			$riffly_base_dir = 
			substr($riffly_id, 0, 4) . '/' .
			substr($riffly_id, 4, 4) . '/' .
			substr($riffly_id, 8, 4) . '/' .
			substr($riffly_id, 12, 4) . '/' .
			substr($riffly_id, 16, 4) . '/' .
			substr($riffly_id, 20, 4) . '/' .
			substr($riffly_id, 24, 4) . '/' .
			substr($riffly_id, 28, 4);

            $comment_id = $riffly_video_comment_num;
            $pattern = '/\[riffly_video\]' . $riffly_id . '\[\/riffly_video\]/';
            $replacement = sprintf($riffly_comment_video, $comment_id, $comment_id, $comment_id, $riffly_id, $comment_id, $riffly_id, $riffly_base_dir, $riffly_id);
            $comment = preg_replace($pattern, $replacement, $comment);
            $riffly_video_comment_num++;
        }

        $pattern = '/\[riffly_audio\](.*)\[\/riffly_audio\]/';
        preg_match_all($pattern, $comment, $matches);

        foreach ($matches[1] as $riffly_id) {
            $comment_id = $riffly_audio_comment_num;
            $pattern = '/\[riffly_audio\]' . $riffly_id . '\[\/riffly_audio\]/';
            $replacement = sprintf($riffly_comment_audio, $comment_id, $comment_id, $comment_id, $riffly_id, $comment_id, $riffly_id);
            $comment = preg_replace($pattern, $replacement, $comment);
            $riffly_audio_comment_num++;
        }
    }

    return $comment;
}

add_filter('admin_head', 'riffly_head');
add_filter('wp_head', 'riffly_head');

add_action('comment_form', 'riffly_comment_form');
add_filter('comment_text', 'riffly_comment_text');

?>
