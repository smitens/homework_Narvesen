<?php

function selectProduct($products): stdClass
{
    $selectedProducts = new stdClass();

    echo "Welcome to our store!\nAvailable products:\n";
    $index = 1;
    foreach ($products -> products as $productType => $productList) {
        echo ($productType) . ":\n";
        foreach ($productList as $product) {
            echo $index . " " . $product -> name . ", $" . number_format($product -> price / 100, 2) . "\n";
            $index++;
        }
    }

    while (true) {
        echo "Enter the number of the product you want to select (type 'stop' to finish): ";
        $choice = readline();

        if ($choice === 'stop') {
            echo "Selection done.\n";
            break;
        }

        $choice = intval($choice);
        if (!is_numeric($choice) || $choice < 1 || $choice > $index - 1) {
            echo "Invalid choice. Please enter a valid number or 'stop' to finish.\n";
            continue;
        }

        $selectedProduct = null;
        $currentIndex = 1;
        foreach ($products -> products as $productList) {
            foreach ($productList as $product) {
                if ($currentIndex == $choice) {
                    $selectedProduct = $product;
                    break 2;
                }
                $currentIndex++;
            }
        }

        if (!empty($selectedProduct)) {
            while (true) {
                echo "Enter the amount of " . $selectedProduct -> name . ": ";
                $amount = intval(readline());

                if ($amount <= 0) {
                    echo "Invalid amount. Please enter a positive number.\n";
                } else {
                    $totalPrice = number_format($selectedProduct -> price / 100 * $amount, 2);
                    echo "Selected: " . $selectedProduct -> name . ", Amount: " . $amount . ", Total Price: EUR " . $totalPrice . "\n";
                    $selectedProducts->{$selectedProduct->name} = ['name' => $selectedProduct->name, 'price' => $selectedProduct->price, 'amount' => $amount];;
                    break;
                }
            }
        } else {
            echo "Invalid choice. Please enter a valid number or 'stop' to finish.\n";
        }
    }

    return $selectedProducts;
}
$products = json_decode(file_get_contents('products.json'), false);
$selectedProducts = selectProduct($products);

echo "Your shopping cart contains the following items:\n";
$totalPrice = 0;
$totalItems = 0;
foreach ($selectedProducts as $productName => $selectedProduct) {
        $totalPrice += $selectedProduct ['price'] / 100 * $selectedProduct ['amount'];
        $totalItems += $selectedProduct ['amount'];
        echo "- " . $selectedProduct ['name'] . ", " . $selectedProduct ['amount'] . " pcs - EUR " .
            number_format($selectedProduct ['price'] / 100 * $selectedProduct ['amount'], 2) . "\n";
}
echo "Total items:" . $totalItems . "\n";
echo "Total price: EUR " . number_format($totalPrice, 2) . "\n";


function confirmPurchase(): bool
{
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

if (!empty((array)$selectedProducts)) {
    if (confirmPurchase()) {
        echo "Thank you for your purchase! Looking forward to see you again soon!\n";
    } else {
        echo "Purchase cancelled.\n";
    }
} else {
    echo "No items selected. Exiting store.\n";
}