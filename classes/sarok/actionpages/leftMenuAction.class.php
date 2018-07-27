<?php

class leftMenuAction extends Action{
protected $sessionFacade;
 	function execute()
 	{
		if($this->context->props["loggedin"])
		{
			$ap=get_class($this->context->ActionPage);
			switch($ap)
			{
			case "settingsAP":
			$menu[0]["name"]="Adatok";
			$menu[0]["url"]="/settings/";
			$menu[1]["name"]="Blog";
			$menu[1]["url"]="/settings/blog";
			$menu[2]["name"]="Barátok";
			$menu[2]["url"]="/settings/friends/";

			$menu[3]["name"]="Képek";
			$menu[3]["url"]="/settings/images/";

			$menu[4]["name"]="Térkép";
			$menu[4]["url"]="/settings/map/";

			$menu[5]["name"]="Külső";
			$menu[5]["url"]="/settings/skin/";

			$menu[6]["name"]="Import/Export/Varázslat";
			$menu[6]["url"]="/settings/other/";
			
			$menu[7]["name"]="Statisztika";
			$menu[7]["url"]="/settings/stats/";
			
						
			$menu[8]["name"]="Snowboardos arc";
			$menu[8]["url"]="/settings/ski/";
			break;
			
			case "aboutAP":
			$menu[0]["name"]="Bemutató";
			$menu[0]["url"]="/about/";
			$menu[1]["name"]="Páciensek listája";
			$menu[1]["url"]="/about/pacients";
			$menu[2]["name"]="Felhasználói térkép";
			$menu[2]["url"]="/about/map/";
			
			break;
			default:
			$menu[0]["name"]="Bejegyzés irása";
			$menu[0]["url"]="/users/".$this->context->user->login."/new/";
			if(isset($this->context->blog))
			{
				$bf=singletonloader:: getInstance("blogfacade");
			if($bf->canAddEntry($this->context->user, $this->context->blog))
				$menu[0]["url"]="/users/".$this->context->blog->login."/new/";
			}
				
				
			$menu[1]["name"]="Level irasa";
			$menu[1]["url"]="/privates/new/";
			$menu[2]["name"]="Beallitasok";
			$menu[2]["url"]="/settings/";
			$menu[3]["name"]="Könyjelzők";
			$menu[3]["url"]="/favourites/";
			$menu[4]["name"]="Páciensek listája";
			$menu[4]["url"]="/about/pacients";
			}
		}
		else
		{

				$menu[0]["name"]="Bemutató";
			$menu[0]["url"]="/about/";
			$menu[1]["name"]="Páciensek listája";
			$menu[1]["url"]="/about/pacients";
			$menu[2]["name"]="Felhasználói térkép";
			$menu[2]["url"]="/about/map/";
			
		}
		$out["menu"]=$menu;

		return $out;
 	}
}
?>