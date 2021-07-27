<?php

class ProductForm extends TPage
{
    protected $form; // form
    protected $detail_list;
    
    public function __construct()
    {
        parent::__construct();
        $this->form = new BootstrapFormBuilder('form_Product');
        $this->form->setFormTitle('Product');
        
        $id = new THidden('id');
        $title = new TEntry('title');
        $title->setEditable(false);
        $detail_uniqid = new THidden('detail_uniqid');
        $detail_id = new THidden('detail_id');
        $detail_name = new TEntry('detail_name');
        $detail_path = new TEntry('detail_path');
        $detail_title = new TEntry('detail_title');

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        $this->form->addFields( [new TLabel('')], [$id] );
        $this->form->addFields( [new TLabel('Title')], [$title] );

        $this->form->addContent( ['<h4>Images</h4><hr>'] );
        $this->form->addFields( [$detail_uniqid] );
        $this->form->addFields( [$detail_id] );
        
        $this->form->addFields( [new TLabel('Name')], [$detail_name] );
        $this->form->addFields( [new TLabel('Path')], [$detail_path] );
        $this->form->addFields( [new TLabel('Title')], [$detail_title] );

        $add = TButton::create('add', [$this, 'onDetailAdd'], 'Register', 'fa:plus-circle green');
        $add->getAction()->setParameter('static','1');
        $this->form->addFields( [], [$add] );
        
        $this->detail_list = new BootstrapDatagridWrapper(new TDataGrid);
        $this->detail_list->setId('Image_list');
        $this->detail_list->generateHiddenFields();
        $this->detail_list->style = "min-width: 700px; width:100%;margin-bottom: 10px";
        
        $this->detail_list->addColumn( new TDataGridColumn('uniqid', 'Uniqid', 'center') )->setVisibility(false);
        $this->detail_list->addColumn( new TDataGridColumn('id', 'Id', 'center') )->setVisibility(false);
        $this->detail_list->addColumn( new TDataGridColumn('name', 'Name', 'left', 100) );
        $this->detail_list->addColumn( new TDataGridColumn('path', 'Path', 'left', 100) );
        $this->detail_list->addColumn( new TDataGridColumn('title', 'Title', 'left', 100) );

        // detail actions
        $action1 = new TDataGridAction([$this, 'onDetailEdit'] );
        $action1->setFields( ['uniqid', '*'] );
        
        $action2 = new TDataGridAction([$this, 'onDetailDelete']);
        $action2->setField('uniqid');
        
        // add the actions to the datagrid
        $this->detail_list->addAction($action1, _t('Edit'), 'fa:edit blue');
        $this->detail_list->addAction($action2, _t('Delete'), 'far:trash-alt red');
        
        $this->detail_list->createModel();
        
        $panel = new TPanelGroup;
        $panel->add($this->detail_list);
        $panel->getBody()->style = 'overflow-x:auto';
        $this->form->addContent( [$panel] );
        
        $this->form->addAction( 'Save',  new TAction([$this, 'onSave'], ['static'=>'1']), 'fa:save green');
        $this->form->addAction( 'Clear', new TAction([$this, 'onClear']), 'fa:eraser red');
        $this->form->addAction( 'Products', new TAction(['ProductFormList', 'onReload']), 'fa:list green');
        
        // create the page container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        parent::add($container);
    }
    public function onClear($param)
    {
        $this->form->clear(TRUE);
    }
    
    public function onDetailAdd( $param )
    {
        try
        {
            $this->form->validate();
            $data = $this->form->getData();
            $uniqid = !empty($data->detail_uniqid) ? $data->detail_uniqid : uniqid();
            $grid_data = [];
            $grid_data['uniqid'] = $uniqid;
            $grid_data['id'] = $data->detail_id;
            $grid_data['name'] = $data->detail_name;
            $grid_data['path'] = $data->detail_path;
            $grid_data['title'] = $data->detail_title;
            
            $row = $this->detail_list->addItem( (object) $grid_data );
            $row->id = $uniqid;
            
            TDataGrid::replaceRowById('Image_list', $uniqid, $row);
            $data->detail_uniqid = '';
            $data->detail_id = '';
            $data->detail_name = '';
            $data->detail_path = '';
            $data->detail_title = '';
            TForm::sendData( 'form_Product', $data, false, false );
        }
        catch (Exception $e)
        {
            $this->form->setData( $this->form->getData());
            new TMessage('error', $e->getMessage());
        }
    }
    
    public static function onDetailEdit( $param )
    {
        $data = new stdClass;
        $data->detail_uniqid = $param['uniqid'];
        $data->detail_id = $param['id'];
        $data->detail_name = $param['name'];
        $data->detail_path = $param['path'];
        $data->detail_title = $param['title'];
        TForm::sendData( 'form_Product', $data, false, false );
    }
    

    public static function onDetailDelete( $param )
    {
        $data = new stdClass;
        $data->detail_uniqid = '';
        $data->detail_id = '';
        $data->detail_name = '';
        $data->detail_path = '';
        $data->detail_title = '';
        TForm::sendData( 'form_Product', $data, false, false );
        TDataGrid::removeRowById('Image_list', $param['uniqid']);
    }
    
    public function onEdit($param)
    {
        try
        {
            TTransaction::open('db');
            
            if (isset($param['key']))
            {
                $key = $param['key'];
                
                $object = new Product($key);
                $items  = Image::where('product_id', '=', $key)->load();
                
                foreach( $items as $item )
                {
                    $item->uniqid = uniqid();
                    $row = $this->detail_list->addItem( $item );
                    $row->id = $item->uniqid;
                }
                $this->form->setData($object);
                TTransaction::close();
            }
            else
            {
                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    public function onSave($param)
    {
        try
        {
            TTransaction::open('db');
            $data = $this->form->getData();
            $this->form->validate();
            
            $master = new Product;
            $master->fromArray( (array) $data);
            $master->store();
            
            Image::where('product_id', '=', $master->id)->delete();
            
            if( $param['Image_list_name'] )
            {
                foreach( $param['Image_list_name'] as $key => $item_id )
                {
                    $detail = new Image;
                    $detail->name  = $param['Image_list_name'][$key];
                    $detail->path  = $param['Image_list_path'][$key];
                    $detail->title  = $param['Image_list_title'][$key];
                    $detail->product_id = $master->id;
                    $detail->store();
                }
            }
            TTransaction::close(); // close the transaction
            
            TForm::sendData('form_Product', (object) ['id' => $master->id]);
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback();
        }
    }
}
