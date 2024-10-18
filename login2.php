<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'inc_header.php';?>
    <title>Login - Milatucases</title>
    <style>
        .login-section .section-title {
            text-align: center;
            margin-bottom: 40px;
        }
        .login-section .section-title h4 {
            color: #007bff;
        }
        .login-section .content {
            max-width: 400px;
            margin: 0 auto;
        }
        .login-section .content .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        .login-section .content .form-control {
            padding-right: 2.5rem;
        }
        .login-section .content .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }
        .login-section .content .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
    </style>
</head>
<body>
    <?php include 'inc_top_bar.php';?>
    <?php include 'inc_nav.php';?>    

    <div class="container-fluid login bg-light py-5">
        <div class="container py-5">
            <div class="text-center mx-auto pb-5 wow fadeInUp section-title" data-wow-delay="0.2s" style="max-width: 800px;">
                <h4 class="display-4 mb-2">Milatucases Login</h4>
            </div>
            <div class="row g-5">
                <div class="col-xl-3"></div>
                <div class="col-xl-6 wow fadeInLeft content" data-wow-delay="0.2s">
                    <div class="card">
                        <div class="card-body p-5">
                            <h4 class="text-primary mb-4 mt-5 text-center">Welcome Back</h4>
                            <form id="loginForm" method="post">
                                <div class="form-group mb-3">
                                    <label for="email" class="mb-2">Email address</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="password" class="mb-2">Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text toggle-password">
                                                <i class="bi bi-eye" id="togglePassword"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block" id="submitBtn">Login</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3"></div>
            </div>
        </div>
    </div>

    <?php include 'footer.php';?>

    <script>
        document.getElementById('togglePassword').addEventListener('click', function (e) {
            const password = document.getElementById('password');
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });

        $('#loginForm').on('submit', function(e) {
            e.preventDefault();

            const email = $('#email').val();
            const password = $('#password').val();

            $.ajax({
                url: 'parsers/loginPage',
                type: 'POST',
                data: {
                    email: email,
                    password: password
                },
                beforeSend:function(){
                    $("#submitBtn").prop("disabled", true).html("Processing....");
                },
                success: function(response) {
                    if(response.includes("Login Successfull")){
                        successMessage("Redirecting you in 1 Second");
                        setTimeout(function(){
                            window.location = 'aa-ab';
                        }, 1000);
                    }else{
                        errorMessage(response);
                        $('#loginForm')[0].reset();
                    }
                    $("#submitBtn").prop("disabled", false).html("Login");
                }
                
            });
        });

        function successMessage(message){
            Swal.fire({
              position: "top-end",
              icon: "success",
              title: message,
              showConfirmButton: false,
              timer: 1500
            });
        }

        function errorMessage(message) {
           
            Swal.fire({
              icon: "error",
              title: "Oops...",
              text: message,
              // footer: '<a href="#">Why do I have this issue?</a>'
            });
        }
    </script>
</body>
</html>
