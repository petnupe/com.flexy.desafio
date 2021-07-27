<?php
/**
 * Product Active Record
 * @author  <your-name-here>
 */
class Product extends TRecord
{
    const TABLENAME = 'product';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    private $images;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('title');
        parent::addAttribute('price');
        parent::addAttribute('description');
        parent::addAttribute('stock');
    }

    
    /**
     * Method addImage
     * Add a Image to the Product
     * @param $object Instance of Image
     */
    public function addImage(Image $object)
    {
        $this->images[] = $object;
    }
    
    /**
     * Method getImages
     * Return the Product' Image's
     * @return Collection of Image
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Reset aggregates
     */
    public function clearParts()
    {
        $this->images = array();
    }

    /**
     * Load the object and its aggregates
     * @param $id object ID
     */
    public function load($id)
    {
        $this->images = parent::loadComposite('Image', 'product_id', $id);
    
        // load the object itself
        return parent::load($id);
    }

    /**
     * Store the object and its aggregates
     */
    public function store()
    {
        // store the object itself
        parent::store();
    
        parent::saveComposite('Image', 'product_id', $this->id, $this->images);
    }

    /**
     * Delete the object and its aggregates
     * @param $id object ID
     */
    public function delete($id = NULL)
    {
        $id = isset($id) ? $id : $this->id;
        parent::deleteComposite('Image', 'product_id', $id);
    
        // delete the object itself
        parent::delete($id);
    }


}
