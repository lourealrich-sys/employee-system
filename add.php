<?php
require_once 'db.php';

$deptStmt = $pdo->query("SELECT * FROM departments ORDER BY dept_name ASC");
$departments = $deptStmt->fetchAll();

$errors = [];
$full_name = $email = $department_id = $position = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name     = trim($_POST['full_name'] ?? '');
    $email         = trim($_POST['email'] ?? '');
    $department_id = trim($_POST['department_id'] ?? '');
    $position      = trim($_POST['position'] ?? '');

    if (empty($full_name))     $errors[] = "Full Name is required.";
    if (empty($email))         $errors[] = "Email is required.";
    if (empty($department_id)) $errors[] = "Department is required.";
    if (empty($position))      $errors[] = "Position is required.";

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO employees (full_name, email, department_id, position) VALUES (:full_name, :email, :department_id, :position)");
            $stmt->execute([
                'full_name'     => $full_name,
                'email'         => $email,
                'department_id' => $department_id,
                'position'      => $position
            ]);
            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            $errors[] = "Database Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Employee</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
    <div class="container" style="max-width: 500px;">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Add New Employee</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($full_name) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Department</label>
                        <select name="department_id" class="form-select">
                            <option value="">-- Select Department --</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?= $dept['id'] ?>" <?= ($department_id == $dept['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($dept['dept_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Position</label>
                        <input type="text" name="position" class="form-control" value="<?= htmlspecialchars($position) ?>">
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="index.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save Employee</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>