# Laravel Cart

Laracart is a cart package designed for Laravel e-commerce projects. This package provides powerful helpers to manage user carts in your e-commerce application.

## How to Use:

1. Make sure you have the required migrations for `Product` and `User`.

2. Install the package using Composer:

   ```bash
   composer require abdulrhmanak213/laracart

   use Abdulrhmanak213\Laracart\Cart;

// Create a new cart instance
$cart = new Cart();

// Show the cart for a specific user (by ID or session ID)
$cart->show($id);

// Add a new product to the cart
$cart->addProduct($product_id, $id, $quantity, $note);

// Remove a product from the cart
$cart->removeProduct($product_id, $id);

// Decrease the quantity of a product in the cart
$cart->decreaseProduct($product_id, $id);
