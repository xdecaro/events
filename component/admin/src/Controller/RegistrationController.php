<?php
namespace Xdecaro\Component\Decaroevents\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;

final class RegistrationController extends FormController
{
    public function checkin(): void
    {
        Session::checkToken() or jexit('Invalid Token');
        $app = Factory::getApplication();
        $id = $app->input->getInt('id');
        if (!$app->getIdentity()->authorise('core.edit.state', 'com_decaroevents') || $id < 1) {
            throw new \RuntimeException('Not authorised', 403);
        }

        /** @var DatabaseInterface $db */
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $now = Factory::getDate()->toSql();
        $query = $db->getQuery(true)
            ->update($db->quoteName('#__decaroevents_registrations'))
            ->set($db->quoteName('checked_in_at') . ' = :now')
            ->where($db->quoteName('id') . ' = :id')
            ->where($db->quoteName('status') . ' = ' . $db->quote('confirmed'))
            ->bind(':now', $now)
            ->bind(':id', $id, ParameterType::INTEGER);
        $db->setQuery($query)->execute();

        if ($db->getAffectedRows() !== 1) {
            $app->enqueueMessage('Il check-in è consentito solo per registrazioni confermate.', 'warning');
        } else {
            $app->enqueueMessage('Check-in registrato.', 'success');
        }
        $app->redirect(Route::_('index.php?option=com_decaroevents&view=registration&layout=edit&id=' . $id, false));
    }
}
