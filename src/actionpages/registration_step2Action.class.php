<?php

class registration_step2Action extends Action{
protected $sessionFacade;
 	function execute()
 	{
		$this->sessionFacade=singletonloader::getInstance("sessionfacade");
		$db=singletonloader::getInstance("mysql");
		$df=singletonloader::getInstance("dbfacade");
		$this->log->debug("registration_step2Action");

		$loginName="";
		$canRun=true;

		//Checking login
		$this->log->debug("Checking login");
		if(isset($_POST["login"]))
		{
			$login=$_POST["login"];
			$out["login"]=$login;
		}
		else
		{
			$canReg=false;

		}
		$login=strtolower($login);

		if(strlen($login)<2 || strlen($login)>15)
		{
			$canReg=false;
		}
		if(!ereg("^[a-z][a-z0-9_]{1,14}$",$login))
		{
			$canReg=false;
		}
		if(ereg("((nyuszi)|(angyal)|(lany)|(angel))",$login,$regs))
		{
			$canReg=false;

		}
		if(ereg("(szar)|(punci)|(pocs)|(fasz)|(picsa)|(geci)|(segg)",$login))
		{
			$canReg=false;
		}

		$q="select count(Login) as num from users where login='$login'";
		$number=$db->querynum($q);
		if($number>0)
		{
			$canReg=false;
		}
		else
		{
			$canReg=true;
		}
		$this->log->debug("Checking passwords");
	//check password
		$pass1=$_POST["pass1"];
		$pass2=$_POST["pass2"];
		if($pass1!=$pass2) $canReg=false;
		if(strlen($pass1)<3) $canReg=false;

	//check email
		$this->log->debug("Checking email");
		$email=$_POST["email"];
		if(!ereg("^.+@.+\..+$",$email)) $canRun=false;

		if($canReg)
			$this->log->debug("Check successfull");
		else
			$this->log->warning("Registration: login, password, email check unsuccessfull!");
		$out["canReg"]=$canReg;
		//$out["name"]=$this->context->user->Name;
		if($canReg)
		{
			$df->addUser($login,$pass1,$email);
			$newUser=$this->context->requestUserDAL($login);
			$newUser->entriesPerPage=10;
			$newUser->messageAccess="ALL";
			$newUser->commentAccess="ALL";
			$newUser->commit();
			
			$this->log->mail("New user registration: Login: $login, pass: $pass1, Email: $email","New user registration: Login: $login");
			$out["location"]="/";
			$this->log->debug("Blogger is ".$_POST["blogger"]);
			if(isset($_POST["blogger"]) and $_POST["blogger"]=='Y')
			{
				$this->log->debug("Blogger is set");
				$out["location"]="/settings/other/";
			}
		$this->sessionFacade->loginUser($login,$pass1);
		$success=true;
		//$out["location"]="/";
		}
		return $out;
 	}
}
?>