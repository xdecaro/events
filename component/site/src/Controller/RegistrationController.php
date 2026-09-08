<?php
namespace Xdecaro\Component\Decaroevents\Site\Controller;
defined('_JEXEC') or die;
use Joomla\CMS\Factory; use Joomla\CMS\MVC\Controller\BaseController; use Joomla\CMS\Router\Route; use Joomla\CMS\Session\Session; use Xdecaro\Component\Decaroevents\Site\Service\RegistrationService;
final class RegistrationController extends BaseController
{
    public function submit(): void
    {
        Session::checkToken() or jexit('Invalid Token');
        $app=Factory::getApplication(); $id=$app->input->getInt('event_id'); $session=$app->input->getInt('session_id'); $name=trim($app->input->getString('name')); $email=trim($app->input->getString('email'));
        try { $status=(new RegistrationService())->register($id,$session,$name,$email); $app->enqueueMessage($status==='waitlist'?'Registrazione inserita in lista d’attesa.':'Registrazione confermata.','success'); } catch(\Throwable $e){$app->enqueueMessage($e->getMessage(),'error');}
        $app->redirect(Route::_('index.php?option=com_decaroevents&view=event&id='.$id,false));
    }
}
