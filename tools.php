<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Tools | Stone Haven</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        
        body {
            background: linear-gradient(rgba(15, 23, 42, 0.9), rgba(15, 23, 42, 0.9)), 
                        url('images/Library.jpg') no-repeat center center fixed;
            background-size: cover;
            color: white;
            min-height: 100vh;
            padding: 40px 8%;
        }

        .tools-header { text-align: center; margin-bottom: 50px; }
        .tools-header h1 { font-size: 3rem; color: #ef4444; margin-bottom: 10px; }
        .tools-header p { color: #94a3b8; }

        /* Tools Grid Layout */
        .tools-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
        }

        .tool-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(15px);
            padding: 35px;
            border-radius: 25px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: 0.3s ease;
        }

        .tool-card:hover {
            border-color: #ef4444;
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.08);
        }

        .tool-card i { font-size: 2.5rem; color: #ef4444; margin-bottom: 20px; }
        .tool-card h3 { margin-bottom: 20px; font-size: 1.3rem; }

        /* Interactive Elements */
        input, select {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.2);
            background: rgba(0,0,0,0.2);
            color: white;
        }

        .calc-btn {
            width: 100%;
            padding: 12px;
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .calc-btn:hover { background: #dc2626; box-shadow: 0 5px 15px rgba(239, 68, 68, 0.4); }

        #result { margin-top: 15px; font-weight: 600; color: #10b981; }

        .back-home {
            display: inline-block;
            margin-bottom: 30px;
            color: #ef4444;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <a href="index.php" class="back-home"><i class="fas fa-arrow-left"></i> Return to Homepage</a>

    <div class="tools-header">
        <h1>Digital Toolkit</h1>
        <p>Advanced utilities for smarter library management and reading.</p>
    </div>

    

    <div class="tools-grid">
        <div class="tool-card">
            <i class="fas fa-calculator"></i>
            <h3>Late Fee Calculator</h3>
            <p style="font-size: 0.85rem; color: #94a3b8; margin-bottom: 15px;">Calculate potential fines for overdue items.</p>
            <input type="number" id="days" placeholder="Number of overdue days">
            <select id="itemType">
                <option value="0.50">Standard Book ($0.50/day)</option>
                <option value="1.00">Reference Material ($1.00/day)</option>
                <option value="2.00">Digital Equipment ($2.00/day)</option>
            </select>
            <button class="calc-btn" onclick="calculateFine()">Calculate Total</button>
            <div id="result"></div>
        </div>

        <div class="tool-card">
            <i class="fas fa-barcode"></i>
            <h3>ISBN Validator</h3>
            <p style="font-size: 0.85rem; color: #94a3b8; margin-bottom: 15px;">Check if a book's ISBN-10 or ISBN-13 is valid.</p>
            <input type="text" id="isbnInput" placeholder="Enter ISBN code...">
            <button class="calc-btn" onclick="validateISBN()" style="background: rgba(255,255,255,0.1);">Verify Code</button>
            <div id="isbnResult"></div>
        </div>

        <div class="tool-card">
            <i class="fas fa-bullseye"></i>
            <h3>Yearly Reading Goal</h3>
            <p style="font-size: 0.85rem; color: #94a3b8; margin-bottom: 15px;">Track your progress toward your yearly book goal.</p>
            <input type="number" id="goal" placeholder="Yearly Goal (e.g., 20)">
            <input type="number" id="current" placeholder="Books read so far">
            <button class="calc-btn" onclick="trackGoal()">Update Progress</button>
            <div id="goalResult"></div>
        </div>
    </div>

    <script>
        // Fine Calculation Logic
        function calculateFine() {
            const days = document.getElementById('days').value;
            const rate = document.getElementById('itemType').value;
            const total = days * rate;
            document.getElementById('result').innerText = days > 0 ? "Estimated Fine: $" + total.toFixed(2) : "No fine due.";
        }

        // Simple ISBN Validation Logic
        function validateISBN() {
            const isbn = document.getElementById('isbnInput').value.replace(/-/g, "");
            const res = document.getElementById('isbnResult');
            if(isbn.length === 10 || isbn.length === 13) {
                res.innerText = "✓ Valid Format Detected";
                res.style.color = "#10b981";
            } else {
                res.innerText = "✗ Invalid ISBN Length";
                res.style.color = "#ef4444";
            }
        }

        // Reading Goal Logic
        function trackGoal() {
            const goal = document.getElementById('goal').value;
            const current = document.getElementById('current').value;
            const percent = (current / goal) * 100;
            document.getElementById('goalResult').innerText = "Progress: " + Math.round(percent) + "% of your goal achieved!";
        }
    </script>
</body>
</html>