<?php
namespace App\Http\Controllers;

class POSController extends Controller
{
    public function index()
    {
        return view('pos.index');
    }
    
    public function addItem() { /* Logique POS */ }
    public function removeItem() { /* Logique POS */ }
    public function updateQuantity() { /* Logique POS */ }
    public function processPayment() { /* Logique POS */ }
    public function searchProducts() { /* Logique POS */ }
}