<?php

namespace App\Router;

use App\Controllers\ProductController;
use App\Controllers\CategoryController;
use App\Controllers\TagController;

class Router
{
    private $productController;
    private $categoryController;
    private $tagController;

    public function __construct(
        ProductController $productController,
        CategoryController $categoryController,
        TagController $tagController
    ) {
        $this->productController = $productController;
        $this->categoryController = $categoryController;
        $this->tagController = $tagController;
    }

    public function handleRequest($requestUri, $requestMethod)
    {
        // $requestUri = strtok($requestUri, '?');
        header('Content-Type: application/json');

        $parsedUrl = parse_url($requestUri);
        $path = $parsedUrl['path'];
        $query = $parsedUrl['query'] ?? null;

        try {
            if (preg_match('/^\/products(\/\d+)?$/', $path, $matches)) {
                $param = isset($matches[1]) ? (int) str_replace('/', '', $matches[1]) : null;
                $sort = null;
                if ($query) {
                    parse_str($query, $queryParams);
                    $sort = $queryParams['sort'] ?? null;
                }
                $this->route($requestMethod, $this->productController, $param, $sort);
            } elseif (preg_match('/^\/products\/(\d+)\/tags$/', $path, $matches) && $requestMethod === 'GET') {
                $productId = $matches[1];
                $this->productController->listTags($productId);
            } elseif (preg_match('/^\/products\/(\d+)\/tags\/(\d+)$/', $path, $matches) && $requestMethod === 'POST') {
                $productId = $matches[1];
                $tagId = $matches[2];
                $this->productController->addTag($productId, $tagId);
            } elseif (preg_match('/^\/categories(\/\d+)?$/', $path, $matches)) {
                $param = isset($matches[1]) ? (int) str_replace('/', '', $matches[1]) : null;
                $this->route($requestMethod, $this->categoryController, $param);
            } elseif (preg_match('/^\/categories\/(\d+)\/products$/', $path, $matches) && $requestMethod === 'GET') {
                $categoryId = $matches[1];
                $this->categoryController->listProducts($categoryId);
            } elseif (preg_match('/^\/tags(\/\d+)?$/', $path, $matches)) {
                $param = isset($matches[1]) ? (int) str_replace('/', '', $matches[1]) : null;
                $this->route($requestMethod, $this->tagController, $param);
            } else {
                $this->sendError('Route not found.', 404);
            }
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), $e->getCode());
        }
    }

    private function route($method, $controller, $param = null, $sort = null)
    {
        switch ($method) {
            case 'GET':
                if ($param) {
                    $controller->show($param);
                } else {
                    $controller->index($sort);
                }
                break;
            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $this->sendError('Invalid JSON.', 400);
                }
                $controller->store($data);
                break;
            case 'PUT':
                $data = json_decode(file_get_contents('php://input'), true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $this->sendError('Invalid JSON.', 400);
                }
                $controller->update($param, $data, false);
                break;
            case 'PATCH':
                $data = json_decode(file_get_contents('php://input'), true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $this->sendError('Invalid JSON.', 400);
                }
                $controller->update($param, $data, true);
                break;
            case 'DELETE':
                $controller->destroy($param);
                break;
            default:
                $this->sendError('Method not allowed.', 405);
                break;
        }
    }

    private function sendError($message, $statusCode = 400)
    {
        http_response_code($statusCode);
        echo json_encode(['message' => $message]);
        exit;
    }
}