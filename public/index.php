<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Router\Router;

use App\Controllers\ProductController;
use App\Services\ProductService;
use App\Repositories\ProductRepository;

use App\Controllers\CategoryController;
use App\Services\CategoryService;
use App\Repositories\CategoryRepository;

use App\Controllers\TagController;
use App\Services\TagService;
use App\Repositories\TagRepository;

$categoryRepository = new CategoryRepository();
$categoryService = new CategoryService($categoryRepository);
$categoryController = new CategoryController($categoryService);

$tagRepository = new TagRepository();
$tagService = new TagService($tagRepository);
$tagController = new TagController($tagService);

$productRepository = new ProductRepository($categoryService);
$productService = new ProductService($productRepository, $categoryRepository, $tagRepository);
$productController = new ProductController($productService, $categoryService);

$router = new Router($productController, $categoryController, $tagController);
$router->handleRequest($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
