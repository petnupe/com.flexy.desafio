<?php

class TagFormList extends TPage
{
    protected $form; // form
    protected $datagrid; // datagrid
    protected $pageNavigation;
    protected $loaded;
    
    public function __construct( $param = null )
    {
        parent::__construct();
        $this->form = new BootstrapFormBuilder('form_Tag');
        $this->form->setFormTitle('Tag');
        $id = new THidden('id');
        $name = new TEntry('name');
        $this->form->addFields( [ new TLabel('') ], [ $id ] );
        $this->form->addFields( [ new TLabel('Name') ], [ $name ] );

        $id->setSize('100%');
        $name->setSize('100%');
        
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        $this->createDatagrid();
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        parent::add($container);
    }
    
    private function createPageNavigation() 
    {
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
    }

    private function createDatagrid()
    {
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $column_id = new TDataGridColumn('id', '', 'left');
        $column_name = new TDataGridColumn('name', 'Name', 'left');
        $this->datagrid->addColumn($column_name);
        $action1 = new TDataGridAction([$this, 'onEdit']);
        $action1->setLabel(_t('Edit'));
        $action1->setImage('far:edit blue');
        $action1->setField('id');
        $action2 = new TDataGridAction([$this, 'onDelete']);
        $action2->setLabel(_t('Delete'));
        $action2->setImage('far:trash-alt red');
        $action2->setField('id');
        $this->datagrid->addAction($action1);
        $this->datagrid->addAction($action2);
        $this->datagrid->createModel();

        $this->createPageNavigation();
    }

    public function onReload($param = NULL)
    {
        try {
            TTransaction::open('db');
            $repository = new TRepository('Tag');
            $limit = 10;
            $criteria = new TCriteria;
            
            if (empty($param['order'])) {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }

            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            $objects = $repository->load($criteria, FALSE);
            
            $this->datagrid->clear();
            
            if ($objects) {
            
                foreach ($objects as $object) {
                    $this->datagrid->addItem($object);
                }
            }

            $criteria->resetProperties();
            $count = $repository->count($criteria);
            
            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($limit); // limit
            TTransaction::close();
            $this->loaded = true;
        } catch (Exception $e)  {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    public static function onDelete($param)
    {
        $action = new TAction([__CLASS__, 'Delete']);
        $action->setParameters($param); // pass the key parameter ahead
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }

    public static function Delete($param)
    {
        try {
            $key = $param['key'];
            TTransaction::open('db');
            $object = new Tag($key, FALSE);
            $object->delete();
            TTransaction::close();
            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'), $pos_action);
        } catch (Exception $e)  {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    public function onSave( $param )
    {
        try {
            TTransaction::open('db');
            $this->form->validate();
            $data = $this->form->getData();
            $object = new Tag; 
            $object->fromArray( (array) $data);
            $object->store();
            $data->id = $object->id;
            $this->form->setData($data);
            TTransaction::close(); 
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
            $this->onReload();
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            $this->form->setData( $this->form->getData() );
            TTransaction::rollback();
        }
    }
    
    public function onClear( $param )
    {
        $this->form->clear(TRUE);
    }
    
    public function onEdit( $param )
    {
        try {
        
            if (isset($param['key'])) {
                $key = $param['key'];
                TTransaction::open('db');
                $object = new Tag($key);
                $this->form->setData($object);
                TTransaction::close();
            } else {
                $this->form->clear(TRUE);
            }

        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    public function show()
    {
        if (!$this->loaded AND (!isset($_GET['method']) OR $_GET['method'] !== 'onReload') ) {
            $this->onReload( func_get_arg(0) );
        }
        parent::show();
    }
}
