<?php include 'header.php'; ?>

<!-- HERO SLIDER -->
<header class="hero-slider">
    <div class="slide active" style="background-image: url('Img/Style/Slider/s1.png');"></div>
    <div class="slide" style="background-image: url('Img/Style/Slider/s2.png');"></div>
</header>

<section id="about" class="bg-pattern">

    <!-- Moved Hero Content -->
    <div style="text-align: center; margin-bottom: 40px;">
        <img src="Img/logo2.png" alt="Aljawad Premium"
            style="max-width: 250px; margin: 0 auto 20px auto; display: block;">
        <h1 style="color: var(--gold); font-family: 'Moshreq', sans-serif; font-size: 36px; margin-bottom: 15px;">
            Aljawad Premium – Hospitality & Food Supply Solutions
        </h1>
        <p style="font-size: 20px; color: #555;">
            Your trusted partner in <strong>hospitality</strong>, catering, and food supply services across Saudi
            Arabia.
        </p>
    </div>

    <h2>Who We Are & Our Presence</h2>

    <p>
        Since its establishment in 2017, Jawad Al-Diyafa Trading has specialized in <strong>catering</strong> and food
        supply services within the <strong>Kingdom of Saudi Arabia</strong>.
    </p>

    <p>
        We take pride in offering comprehensive solutions, having served over 50 entities with quality, efficiency, and
        reliability at the core of our operations.
    </p>


    <p>
        We are proud to have 14 branches across 12 cities in the Kingdom, enhancing our ability to deliver services
        efficiently.
    </p>

    <p>
        We import products from trusted suppliers worldwide, ensuring <strong>certified quality</strong> and maintaining
        a strict commitment to food safety and sustainability—ensuring the delivery of products that meet the
        expectations of the Saudi market.
    </p>

</section>


<!-- LOGISTICS -->
<section id="logistics">
    <h2>Our Logistics System</h2>

    <div class="logistics-container">

        <div class="logistics-text">
            <p style="margin: 0;">
                We operate a fully integrated logistics system that is considered one of the strongest in the frozen
                food sector within the Kingdom. Our services cover all regions across Saudi Arabia through an advanced
                distribution network.
            </p>

            <p class="feature-title"><strong>Key Features</strong></p>

            <ul class="features">
                <li>
                    <strong>Strict Commitment to Food Safety Standards</strong><br>
                    Rigorous procedures for sanitation, storage, and handling in line with global best practices and
                    SFDA standards.
                </li>

                <li>
                    <strong>Fully Equipped Fleet</strong><br>
                    Refrigerated and frozen trucks compliant with the highest European standards for food
                    transportation.
                </li>

                <li>
                    <strong>Precise Temperature Control</strong><br>
                    Continuous temperature monitoring to ensure product safety from source to point of sale.
                </li>

                <li>
                    <strong>Smart Tracking System</strong><br>
                    Real-time tracking technologies for location and temperature, ensuring transparency and safety.
                </li>

                <li>
                    <strong>Strategically Located Distribution Centers</strong><br>
                    Distribution hubs positioned to ensure fast delivery and reduced loading and delivery times.
                </li>
            </ul>
        </div>

        <div class="logistics-img">
            <img src="Img/Style/Logistics/car.png" alt="Logistics Fleet">
        </div>

    </div>
</section>

<!-- LICENSES -->
<section id="licenses">
    <h2>Our Licenses</h2>

    <p>
        Jawad Al-Diyafa operates with official licenses approved by the relevant government authorities.
        All our facilities are certified by the <strong>Saudi Food and Drug Authority (SFDA)</strong>, ensuring strict
        adherence

        to the highest health and environmental standards.
    </p>

    <p>
        We are committed to delivering services that fully comply with the regulations and laws of the Kingdom
        of Saudi Arabia. This commitment is the foundation of our clients’ trust and the quality we consistently
        deliver.
    </p>

    <img src="Img/sfda-logo.jpg" class="license-logo" alt="SFDA">
</section>

<!-- FACTORY -->
<section id="factory">
    <h2>Our New Factory</h2>

    <p>
        We are proud to announce that our dedicated production facility is currently under development and set
        to be fully operational by <strong>December 2025</strong>.

    </p>

    <p>
        Located in <strong>Alasyah Industrial City</strong>, this state-of-the-art factory is designed to support
        our expanding operations in the food sector.
    </p>

    <p class="feature-title"><strong>The facility will specialize in:</strong></p>

    <ul class="features">
        <li>Frozen products</li>
        <li>Poultry processing</li>
        <li>Ready-to-eat meals</li>
        <li>Co-packing services</li>
    </ul>

    <p>
        This strategic investment marks a significant milestone in our growth, enabling us to enhance quality
        control, increase production capacity, and meet rising market demand with greater efficiency.
    </p>
</section>

<section id="brands">
    <h2>Our Brands & Partners</h2>


    <div class="partners-slider">
        <div class="partners-track">
            <?php
            $brands = glob("Img/brands/*.{png,jpg,jpeg,webp}", GLOB_BRACE);

            // First loop
            foreach ($brands as $logo) {
                echo '<div class="partner-item">
                        <img src="/' . $logo . '" alt="Brand Logo">
                      </div>';
            }

            // Duplicate for smooth infinite scroll
            foreach ($brands as $logo) {
                echo '<div class="partner-item">
                        <img src="/' . $logo . '" alt="Brand Logo">
                      </div>';
            }
            ?>
        </div>
    </div>
</section>





<div class="partners-slider">
    <div class="partners-track">
        <?php
        $partners = glob("Img/partner/*.{png,jpg,jpeg,webp}", GLOB_BRACE);

        // FIRST LOOP
        foreach ($partners as $logo) {
            echo '<div class="partner-item">
                        <img src="/' . $logo . '" alt="Partner">
                      </div>';
        }

        // DUPLICATE FOR SMOOTH LOOP
        foreach ($partners as $logo) {
            echo '<div class="partner-item">
                        <img src="/' . $logo . '" alt="Partner">
                      </div>';
        }
        ?>
    </div>
</div>


<script>
    const slides = document.querySelectorAll('.slide');
    let currentSlide = 0;

    function nextSlide() {
        slides[currentSlide].classList.remove('active');
        currentSlide = (currentSlide + 1) % slides.length;
        slides[currentSlide].classList.add('active');
    }

    setInterval(nextSlide, 5000); // Change image every 5 seconds
</script>

<?php include 'footer.php'; ?>