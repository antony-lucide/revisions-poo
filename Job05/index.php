<?php
class Category {
    public int $id;
    public string $name;
    public string $description;
    public string $category_id;
    public DateTime $createdAt;
    public DateTime $updatedAt;

    public function __construct($id = 0, $name = "", $photos = [], $price = 0, $description = "", $quantity = 0, $createdAt = null, $updatedAt = null) 
    {
        $this->id = $id;
        $this->name = $name;
        $this->photos = $photos;
        $this->price = $price;
        $this->description = $description;
        $this->quantity = $quantity;
        $this->createdAt = $createdAt ?? new DateTime();
        $this->updatedAt = $updatedAt ?? new DateTime();
        $this->category_id = $category_id;
    }

    public static function request($id) : ?Category
    {
        $host = 'localhost';
        $db = 'draft-shop';
        $user = 'root';
        $pass = '';
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

        try {
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, 
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);

            $stmt = $pdo->prepare('SELECT * FROM product WHERE id = :id');
            $stmt->execute(['id' => $id]);

            $productData = $stmt->fetch();

            if ($productData) {
                return new Product(
                    $productData['id'],
                    $productData['name'],
                    $productData['description'],
                    new DateTime($productData['createdAt']),
                    new DateTime($productData['updatedAt']),
                    $productData['category_id'];
                );
            } else {
                return null;
            }
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }
    }

    public function getCategory(): ?Category
    {
        return Category::request($this->categoryId);
    }


    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }


    public function getDescription(): string
    {
        return $this->description;
    }


    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }
}

// Fetch the product from the database
$product = Category::request(1);

if ($product !== null) {
    echo "Product ID : " . $product->getId() . "<br>";
    echo "Product name : " . $product->getName() . "<br>";
    echo "Product photos : " . implode(", ", $product->getPhotos()) . "<br>";
    echo "Product description : " . $product->getDescription() . "<br>";
    echo "Product created at : " . $product->getCreatedAt()->format('Y-m-d H:i:s') . "<br>";
    echo "Product updated at : " . $product->getUpdatedAt()->format('Y-m-d H:i:s') . "<br>";
} else {
    echo "Product not found.";
}
?>
