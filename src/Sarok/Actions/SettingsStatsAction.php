<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Util;
use Sarok\Service\UserService;
use Sarok\Models\User;
use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\Action;
use DateTime;
use DatePeriod;
use DateInterval;

class SettingsStatsAction extends Action
{
    private StatService $statService;

    public function __construct(Logger $logger, Context $context, StatService $statService)
    {
        parent::__construct($logger, $context);
        $this->statService = $statService;
    }

    public function execute() : array
    {
        $this->log->debug('Running SettingsStatsAction');

        $user = $this->context->getUser();
        
        $startDate = $user->getCreateDate();
        $endDate = Util::utcDateTimeFromString();
        $monthList = $this->getMonthList($startDate, $endDate);

        $blogStat = $this->statService->getBlogStatistics($userID);
        $lastCollection = $this->statService->getLastCollectionDate();
    }
    
    private function getMonthList(DateTime $startDate, DateTime $endDate) : array
    {
        // Set creation date to the beginning of the month
        $startDate->setDate((int) $startDate->format('Y'), (int) $startDate->format('m'), 1);
        $startDate->setTime(0, 0);
        
        // The end date is not modified; add one month however as DatePeriod is exclusive
        $endDate->setTime(0, 0);
        $endDate = $endDate->modify('+1 month');

        $interval = DateInterval::createFromDateString('1 month');
        $range = new DatePeriod($startDate, $interval, $endDate);
        
        $monthList = array();
        foreach ($range as $date) {
            $monthList[] = $date;
        }

        return $monthList;
    }
}
