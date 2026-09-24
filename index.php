<?php
require_once 'db.php';

// Fetch all employees initially
$stmt = $pdo->query("SELECT * FROM employees ORDER BY id ASC");
$employees = $stmt->fetchAll();
$totalRecords = count($employees);

// Helper function to return unique Bootstrap badge classes per department
function getDepartmentBadgeClass($dept) {
    return match($dept) {
        'Engineering' => 'bg-primary-subtle text-primary border-primary-subtle',
        'Product'     => 'bg-info-subtle text-info border-info-subtle',
        'Design'      => 'bg-danger-subtle text-danger border-danger-subtle',
        'Sales'       => 'bg-warning-subtle text-warning border-warning-subtle',
        'HR'          => 'bg-success-subtle text-success border-success-subtle',
        default       => 'bg-secondary-subtle text-secondary border-secondary-subtle',
    };
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Records System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light min-vh-100 flex-column d-flex">

    <!-- Header / Navbar -->
    <div class="bg-white border-bottom py-3 px-4 shadow-sm">
        <div class="container-fluid d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h2 class="h4 mb-0 fw-bold text-dark">Employee Directory</h2>
                <small class="text-muted"><span id="recordCount"><?= $totalRecords ?></span> of <?= $totalRecords ?> records</small>
            </div>
            
            <div class="d-flex gap-3 align-items-center">
                <!-- Instant Search Bar -->
                <div>
                    <input 
                        type="text" 
                        id="searchInput" 
                        placeholder="Search employees..." 
                        class="form-control"
                        style="width: 250px;"
                    >
                </div>
                <a href="add.php" class="btn btn-primary text-nowrap">+ Add Employee</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap Full-Width Table Container -->
    <div class="container-fluid flex-grow-1 p-0 overflow-auto">
        <table class="table table-hover align-middle mb-0 w-100" id="employeeTable">
            <thead class="table-light sticky-top">
                <tr>
                    <th class="ps-4">#</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Position</th>
                    <th class="pe-4 text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($employees)): ?>
                    <?php foreach ($employees as $index => $emp): ?>
                        <tr class="employee-row">
                            <td class="ps-4 text-secondary font-monospace"><?= sprintf('%02d', $index + 1) ?></td>
                            <td class="fw-semibold text-dark search-target"><?= htmlspecialchars($emp['full_name']) ?></td>
                            <td class="text-muted font-monospace search-target"><?= htmlspecialchars($emp['email']) ?></td>
                            <td class="search-target">
                                <span class="badge border px-2 py-1 <?= getDepartmentBadgeClass($emp['department']) ?>">
                                    <?= htmlspecialchars($emp['department']) ?>
                                </span>
                            </td>
                            <td class="text-secondary search-target"><?= htmlspecialchars($emp['position']) ?></td>
                            <td class="pe-4 text-end">
                                <a href="edit.php?id=<?= $emp['id'] ?>" class="btn btn-sm btn-outline-primary me-1">Edit</a>
                                <a href="delete.php?id=<?= $emp['id'] ?>" onclick="return confirm('Are you sure you want to delete this employee?')" class="btn btn-sm btn-outline-danger">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr id="noRecordsRow">
                        <td colspan="6" class="text-center py-4 text-muted">No employee records found.</td>
                    </tr>
                <?php endif; ?>
                
                <!-- Hidden dynamic row for empty search results -->
                <tr id="noMatchRow" style="display: none;">
                    <td colspan="6" class="text-center py-4 text-muted">No matching employees found.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- JavaScript Instant Search Script -->
    <script>
        document.getElementById('searchInput').addEventListener('input', function() {
            const filter = this.value.toLowerCase().trim();
            const rows = document.querySelectorAll('.employee-row');
            let visibleCount = 0;

            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                if (text.includes(filter)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Update record count text
            document.getElementById('recordCount').innerText = visibleCount;

            // Show "No matching employees found" row if no rows match
            const noMatchRow = document.getElementById('noMatchRow');
            if (visibleCount === 0 && rows.length > 0) {
                noMatchRow.style.display = '';
            } else if (noMatchRow) {
                noMatchRow.style.display = 'none';
            }
        });
    </script>

</body>
</html>