<!DOCTYPE html>
<html lang="en">
<head>
    
    <title>MilatuCases Pricing</title>
    <?php include 'inc_header.php';?>

    <style>
        .pricing-item {
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            transition: all 0.3s;
            padding: 2rem; /* Add padding to the pricing item */
        }
        .pricing-item:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .pricing-item h4 {
            text-align: center; /* Center the plan title */
            padding: 1rem 0; /* Add some vertical padding to the title */
            background-color: #f8f9fa; /* Light background for the title */
            margin: -2rem -2rem 2rem -2rem; /* Negative margin to extend to edges */
            border-bottom: 1px solid #dee2e6; /* Add a border below the title */
        }
        /* New styles for the range slider */
        .range-slider {
            width: 100%;
            margin: 20px 0;
        }

        .form-range {
            -webkit-appearance: none;
            width: 100%;
            height: 10px;
            border-radius: 5px;
            background: #d7dcdf;
            outline: none;
            padding: 0;
            margin: 0;
        }

        .form-range::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #007bff;
            cursor: pointer;
            transition: background .15s ease-in-out;
        }

        .form-range::-moz-range-thumb {
            width: 20px;
            height: 20px;
            border: 0;
            border-radius: 50%;
            background: #007bff;
            cursor: pointer;
            transition: background .15s ease-in-out;
        }

        .form-range::-webkit-slider-thumb:hover {
            background: #0056b3;
        }

        .form-range:active::-webkit-slider-thumb {
            background: #0056b3;
        }

        .form-range::-moz-range-thumb:hover {
            background: #0056b3;
        }

        .form-range:active::-moz-range-thumb {
            background: #0056b3;
        }

        .range-slider .form-label {
            font-weight: bold;
            margin-bottom: 10px;
        }

        #duration-text {
            font-size: 1.1em;
            font-weight: bold;
            color: #007bff;
        }

        /* Additional styles for better spacing */
        .pricing-item ul {
            margin-bottom: 2rem;
        }
        .pricing-item .btn {
            width: 100%; /* Make the button full width */
        }
    </style>
</head>
<body>
    <?php include 'inc_top_bar.php';?>

    <?php include 'inc_nav.php';?>
    <div class="container-fluid feature bg-light py-5">
        <div class="container py-5">
            <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.2s" style="max-width: 800px;">
                <h1 class="display-4 mb-4">Pricing for MilatuCases</h1>
                <p class="mb-0">Choose the plan that best fits your law firm's needs and budget.</p>
            </div>
            <div class="mb-4">
                <label for="duration-range" class="form-label">Subscription Duration</label>
                <input type="range" class="form-range" id="duration-range" min="1" max="12" step="1" value="1">
                <div class="text-center mt-2" id="duration-text">Monthly</div>
            </div>
            <div class="row g-4" id="pricing-container">
                <!-- Pricing items will be dynamically inserted here -->
            </div>
        </div>
    </div>
        <?php include "footer.php" ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const plans = [
        {
            title: "Starter Plan",
            basePrice: 30,
            features: [
                "Up to 5 users",
                "10 GB storage",
                "Client Management",
                "Company Calendar",
                "Time tracking and billing",
                "Invoice Generation"
            ]
        },
        {
            title: "Professional Plan",
            basePrice: 55,
            features: [
                "All in Basic",
                "Up to 13 Users",
                "15 GB storage",
                "Personal Calendar",
                "Send invoices instantly",
                "Fee notes generation"
            ]
        },
        {
            title: "Enterprise Plan",
            basePrice: 99,
            features: [
                "All in Professional Plan",
                "Unlimited users",
                "25 GB storage",
                "Document management",
                "Law Firms Client's login"
            ]
        }
    ];

    function getDiscount(months) {
        if (months === 3) return 0.05;
        if (months === 6) return 0.10;
        if (months === 12) return 0.15;
        return 0;
    }

    function calculatePrice(basePrice, months) {
        const discount = getDiscount(months);
        const monthlyPrice = basePrice * (1 - discount);
        return (monthlyPrice * months).toFixed(2);
    }

    function updatePricing(months) {
        const pricingContainer = document.getElementById('pricing-container');
        pricingContainer.innerHTML = '';

        plans.forEach(plan => {
            const price = calculatePrice(plan.basePrice, months);
            const planHtml = `
                <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.2s">
                    <div class="pricing-item p-4 pt-0">
                        <h4 class="mb-4">${plan.title}</h4>
                        <div class="d-flex justify-content-center align-items-baseline my-4">
                            <h1 class="me-2">$${price}</h1>
                            <span>/${months} ${months === 1 ? 'month' : 'months'}</span>
                        </div>
                        <ul class="list-unstyled mb-4">
                            ${plan.features.map(feature => `<li>${feature}</li>`).join('')}
                        </ul>
                        <a href="signup" class="btn btn-primary">Get Started</a>
                    </div>
                </div>
            `;
            pricingContainer.innerHTML += planHtml;
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        const durationRange = document.getElementById('duration-range');
        const durationText = document.getElementById('duration-text');

        durationRange.addEventListener('input', (event) => {
            const months = parseInt(event.target.value);
            updatePricing(months);

            if (months === 1) durationText.textContent = 'Monthly';
            else if (months === 3) durationText.textContent = 'Quarterly (5% off)';
            else if (months === 6) durationText.textContent = 'Half-yearly (10% off)';
            else if (months === 12) durationText.textContent = 'Yearly (15% off)';
            else durationText.textContent = `${months} months`;
        });

        // Initial pricing update
        updatePricing(1);
    });
</script>

</body>
</html>