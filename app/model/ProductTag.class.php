<?php

class ProductTag extends TRecord
{
    const TABLENAME = 'product_tag';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial';

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('product_id');
        parent::addAttribute('tag_id');
    }
}