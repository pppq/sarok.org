<?php declare(strict_types=1);

namespace Sarok;

use InvalidArgumentException;
use Sarok\Actions\Action;
use Sarok\Models\MenuItem;
use Sarok\Models\User;
use Sarok\Pages\AboutPage;
use Sarok\Pages\AuthPage;
use Sarok\Pages\BlogPage;
use Sarok\Pages\ErrorPage;
use Sarok\Pages\FavouritesPage;
use Sarok\Pages\ImageBrowserPage;
use Sarok\Pages\IndexPage;
use Sarok\Pages\LogoutPage;
use Sarok\Pages\MailPage;
use Sarok\Pages\RegistrationPage;
use Sarok\Pages\SplashPage;

class Context
{
    private const SEGMENT_LIMIT = 15;

    /** The currently logged in user */
    private User $user;
    
    /** The owner of the currently browsed diary */
    private User $blog;
    /** The ID of the currently browsed entry */
    private int $entryID;

    /** 
     * Path parameters as an associative array 
     * @var array<string, mixed>
     */
    private array $pathParams;

    /** An array of links to be displayed in the sidebar */
    private array $leftMenuItems;

    /** The master template to use for rendering the current page */
    private string $templateName = 'default';

    /** Request path */
    private string $path;
    /** Request path segments */
    private array $segments;

    private Logger $logger;
    private DIContainer $container;

    public function __construct(Logger $logger, DIContainer $container)
    {
        $this->logger = $logger;
        $this->container = $container;
    }

    public function getUser() : User
    {
        return $this->user;
    }

    public function setUser(User $user) : void
    {
        $this->user = $user;
    }

    public function isLoggedIn() : bool
    {
        return $this->user->getID() === User::ID_ANONYMOUS;
    }

    public function getBlog() : User
    {
        return $this->blog;
    }

    public function setBlog(User $blog) : void
    {
        $this->blog = $blog;
    }

    public function getEntryID() : int
    {
        return $this->entryID;
    }

    public function setEntryID(int $entryID) : void
    {
        $this->entryID = $entryID;
    }

    public function hasEntryID() : bool
    {
        return isset($this->entryID);
    }

    public function getTemplateName() : string
    {
        return $this->templateName;
    }

    public function setTemplateName(string $templateName) : void
    {
        $this->templateName = $templateName;
    }

    public function getLeftMenuItems() : array
    {
        return $this->leftMenuItems;
    }

    public function getBlogEntries() : array
    {
        return $this->blogEntries;
    }

    public function setBlogEntries(array $blogEntries) : void
    {
        $this->blogEntries = $blogEntries;
    }

    public function getPathParams() : array
    {
        return $this->pathParams;
    }

    public function setPathParams(array $pathParams) : void
    {
        $this->pathParams = $pathParams;
    }

    public function setLeftMenuItems(MenuItem ...$leftMenuItems) : void
    {
        $this->leftMenuItems = $leftMenuItems;
    }

    public function hasLeftMenuItems() : bool
    {
        return isset($this->leftMenuItems);
    }

    public function getPathSegment(int $segment) : string
    {
        $pathSegment = array_slice($this->segments, $segment, 1);

        if (count($pathSegment) > 0) {
            return $pathSegment[0];
        } else {
            return '';
        }
    }

    public function removeFirstSegment() : string
    {
        $firstSegment = array_shift($this->segments);
        
        if ($firstSegment !== null) {
            return $firstSegment;
        } else {
            return '';
        }
    }

    public function parsePath(string $path) : string
    {
        $this->logger->debug("Parsing request path '$path'");
        $this->path = $path;

        // Split by path separator, remove trailing empty separator
        $this->segments = explode('/', $path, self::SEGMENT_LIMIT);

        $lastPos = count($this->segments) - 1;
        if ($lastPos >= 0 && strlen($this->segments[$lastPos]) === 0) {
            unset($this->segments[$lastPos]);
        }

        // Root path corresponds to the dashboard or the login page
        if (count($this->segments) === 0) {
            if ($this->isLoggedIn()) {
                return IndexPage::class;
            } else {
                return SplashPage::class;
            }
        }

        // Otherwise the first path segment decides which page to display
        $firstSegment = $this->removeFirstSegment();

        switch ($firstSegment) {
            case 'about':
                return AboutPage::class;
            case 'auth':
                return AuthPage::class;
            case 'users':
                return BlogPage::class;
            case 'favourites':
                return FavouritesPage::class;
            case 'imagebrowser':
                return ImageBrowserPage::class;
            case 'logout':
                return LogoutPage::class;
            case 'mail': // fall-through
            case 'privates':
                return MailPage::class;
            case 'registration':
                return RegistrationPage::class;
            case 'settings':
                return SettingsPage::class;
            default:
                return ErrorPage::class;
        }
    }

    public function getPath() : string
    {
        return $this->path;
    }

    public function isPOST() : bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    public function getPOST(string $name, mixed $defaultValue = '') : mixed
    {
        if (isset($_POST[$name])) {
            return $_POST[$name];
        } else {
            return $defaultValue;
        }
    }

    public function getUpload(string $name) : array {
        return $_FILES[$name];
    }

    public function getAction(string $name) : Action
    {
        if (!is_subclass_of($name, Action::class)) {
            throw new InvalidArgumentException("Class '$name' is not a subclass of Action.");
        }
        
        return $this->container->get($name, true);
    }
}
