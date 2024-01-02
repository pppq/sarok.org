<?php

class settings_infoAction extends Action
{
    protected $sessionFacade;

 	function execute()
 	{
		$this->sessionFacade = singletonloader::getInstance('sessionfacade');
		$this->log->debug('Running settings_infoAction');

		$userDataKeys = array(
            'birthDate',
            'birthYear',
            'city',
            'country',
            'description',
            'district',
            'email',
            'eyeColor',
            'hairColor',
            'ICQ',
            'MSN',
            'skype',
            'name',
            'keywords',
            'occupation',
            'phone',
            'publicInfo',
            'sex',
            'state',
            'WIW'
        );
	    
        $userData = $this->context->getUserData($this->context->user->ID);
        foreach ($userDataKeys as $key) {
            $out[$key] = $userData[$key];
        }

		return $out;
 	}
}
