<?php
/**
 * Tag Active Record
 * @author  <your-name-here>
 */
class Tag extends TRecord
{
    const TABLENAME = 'tag';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('name');
    }


}
