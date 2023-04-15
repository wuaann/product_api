<?php
namespace Src\Controller;

use Src\TableGateways\ProductGateway;

class ProductController {

    private $db;
    private $requestMethod;
    private $id;

    private $personGateway;

    public function __construct($db, $requestMethod, $id)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->id = $id;

        $this->producGateway = new ProductGateway($db);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->id) {
                    $response = $this->getProduct($this->id);
                } else {
                    $response = $this->getAllProduct();
                };
                break;
            case 'POST':
                $response = $this->createProduct();
                break;
            case 'PUT':
                $response = $this->updateUserFromRequest($this->id);
                break;
            case 'DELETE':
                $response = $this->deleteUser($this->id);
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    private function getAllProduct()
    {
        $result = $this->producGateway->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getProduct($id)
    {
        $result = $this->producGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createProduct()
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validatePerson($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->producGateway->insert($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = null;
        return $response;
    }

}
