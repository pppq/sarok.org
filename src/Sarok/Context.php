<?php declare(strict_types=1);

namespace Sarok;

use Sarok\Models\User;
use Sarok\Logger;

class Context
{
    const PROP_IS_LOGGED_IN = 'isLoggedIn';
    const PROP_ENTRY_ID = 'entryID';
    const PROP_MENU_ITEMS = 'menuItems';

    public $users = array(); //hash of loaded users
    public $session; // sessionClass that stores current session values
    public $entries = array(); // hash of loaded entries
    public $comments = array(); //hash of loaded comments
    public $mails = array(); // hash of loaded mails
    public $blog; // current blog
    public $params; // paramaeters from the URL
    public $ActionPage; //action to execute
    
    
    public User $user;
    private array $properties = array();
    private Logger $logger;
    private string $templateName = 'default';

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function getTemplateName() : string
    {
        return $this->templateName;
    }

    public function setTemplateName(string $templateName) : void
    {
        $this->templateName = $templateName;
    }

    public function getUser() : User
    {
        return $this->user;
    }

    public function getProperty(string $name, $defaultValue = false) : mixed
    {
        if (isset($this->properties[$name])) {
            return $this->properties[$name];
        } else {
            return $defaultValue;
        }
    }

    public function setProperty(string $name, $value) : void
    {
        $this->properties[$name] = $value;
    }

    public function getPathSegment(int $segment) : string
    {
        // Return empty string if there are not enough segments
    }




    public function requestUserDAL($ida)
    {
        $this->log->debug2("requestUserDAL($ida)");
        $id=userDAL::findID($ida);
        if (!array_key_exists($id, $this->users)) {
            new userDAL($id);
            $this->log->debug("$id added to container");
        }

        return ($this->users[$id]);
    }


    public function parseURL($url)
    {
        $this->log->debug2("parsing url $url");
        if (strlen($url) == 0) {
            if ($this->getProperty("loggedin")==false) {
                $ActionPage = "splash";
            } else {
                $ActionPage = "default";
            }
            $this->log->debug("Action Page is {$ActionPage}");
            return $ActionPage;
        }
        $p = explode("/", $url);
        $this->params=$p;
        if ($p[sizeof($p) - 1] == "") {
            unset($p[sizeof($p) - 1]);
        }
        if ($p[0] == "users") {
            if (sizeof($p) > 1 and $p[1]!='rss') {
                $this->blog = $this->requestUserDAL($p[1]);
                array_shift($p);
            } else {
                $this->blog = $this->requestUserDAL("all");
            }
            $this->props["blog"] = true;
            $this->log->debug("blog is set");

            array_shift($p);
            $this->params=$p;
            $ActionPage = "blog";
            return $ActionPage;
        }
        if ($p[0]=="mail" or $p[0]=="privates") {
            $ActionPage = "mail";

            return $ActionPage;
        }

        if (class_exists($p[0]."AP")) {
            $ActionPage = $p[0];
            array_shift($p);
        } else {
            $ActionPage = "error";
        }
        $this->params=$p;
        $this->log->debug("Action Page is {$ActionPage}");
        return $ActionPage;
    }
}
