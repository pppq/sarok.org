<?php

class menuAction extends Action{
protected $sessionFacade;
 	function execute()
 	{
 		global $APclass;
 		$i=0;
 		$menu[$i]["name"]="Főlap";
		$menu[$i]["url"]="/";
		if($this->context->props["loggedin"])
		{


				//$i;
				$i++;
				$menu[$i]["name"]="Bejegyzés irása";
				$menu[$i]["url"]="/users/".$this->context->user->login."/new/";
				$i++;
				$menu[$i]["name"]="Beállitások";
				$menu[$i]["url"]="/settings/";
				$i++;
				$menu[$i]["name"]="Levelezés";
				$menu[$i]["url"]="/privates/";
				$i++;
				$menu[$i]["name"]="Pizzaszelet";
				$menu[$i]["url"]="/users/".$this->context->user->login."/";

			/*else
			{
				$menu[$i]["name"]=$this->context->blog->login;
				$menu[$i]["url"]="/users/".$this->context->blog->login."/";
				$i++;
				$menu[$i]["name"]="Adatlap";
				$menu[$i]["url"]="/users/".$this->context->blog->login."/info/";
				$i++;
				$menu[$i]["name"]="Archivum";
				$menu[$i]["url"]="/users/".$this->context->blog->login."/".year()."/";
				if(sizeof($this->context->blog->friends)>0)
				{
					$i++;
					$menu[$i]["name"]="Barátok";
					$menu[$i]["url"]="/users/".$this->context->blog->login."/friends/";
				}
				$i++;
				$menu[$i]["name"]="RSS";
				$menu[$i]["url"]="/users/".$this->context->blog->login."/rss/";
			}*/
		}
		else{
				$i++;
				$menu[$i]["name"]="Regisztráció";
				$menu[$i]["url"]="/registration/";

				$i++;
				$menu[$i]["name"]="Rolunk";
				$menu[$i]["url"]="/about/";

		}
		//print_r($menu);
		$out["menu"]=$menu;
		//		print_r($this->context);
		$out["url"]=$APclass;  //magyon gaz!
		//print_r($out);
		return $out;
 	}
}
?>