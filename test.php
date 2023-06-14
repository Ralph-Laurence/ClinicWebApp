<?php
// Connect to the database
$db = new mysqli('localhost', 'root', '', 'patient_infosys');

// Check for errors
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Set the product ID and the quantity to pull out
$product_id = 1;
$quantity_to_pull = 1;

// Update the quantity field in the products table
$update_products_query = "UPDATE t_products SET quantity = quantity - ? WHERE id = ?";
$update_products_stmt = $db->prepare($update_products_query);
$update_products_stmt->bind_param('is', $quantity_to_pull, $product_id);
$update_products_stmt->execute();

// Update the quantity field in the stock table using a FIFO approach
$select_stock_query = "SELECT id, quantity FROM t_stock WHERE product_id = ? ORDER BY expiry_date ASC";
$select_stock_stmt = $db->prepare($select_stock_query);
$select_stock_stmt->bind_param('s', $product_id);
$select_stock_stmt->execute();
$select_stock_result = $select_stock_stmt->get_result();

while ($row = $select_stock_result->fetch_assoc()) 
{
    $stock_id = $row['id'];
    $stock_quantity = $row['quantity'];

    if ($quantity_to_pull >= $stock_quantity) 
    {
        // Pull out all units from this stock entry
        $update_stock_query = "UPDATE t_stock SET quantity = 0 WHERE id = ?";
        $update_stock_stmt = $db->prepare($update_stock_query);
        $update_stock_stmt->bind_param('i', $stock_id);
        $update_stock_stmt->execute();

        // Subtract the pulled quantity from the remaining quantity to pull
        $quantity_to_pull -= $stock_quantity;
    } 
    else 
    {
        // Pull out only the remaining quantity to pull from this stock entry
        $update_stock_query = "UPDATE t_stock SET quantity = quantity - ? WHERE id = ?";
        $update_stock_stmt = $db->prepare($update_stock_query);
        $update_stock_stmt->bind_param('ii', $quantity_to_pull, $stock_id);
        $update_stock_stmt->execute();

        // No more quantity left to pull
        break;
    }
}

// Close the database connection
$db->close();
?>