<?php include 'header.php'; ?>

<!-- HERO -->
<header class="hero">
    <img src="Img/hero-image.png" alt="aljawad" class="hero-logo">
    <h1>Aljawad Premium – Hospitality & Food Supply Solutions</h1>
    <p>Your trusted partner in <strong>hospitality</strong>, <strong>catering</strong>, and <strong>food supply
            services</strong> across <strong>Saudi Arabia</strong>.</p>

</header>

<section id="about">
    <h2>Who We Are & Our Presence</h2>

    <p>
        Since its establishment in <strong>2017</strong>, Jawad Al-Diyafa Trading has specialized in
        <strong>catering</strong> and <strong>food supply services</strong>, becoming one of the leading entities in
        this field within the <strong>Kingdom of Saudi Arabia</strong>.
    </p>

    <p>
        We take pride in offering comprehensive solutions that meet the needs of both governmental and private sectors,
        having served over <strong>50 entities</strong> with quality, efficiency, and reliability at the core of our
        operations.
    </p>

    <p>
        We are proud to have <strong>14 branches</strong> across <strong>12 cities</strong> in the Kingdom, enhancing
        our ability to deliver our services efficiently and swiftly.
    </p>

    <p>
        We import products from a select group of trusted and reputable suppliers around the world, ensuring
        <strong>certified quality</strong> and maintaining a strict commitment to the highest standards of <strong>food
            safety</strong>, sustainability, and compliance with local and international regulations—ensuring the
        delivery of products and services that meet the expectations of the Saudi market and contribute to its
        <strong>food security</strong>.
    </p>
</section>


<!-- LOGISTICS -->
<section id="logistics">
    <h2>Our Logistics System</h2>

    <p>
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
            Refrigerated and frozen trucks compliant with the highest European standards for food transportation.
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

    <img src="/aljawad/Img/sfda-logo.jpg" class="license-logo" alt="SFDA">
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


<?php include 'footer.php'; ?>