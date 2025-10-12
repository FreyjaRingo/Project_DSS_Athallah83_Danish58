<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Decision Support System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding-bottom: 50px;
        }
        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-top: 30px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.2);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .result-card {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }
        .rank-1 { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; }
        .rank-2 { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; }
        .rank-3 { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="<?= base_url('/') ?>">🎯 DSS Simulator</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('/') ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('ahp') ?>">AHP</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('topsis') ?>">TOPSIS</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('saw') ?>">SAW</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('wp') ?>">WP</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-bold text-primary" href="<?= base_url('compare') ?>">🔄 Perbandingan</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>