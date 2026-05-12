<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // --- SPAM TRAP (Honeypot) ---
    // If the hidden 'botcheck' field is filled out, it's a bot. Stop execution.
    if (!empty($_POST['botcheck'])) {
        die("Spam detected.");
    }

    // 1. Setup Email Addresses
    $to = "Astitchaboveembroidery@gmail.com"; 
    $from = "quotes@astitchaboveembroidery.com"; 
    
    // 2. Collect and Sanitize Form Data
    $customerName = htmlspecialchars($_POST['name'] ?? 'Customer');
    $customerEmail = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $phone = htmlspecialchars($_POST['phone'] ?? 'Not provided');
    
    $sku = htmlspecialchars($_POST['sku'] ?? 'Not provided');
    $color = htmlspecialchars($_POST['color'] ?? 'Not provided');
    $size = htmlspecialchars($_POST['size'] ?? 'Not provided');
    $quantity = htmlspecialchars($_POST['quantity'] ?? '0');
    
    $projectDescription = htmlspecialchars($_POST['project_description'] ?? 'No description provided.');
    $paymentMethod = htmlspecialchars($_POST['payment_method'] ?? 'Not specified');
    
    $subject = "New Quote Request from " . $customerName;

    // 3. Create a Unique Boundary for the Attachment
    $boundary = md5(time());

    // 4. Set Email Headers
    $headers = "From: A Stitch Above Website <" . $from . ">\r\n";
    $headers .= "Reply-To: " . $customerEmail . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"" . $boundary . "\"\r\n";

    // 5. Build the Text Portion of the Email Body
    $body = "--" . $boundary . "\r\n";
    $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    
    // Formatting the email content cleanly into sections
    $message = "You have received a new quote request from the website.\n\n";
    
    $message .= "--- CONTACT INFORMATION ---\n";
    $message .= "Name: " . $customerName . "\n";
    $message .= "Email: " . $customerEmail . "\n";
    $message .= "Phone: " . $phone . "\n\n";
    
    $message .= "--- ITEM DETAILS ---\n";
    $message .= "Item Number (SKU): " . $sku . "\n";
    $message .= "Garment Color: " . $color . "\n";
    $message .= "Size(s): " . $size . "\n";
    $message .= "Total Quantity: " . $quantity . "\n\n";
    
    $message .= "--- EMBROIDERY DESIGN ---\n";
    $message .= "Placement & Design Details:\n" . $projectDescription . "\n\n";
    
    $message .= "--- PAYMENT PREFERENCE ---\n";
    $message .= "Method: " . $paymentMethod . "\n\n";
    
    $body .= $message . "\r\n\r\n";

    // 6. Handle the File Attachment (Checking the 'upload' name attribute)
    if (isset($_FILES['upload']) && $_FILES['upload']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['upload']['tmp_name'];
        $fileName = $_FILES['upload']['name'];
        $fileType = $_FILES['upload']['type'];
        
        // Read the file and encode it for email transmission
        $fileContent = file_get_contents($fileTmpPath);
        $encodedContent = chunk_split(base64_encode($fileContent));

        // Append the encoded file to the email body
        $body .= "--" . $boundary . "\r\n";
        $body .= "Content-Type: " . $fileType . "; name=\"" . basename($fileName) . "\"\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n";
        $body .= "Content-Disposition: attachment; filename=\"" . basename($fileName) . "\"\r\n\r\n";
        $body .= $encodedContent . "\r\n\r\n";
    }

    // Close the boundary
    $body .= "--" . $boundary . "--";

    // 7. Send the Email and Redirect
    if (mail($to, $subject, $body, $headers, "-f", $from)) {
        header("Location: thanks.html");
        exit;
    } else {
        echo "Error: The server was unable to process the email.";
    }
} else {
    // Blocks direct browser access to mailer.php
    echo "Invalid request.";
}
?>