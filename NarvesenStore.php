<?php

function displayProducts($products): void
{
    $groupedProducts = [];
    foreach ($products as $product) {
        $category = $product->category;
        if (!isset($groupedProducts[$category])) {
            $groupedProducts[$category] = [];
        }
        $groupedProducts[$category][] = $product;
    }

    echo "Available products:\n";
    $index = 1;
    foreach ($groupedProducts as $category => $products) {
        echo "$category: \n";
        foreach ($products as $product) {
            echo $index . " " . $product->name . ", EUR " . number_format($product->price / 100, 2) . "\n";
            $index++;
        }
    }
}

$products = json_decode(file_get_contents('products.json'));
$cart = [];

echo "Welcome to our store!\n";

while (true) {

    displayProducts($products);

    $choice = readline("Enter the number of the product you want to select (type 'stop' to finish): ");
    if ($choice === 'stop') {
        if (empty($cart)) {
            echo "Your cart is empty. Do you want to continue shopping?\n";
            $continueShopping = readline("Enter 'yes' to continue or 'no' to finish: ");
            if ($continueShopping === 'yes') {
                continue;
            } else {
                echo "Looking forward to meet you again ir our shop!\n";
                break;
            }
        } else {
            echo "Selection done.\n";
            break;
        }
    } elseif (!is_numeric($choice) || $choice < 1 || $choice > count($products)) {
        echo "Invalid choice. Please enter a valid number or 'stop' to finish.\n";
    } else {
        $product = $products[$choice - 1];
        echo "Enter the amount of " . $product->name . ": ";
        $amount = intval(readline());
        if ($amount <= 0) {
            echo "Invalid amount. Please enter a positive number.\n";
        } else {
            $item = new stdClass();
            $item -> product = $product;
            $item -> amount = $amount;
            $cart [] = $item;
            $totalProductPrice = number_format($product->price / 100 * $amount, 2);
            echo "Selected: " . $product->name . ", Amount: " . $amount . ", Total Price: EUR " . $totalProductPrice . "\n";
        }
    }
}

echo "Your shopping cart contains the following items:\n";
$totalPrice = 0;
foreach ($cart as $item) {
    $totalPrice += $item->product->price / 100 * $item->amount;
    echo "- " . $item->product->name . ", " . $item->amount . " pcs - EUR " .
        number_format($item->product->price / 100 * $item->amount, 2) . "\n";
}
$totalItems = array_sum(array_column($cart, 'amount'));
echo "Total items:" . $totalItems . "\n";
echo "Total price: EUR " . number_format($totalPrice, 2) . "\n";

echo "Do you want to proceed with the purchase? (yes/no): ";
    $choice = strtolower(readline());
    if ($choice === 'yes') {
        echo "Thank you for your purchase! Looking forward to see you again soon!\n";
    } else {
        echo "Purchase has been canceled.\n";
    }