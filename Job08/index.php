<?php

// Connection to database
$host = 'localhost';
$dbname = 'draft-shop';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}



// Class Product :
class Product{
    // Properties :
    private int $id;
    private string $name;
    private array $photos;
    private int $price;
    private string $description;
    private int $quantity;
    private Datetime $createdAt;
    private Datetime $updatedAt;
    private int $category_id;
    private ?Category $category = null; // New propertie to store category instance (here Category is an class defined to represent an entity of a category with sverals properties and methods)


    // Constructor to initiate properties :
    public function __construct(int $id = 0, string $name = '', array $photos = [], int $price = 0, string $description = '', int $quantity = 0, Datetime $createdAt = null, Datetime $updatedAt = null, int $category_id = 0, ?Category $category = null){
        $this->id = $id;
        $this->name = $name;
        $this->photos = $photos;
        $this->price = $price;
        $this-> description = $description;
        $this->quantity = $quantity;
        $this->createdAt = $createdAt ?? new DateTime();
        $this->updatedAt = $updatedAt ?? new DateTime();
        $this->category_id = $category_id;
        $this->category = $category;
    } // Constructor closed


    // Getters :
    public function getId(): int{
        return $this->id;
    }

    public function getName(): string{
        return $this->name;
    }

    public function getPhotos(): array{
        return $this->photos;
    }

    public function getPrice(): int{
        return $this->price;
    }

    public function getDescription(): string{
        return $this->description;
    }

    public function getQuantity(): int{
        return $this->quantity;
    }

    public function getCreatedAt(): Datetime{
        return $this->createdAt;
    }

    public function getUpdatedAt(): Datetime{
        return $this->updatedAt;
    }

    public function getCategoryId(): int{
        return $this->category_id;
    }

    public function getCategory(): ?Category{ // get the category associated with the product
        if($this->category === null){
            global $pdo; // Use the global $pdo instance

            $sql = "SELECT * FROM category WHERE id = :category_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':category_id', $this->category_id, PDO::PARAM_INT);
            $stmt->execute();

            $categoryData = $stmt->fetch(PDO::FETCH_ASSOC);

            if($categoryData){
                $this->category = new Category(
                    $categoryData['id'],
                    $categoryData['name'],
                    $categoryData['description'],
                    new DateTime($categoryData['createdAt']),
                    new DateTime($categoryData['updatedAt'])
                );
            } else {
                $this->category = null; // No category found
            }
        }
        return $this->category;
    }



    // Setters : 
    public function setId(int $id): void{
        $this->id = $id;
    }

    public function setName(string $name): void{
        $this->name = $name;
    }

    public function setPhotos(array $photos): void{
        $this->photos = $photos;
    }

    public function setPrice(int $price): void{
        $this->price = $price;
    }

    public function setDescription(string $description): void{
        $this->description = $description;
    }

    public function setQuantity(int $quantity): void{
        $this->quantity = $quantity;
    }

    public function setCreatedAt(DateTime $createdAt): void{
        $this->createdAt = $createdAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): void{
        $this->updatedAt = $updatedAt;
    }

    public function setCategoryId(int $category_id){
        $this->category_id = $category_id;
    }

    // Method to find a product by ID
    public static function findOneById(int $id){
        global $pdo;

        $sql = "SELECT * FROM product WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $productData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($productData){
            return new Product(
                $productData['id'],
                $productData['name'],
                explode(',', $productData['photos']),
                (int)$productData['price'],
                $productData['description'],
                (int)$productData['quantity'],
                new DateTime($productData['createdAt']),
                new DateTime($productData['updatedAt']),
                (int)$productData['category_id']
            );
        } else{
            return false;
        }

    }

    public static function findAll(): array {
        global $pdo;

        $sql = "SELECT * FROM product";
        $stmt = $pdo->query($sql);

        $products = [];

        while ($productData = $stmt->fetch(PDO::FETCH_ASSOC)){
            $products[] = new Product(
                $productData['id'],
                $productData['name'],
                explode(',',$productData['photos']),
                (int)$productData['price'],
                $productData['description'],
                (int)$productData['quantity'],
                new DateTime($productData['createdAt']),
                new DateTime ($productData['updatedAt']),
                (int)$productData['category_id']
            );
        }
        return $products;
    }

} // Class Product closed




