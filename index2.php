<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Milatu Cases</title>
	<link rel="stylesheet" type="text/css" href="dist/css/bootstrap.min.css">
	<style>
    /* ... (existing styles) ... */

    .navbar-toggler:focus {
        outline: none;
        box-shadow: none;
    }

    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%280, 0, 0, 0.55%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        transition: background-image 0.3s ease-in-out;
    }

    .navbar-toggler[aria-expanded="true"] .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='rgba%280, 0, 0, 0.55%29' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 2l12 12M14 2L2 14'/%3e%3c/svg%3e");
    }
</style>
</head>
<body>
	<!-- Navigation -->
	<nav class="navbar navbar-expand-lg navbar-light bg-light">
	    <div class="container">
	        <a class="navbar-brand" href="#">LegalEase</a>
	        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
	            <span class="navbar-toggler-icon"></span>
	        </button>
	        <div class="collapse navbar-collapse" id="navbarNav">
	            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
	                <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
	                <li class="nav-item"><a class="nav-link" href="#pricing">Pricing</a></li>
	                <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
	            </ul>
	            <div class="d-flex">
	                <li class="nav-item"><a href="#" class="nav-link">Login</a></li>
	                <li class="nav-item"><a href="#" class="nav-link">Create Account</a></li>
	            </div>
	        </div>
	    </div>
	</nav>
	

	<script>
	    document.addEventListener('DOMContentLoaded', function() {
	        var navbarToggler = document.querySelector('.navbar-toggler');
	        navbarToggler.addEventListener('click', function() {
	            this.classList.toggle('active');
	        });
	    });
	</script>
    <script type="text/javascript" src="dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>