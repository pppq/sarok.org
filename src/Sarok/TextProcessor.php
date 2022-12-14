<?php declare(strict_types=1);

namespace Sarok;

use tidy;

/**
 * Provides methods for cleanup and sanitization of user input.
 */
final class TextProcessor 
{
    private const ALLOWED_TAGS = '<a><h1><h2><h3><center><i><b><span><div><ul><li><small>' .
        '<big><sup><sub><strike><u><input><select><button><pre><hr><blockquote><code><img>' .
        '<object><param><embed><nowrap><select><option><form><iframe><br>';
    
    // Visibility increased for testing
    public const WRONG_WINDOW = 'Tegnap megismerkedtem egy fiúval és azóta csak rá gondolok. ' .
        'Gyönyörű göndör haja van, kék szeme és vastag farka, amely alig fér be a se... Bocs, ' .
        'azt hiszem rossz ablakba írtam!';

    private const DEFAULT_TIDY_CONFIG = array(
        'clean' => true,
        'output-xhtml' => true,
        'show-body-only' => true,
        'wrap' => 120, 
    );
    
    private Logger $logger;

    public function __construct(Logger $logger) 
    {
        $this->logger = $logger;
        $this->logger->debug("TextProcessor initialized");
    }

    public function preFormat(string $text) : string 
    {
        $this->logger->debug("Preformatting '${text}'");
        
        if (strlen($text) > 0) {
            $text = strip_tags($text, self::ALLOWED_TAGS);
            $text = str_replace('!!!!!!!!!!!!!!!', 'Idióta vagyok! Valaki lőjön le engem!', $text);
            $text = str_replace('???????????????', 'Idióta vagyok? Valaki lőjön le engem!', $text);
            $text = str_replace(' lol ', self::WRONG_WINDOW, $text);
            
            $text = preg_replace('/!!![! \n\r]+/', '!!!', $text);
            $text = preg_replace('/\?\?\?[\? \n\r]+/', '???', $text);
            $text = preg_replace('/\?!\?![\?! \n\r]+/', '?!?!', $text);
    
            $text = str_replace('(c)', '&copy;', $text);
            $text = str_replace('(r)', '&reg;', $text);
            $text = str_replace('(tm)', '&trade;', $text);
        }
        
        $this->logger->debug("Done preformatting");
        return $text;
    }

    public function postFormat(string $text, string $searchKeyword = '') : string 
    {
        $this->logger->debug("Post-formatting '${text}'");
        
        if (strlen($text) > 0) {
            $text = str_replace(' -- ', ' &ndash; ', $text);
            $text = str_replace(',-- ', ',&ndash; ', $text);
            $text = str_replace(' --,', ' &ndash;,', $text);
            
            // Auto-link to user's profile page
            $text = preg_replace('/uid_([A-Za-z0-9]+)/', '<a href="/users/\\1/" class="personid">\\1</a>', $text);
        
            /* 
             * Regexp detecting non-HTML tag sections: from CakePHP
             * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
             * Licensed under The MIT License
             * 
             * https://github.com/cakephp/cakephp/blob/3cce4816095cfb0bb858b4421df34511c9277458/src/Utility/Text.php#L531-L540
             */
            if (strlen($searchKeyword) > 0) {
                $searchKeyword = '/(?![^<]+>)(' . preg_quote($searchKeyword, '/') . ')(?![^<]+>)/iu';
                $text = preg_replace($searchKeyword, '<span class="search">\\1</span>', $text);
            }
        }
        
        $this->logger->debug("Done post-formatting");
        return $text;
    }

    public function tidy(string $text, array $tidy_config = self::DEFAULT_TIDY_CONFIG) : string 
    {
        $this->logger->debug("HTML Tidy-ing '${text}'");
        
        if (strlen($text) > 0) {
            $tidy = new tidy();
            $tidy->parseString($text, $tidy_config, 'utf8');
            $tidy->cleanRepair();
            $text = tidy_get_output($tidy); 
        }
        
        $this->logger->debug("Done HTML Tidy-ing");
        return $text;
    }
}
