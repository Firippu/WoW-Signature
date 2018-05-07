<?php
header('content-type: image/png');

$str_input_realm=$_GET['r'];
$str_input_name=$_GET['cn'];

$str_apikey='';

$str_input_realm=str_replace(' ','-',$str_input_realm);

$str_res_dir='./resources/';

$str_font=$str_res_dir.'MORPHEUS.TTF';
$str_border=$str_res_dir.'border.png';
$str_bkgnd=$str_res_dir.'bkgnd.png';

$res_image_main=imagecreatefrompng($str_bkgnd);

$res_font_color=imagecolorallocate($res_image_main,255,250,205);

$num_font_size=14;

function print_error($error) {
	global $res_image_main,$num_font_size,$res_font_color,$str_font;
	imagettftext($res_image_main,$num_font_size,0,10,27,$res_font_color,$str_font,$error);
};

if(empty($str_input_realm) || empty($str_input_name)) {
	print_error('Invalid Arguments');
} else {
	$str_json=file_get_contents('http://us.battle.net/api/wow/character/'.$str_input_realm.'/'.$str_input_name.'?fields=guild,titles'.'&apikey='.$str_apikey);

	if($str_json==false) {
		print_error('No Response From Server');
	} else {
		$var_item=json_decode($str_json,true);

		$str_status=$var_item['status'];
		$str_reason=$var_item['reason'];

		if($str_status=='nok') {
			print_error($str_reason);
		} else {
			$str_name=$var_item['name'];
			$str_realm=$var_item['realm'];
			$str_thumb=$var_item['thumbnail'];
			$str_guild=$var_item['guild']['name'];
			$num_achive=$var_item['achievementPoints'];
			$num_gender=$var_item['gender'];
			$num_faction=$var_item['faction'];
			$num_class=$var_item['class'];
			$num_race=$var_item['race'];
			$num_level=$var_item['level'];
			$arr_titles=$var_item['titles'];

			$str_url_thumbnail='http://render-api-us.worldofwarcraft.com/static-render/us/'.$str_thumb;

			$res_avatar=imagecreatefromjpeg($str_url_thumbnail);
			imagecopyresampled($res_image_main,$res_avatar,30,8,0,0,64,64,84,84);
			imagedestroy($res_avatar);

			$res_border=imagecreatefrompng($str_border);
			imagecopy($res_image_main,$res_border,29,7,0,0,66,66);
			imagedestroy($res_border);

			$str_image_achive='http://us.battle.net/wow/static/images/icons/achievements.gif';
			$res_achive=imagecreatefromgif($str_image_achive);
			imagecopy($res_image_main,$res_achive,323,56,0,0,8,10);

			imagettftext($res_image_main,$num_font_size,0,335,66,$res_font_color,$str_font,$num_achive);

			$str_image_faction='http://us.media.blizzard.com/wow/icons/18/faction_'.$num_faction.'.jpg';

			$res_image_faction=imagecreatefromjpeg($str_image_faction);
			imagecopy($res_image_main,$res_image_faction,6,8,0,0,18,18);
			imagedestroy($res_image_faction);

			$str_url_icons='http://us.media.blizzard.com/wow/icons/18/';

			$str_image_race=$str_url_icons.'race_'.$num_race.'_'.$num_gender.'.jpg';
			$res_image_race=imagecreatefromjpeg($str_image_race);
			imagecopy($res_image_main,$res_image_race,6,31,0,0,18,18);
			imagedestroy($res_image_race);

			$arr_classes=array(NULL,'warrior','paladin','hunter','rogue','priest','death-knight','shaman','mage','warlock','monk','druid');

			$str_image_class=$str_url_icons.'class_'.$arr_classes[$num_class].'.jpg';
			$res_image_class=imagecreatefromjpeg($str_image_class);
			imagecopy($res_image_main,$res_image_class,6,54,0,0,18,18);
			imagedestroy($res_image_class);

			$num_titles=count($arr_titles);
			for($i=0; $i<=$num_titles; $i++) {
				if($arr_titles[$i]['selected']==true) {
					$str_name=str_replace('%s',$str_name,$arr_titles[$i]['name']);
					continue;
				}
			}

			imagettftext($res_image_main,$num_font_size,0,31,70,$res_font_color,$str_font,$num_level);

			imagettftext($res_image_main,$num_font_size,0,101,22,$res_font_color,$str_font,$str_name);

			imagettftext($res_image_main,$num_font_size,0,101,46,$res_font_color,$str_font,$str_realm);

			imagettftext($res_image_main,$num_font_size,0,101,70,$res_font_color,$str_font,$str_guild ? '< '.$str_guild.' >' : '');
		}
	}
}

imagepng($res_image_main,NULL,9);
imagedestroy($res_image_main);
?>
