<?php
namespace Xdecaro\Component\Decaroevents\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\Database\ParameterType;

final class DashboardModel extends BaseDatabaseModel
{
    public function getDashboard(): array
    {
        $db = $this->getDatabase();
        $now = Factory::getDate()->toSql();

        $events = $this->countRows('#__decaroevents_events');
        $sessions = $this->countRows('#__decaroevents_sessions');
        $registrations = $this->countRows('#__decaroevents_registrations');
        $pending = $this->countRegistrationStatus('pending');
        $waitlist = $this->countRegistrationStatus('waitlist');

        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__decaroevents_events'))
            ->where($db->quoteName('published') . ' = 1')
            ->where($db->quoteName('start_at') . ' >= :now')
            ->bind(':now', $now);
        $db->setQuery($query);
        $upcomingEvents = (int) $db->loadResult();

        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__decaroevents_registrations'))
            ->where($db->quoteName('checked_in_at') . ' IS NOT NULL');
        $db->setQuery($query);
        $checkedIn = (int) $db->loadResult();

        $query = $db->getQuery(true)
            ->select([
                $db->quoteName('id'),
                $db->quoteName('title'),
                $db->quoteName('start_at'),
                $db->quoteName('location'),
                $db->quoteName('capacity'),
                $db->quoteName('registration_open'),
            ])
            ->from($db->quoteName('#__decaroevents_events'))
            ->where($db->quoteName('published') . ' = 1')
            ->where($db->quoteName('start_at') . ' >= :now')
            ->order($db->quoteName('start_at') . ' ASC')
            ->setLimit(5)
            ->bind(':now', $now);
        $db->setQuery($query);
        $nextEvents = $db->loadObjectList();

        return [
            'events' => $events,
            'upcoming_events' => $upcomingEvents,
            'sessions' => $sessions,
            'registrations' => $registrations,
            'pending' => $pending,
            'waitlist' => $waitlist,
            'checked_in' => $checkedIn,
            'next_events' => $nextEvents,
        ];
    }

    private function countRows(string $table): int
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName($table));
        $db->setQuery($query);

        return (int) $db->loadResult();
    }

    private function countRegistrationStatus(string $status): int
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__decaroevents_registrations'))
            ->where($db->quoteName('status') . ' = :status')
            ->bind(':status', $status, ParameterType::STRING);
        $db->setQuery($query);

        return (int) $db->loadResult();
    }
}
