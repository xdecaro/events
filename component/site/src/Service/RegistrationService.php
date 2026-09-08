<?php
namespace Xdecaro\Component\Decaroevents\Site\Service;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;

final class RegistrationService
{
    public function register(int $eventId, int $sessionId, string $name, string $email): string
    {
        $name = trim($name);
        $email = strtolower(trim($email));
        if ($eventId < 1 || $name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \RuntimeException('Dati di registrazione non validi.');
        }

        /** @var DatabaseInterface $db */
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $db->transactionStart();

        try {
            $event = $this->loadEventForUpdate($db, $eventId);
            if (!$event || (int) $event['published'] !== 1 || (int) $event['registration_open'] !== 1) {
                throw new \RuntimeException('Registrazione non disponibile.');
            }

            $levels = array_map('intval', Factory::getApplication()->getIdentity()->getAuthorisedViewLevels());
            if (!in_array((int) $event['access'], $levels, true)) {
                throw new \RuntimeException('Evento non accessibile.');
            }

            $cutoff = !empty($event['end_at']) ? $event['end_at'] : $event['start_at'];
            if (Factory::getDate($cutoff)->getTimestamp() < Factory::getDate()->getTimestamp()) {
                throw new \RuntimeException('Evento concluso.');
            }

            $session = null;
            if ($sessionId > 0) {
                $session = $this->loadSessionForUpdate($db, $sessionId);
                if (!$session || (int) $session['event_id'] !== $eventId || (int) $session['published'] !== 1) {
                    throw new \RuntimeException('Sessione non valida.');
                }
            }

            $query = $db->getQuery(true)
                ->select('COUNT(*)')
                ->from($db->quoteName('#__decaroevents_registrations'))
                ->where($db->quoteName('event_id') . ' = :event')
                ->where($db->quoteName('email') . ' = :email')
                ->where($db->quoteName('status') . ' <> ' . $db->quote('cancelled'))
                ->bind(':event', $eventId, ParameterType::INTEGER)
                ->bind(':email', $email);
            if ((int) $db->setQuery($query)->loadResult() > 0) {
                throw new \RuntimeException('Esiste già una registrazione con questa email.');
            }

            $status = 'confirmed';
            $eventCapacity = (int) $event['capacity'];
            if ($eventCapacity > 0 && $this->confirmedCount($db, $eventId, 0) >= $eventCapacity) {
                $status = 'waitlist';
            }
            if ($status === 'confirmed' && $session && (int) $session['capacity'] > 0
                && $this->confirmedCount($db, $eventId, $sessionId) >= (int) $session['capacity']) {
                $status = 'waitlist';
            }

            $now = Factory::getDate()->toSql();
            $query = $db->getQuery(true)
                ->insert($db->quoteName('#__decaroevents_registrations'))
                ->columns([
                    $db->quoteName('event_id'),
                    $db->quoteName('session_id'),
                    $db->quoteName('name'),
                    $db->quoteName('email'),
                    $db->quoteName('status'),
                    $db->quoteName('created'),
                ])
                ->values(':event, ' . ($sessionId > 0 ? ':session' : 'NULL') . ', :name, :email, :status, :created')
                ->bind(':event', $eventId, ParameterType::INTEGER)
                ->bind(':name', $name)
                ->bind(':email', $email)
                ->bind(':status', $status)
                ->bind(':created', $now);
            if ($sessionId > 0) {
                $query->bind(':session', $sessionId, ParameterType::INTEGER);
            }
            $db->setQuery($query)->execute();
            $db->transactionCommit();

            return $status;
        } catch (\Throwable $exception) {
            $db->transactionRollback();
            throw $exception;
        }
    }

    private function loadEventForUpdate(DatabaseInterface $db, int $eventId): ?array
    {
        $query = $db->getQuery(true)
            ->select(['id', 'capacity', 'registration_open', 'published', 'access', 'start_at', 'end_at'])
            ->from($db->quoteName('#__decaroevents_events'))
            ->where($db->quoteName('id') . ' = ' . (int) $eventId);

        return $db->setQuery((string) $query . ' FOR UPDATE')->loadAssoc() ?: null;
    }

    private function loadSessionForUpdate(DatabaseInterface $db, int $sessionId): ?array
    {
        $query = $db->getQuery(true)
            ->select(['id', 'event_id', 'capacity', 'published'])
            ->from($db->quoteName('#__decaroevents_sessions'))
            ->where($db->quoteName('id') . ' = ' . (int) $sessionId);

        return $db->setQuery((string) $query . ' FOR UPDATE')->loadAssoc() ?: null;
    }

    private function confirmedCount(DatabaseInterface $db, int $eventId, int $sessionId): int
    {
        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__decaroevents_registrations'))
            ->where($db->quoteName('event_id') . ' = :event')
            ->where($db->quoteName('status') . ' = ' . $db->quote('confirmed'))
            ->bind(':event', $eventId, ParameterType::INTEGER);
        if ($sessionId > 0) {
            $query->where($db->quoteName('session_id') . ' = :session')
                ->bind(':session', $sessionId, ParameterType::INTEGER);
        }

        return (int) $db->setQuery($query)->loadResult();
    }
}
