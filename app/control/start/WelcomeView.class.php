<?php

class WelcomeView extends TPage
{
    private $html;

    function __construct()
    {
        parent::__construct();
     
        $data = $this->getData();
        $html = new THtmlRenderer('app/resources/google_pie_chart.html');
        $html->enableSection('main', array('data'   => json_encode($data),
                                           'width'  => '100%',
                                           'height'  => '400px',
                                           'title'  => 'MOST TAGS PRODUCTS',
                                           'ytitle' => 'Accesses', 
                                           'xtitle' => 'Day',
                                           'uniqid' => uniqid()));
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($html);
        parent::add($container);
    }
    
    private function getData() : array
    {
        try { 
            TTransaction::open('db'); // open transaction
            $repoProductTag = new TRepository('ProductTag');
            $total = $repoProductTag->count();
            $conn = TTransaction::get(); // get PDO connection
            $result = $conn->query('select count(tag_id) as qtd, tag_id from product_tag group by tag_id');
            $data = array();
            
            $data[] = [ 'tag_id', 'qtd' ];
            foreach ($result as $row) {
                $Tag = new Tag($row['tag_id']);
                $data[] = [$Tag->name, (integer)$row['qtd']];
            } 
            
            TTransaction::close(); // close transaction 
        } catch (Exception $e) { 
            new TMessage('error', $e->getMessage()); 
        }
        return $data;         
    }
}