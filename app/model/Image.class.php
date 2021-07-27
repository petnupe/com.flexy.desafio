<?php
/**
 * Image Active Record
 * @author  <your-name-here>
 */
class Image extends TRecord
{
    const TABLENAME = 'image';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('name');
        parent::addAttribute('path');
        parent::addAttribute('title');
        parent::addAttribute('product_id');
    }


}
