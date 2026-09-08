<?php
namespace Xdecaro\Component\Decaroevents\Administrator\Table;

defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

final class RegistrationTable extends Table
{
    private const STATUSES = ['pending', 'confirmed', 'waitlist', 'cancelled'];

    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__decaroevents_registrations', 'id', $db);
    }

    public function check(): bool
    {
        $this->event_id = (int) $this->event_id;
        $this->session_id = (int) $this->session_id ?: null;
        $this->name = trim((string) $this->name);
        $this->email = strtolower(trim((string) $this->email));
        $this->status = (string) $this->status;
        $this->checked_in_at = trim((string) $this->checked_in_at) !== '' ? $this->checked_in_at : null;

        if ($this->event_id < 1 || $this->name === '' || !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->setError('Event, name and a valid email are required.');
            return false;
        }
        if (!in_array($this->status, self::STATUSES, true)) {
            $this->setError('Invalid registration status.');
            return false;
        }

        return true;
    }
}
