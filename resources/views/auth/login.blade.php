<!DOCTYPE html>
<html lang="id">
<head>
    <title>Login - Kayuhan Coffee</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <div id="view-login">
        <div class="login-card text-center">
            <div class="d-flex justify-content-center mb-4">
                <div class="bg-white p-3 rounded-circle shadow-sm" style="width: 80px; height: 80px; display:flex; align-items:center; justify-content:center;">
                    <h1 class="m-0 fw-bold" style="color: var(--primary)">K</h1>
                </div>
            </div>
            <h4 class="fw-bold text-primary-custom mb-1">KAYUHAN COFFEE</h4>
            
            <form method="POST" action="{{ route('login') }}">
                @csrf 
                <div class="form-floating mb-3 text-start">
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                    <label>Email User</label>
                </div>
                <div class="form-floating mb-4 text-start">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                    <label>Password</label>
                </div>
                <div class="d-grid gap-2 mb-3">
                    <button type="submit" class="btn btn-accent py-3 fw-bold shadow-sm">MASUK SISTEM</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>