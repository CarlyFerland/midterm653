<?php

class Database {
    private $host = 'localhost';
    private $db_name = 'quotesdb';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function connect() {
        $this->conn = null;

        try {
            $this->conn = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo 'Connection Error: ' . $e->getMessage();
        }

        return $this->conn;
    }
}

class Quote {
    private $conn;
    private $table = 'quotes';

    
    public function __construct($db) {
        $this->conn = $db;
    }

    
    public function getAll() {
        $query = 'SELECT q.id, q.quote, a.author, c.category
                  FROM ' . $this->table . ' q
                  LEFT JOIN authors a ON q.author_id = a.id
                  LEFT JOIN categories c ON q.category_id = c.id';
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    
    public function getById($id) {
        $query = 'SELECT q.id, q.quote, a.author, c.category
                  FROM ' . $this->table . ' q
                  LEFT JOIN authors a ON q.author_id = a.id
                  LEFT JOIN categories c ON q.category_id = c.id
                  WHERE q.id = ?';
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        return $stmt;
    }

    
    public function getByAuthorId($author_id) {
        $query = 'SELECT q.id, q.quote, a.author, c.category
                  FROM ' . $this->table . ' q
                  LEFT JOIN authors a ON q.author_id = a.id
                  LEFT JOIN categories c ON q.category_id = c.id
                  WHERE q.author_id = ?';
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $author_id);
        $stmt->execute();

        return $stmt;
    }

    
    public function getByCategoryId($category_id) {
        $query = 'SELECT q.id, q.quote, a.author, c.category
                  FROM ' . $this->table . ' q
                  LEFT JOIN authors a ON q.author_id = a.id
                  LEFT JOIN categories c ON q.category_id = c.id
                  WHERE q.category_id = ?';
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $category_id);
        $stmt->execute();

        return $stmt;
    }

    
    public function getByAuthorAndCategory($author_id, $category_id) {
        $query = 'SELECT q.id, q.quote, a.author, c.category
                  FROM ' . $this->table . ' q
                  LEFT JOIN authors a ON q.author_id = a.id
                  LEFT JOIN categories c ON q.category_id = c.id
                  WHERE q.author_id = ? AND q.category_id = ?';
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $author_id);
        $stmt->bindParam(2, $category_id);
        $stmt->execute();

        return $stmt;
    }
}

class Author {
    private $conn;
    private $table = 'authors';

    
    public function __construct($db) {
        $this->conn = $db;
    }

    
    public function getAll() {
        $query = 'SELECT id, author FROM ' . $this->table;
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    
    public function getById($id) {
        $query = 'SELECT id, author FROM ' . $this->table . ' WHERE id = ?';
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        return $stmt;
    }
}

class Category {
    private $conn;
    private $table = 'categories';

    
    public function __construct($db) {
        $this->conn = $db;
    }

    
    public function getAll() {
        $query = 'SELECT id, category FROM ' . $this->table;
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    
    public function getById($id) {
        $query = 'SELECT id, category FROM ' . $this->table . ' WHERE id = ?';
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        return $stmt;
    }
}


$database = new Database();
$db = $database->connect();


$quote = new Quote($db);
$url_params = $_GET;
$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    
	$url_path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

	$file_name = dirname($url_path);
	
	$REQUEST_URI = $_SERVER['REQUEST_URI'];

	$request_path = str_replace('/'.$file_name, '', $_SERVER['REQUEST_URI']);
	
	$request_path = str_replace('?'.$_SERVER['QUERY_STRING'], '', $request_path);
	
	parse_str($_SERVER['QUERY_STRING'], $url_params);

    switch ($request_path) {
        case '/quotes/':
            if (isset($url_params['author_id']) && isset($url_params['category_id'])) {
                $stmt = $quote->getByAuthorAndCategory($url_params['author_id'], $url_params['category_id']);
            } elseif (isset($url_params['author_id'])) {
                $stmt = $quote->getByAuthorId($url_params['author_id']);
            } elseif (isset($url_params['category_id'])) {
                $stmt = $quote->getByCategoryId($url_params['category_id']);
            } elseif (isset($url_params['id'])) {
                $stmt = $quote->getById($url_params['id']);
            } else {
                $stmt = $quote->getAll();
            }
            
            if ($stmt->rowCount() > 0) {
                $response['quotes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $response['message'] = 'No Quotes Found';
            }
            break;

        case '/authors/':
            if (isset($url_params['id'])) {
                $author = new Author($db);
                $stmt = $author->getById($url_params['id']);
                if ($stmt->rowCount() > 0) {
                    $response['authors'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } else {
                    $response['message'] = 'Author Not Found';
                }
            } else {
                $author = new Author($db);
                $stmt = $author->getAll();
                $response['authors'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            break;

        case '/categories/':
            if (isset($url_params['id'])) {
                $category = new Category($db);
                $stmt = $category->getById($url_params['id']);
                if ($stmt->rowCount() > 0) {
                    $response['categories'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } else {
                    $response['message'] = 'Category Not Found';
                }
            } else {
                $category = new Category($db);
                $stmt = $category->getAll();
                $response['categories'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            break;

        default:
            http_response_code(404);
            $response['message'] = 'Endpoint Not Found';
            break;
    }
} else {
    http_response_code(405);
    $response['message'] = 'Method Not Allowed';
}

header('Content-Type: application/json');
echo json_encode($response);

?>
