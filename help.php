<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Help Center | Stone Haven</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { 
            background: #0f172a; color: white; font-family: 'Poppins', sans-serif;
            padding: 50px 8%; 
        }
        .help-container { max-width: 900px; margin: 0 auto; }
        
        /* Glassmorphism Search */
        .search-box {
            background: rgba(255,255,255,0.05); padding: 40px;
            border-radius: 20px; border: 1px solid rgba(255,255,255,0.1);
            text-align: center; margin-bottom: 40px;
        }
        input { 
            width: 80%; padding: 15px; border-radius: 10px; border: none; 
            outline: none; font-size: 1rem; 
        }

        /* FAQ Accordion Logic */
        .faq-item {
            background: rgba(255,255,255,0.05); margin-bottom: 10px;
            border-radius: 10px; overflow: hidden;
        }
        .faq-question {
            padding: 20px; cursor: pointer; display: flex;
            justify-content: space-between; font-weight: 600;
        }
        .faq-answer {
            padding: 0 20px 20px; color: #94a3b8; display: none;
        }
    </style>
</head>
<body>
    <div class="help-container">
        <a href="index.php" style="color: #ef4444; text-decoration: none;"><i class="fas fa-arrow-left"></i> Back</a>
        
        <div class="search-box">
            <h1>How can we help you?</h1>
            <input type="text" placeholder="Search for help topics...">
        </div>

        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                How do I borrow a book? <i class="fas fa-plus"></i>
            </div>
            <div class="faq-answer">
                Simply browse the catalog, click 'Borrow' on your chosen title, and pick it up at the front desk using your Digital ID.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                What is the loan period? <i class="fas fa-plus"></i>
            </div>
            <div class="faq-answer">
                Standard loans are for 14 days. You can renew items once through your 'My Account' dashboard.
            </div>
        </div>
    </div>

    <script>
        function toggleFaq(element) {
            const answer = element.nextElementSibling;
            answer.style.display = (answer.style.display === "block") ? "none" : "block";
        }
    </script>
</body>
</html>