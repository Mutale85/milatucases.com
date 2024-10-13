<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'inc_header.php';?>
    <title>Create your account - Milatucases</title>
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
        #passwordRequirements {
            font-size: 0.9em;
            margin-top: 10px;
        }

        #passwordRequirements ul {
            list-style-type: none;
            padding-left: 0;
        }

        #passwordRequirements li::before {
            content: '❌ ';
        }

        #passwordRequirements li.valid::before {
            content: '✅ ';
        }

    </style>
</head>
<body>
    <div class="container-fluid login bg-light py-5">
        <div class="container py-5">
            
            <div class="row g-5">
                <div class="col-xl-3"></div>
                <div class="col-xl-6 wow fadeInLeft content" data-wow-delay="0.2s">
                    <div class="card">
                        <div class="card-header text-center">
                            <a href="./">
                                <img src="sampleLogo.png" class="img-fluid" style="width:120px;height:120px;border-radius: 50%;">
                            </a>
                            <h4 class="text-primary mb-4 mt-5 text-center">Get Started</h4>
                        </div>
                        <div class="card-body">
                            <form id="createLawFirmForm">
                                <div class="row">
                                    <div class="col-lg-12 col-xl-12">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control border-1" name="firmName" id="firmName" placeholder="Firm Name" required>
                                            <label for="firmName">Law Firm Name</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-xl-6">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control border-1" name="names" id="names" placeholder="Names" required>
                                            <label for="names">Your Names</label>
                                        </div>
                                    </div>                                    
                                    <div class="col-lg-12 col-xl-6">
                                        <div class="form-floating mb-3">
                                            <input type="email" class="form-control border-1" name="email" id="email" placeholder="Email" required>
                                            <label for="email">Email</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-xl-6">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control border-1" name="phonenumber" id="phonenumber" placeholder="Phone Number" required>
                                            <label for="phonenumber">Phone Number</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-xl-6">
                                        <div class="form-floating mb-3">
                                            <input type="password" class="form-control border-1" name="password" id="password" placeholder="Password" required
                                                   data-minlength="8" data-uppercase="1" data-lowercase="1" data-number="1" data-special="1">
                                            <label for="password">Password</label>
                                        </div>
                                        <div class="mb">
                                            <span class="bi bi-eye" id="togglePassword"></span>
                                        </div>
                                        <div id="passwordRequirements">
                                            <p>Password must contain:</p>
                                            <ul>
                                                <li id="length">At least 8 characters</li>
                                                <li id="uppercase">At least 1 uppercase letter</li>
                                                <li id="lowercase">At least 1 lowercase letter</li>
                                                <li id="number">At least 1 number</li>
                                                <li id="special">At least 1 special character</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-xl-6">
                                        <div class="form-floating mb-3">
                                            <select class="form-control border-1" id="job" name="job" required>
                                                <option value="">Select</option>
                                                <option value="Lawyer">Lawyer</option>
                                                <option value="Secretary">Secretary</option>
                                                <option value="Paralegal">Paralegal</option>
                                                <option value="Financial Officer">Financial Officer</option>                      
                                            </select>
                                            <label for="job">What is Job</label>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 col-xl-6">
                                        <div class="form-floating mb-3">
                                            <select class="form-control border-1" id="members" name="members" required>
                                                <option value="">Select</option>
                                                <option value="1">1</option>
                                                <option value="5">2-5</option>
                                                <option value="10">6-10</option>
                                                <option value="20">10-20</option> 
                                                <option value="50">20 - 50</option>                      
                                            </select>
                                            <label for="job">Firm Size</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary w-100 btn-lg" id="submitBtn">Get Started</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3"></div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function (e) {
            const password = document.getElementById('password');
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });

        $('#createLawFirmForm').on('submit', function(e) {
            e.preventDefault();

            const email = $('#email').val();
            const password = $('#password').val();
            // var formData = $(this).serialize();
            var formData = new FormData(this);
            $.ajax({
                url: 'parsers/createAccount',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                beforeSend:function(){
                    $("#submitBtn").prop("disabled", true).html(`Processing ... <div id="spinner" class="spinner-border text-primary" role="status" >
                                </div>`);
                },
                success: function(response) {
                    if(response.includes("Registration successful")){
                        successMessage("Your account has been successfully created, check the verification link in you email " + email);
                        setTimeout(function(){
                            window.location = 'login';
                        }, 1000);
                    }else{
                        errorMessage(response);
                        // $('#createLawFirmForm')[0].reset();
                    }
                    $("#submitBtn").prop("disabled", false).html("Submit Form");
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


        document.addEventListener('DOMContentLoaded', function() {
            const password = document.getElementById('password');
            const requirements = {
                length: document.getElementById('length'),
                uppercase: document.getElementById('uppercase'),
                lowercase: document.getElementById('lowercase'),
                number: document.getElementById('number'),
                special: document.getElementById('special')
            };

            password.addEventListener('input', function() {
                const value = this.value;
                
                // Check length
                if (value.length >= 8) {
                    requirements.length.classList.add('valid');
                } else {
                    requirements.length.classList.remove('valid');
                }
                
                // Check uppercase
                if (/[A-Z]/.test(value)) {
                    requirements.uppercase.classList.add('valid');
                } else {
                    requirements.uppercase.classList.remove('valid');
                }
                
                // Check lowercase
                if (/[a-z]/.test(value)) {
                    requirements.lowercase.classList.add('valid');
                } else {
                    requirements.lowercase.classList.remove('valid');
                }
                
                // Check number
                if (/[0-9]/.test(value)) {
                    requirements.number.classList.add('valid');
                } else {
                    requirements.number.classList.remove('valid');
                }
                
                // Check special character
                if (/[!@#$%^&*(),.?":{}|<>]/.test(value)) {
                    requirements.special.classList.add('valid');
                } else {
                    requirements.special.classList.remove('valid');
                }
            });
        });
    </script>
</body>
</html>
