<?php
$products = json_decode(file_get_contents('products.json'), true);

function selectProduct($products): array
{
    $selectedProducts = [];

    echo "Welcome to our store!\nAvailable products:\n";
    $index = 1;
    foreach ($products['products'] as $productType => $productList) {
        echo ($productType) . ":\n";
        foreach ($productList as $product) {
            echo $index . " " . $product['name'] . ", $" . number_format($product['price'] / 100, 2) . "\n";
            $index++;
        }
    }

    while (true) {
        echo "Enter the number of the product you want to select (type 'stop' to finish): ";
        $choice = readline();

        if ($choice === 'stop') {
            echo "Selection done.\n";
            break; // Exit the loop and function
        }

        $choice = intval($choice);
        if (!is_numeric($choice) || $choice < 1 || $choice > $index - 1) {
            echo "Invalid choice. Please enter a valid number or 'stop' to finish.\n";
            continue; // Continue the loop to prompt user again
        }

        $selectedProduct = null;
        $currentIndex = 1;
        foreach ($products['products'] as $productList) {
            foreach ($productList as $product) {
                if ($currentIndex == $choice) {
                    $selectedProduct = $product;
                    break 2; // Exit both loops
                }
                $currentIndex++;
            }
        }

        if (!empty($selectedProduct)) {
            while (true) {
                echo "Enter the amount of " . $selectedProduct['name'] . ": ";
                $amount = intval(readline());

                if ($amount <= 0) {
                    echo "Invalid amount. Please enter a positive number.\n";
                    continue; // Continue the loop to prompt user again
                } else {
                    $totalPrice = number_format($selectedProduct['price'] / 100 * $amount, 2);
                    echo "Selected: " . $selectedProduct['name'] . ", Amount: " . $amount . ", Total Price: $" . $totalPrice . "\n";
                    $selectedProducts[] = ['name' => $selectedProduct['name'], 'price' => $selectedProduct['price'], 'amount' => $amount];
                    break; // Exit the loop to proceed to the next product selection
                }
            }
        } else {
            echo "Invalid choice. Please enter a valid number or 'stop' to finish.\n";
        }
    }

    return $selectedProducts;
}

$selectedProducts = selectProduct($products);

echo "Selected products:\n";
$totalPrice = 0;
$totalItems = 0;
foreach ($selectedProducts as $selectedProduct) {
    $totalPrice += $selectedProduct['price'] / 100 * $selectedProduct['amount'];
    $totalItems += $selectedProduct['amount'];
    echo "- " . $selectedProduct['name'] . ", " . $selectedProduct['amount'] . " pcs - $" .
        number_format($selectedProduct['price'] / 100 * $selectedProduct['amount'], 2) . "\n";
}
echo "Total items:" . $totalItems . "\n";
echo "Total price: $" . number_format($totalPrice, 2) . "\n";


function confirmPurchase($selectedProducts): bool {
    echo "Your shopping cart contains the following items:\n";
    foreach ($selectedProducts as $selectedProduct) {
        echo "- " . $selectedProduct['name'] . ", " . $selectedProduct['amount'] . " pcs - $" .
            number_format($selectedProduct['price'] / 100 * $selectedProduct['amount'], 2) . "\n";
    }

    echo "Total items: " . array_sum(array_column($selectedProducts, 'amount')) . "\n";
    echo "Total price: $" . number_format(array_sum(array_map(function ($product) {
            return $product['price'] / 100 * $product['amount'];
        }, $selectedProducts)), 2) . "\n";

    while (true) {
    echo "Do you want to proceed with the purchase? (yes/no): ";
    $choice = strtolower(readline());
        if ($choice === 'yes') {
            return true;
        } elseif ($choice === 'no') {
            return false;
        } else {
            echo "Invalid value. Please enter 'yes' or 'no'.\n";
        }
    }
}

if (!empty($selectedProducts)) {
    if (confirmPurchase($selectedProducts)) {
        // Process the purchase
        echo "Thank you for your purchase! Looking forward to see you again soon!\n";
    } else {
        echo "Purchase cancelled.\n";
    }
} else {
    echo "No items selected. Exiting store.\n";
}