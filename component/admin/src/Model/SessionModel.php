<?php
namespace Xdecaro\Component\Decaroevents\Administrator\Model;
defined('_JEXEC') or die;
use Joomla\CMS\Factory; use Joomla\CMS\MVC\Model\AdminModel; use Joomla\CMS\Table\Table; use Joomla\Database\DatabaseInterface; use Joomla\Database\ParameterType;
final class SessionModel extends AdminModel
{
    public function getTable($type='Session',$prefix='Administrator',$config=[]):Table{return parent::getTable($type,$prefix,$config);}
    public function getForm($data=[],$loadData=true){return $this->loadForm('com_decaroevents.session','session',['control'=>'jform','load_data'=>$loadData]);}
    protected function loadFormData(){return Factory::getApplication()->getUserState('com_decaroevents.edit.session.data',[]) ?: $this->getItem();}
    public function save($data):bool
    {
        $eventId=(int)($data['event_id']??0); $db=Factory::getContainer()->get(DatabaseInterface::class);
        $q=$db->getQuery(true)->select('COUNT(*)')->from($db->quoteName('#__decaroevents_events'))->where($db->quoteName('id').' = :id')->bind(':id',$eventId,ParameterType::INTEGER);
        if((int)$db->setQuery($q)->loadResult()!==1) throw new \RuntimeException('Evento non valido.');
        return parent::save($data);
    }
    protected function prepareTable($table):void{$now=Factory::getDate()->toSql(); if(empty($table->id))$table->created=$now; else $table->modified=$now;}
    protected function canDelete($record){return Factory::getApplication()->getIdentity()->authorise('core.delete','com_decaroevents');}
    protected function canEditState($record){return Factory::getApplication()->getIdentity()->authorise('core.edit.state','com_decaroevents');}
}
