<?php

function getBackground() {
	if (!defined('RAND_BACKGROUND')) {
		define("GlobalBackground", "http://ww4.sinaimg.cn/large/a15b4afegw1etu06dnx4dj21hc0u0nd0");
		return GlobalBackground;
	} else {
		//Background resources
		define("storm_ui_ingame_heroselect_azmodan", "http://ww4.sinaimg.cn/large/a15b4afegw1etxppgztr3j20sg0jg13i");
		define("storm_ui_ingame_heroselect_barbarian", "http://ww4.sinaimg.cn/large/a15b4afegw1etxpq97i3oj20sg0jgdjv");
		define("storm_ui_ingame_heroselect_butcher", "http://ww4.sinaimg.cn/large/a15b4afegw1etxps8pg8bj20sg0jgae2");
		define("storm_ui_ingame_heroselect_chen", "http://ww4.sinaimg.cn/large/a15b4afegw1etxpshagokj20sg0jgtcn");
		define("storm_ui_ingame_heroselect_demonhunter", "http://ww4.sinaimg.cn/large/a15b4afegw1etxptctt2yj20sg0jg425");
		define("storm_ui_ingame_heroselect_faeriedragon", "http://ww4.sinaimg.cn/large/a15b4afegw1etxptisg22j20sg0jgtc2");
		define("storm_ui_ingame_heroselect_femalebarbarian", "http://ww4.sinaimg.cn/large/a15b4afegw1etxptpwzvpj20sg0jgtcx");
		define("storm_ui_ingame_heroselect_goblintechies", "http://ww4.sinaimg.cn/large/a15b4afegw1etxptv1qoyj20sg0jgmxm");
		define("storm_ui_ingame_heroselect_infestor", "http://ww4.sinaimg.cn/large/a15b4afegw1etxpu2gy6kj20sg0jgwf9");
		define("storm_ui_ingame_heroselect_jaina", "http://ww4.sinaimg.cn/large/a15b4afegw1etxpucwxykj20sg0jgq3o");
		define("storm_ui_ingame_heroselect_kingleoric", "http://ww4.sinaimg.cn/large/a15b4afegw1etxpuj9mcnj20sg0jgdgc");
		define("storm_ui_ingame_heroselect_lili", "http://ww4.sinaimg.cn/large/a15b4afegw1etxpupcob4j20sg0jg74u");
		define("storm_ui_ingame_heroselect_malfurion", "http://ww4.sinaimg.cn/large/a15b4afegw1etxpuu4ownj20sg0jgt9b");
		define("storm_ui_ingame_heroselect_murky", "http://ww4.sinaimg.cn/large/a15b4afegw1etxpuz57zhj20sg0jggm6");
		define("storm_ui_ingame_heroselect_scv", "http://ww4.sinaimg.cn/large/a15b4afegw1etxpv4u8ytj20sg0jggma");
		define("storm_ui_ingame_heroselect_stitches", "http://ww4.sinaimg.cn/large/a15b4afegw1etxpva8mh3j20sg0jgdgg");
		define("storm_ui_ingame_heroselect_tyrael", "http://ww4.sinaimg.cn/large/a15b4afegw1etxpvghv3fj20sg0jgjrw");
		define("storm_ui_ingame_heroselect_tyrande", "http://ww4.sinaimg.cn/large/a15b4afegw1etxpvtvtbfj20sg0jg3z2");

		define("storm_ui_homescreenbackground_nexus", "http://ww4.sinaimg.cn/large/a15b4afegw1euhg74eylpj21hc0xcagp");
		define("storm_ui_homescreenbackground_eternalconflict_dark", "http://ww4.sinaimg.cn/large/a15b4afegw1euhg73fs59j21hc0xctfv");
		define("storm_ui_homescreenbackground_eternalconflict", "http://ww4.sinaimg.cn/large/a15b4afegw1euhg71ylfhj21hc0xcwm5");
		define("storm_ui_homescreenbackground_diablo", "http://ww4.sinaimg.cn/large/a15b4afegw1euhg710c6cj21hc0xcdlf");
		define("storm_ui_homescreenbackground", "http://ww4.sinaimg.cn/large/a15b4afegw1euhg707a6vj21hc0xc432");
		define("storm_ui_homescreen_lighting", "http://ww4.sinaimg.cn/large/a15b4afegw1euhg6zidnyj21hc0xcn5g");
		$backgroundPool = array(
			storm_ui_ingame_heroselect_azmodan,
			storm_ui_ingame_heroselect_barbarian,
			storm_ui_ingame_heroselect_butcher,
			storm_ui_ingame_heroselect_chen,
			storm_ui_ingame_heroselect_demonhunter,
			storm_ui_ingame_heroselect_faeriedragon,
			storm_ui_ingame_heroselect_femalebarbarian,
			storm_ui_ingame_heroselect_goblintechies,
			storm_ui_ingame_heroselect_infestor,
			storm_ui_ingame_heroselect_jaina,
			storm_ui_ingame_heroselect_kingleoric,
			storm_ui_ingame_heroselect_lili,
			storm_ui_ingame_heroselect_malfurion,
			storm_ui_ingame_heroselect_murky,
			storm_ui_ingame_heroselect_scv,
			storm_ui_ingame_heroselect_stitches,
			storm_ui_ingame_heroselect_tyrael,
			storm_ui_ingame_heroselect_tyrande,
		);
		return $backgroundPool[array_rand($backgroundPool, 1)];
	}
}

//UI resources
define('UI_GLOBAL_PREFIX', 'resources/ui/');

//UI Group Preloader
function resPreloader($uiGroup) {
	switch ($uiGroup) {
	case 'gcwcDraft':
		$path = "resources/gcwc-ui/draft/board/";
		break;

	case 'toppanel':
		$path = UI_GLOBAL_PREFIX . "toppanel/";
		break;

	case 'heroPortrait':
		$path = "resources/gcwc-ui/heroes/";
		break;

	case 'heroBtn':
		$path = "resources/gcwc-ui/heroesButton/";
		break;

	case 'blueBtn':
		$path = UI_GLOBAL_PREFIX . "button/blue/";
		break;

	case 'blueSmallBtn':
		$path = UI_GLOBAL_PREFIX . "button/blue_small/";
		break;

	case 'blueMiniBtn':
		$path = UI_GLOBAL_PREFIX . "button/blue_mini/";
		break;

	case 'playBtn':
		$path = UI_GLOBAL_PREFIX . "button/play/";
		break;

	case 'playBtnDual':
		$path = UI_GLOBAL_PREFIX . "button/play_dual/";
		break;

	case 'countdown':
		$path = UI_GLOBAL_PREFIX . "coutdown/";
		break;

	default:
		# code...
		break;
	}
	$ret = scandir($path);
	//unset parent directory (..) and this directory (.)
	unset($ret[0]);
	unset($ret[1]);
	//DIV Header
	echo "<div class=\"preloader\">";
	//content
	foreach ($ret as $filename) {
		echo "<img src=\"" . $path . $filename . "\" >";
	}
	//DIV Footer
	echo "</div>";
}
?>