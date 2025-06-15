<?php

namespace App\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    protected $product;

    public function __construct($pdo)
    {
        parent::__construct($pdo);
        $this->product = new Product($pdo);
    }

    public function index()
    {
        $products = $this->product->all();
        return $this->renderView('products/index', compact('products'));
    }

    public function cart()
    {
        return $this->renderView('products/cart');
    }
}
