<?php
class Product{
    public int $id;
    public string $name;
    public array $photos;
    public int $price;
    public string $description;
    public int $quantity;
    public DateTime $createdAt;
    public UpdateDate $updatedAt;


    public function __construct($id = 0, $name = "", $photos = "", $price = 0, $description = "", $quantity = 0, $createdAt = "", $updatedAt = "") 
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->description = $description;
        $this->quantity = $quantity;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }


    
    public function GetId($id)
    {
        return $this->id;
    }
    
    public function Getname($name)
    {
        return $this->name;
    }
    
    public function Getprice($price)
    {
        return $this->price;
    }
    
    public function Getdescription($description)
    {
        return $this->description;
    }
    
    public function Getquantity($quantity)
    {
        return $this->quantity;
    }

    
    public function GetupdatedAt($updatedAt)
    {
        return $this->updatedAt;
    }

    
    public function GetcreatedAt($createdAt)
    {
        return $this->createdAt;
    }

    public function Getphotos($photos)
    {
        return $this->photos;
    }
}

?>