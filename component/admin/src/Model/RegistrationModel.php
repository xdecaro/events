<?php
namespace Xdecaro\Component\Decaroevents\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;

final class RegistrationModel extends AdminModel
{
    public function getTable($type = 'Registration', $prefix = 'Administrator', $config = []): Table
    {
        return parent::getTable($type, $prefix, $config);
    }

    public function getForm($data = [], $loadData = true)
    {
        return $this->loadForm('com_decaroevents.registration', 'registration', ['control' => 'jform', 'load_data' => $loadData]);
    }

    protected function loadFormData()
    {
        return Factory::getApplication()->getUserState('com_decaroevents.edit.registration.data', []) ?: $this->getItem();
    }

    public function save($data): bool
    {
        $eventId = (int) ($data['event_id'] ?? 0);
        $sessionId = (int) ($data['session_id'] ?? 0);
        $status = (string) ($data['status'] ?? 'pending');
        $id = (int) ($data['id'] ?? 0);
        $email = strtolower(trim((string) ($data['email'] ?? '')));
        /** @var DatabaseInterface $db */
        $db = Factory::getContainer()->get(DatabaseInterface::class);

        $query = $db->getQuery(true)
            ->select([$db->quoteName('id'), $db->quoteName('capacity')])
            ->from($db->quoteName('#__decaroevents_events'))
            ->where($db->quoteName('id') . ' = :event')
            ->bind(':event', $eventId, ParameterType::INTEGER);
        $event = $db->setQuery($query)->loadAssoc();
        if (!$event) {
            throw new \RuntimeException('Evento non valido.');
        }

        $sessionCapacity = 0;
        if ($sessionId > 0) {
            $query = $db->getQuery(true)
                ->select([$db->quoteName('event_id'), $db->quoteName('capacity')])
                ->from($db->quoteName('#__decaroevents_sessions'))
                ->where($db->quoteName('id') . ' = :session')
                ->bind(':session', $sessionId, ParameterType::INTEGER);
            $session = $db->setQuery($query)->loadAssoc();
            if (!$session || (int) $session['event_id'] !== $eventId) {
                throw new \RuntimeException('La sessione non appartiene all evento selezionato.');
            }
            $sessionCapacity = (int) $session['capacity'];
        }

        if ($email !== '') {
            $query = $db->getQuery(true)
                ->select('COUNT(*)')
                ->from($db->quoteName('#__decaroevents_registrations'))
                ->where($db->quoteName('event_id') . ' = :event')
                ->where($db->quoteName('email') . ' = :email')
                ->bind(':event', $eventId, ParameterType::INTEGER)
                ->bind(':email', $email);
            if ($id > 0) {
                $query->where($db->quoteName('id') . ' <> :id')->bind(':id', $id, ParameterType::INTEGER);
            }
            if ((int) $db->setQuery($query)->loadResult() > 0) {
                throw new \RuntimeException('Esiste già una registrazione per questa email e questo evento.');
            }
        }

        if ($status === 'confirmed') {
            $eventCapacity = (int) $event['capacity'];
            if ($eventCapacity > 0 && $this->confirmedCount($db, $eventId, 0, $id) >= $eventCapacity) {
                throw new \RuntimeException('Capienza evento esaurita: usa lo stato lista d attesa.');
            }
            if ($sessionId > 0 && $sessionCapacity > 0 && $this->confirmedCount($db, $eventId, $sessionId, $id) >= $sessionCapacity) {
                throw new \RuntimeException('Capienza sessione esaurita: usa lo stato lista d attesa.');
            }
        }

        return parent::save($data);
    }

    private function confirmedCount(DatabaseInterface $db, int $eventId, int $sessionId, int $excludeId): int
    {
        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__decaroevents_registrations'))
            ->where($db->quoteName('event_id') . ' = :event')
            ->where($db->quoteName('status') . ' = ' . $db->quote('confirmed'))
            ->bind(':event', $eventId, ParameterType::INTEGER);
        if ($sessionId > 0) {
            $query->where($db->quoteName('session_id') . ' = :session')->bind(':session', $sessionId, ParameterType::INTEGER);
        }
        if ($excludeId > 0) {
            $query->where($db->quoteName('id') . ' <> :exclude')->bind(':exclude', $excludeId, ParameterType::INTEGER);
        }
        return (int) $db->setQuery($query)->loadResult();
    }

    protected function prepareTable($table): void
    {
        $now = Factory::getDate()->toSql();
        if (empty($table->id)) {
            $table->created = $now;
        } else {
            $table->modified = $now;
        }
    }

    protected function canDelete($record)
    {
        return Factory::getApplication()->getIdentity()->authorise('core.delete', 'com_decaroevents');
    }
}
