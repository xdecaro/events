<?php
namespace Xdecaro\Component\Decaroevents\Administrator\Controller;
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Controller\AdminController;
final class EventsController extends AdminController { public function getModel($name='Event',$prefix='Administrator',$config=['ignore_request'=>true]){return parent::getModel($name,$prefix,$config);} }
