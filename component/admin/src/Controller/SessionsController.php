<?php
namespace Xdecaro\Component\Decaroevents\Administrator\Controller;
defined('_JEXEC') or die; use Joomla\CMS\MVC\Controller\AdminController; final class SessionsController extends AdminController { public function getModel($name='Session',$prefix='Administrator',$config=['ignore_request'=>true]){return parent::getModel($name,$prefix,$config);} }
