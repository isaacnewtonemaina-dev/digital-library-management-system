<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Story | Stone Haven Library</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        
        body {
            background: #0f172a;
            color: white;
            line-height: 1.6;
        }

        /* 1. HERO SECTION */
        .about-hero {
            height: 60vh;
            background: linear-gradient(rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.8)), 
                        url('images/Library.jpg') no-repeat center center;
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 0 8%;
        }
        .about-hero h1 { font-size: 4rem; font-weight: 800; color: #ef4444; }

        /* 2. MISSION SECTION */
        .content-section { padding: 80px 15%; text-align: center; }
        .glass-box {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(15px);
            padding: 50px;
            border-radius: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .glass-box h2 { font-size: 2.2rem; margin-bottom: 20px; color: #ef4444; }
        .glass-box p { font-size: 1.1rem; color: #94a3b8; max-width: 800px; margin: 0 auto; }

        /* 3. TIMELINE LOGIC */
        .timeline {
            position: relative;
            max-width: 1000px;
            margin: 50px auto;
            padding: 40px 0;
        }
        .timeline::after {
            content: '';
            position: absolute;
            width: 4px;
            background: #ef4444;
            top: 0; bottom: 0; left: 50%;
            margin-left: -2px;
        }
        .container {
            padding: 10px 40px;
            position: relative;
            width: 50%;
        }
        .container::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            right: -10px;
            background-color: white;
            border: 4px solid #ef4444;
            top: 15px;
            border-radius: 50%;
            z-index: 1;
        }
        .left { left: 0; text-align: right; }
        .right { left: 50%; text-align: left; }
        .right::after { left: -10px; }

        .timeline-content {
            padding: 20px 30px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .back-btn {
            position: absolute; top: 30px; left: 8%;
            color: white; text-decoration: none; font-weight: 600;
            background: #ef4444; padding: 10px 20px; border-radius: 50px;
        }
    </style>
</head>
<body>

    <a href="index.php" class="back-btn"><i class="fas fa-arrow-left"></i> Home</a>

    <header class="about-hero">
        <div>
            <h1>Our Story</h1>
            <p style="color: #94a3b8; font-size: 1.2rem;">Preserving Knowledge, Powering the Future.</p>
        </div>
    </header>

    <section class="content-section">
        <div class="glass-box">
            <h2>The Vision</h2>
            <p>Stone Haven began with a simple question: How can we make a thousand years of human wisdom accessible in a single click? Our platform is the result of dedicated engineering aimed at bridging the gap between physical archives and digital convenience.</p>
        </div>
    </section>

    <div class="timeline">
        <div class="container left">
            <div class="timeline-content">
                <h3 style="color: #ef4444;">2024</h3>
                <p>The Stone Haven Foundation was established to digitize rare manuscripts.</p>
            </div>
        </div>
        <div class="container right">
            <div class="timeline-content">
                <h3 style="color: #ef4444;">2025</h3>
                <p>Launch of the Digital Library Core with support for SQL-driven cataloging.</p>
            </div>
        </div>
        <div class="container left">
            <div class="timeline-content">
                <h3 style="color: #ef4444;">2026</h3>
                <p>Integrated the advanced dashboard and real-time synchronization logic.</p>
            </div>
        </div>
    </div>

    <footer style="text-align: center; padding: 60px 0; color: #64748b; font-size: 0.9rem;">
        &copy; 2026 Stone Haven Library. Built with passion for knowledge.
    </footer>

</body>
</html>