<?php
require_once '../includes/functions.php';
require_once '../includes/header.php';

// Redirect to login if not logged in
if (!isLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

// Check if user is admin or staff
if (!isAdmin() && $_SESSION['role'] !== 'staff') {
    header("Location: ../index.php");
    exit();
}

$conn = getDBConnection();

// Get certificate ID from URL
$cert_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($cert_id === 0) {
    header("Location: dashboard.php");
    exit();
}

// Get application details
$app = $conn->query("SELECT * FROM marriage_applications WHERE id = $cert_id")->fetch_assoc();

if (!$app) {
    header("Location: dashboard.php");
    exit();
}

// Handle signature upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $signature_type = sanitizeInput($_POST['signature_type']);
    $signature_data = sanitizeInput($_POST['signature_data']);
    
    // Create signatures directory if not exists
    $signaturesDir = '../certificates/signatures';
    if (!file_exists($signaturesDir)) {
        mkdir($signaturesDir, 0777, true);
    }
    
    // Save signature data
    $signatureFile = $signaturesDir . '/signature_' . $cert_id . '_' . $signature_type . '.txt';
    file_put_contents($signatureFile, $signature_data);
    
    $success = "Signature saved successfully!";
}

// Get existing signatures
$signatures = [];
$signaturesDir = '../certificates/signatures';
if (file_exists($signaturesDir)) {
    $files = scandir($signaturesDir);
    foreach ($files as $file) {
        if (strpos($file, 'signature_' . $cert_id . '_') === 0) {
            $parts = explode('_', str_replace('.txt', '', $file));
            $signatures[] = [
                'type' => $parts[2] ?? 'unknown',
                'data' => file_get_contents($signaturesDir . '/' . $file)
            ];
        }
    }
}
?>

<div class="admin-container">
    <div class="admin-header">
        <h1><i class="fas fa-signature"></i> Certificate Signatures</h1>
        <p>Add official signatures and government stamps</p>
    </div>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>
    
    <div class="admin-content">
        <div class="admin-section">
            <h2><i class="fas fa-certificate"></i> Certificate #<?php echo $app['certificate_number'] ?: 'N/A'; ?></h2>
            
            <div class="applications-table">
                <table>
                    <tr>
                        <td><strong>Couple:</strong></td>
                        <td><?php echo $app['groom_name']; ?> & <?php echo $app['bride_name']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Marriage Date:</strong></td>
                        <td><?php echo date('F d, Y', strtotime($app['marriage_date'])); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Location:</strong></td>
                        <td><?php echo $app['marriage_location']; ?></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="admin-section">
            <h2><i class="fas fa-pen-fancy"></i> Add Signature</h2>
            <div class="profile-card">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="signature_type">Signature Type</label>
                        <select id="signature_type" name="signature_type" class="form-control" required>
                            <option value="">Select Type</option>
                            <option value="registrar">Registrar Signature</option>
                            <option value="witness">Witness Signature</option>
                            <option value="official">Official Stamp</option>
                            <option value="seal">Government Seal</option>
                            <option value="minister">Minister Signature</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="signature_data">Signature/Stamp Data</label>
                        <textarea id="signature_data" name="signature_data" rows="4" class="form-control" placeholder="Enter signature name, stamp details, or official text" required></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Signature
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="admin-section">
            <h2><i class="fas fa-list"></i> Existing Signatures</h2>
            <div class="applications-table">
                <table>
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Data</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($signatures)): ?>
                            <tr>
                                <td colspan="3">No signatures added yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($signatures as $sig): ?>
                                <tr>
                                    <td>
                                        <span class="status-badge status-approved">
                                            <?php echo ucfirst($sig['type']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($sig['data']); ?></td>
                                    <td>
                                        <button class="btn-small btn-edit" onclick="editSignature('<?php echo $sig['type']; ?>', '<?php echo htmlspecialchars($sig['data']); ?>')">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn-small btn-view" onclick="deleteSignature('<?php echo $sig['type']; ?>')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="button" onclick="location.href='view_application.php?id=<?php echo $cert_id; ?>'" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Application
            </button>
            <button type="button" onclick="location.href='dashboard.php'" class="btn btn-secondary">
                <i class="fas fa-home"></i> Dashboard
            </button>
        </div>
    </div>
</div>

<script>
function editSignature(type, data) {
    document.querySelector('select[name="signature_type"]').value = type;
    document.querySelector('textarea[name="signature_data"]').value = data;
}

function deleteSignature(type) {
    if (confirm('Are you sure you want to delete this ' + type + ' signature?')) {
        // In a real system, this would make an AJAX call to delete
        alert('Delete functionality would be implemented here');
    }
}
</script>

<?php require_once '../includes/footer.php'; ?>