// Class Category :
class Category{
    private int $id;
    private string $name;
    private string $description;
    private DateTime $createdAt;
    private DateTime $updatedAt;


    // Constructor to initiate properties :
    public function __construct(int $id, string $name, string $description, DateTime $createdAt, DateTime $updatedAt){
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    // Getters :
    public function getId(): int{
        return $this->id;
    }

    public function getName(): string{
        return $this->name;
    }

    public function getDescription(): string{
        return $this->description;
    }

    public function getCreatedAt(): DateTime{
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime{
        return $this->updatedAt;
    }

    public function getProducts(): array {
        global $pdo;
        $products = [];

        $sql = "SELECT * FROM product WHERE category_id = :category_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':category_id', $this->id, PDO::PARAM_INT);
        $stmt->execute();

        // Fetching all product data
        $productDataList = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Loop through each product data and create a Product instance
        foreach ($productDataList as $productData){
            $product = new Product(
                $productData['id'],
                $productData['name'],
                explode(',', $productData['photos']),
                $productData['price'],
                $productData['description'],
                $productData['quantity'],
                new DateTime($productData['createdAt']),
                new DateTime($productData['updatedAt']),
                $productData['category_id']
            );
            $products[] = $product; // Add the product instance to the array
        }
        return $products; // Return the array of products or an empty arry if there is no product
    }





    // Setters :
    public function setId(int $id): void{
        $this->id = $id;
    }

    public function setName(string $name){
        $this->name = $name;
    }

    public function setDescription(string $description){
        $this->description = $description;
    }

    public function setCreatedAt(DateTime $createdAt){
        $this->createdAt = $createdAt;
    }

    public function setUpdatedAt(DateTime $updatedAt){
        $this->updatedAt = $updatedAt;
    }


} // Class Category closed



// Class category test
// Creating an instance of the category
$category = new Category(1, "Electronics", "Category for electronics products", new DateTime('now'), new DateTime('now'));

// Class Product test
// Creating an instance of the product
$product = new Product(1, "Laptop", ["photoLaptop.jpg"], 1500, "A high-performance laptop", 10, new DateTime('now'), new DateTime('now'), $category->getId());
$product2 = new Product();

// Use of getters
// $product
echo "Product 1:<br>";
var_dump($product->getId());echo '<br>';
var_dump($product->getName());echo '<br>';
var_dump($product->getPhotos());echo '<br>';
var_dump($product->getPrice());echo '<br>';
var_dump($product->getDescription());echo '<br>';
var_dump($product->getQuantity());echo '<br>';
var_dump($product->getCreatedAt());echo '<br>';
var_dump($product->getUpdatedAt());echo '<br>';
var_dump($product->getCategoryId());echo '<br><br>';

// Use of setters
// $product
$product->setPrice(1200);
$product->setQuantity(9);
$product->setCategoryId(2);

// Modifications checking :
// $product
echo "changes product1:<br>";
var_dump($product->getPrice());echo '<br>';
var_dump($product->getQuantity());echo '<br>';
var_dump($product->getCategoryId()); echo '<br><br><br>';

// $product2 :
echo "Product 2:<br>";
var_dump($product2->getId());echo '<br>';
var_dump($product2->getName());echo '<br>';
var_dump($product2->getPhotos());echo '<br>';
var_dump($product2->getPrice());echo '<br>';
var_dump($product2->getDescription());echo '<br>';
var_dump($product2->getQuantity());echo '<br>';
var_dump($product2->getCreatedAt());echo '<br>';
var_dump($product2->getUpdatedAt());echo '<br>';
var_dump($product2->getCategoryId());echo '<br><br>';

// Use of setters
// $product2
$product2->setName('PS5');
$product2->setPrice(40);
$product2->setQuantity(3);
$product2->setCategoryId(5);

// Modifications checking :
// $product
echo "changes product2:<br>";
var_dump($product2->getName());echo '<br>';
var_dump($product2->getPrice());echo '<br>';
var_dump($product2->getQuantity());echo '<br>';
var_dump($product2->getCategoryId()); echo '<br><br><br>';


// Request to retrieve the product with id 7
$id = 7;
$sql = "SELECT * FROM product WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();


// Retrieve product data in the form of an associative array
$productData = $stmt->fetch(PDO::FETCH_ASSOC);

// Hydrate a new instance of the Product class with data from the database
if ($productData) {
    $product = new Product(
        $productData['id'],
        $productData['name'],
        explode(',', $productData['photos']),
        (int)$productData['price'],
        $productData['description'],
        (int)$productData['quantity'],
        new DateTime($productData['createdAt']),
        new DateTime($productData['updatedAt']),
        (int)$productData['category_id']
    );

    // Retrieve and display category information
    $category = $product->getCategory();

    if ($category) {
        echo "Category ID: " . $category->getId() . "<br>";
        echo "Category Name: " . $category->getName() . "<br>";
        echo "Category Description: " . $category->getDescription() . "<br>";
        echo "Category Created At: " . $category->getCreatedAt()->format('Y-m-d H:i:s') . "<br>";
        echo "Category Updated At: " . $category->getUpdatedAt()->format('Y-m-d H:i:s') . "<br><br>";
    } else {
        echo "Category not found.<br>";
    }

    // Viewing product data to verify everything is working
    echo "Product ID : " . $product->getId() . "<br>";
    echo "Product name : " . $product->getName() . "<br>";
    echo "Product photos : " . implode(", ", $product->getPhotos()) . "<br>";
    echo "Product price : " . $product->getPrice() . "<br>";
    echo "Product description : " . $product->getDescription() . "<br>";
    echo "Product quantity : " . $product->getQuantity() . "<br>";
    echo "Product created at : " . $product->getCreatedAt()->format('Y-m-d H:i:s') . "<br>";
    echo "Product updated at : " . $product->getUpdatedAt()->format('Y-m-d H:i:s') . "<br>";
    echo "Product category ID : " . $product->getCategoryId() . "<br><br>";
} else {
    die("No product found with the id ".$id)."<br><br>";
}

$products = $category->getProducts();

if (empty($product)) {
    echo "No product found for this category.<br>";
} else {
    foreach ($products as $product) {
        echo "Product name : " . $product->getName() . "<br>";
        echo "Product price : " . $product->getPrice() . "<br><br>";
    }
}

$product = Product::findOneById(6);

if($product){
    echo "Function findOneById:<br>";
    echo "Product name : ". $product->getName()."<br>";
    echo "Product photos : " . implode(", ", $product->getPhotos()) . "<br>";
    echo "Product price : ". $product->getPrice()."<br>";
    echo "Product description : ". $product->getdescription()."<br>";
    echo "Product quantity : ". $product->getQuantity()."<br>";
    echo "Product created at : " . $product->getCreatedAt()->format('Y-m-d H:i:s') . "<br>";
    echo "Product updated at : " . $product->getUpdatedAt()->format('Y-m-d H:i:s') . "<br>";
    echo "Product category ID : ". $product->getCategoryId()."<br><br>";
} else {
    echo "No product found with this ID.";
}

$allProducts = Product::findAll();

if (!empty($allProducts)){
    foreach ($allProducts as $product){
        echo "Function findAll :<br>";
        echo "Product ID : " . $product->getId() . "<br>";
        echo "Product name : " . $product->getName() . "<br>";
        echo "Product photo : " . implode(",", $product->getPhotos())."<br>";
        echo "Product price : " . $product->getPrice() ."<br>";
        echo "Product description : " . $product->getDescription() . "<br>";
        echo "Production quantity : " . $product->getQuantity() . "<br>";
        echo "Product created at : " . $product->getCreatedAt()->format('Y-m-d H:i:s') . "<br>";
        echo "Product updated at : " . $product->getUpdatedAt()->format('Y-m-d H:i:s') . "<br>";
        echo "Product category ID : " . $product->getCategoryId()."<br><br>";
    }
}

?>