<?php

declare(strict_types=1);

define('_JEXEC', 1);
define('JPATH_BASE', getcwd());
require JPATH_BASE . '/includes/defines.php';
require JPATH_BASE . '/includes/framework.php';

use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\Database\DatabaseInterface;
use Xdecaro\Component\Decaroevents\Site\Service\RegistrationService;

$container = Factory::getContainer();
$container->alias('session', 'session.cli')
    ->alias('JSession', 'session.cli')
    ->alias(Session::class, 'session.cli')
    ->alias(\Joomla\Session\Session::class, 'session.cli')
    ->alias(\Joomla\Session\SessionInterface::class, 'session.cli');
$app = $container->get(\Joomla\Console\Application::class);
Factory::$application = $app;
$app->createExtensionNamespaceMap();
$app->loadIdentity($container->get(UserFactoryInterface::class)->loadUserById(0));

/** @var DatabaseInterface $db */
$db = $container->get(DatabaseInterface::class);
$now = Factory::getDate()->toSql();

$db->setQuery(
    $db->getQuery(true)
        ->insert($db->quoteName('#__decaroevents_events'))
        ->columns([
            $db->quoteName('title'), $db->quoteName('alias'), $db->quoteName('event_type'),
            $db->quoteName('description'), $db->quoteName('location'), $db->quoteName('start_at'),
            $db->quoteName('end_at'), $db->quoteName('capacity'), $db->quoteName('registration_open'),
            $db->quoteName('access'), $db->quoteName('published'), $db->quoteName('created'),
        ])
        ->values(
            $db->quote('Runtime event') . ', ' . $db->quote('runtime-event') . ', ' . $db->quote('test') . ', ' .
            $db->quote('') . ', ' . $db->quote('Test room') . ', ' . $db->quote('2099-01-10 10:00:00') . ', ' .
            $db->quote('2099-01-10 18:00:00') . ', 10, 1, 1, 1, ' . $db->quote($now)
        )
)->execute();
$eventId = (int) $db->insertid();

$sessionIds = [];
foreach ([['Session A', '2099-01-10 11:00:00'], ['Session B', '2099-01-10 15:00:00']] as [$title, $start]) {
    $db->setQuery(
        $db->getQuery(true)
            ->insert($db->quoteName('#__decaroevents_sessions'))
            ->columns([
                $db->quoteName('event_id'), $db->quoteName('title'), $db->quoteName('location'),
                $db->quoteName('start_at'), $db->quoteName('end_at'), $db->quoteName('capacity'),
                $db->quoteName('published'), $db->quoteName('created'),
            ])
            ->values(
                $eventId . ', ' . $db->quote($title) . ', ' . $db->quote('Test room') . ', ' .
                $db->quote($start) . ', ' . $db->quote($start) . ', 10, 1, ' . $db->quote($now)
            )
    )->execute();
    $sessionIds[] = (int) $db->insertid();
}

$service = new RegistrationService();
$email = 'same.person@example.invalid';

if ($service->register($eventId, $sessionIds[0], 'Same Person', $email) !== 'confirmed') {
    throw new RuntimeException('First session registration was not confirmed.');
}

if ($service->register($eventId, $sessionIds[1], 'Same Person', $email) !== 'confirmed') {
    throw new RuntimeException('Same email must be allowed on a different session of the same event.');
}

$duplicateRejected = false;
try {
    $service->register($eventId, $sessionIds[0], 'Same Person', $email);
} catch (RuntimeException $exception) {
    $duplicateRejected = str_contains($exception->getMessage(), 'già una registrazione');
}

if (!$duplicateRejected) {
    throw new RuntimeException('Duplicate registration on the same session must be rejected.');
}

echo "Events registration runtime scenarios OK\n";
