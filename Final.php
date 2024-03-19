<?php
header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'OPTIONS') {
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
        header('Access-Control-Allow-Headers: Origin, Accept, Content-Type, X-Requested-With');
        exit();
    }
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
	
	public function create($quote, $author_id, $category_id) {
        $query = 'INSERT INTO ' . $this->table . ' (quote, author_id, category_id) VALUES (:quote, :author_id, :category_id)';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quote', $quote);
        $stmt->bindParam(':author_id', $author_id);
        $stmt->bindParam(':category_id', $category_id);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
	
	public function update($id, $quote, $author_id, $category_id) {
        $query = 'UPDATE ' . $this->table . ' 
                  SET quote = :quote, author_id = :author_id, category_id = :category_id
                  WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quote', $quote);
        $stmt->bindParam(':author_id', $author_id);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
	
	public function delete($id) {
        $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
				return $id;
			} else {
				return false;
			}
        } else {
            return false;
        }
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
	
	public function create($author) {
        $query = 'INSERT INTO ' . $this->table . ' (author) VALUES (:author)';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':author', $author);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
	
	public function update($id, $author) {
        $query = 'UPDATE ' . $this->table . ' 
                  SET author = :author
                  WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':author', $author);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
	
	public function delete($id) {
        $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
				return $id;
			} else {
				return false;
			}
        }else {
            return false;
        }
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
	
	public function create($category) {
        $query = 'INSERT INTO ' . $this->table . ' (category) VALUES (:category)';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category', $category);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
	
	public function update($id, $category) {
        $query = 'UPDATE ' . $this->table . ' 
                  SET category = :category
                  WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
	
	public function delete($id) {
        $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
				return $id;
			} else {
				return false;
			}
        } else {
            return false;
        }
    }
}

$database = new Database();
$db = $database->connect();
$author = new Author($db); 
$category = new Category($db);


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
                    $response['message'] = 'author_id Not Found';
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
                    $response['message'] = 'category_id Not Found';
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
}

elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $content_type = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
    
    if ($content_type === 'application/json') {
        $json_data = file_get_contents('php://input');

        $data = json_decode($json_data, true);		
		$REQUEST_URI = $_SERVER['REQUEST_URI'];				
		$url_path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');		
		$file_name = dirname($url_path);		
		$request_path = str_replace('/'.$file_name, '', $_SERVER['REQUEST_URI']);

        switch ($request_path) {
            case '/quotes/':
                if (isset($data['quote']) && isset($data['author_id']) && isset($data['category_id'])) {
                    
                    if ($author->getById($data['author_id'])->rowCount() == 0) {
                        http_response_code(404);
                        $response['message'] = 'author_id Not Found';
                    } elseif ($category->getById($data['category_id'])->rowCount() == 0) {
                        http_response_code(404);
                        $response['message'] = 'category_id Not Found';
                    } else {                    
                        if ($quote->create($data['quote'], $data['author_id'], $data['category_id'])) {
                            http_response_code(201);
                            $response['quote'] = array(
                                'id' => $db->lastInsertId(),
                                'quote' => $data['quote'],
                                'author_id' => $data['author_id'],
                                'category_id' => $data['category_id']
                            );
                        } else {
                            http_response_code(500);
                            $response['message'] = 'Failed to create quote';
                        }
                    }
                } else {
                    http_response_code(400);
                    $response['message'] = 'Missing Required Parameters';
                }
                break;

            case '/authors/':
                if (isset($data['author'])) {
                    if ($author->create($data['author'])) {
                        http_response_code(201);
                        $response['author'] = array(
                            'id' => $db->lastInsertId(),
                            'author' => $data['author']
                        );
                    } else {
                        http_response_code(500);
                        $response['message'] = 'Failed to create author';
                    }
                } else {
                    http_response_code(400);
                    $response['message'] = 'Missing Required Parameters';
                }
                break;

            case '/categories/':
                if (isset($data['category'])) {
                    if ($category->create($data['category'])) {
                        http_response_code(201);
                        $response['category'] = array(
                            'id' => $db->lastInsertId(),
                            'category' => $data['category']
                        );
                    } else {
                        http_response_code(500);
                        $response['message'] = 'Failed to create category';
                    }
                } else {
                    http_response_code(400);
                    $response['message'] = 'Missing Required Parameters';
                }
                break;

            default:
                http_response_code(404);
                $response['message'] = 'Endpoint Not Found';
                break;
        }
    } else {
        http_response_code(415);
        $response['message'] = 'Unsupported Media Type';
    }
}elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $content_type = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

    if ($content_type === 'application/json') {
        $json_data = file_get_contents('php://input');
        $data = json_decode($json_data, true);      
        $REQUEST_URI = $_SERVER['REQUEST_URI'];                
        $url_path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');      
        $file_name = dirname($url_path);        
        $request_path = str_replace('/'.$file_name, '', $_SERVER['REQUEST_URI']);

        switch ($request_path) {
            case '/quotes/':
                if (isset($data['id'], $data['quote'], $data['author_id'], $data['category_id'])) {
                    $stmt = $quote->getById($data['id']);
                    if ($stmt->rowCount() == 0) {
                        http_response_code(404);
                        $response['message'] = 'No Quotes Found';
                    } else {
                        if ($author->getById($data['author_id'])->rowCount() == 0) {
                            http_response_code(404);
                            $response['message'] = 'author_id Not Found';
                        } elseif ($category->getById($data['category_id'])->rowCount() == 0) {
                            http_response_code(404);
                            $response['message'] = 'category_id Not Found';
                        } else {
                            if ($quote->update($data['id'], $data['quote'], $data['author_id'], $data['category_id'])) {
                                http_response_code(200);
                                $response['quote'] = array(
                                    'id' => $data['id'],
                                    'quote' => $data['quote'],
                                    'author_id' => $data['author_id'],
                                    'category_id' => $data['category_id']
                                );
                            } else {
                                http_response_code(500);
                                $response['message'] = 'Failed to update quote';
                            }
                        }
                    }
                } else {
                    http_response_code(400);
                    $response['message'] = 'Missing Required Parameters';
                }
                break;

            case '/authors/':
                if (isset($data['id'], $data['author'])) {
                    $stmt = $author->getById($data['id']);
                    if ($stmt->rowCount() == 0) {
                        http_response_code(404);
                        $response['message'] = 'author_id Not Found';
                    } else {
                        if ($author->update($data['id'], $data['author'])) {
                            http_response_code(200);
                            $response['author'] = array(
                                'id' => $data['id'],
                                'author' => $data['author']
                            );
                        } else {
                            http_response_code(500);
                            $response['message'] = 'Failed to update author';
                        }
                    }
                } else {
                    http_response_code(400);
                    $response['message'] = 'Missing Required Parameters';
                }
                break;

            case '/categories/':
                if (isset($data['id'], $data['category'])) {
                    $stmt = $category->getById($data['id']);
                    if ($stmt->rowCount() == 0) {
                        http_response_code(404);
                        $response['message'] = 'category_id Not Found';
                    } else {
                        if ($category->update($data['id'], $data['category'])) {
                            http_response_code(200);
                            $response['category'] = array(
                                'id' => $data['id'],
                                'category' => $data['category']
                            );
                        } else {
                            http_response_code(500);
                            $response['message'] = 'Failed to update category';
                        }
                    }
                } else {
                    http_response_code(400);
                    $response['message'] = 'Missing Required Parameters';
                }
                break;

            default:
                http_response_code(404);
                $response['message'] = 'Endpoint Not Found';
                break;
        }
    } else {
        http_response_code(415);
        $response['message'] = 'Unsupported Media Type';
    }
}elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $url_path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $path_segments = explode('/', $url_path);
    $endpoint = $path_segments[2];
    if (isset($path_segments[3])) {
		$id = $path_segments[3];
    
	}
	
    switch ($endpoint) {
        case 'quotes':
            $deleted_id = $quote->delete($id);
            if ($deleted_id) {
                $response['id'] = $deleted_id;
            } else {
                http_response_code(404);
                $response['message'] = 'No Quotes Found';
            }
            break;

        case 'authors':
            $deleted_id = $author->delete($id);
            if ($deleted_id) {
                $response['id'] = $deleted_id;
            } else {
                http_response_code(404);
                $response['message'] = 'author_id Not Found';
            }
            break;

        case 'categories':
            $deleted_id = $category->delete($id);
            if ($deleted_id) {
                $response['id'] = $deleted_id;
            } else {
                http_response_code(404);
                $response['message'] = 'category_id Not Found';
            }
            break;

        default:
            http_response_code(404);
            $response['message'] = 'Endpoint Not Found';
            break;
    }
}


 else {
    http_response_code(405);
    $response['message'] = 'Method Not Allowed';
}

header('Content-Type: application/json');
echo json_encode($response);

?>
