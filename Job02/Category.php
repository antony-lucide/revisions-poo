<?php
class Category{
    public int $id;
    public string $name;
    public string $description;
    public DateTime $createdAt;
    public DateTime $updatedAt;
    public int $Category_id;



    public function __construct($id, $name, $description, $createdAt, $updatedAt, $Category_id;) 
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->Category_id = $Category_id;
    }

    
    public function GetId($id)
    {
        return $this->id;
    }

    
    public function Getname($name)
    {
        return $this->name;
    }

    
    public function Getdescription($description)
    {
        return $this->description;
    }

    
    public function GetcreatedAt($createdAt)
    {
        return $this->createdAt;
    }

    
    public function GetupdatedAt($updatedAt)
    {
        return $this->updatedAt;
    }

    
    public function Getcategory_id($category_id)
    {
        return $this->category_id;
    }

}

?>