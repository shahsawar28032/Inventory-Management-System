<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$database = new Database();
$conn = $database->getConnection();

$page_title = "Edit Supplier";
$error = '';
$success = '';

// Get supplier ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$supplier_id = $_GET['id'];

// Get supplier data
$query = "SELECT * FROM suppliers WHERE id = :id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $supplier_id);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    header("Location: index.php");
    exit;
}

$supplier = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_POST) {
    $name = sanitize($_POST['name']);
    $contact_person = sanitize($_POST['contact_person']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    
    // Validate input
    if (empty($name)) {
        $error = "Supplier name is required.";
    } else {
        // Check if supplier already exists (excluding current supplier)
        $check_query = "SELECT id FROM suppliers WHERE name = :name AND id != :id";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bindParam(':name', $name);
        $check_stmt->bindParam(':id', $supplier_id);
        $check_stmt->execute();
        
        if ($check_stmt->rowCount() > 0) {
            $error = "Supplier name already exists.";
        } else {
            // Update supplier
            $query = "UPDATE suppliers SET name = :name, contact_person = :contact_person, 
                     email = :email, phone = :phone, address = :address WHERE id = :id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':contact_person', $contact_person);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':id', $supplier_id);
            
            if ($stmt->execute()) {
                $success = "Supplier updated successfully!";
                // Refresh supplier data
                $supplier['name'] = $name;
                $supplier['contact_person'] = $contact_person;
                $supplier['email'] = $email;
                $supplier['phone'] = $phone;
                $supplier['address'] = $address;
            } else {
                $error = "Error updating supplier. Please try again.";
            }
        }
    }
}

require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Edit Supplier</h1>
        <a href="index.php" class="btn btn-secondary">Back to Suppliers</a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Supplier Name *</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo htmlspecialchars($supplier['name']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="contact_person" class="form-label">Contact Person</label>
                            <input type="text" class="form-control" id="contact_person" name="contact_person"
                                   value="<?php echo htmlspecialchars($supplier['contact_person']); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="<?php echo htmlspecialchars($supplier['email']); ?>">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone"
                                   value="<?php echo htmlspecialchars($supplier['phone']); ?>">
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($supplier['address']); ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Update Supplier</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</main>

<?php require_once '../../includes/footer.php'; ?>