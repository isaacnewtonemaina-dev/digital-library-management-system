<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stone Haven | Advanced Digital Library</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        /* 1. CORE STYLES & VARIABLES */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; scroll-behavior: smooth; }
        :root {
            --primary: #ef4444;
            --dark: #0f172a;
            --slate: #1e293b;
            --text-muted: #94a3b8;
            --glass-bg: rgba(255, 255, 255, 0.1);
        }
        body { background: #f8fafc; color: var(--slate); overflow-x: hidden; }

        /* 2. NAVIGATION */
        nav {
            display: flex; justify-content: space-between; align-items: center;
            padding: 15px 8%; background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px); box-shadow: 0 4px 30px rgba(0,0,0,0.05);
            position: sticky; top: 0; z-index: 2000;
        }
        .logo h2 { color: var(--primary); font-weight: 800; letter-spacing: -1.5px; cursor: pointer; }
        
        .nav-links { display: flex; gap: 35px; list-style: none; align-items: center; }
        .nav-links a { text-decoration: none; color: var(--slate); font-weight: 600; transition: 0.3s; font-size: 0.9rem; }
        .nav-links a:hover { color: var(--primary); }

        .dropdown { position: relative; display: inline-block; padding: 10px 0; }
        .drop-btn { cursor: pointer; display: flex; align-items: center; gap: 8px; font-weight: 600; color: var(--slate); font-size: 0.9rem; }
        .dropdown:hover .drop-btn { color: var(--primary); }

        .dropdown-content {
            display: none; position: absolute; top: 100%; left: 50%;
            transform: translateX(-50%); background: white;
            min-width: 220px; box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            border-radius: 15px; padding: 12px; border: 1px solid rgba(0,0,0,0.05);
            animation: slideUp 0.3s ease;
        }
        .dropdown:hover .dropdown-content { display: block; }

        .dropdown-content a {
            padding: 12px 15px; display: flex; align-items: center; gap: 12px;
            color: var(--slate); font-size: 0.85rem; border-radius: 10px; transition: 0.2s;
        }
        .dropdown-content a i { width: 20px; color: var(--primary); font-size: 1rem; }
        .dropdown-content a:hover { background: #fff1f2; color: var(--primary); transform: translateX(5px); }

        /* 3. HERO SECTION */
        .hero {
            height: 90vh; display: flex; align-items: center; padding: 0 8%;
            background: linear-gradient(rgba(15, 23, 42, 0.65), rgba(15, 23, 42, 0.65)), 
                        url('images/shutter.jpg') no-repeat center center;
            background-size: cover; background-attachment: fixed; color: white;
        }

        .hero-glass {
            background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(20px);
            padding: 60px; border-radius: 35px; border: 1px solid rgba(255, 255, 255, 0.15);
            max-width: 750px; animation: slideUp 0.8s ease;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .hero-glass h1 { font-size: 4rem; line-height: 1.1; margin-bottom: 25px; font-weight: 800; }
        .hero-glass p { font-size: 1.2rem; color: #e2e8f0; margin-bottom: 40px; line-height: 1.6; }

        .btn-main {
            background: var(--primary); color: white; padding: 18px 45px;
            border-radius: 50px; text-decoration: none; font-weight: 700;
            transition: 0.4s; display: inline-flex; align-items: center; gap: 12px;
            box-shadow: 0 10px 25px rgba(239, 68, 68, 0.4);
        }
        .btn-main:hover { background: #dc2626; transform: translateY(-5px); box-shadow: 0 15px 30px rgba(239, 68, 68, 0.5); }

        /* 4. ADVANCED BACKGROUND FOR CURATED COLLECTIONS */
        .premium-bottom {
            background: linear-gradient(rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.95)), 
                        url('images/shutter.jpg') no-repeat center center;
            background-size: cover; 
            background-attachment: fixed; /* Parallax effect */
            padding: 120px 0; 
            position: relative; 
            overflow: hidden;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .book-marquee {
            display: flex; overflow: hidden; user-select: none; gap: 40px; padding: 60px 0;
            mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
            -webkit-mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
        }

        .marquee-content { display: flex; gap: 40px; animation: scroll 35s linear infinite; }
        .book-marquee:hover .marquee-content { animation-play-state: paused; }

        /* Enhancing the glass effect on the cards */
        .book-item {
            width: 200px; 
            height: 290px; 
            background: rgba(255, 255, 255, 0.07); 
            backdrop-filter: blur(15px); 
            -webkit-backdrop-filter: blur(15px);
            border-radius: 20px; 
            flex-shrink: 0; 
            display: flex; 
            flex-direction: column; 
            align-items: center; 
            justify-content: center;
            border: 1px solid rgba(255, 255, 255, 0.15); 
            color: white;
            transition: 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
            cursor: pointer; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            position: relative; overflow: hidden;
        }

        .book-item h4 {
            margin-top: 15px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-size: 0.8rem;
        }

        .book-item i {
            opacity: 0.8;
            transition: 0.3s;
        }

        .book-item:hover {
            transform: scale(1.08) translateY(-20px) rotate(4deg);
            border-color: rgba(239, 68, 68, 0.6);
            box-shadow: 0 30px 60px rgba(239, 68, 68, 0.3);
        }

        .book-item:hover i {
            transform: scale(1.2);
            opacity: 1;
            color: var(--primary);
        }

        /* 5. MODAL STYLING */
        .modal-overlay {
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.9); z-index: 3000; backdrop-filter: blur(8px);
        }
        .modal-glass {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
            width: 90%; max-width: 600px; background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1); border-radius: 30px; padding: 40px; color: white;
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
        }
        .close-modal { position: absolute; top: 20px; right: 30px; font-size: 2rem; cursor: pointer; color: #ef4444; }
        .mini-card { background: rgba(255,255,255,0.05); padding: 15px; border-radius: 10px; margin-bottom: 10px; }
        
        .btn-glow {
            background: var(--primary); color: white; padding: 12px 25px; border-radius: 50px;
            text-decoration: none; font-weight: 600; transition: 0.3s; box-shadow: 0 0 15px rgba(239, 68, 68, 0.4);
        }

        @keyframes scroll { from { transform: translateX(0); } to { transform: translateX(-50%); } }
        @keyframes slideUp { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: translateY(0); } }

        footer { background: var(--dark); color: var(--text-muted); padding: 80px 8% 40px; text-align: center; }
        .footer-logo { font-size: 1.8rem; color: white; margin-bottom: 25px; font-weight: 800; }
    </style>
</head>
<body>

    <nav>
        <div class="logo"><h2>Stone Haven</h2></div>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li class="dropdown">
                <span class="drop-btn">Library Services <i class="fas fa-chevron-down" style="font-size: 0.7rem;"></i></span>
                <div class="dropdown-content">
                    <a href="books.php"><i class="fas fa-search"></i> Browse Books</a>
                    <a href="dashboard.php"><i class="fas fa-user-circle"></i> My Account</a>
                    <a href="tools.php"><i class="fas fa-tools"></i> Library Tools</a>
                    <a href="reports.php"><i class="fas fa-chart-pie"></i> System Reports</a>
                    <hr style="margin: 8px 0; border: 0; border-top: 1px solid #eee;">
                    <a href="help.php"><i class="fas fa-question-circle"></i> Get Help</a>
                </div>
            </li>
            <li><a href="about.php">Our Story</a></li>
            <li><a href="login.php" style="background: var(--primary); color: white; padding: 12px 30px; border-radius: 50px;">Member Login</a></li>
        </ul>
    </nav>

    <section class="hero">
        <div class="hero-glass">
            <h1>Unlock a World of Knowledge</h1>
            <p>Step into the next generation of digital learning. Stone Haven provides instant access to premium resources and smart borrowing tracking.</p>
            <a href="login.php" class="btn-main">Start Your Journey <i class="fas fa-bolt"></i></a>
        </div>
    </section>

    <section class="premium-bottom">
        <div style="text-align: center; margin-bottom: 40px; position: relative; z-index: 2;">
            <h2 style="color: white; font-size: 2.8rem; font-weight: 800; letter-spacing: -1px;">Curated Collections</h2>
            <p style="color: rgba(255,255,255,0.6); font-size: 1.1rem;">Explore hand-picked masterpieces from our vault.</p>
        </div>

        <div class="book-marquee">
            <div class="marquee-content">
                <div class="book-item" onclick="openCategory('Fiction')"><i class="fas fa-book-open fa-2x"></i><h4>Fiction</h4></div>
                <div class="book-item" onclick="openCategory('History')"><i class="fas fa-atlas fa-2x"></i><h4>History</h4></div>
                <div class="book-item" onclick="openCategory('Science')"><i class="fas fa-flask fa-2x"></i><h4>Science</h4></div>
                <div class="book-item" onclick="openCategory('Technology')"><i class="fas fa-laptop-code fa-2x"></i><h4>Technology</h4></div>
                <div class="book-item" onclick="openCategory('Design')"><i class="fas fa-palette fa-2x"></i><h4>Design</h4></div>
                <div class="book-item" onclick="openCategory('Fiction')"><i class="fas fa-book-open fa-2x"></i><h4>Fiction</h4></div>
                <div class="book-item" onclick="openCategory('History')"><i class="fas fa-atlas fa-2x"></i><h4>History</h4></div>
                <div class="book-item" onclick="openCategory('Science')"><i class="fas fa-flask fa-2x"></i><h4>Science</h4></div>
                <div class="book-item" onclick="openCategory('Technology')"><i class="fas fa-laptop-code fa-2x"></i><h4>Technology</h4></div>
                <div class="book-item" onclick="openCategory('Design')"><i class="fas fa-palette fa-2x"></i><h4>Design</h4></div>
            </div>
        </div>
    </section>

    <div id="categoryModal" class="modal-overlay">
        <div class="modal-glass">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <div class="modal-content">
                <h2 id="modalTitle">Category Name</h2>
                <p id="modalDesc">Explore our highly requested titles in this genre.</p>
                <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.1); margin: 20px 0;">
                <div class="preview-books">
                    <div class="mini-card"><i class="fas fa-star" style="color:#ef4444"></i> The Great Gatsby (Available)</div>
                    <div class="mini-card"><i class="fas fa-star" style="color:#ef4444"></i> 1984 - George Orwell</div>
                    <div class="mini-card"><i class="fas fa-clock" style="color:#eab308"></i> Brave New World (Reserved)</div>
                </div>
                <a href="books.php" class="btn-glow" style="display:inline-block; margin-top:20px;">View Full Catalog</a>
            </div>
        </div>
    </div>

    <footer>
        <div class="footer-logo">Stone Haven Library</div>
        <p>Providing the community with digital excellence and historical wisdom.</p>
        <p style="font-size: 0.85rem; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 30px; margin-top: 30px;">
            &copy; 2026 Stone Haven Library System. Powered by MySQL & PHP Logic.
        </p>
    </footer>

    <script>
        function openCategory(name) {
            document.getElementById('modalTitle').innerText = name;
            document.getElementById('categoryModal').style.display = 'block';
        }
        function closeModal() {
            document.getElementById('categoryModal').style.display = 'none';
        }
        window.onclick = function(event) {
            let modal = document.getElementById('categoryModal');
            if (event.target == modal) { closeModal(); }
        }
    </script>
</body>
</html>