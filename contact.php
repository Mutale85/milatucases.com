<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'inc_header.php';?>
    <title>Contact Us - MilatuCases</title>
    <style>
        .contact-section .form-control:focus {
            box-shadow: none;
            border-color: #007bff;
        }
        .contact-section .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .contact-section .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .contact-section .accordion-item {
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <?php include 'inc_top_bar.php';?>
    <?php include 'inc_nav.php';?>    
    <div class="container-fluid contact bg-light py-5">
        <div class="container py-5">
            <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.2s" style="max-width: 800px;">
                <h4 class="text-primary">Contact Us</h4>
                <h4 class="display-4 mb-4">We would love to here from you</h4>
            </div>
            <div class="row g-5">
                <div class="col-xl-6 wow fadeInLeft" data-wow-delay="0.2s">
                    <div class="contact-img d-flex justify-content-center">
                        <div class="contact-img-inner">
                            <img src="assets/img/elements/1.jpg" class="img-fluid w-100" alt="Image">
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 wow fadeInRight" data-wow-delay="0.4s">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="text-primary">Send Your Message</h4>
                        </div>
                        <div class="card-body">
                            <form method="contactForm">
                                <div class="row g-3">
                                    <div class="col-lg-12 col-xl-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control border-0" id="name" placeholder="Your Name">
                                            <label for="name">Your Name</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-xl-6">
                                        <div class="form-floating">
                                            <input type="email" class="form-control border-0" id="email" placeholder="Your Email">
                                            <label for="email">Your Email</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-xl-6">
                                        <div class="form-floating">
                                            <input type="phone" class="form-control border-0" id="phone" placeholder="Phone">
                                            <label for="phone">Your Phone</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-xl-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control border-0" id="lawfirm" placeholder="lawfirm">
                                            <label for="lawfirm">Your Law Firm</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control border-0" id="subject" placeholder="Subject">
                                            <label for="subject">Subject</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <textarea class="form-control border-0" placeholder="Leave a message here" id="message" style="height: 120px"></textarea>
                                            <label for="message">Message</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button class="btn btn-primary w-100 py-3">Send Message</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php';?>
</body>
</html>
