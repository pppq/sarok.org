<?php declare(strict_types=1);

namespace Sarok\Models;

/**
 * Represents a menu item with a display name and an association hyperlink URL.
 */
class MenuItem
{
    private string $name;
    private string $url;

    public function __construct(string $name, string $url)
    {
        $this->name = $name;
        $this->url = $url;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getUrl() : string
    {
        return $this->url;
    }
}
