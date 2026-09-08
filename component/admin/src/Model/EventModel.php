<?php
namespace Xdecaro\Component\Decaroevents\Administrator\Model;
defined('_JEXEC') or die;
use Joomla\CMS\Factory; use Joomla\CMS\Filter\OutputFilter; use Joomla\CMS\MVC\Model\AdminModel; use Joomla\CMS\Table\Table;
final class EventModel extends AdminModel
{
    public function getTable($type='Event',$prefix='Administrator',$config=[]): Table { return parent::getTable($type,$prefix,$config); }
    public function getForm($data=[],$loadData=true){return $this->loadForm('com_decaroevents.event','event',['control'=>'jform','load_data'=>$loadData]);}
    protected function loadFormData(){return Factory::getApplication()->getUserState('com_decaroevents.edit.event.data',[]) ?: $this->getItem();}
    protected function prepareTable($table): void { $now=Factory::getDate()->toSql(); if(empty($table->id))$table->created=$now; else $table->modified=$now; if(trim((string)$table->alias)==='')$table->alias=OutputFilter::stringURLSafe((string)$table->title); }
    protected function canDelete($record){return Factory::getApplication()->getIdentity()->authorise('core.delete','com_decaroevents');}
    protected function canEditState($record){return Factory::getApplication()->getIdentity()->authorise('core.edit.state','com_decaroevents');}
}
