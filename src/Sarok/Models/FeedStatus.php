<?php declare(strict_types=1);

namespace Sarok\Models;

/**
 * Enumerates possible syndication feed status values.
 */
enum FeedStatus : string
{
    /** 
     * Source feed can be periodically polled for new entries
     */
    case ALLOWED = 'allowed';

    /** 
     * No new entries will be added from the specified feed
     */
    case BANNED = 'banned';

    /**
     * No explicit permission or denial specified for this feed
     */
    case DEFAULT = '-';
}
