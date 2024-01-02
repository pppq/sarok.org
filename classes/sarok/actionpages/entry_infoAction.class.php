<?php

class entry_infoAction extends Action
{
    protected $sf;
    protected $bf;
    protected $mysql;

 	function execute()
 	{
		$blog=$this->context->blog;
		$user=$this->context->user;
		$out=array();
		$this->sf=singletonloader::getInstance("sessionfacade");
		$this->mysql=singletonloader::getInstance("mysql");
		$params=$this->context->ActionPage->params;
		$this->log->debug("Info Action inited");

		$out["blogLogin"]=$this->context->blog->login;
		$out["friends"]=$this->context->blog->friends;
		$out["friendOfs"]=$this->context->blog->friendOfs;
		$out["myFriends"]=$this->context->user->friends;

        // Core properties of each user
        $userKeys = array(
            'ID',
            'login',
            'createDate',
            'loginDate',
            'activationDate',
        );

        // Keys for metadata key-value pairs stored in a separate table
        $userDataKeys = array(
            'name',
            'blogName',
            'occupation',
            'hairColor',
            'eyeColor',
            'blogName',
            'description',
            'sex',
            'district',
            'country',
            'city',
            'email',
            'WIW',
            'MSN',
            'ICQ',
            'skype',
            'phone',
            'birthYear',
            'birthDate'
        );

		foreach ($userKeys as $key)
		{
			$this->log->debug("getting {$key}");
			$out[$key] = $blog->$key;
		}

        $userData = $this->context->getUserData($this->context->blog->ID);
        foreach ($userDataKeys as $key) {
            $out[$key] = $userData[$key];
        }
		
        // For debugging purposes only (keys are used to enumerate all values available within the template)
        $out["props"] = array_merge($userKeys, $userDataKeys);
		$out["logins"] = $this->sf->getUserLogins(array_merge($out["friends"], $out["friendOfs"]));
		return $out;
 	}
}
