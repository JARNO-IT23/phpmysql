<?php
require_once 'header.php';

if ($currentUser) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($auth->register(
        $_POST['first_name'],
        $_POST['last_name'],
        $_POST['personal_id'],
        $_POST['email'],
        $_POST['password']
    )) {
        $_SESSION['message'] = 'Registration successful! Please login.';
        header("Location: login.php");
        exit();
    } else {
        $error = "Registration failed. Email or personal ID may already be in use.";
    }
}
?>

<h2>Register</h2>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="post">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="first_name" class="form-label">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="last_name" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name" required>
        </div>
    </div>
    <div class="mb-3">
        <label for="personal_id" class="form-label">Personal Identification Code</label>
        <input type="text" class="form-control" id="personal_id" name="personal_id" required>
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <button type="submit" class="btn btn-primary">Register</button>
</form>

<?php require_once 'footer.php'; ?>