<?php
namespace Xdecaro\Component\Decaroevents\Administrator\Controller;
defined('_JEXEC') or die; use Joomla\CMS\MVC\Controller\AdminController; final class RegistrationsController extends AdminController { public function getModel($name='Registration',$prefix='Administrator',$config=['ignore_request'=>true]){return parent::getModel($name,$prefix,$config);} }
