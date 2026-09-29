<?php
require_once 'db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM employees WHERE id = :id");
$stmt->execute(['id' => $id]);
$employee = $stmt->fetch();

if (!$employee) {
    header("Location: index.php");
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $position = trim($_POST['position'] ?? '');

    if (empty($full_name)) $errors[] = "Full Name is required.";
    if (empty($email)) $errors[] = "Email is required.";
    if (empty($department)) $errors[] = "Department is required.";
    if (empty($position)) $errors[] = "Position is required.";

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE employees SET full_name = :full_name, email = :email, department = :department, position = :position WHERE id = :id");
        $stmt->execute([
            'full_name'  => $full_name,
            'email'      => $email,
            'department' => $department,
            'position'   => $position,
            'id'         => $id
        ]);
        header("Location: index.php");
        exit;
    }
} else {
    $full_name = $employee['full_name'];
    $email = $employee['email'];
    $department = $employee['department'];
    $position = $employee['position'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Employee</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
    <div class="container" style="max-width: 500px;">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="card-title mb-0">Edit Employee Record</h5>
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
                        <input type="text" name="department" class="form-control" value="<?= htmlspecialchars($department) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Position</label>
                        <input type="text" name="position" class="form-control" value="<?= htmlspecialchars($position) ?>">
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="index.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-warning">Update Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>