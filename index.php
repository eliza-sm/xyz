<?php
require 'vendor/autoload.php';
use Aws\SecretsManager\SecretsManagerClient;
use Aws\Exception\AwsException;

$client = new SecretsManagerClient([
    'version' => 'latest',
    'region' => 'us-east-1a',
]);

$secret_name = 'rds!db-10887daf-23f8-4254-b519-5f5f1f8af99a';

try {
    $result = $client->getSecretValue([
        'SecretId' => $secret_name,
    ]);
} catch (AwsException $e) {
    // For a list of exceptions thrown, see
    // https://<<{{DocsDomain}}>>/secretsmanager/latest/apireference/API_GetSecretValue.html
    throw $e;
}
$secret = $result['SecretString'];
$secrets = json_decode( $secret, true );
// Database connection (you'll need to set up your database)
$db_host = 'themeparkdb.c2mkzpzryjtg.us-east-1.rds.amazonaws.com';
$db_user = $secrets['username'];
$db_pass = $secrets['password'];
$db_name = 'themeparkdb';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// // Handle consent form submission
// $consent_message = '';
// if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['visitor_name'])) {
//     $visitor_name = $_POST['visitor_name'] ?? '';
//     $age = $_POST['age'] ?? '';
//     $emergency_contact = $_POST['emergency_contact'] ?? '';
//     $medical_conditions = $_POST['medical_conditions'] ?? '';
//     $terms_agree = isset($_POST['terms_agree']) ? 1 : 0;
//     $liability_agree = isset($_POST['liability_agree']) ? 1 : 0;
    
//     if (!empty($visitor_name) && !empty($age) && !empty($emergency_contact)) {
//         $stmt = $conn->prepare("INSERT INTO visitor_consent (name, age, emergency_contact, medical_conditions, terms_agree, liability_agree) VALUES (?, ?, ?, ?, ?, ?)");
//         $stmt->bind_param("sisssi", $visitor_name, $age, $emergency_contact, $medical_conditions, $terms_agree, $liability_agree);
        
//         if ($stmt->execute()) {
//             $consent_message = "Thank you for submitting your consent form!";
//         } else {
//             $consent_message = "Sorry, there was an error submitting your consent form.";
//         }
//         $stmt->close();
//     }
// }

// // Handle form submission
// $message = '';
// if ($_SERVER["REQUEST_METHOD"] == "POST") {
//     $name = $_POST['name'] ?? '';
//     $email = $_POST['email'] ?? '';
//     $message_text = $_POST['message'] ?? '';
    
//     if (!empty($name) && !empty($email) && !empty($message_text)) {
//         $stmt = $conn->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
//         $stmt->bind_param("sss", $name, $email, $message_text);
        
//         if ($stmt->execute()) {
//             $message = "Thank you for your message! We'll get back to you soon.";
//         } else {
//             $message = "Sorry, there was an error sending your message.";
//         }
//         $stmt->close();
//     }
// }

// // Get attractions from database
// $attractions = [];
// $result = $conn->query("SELECT * FROM attractions");
// if ($result) {
//     while ($row = $result->fetch_assoc()) {
//         $attractions[] = $row;
//     }
// }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adventure World Theme Park</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            line-height: 1.6;
        }

        .header {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.unsplash.com/photo-1565800452-f2d14754b919?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
            padding: 0 20px;
        }

        .header h1 {
            font-size: 4rem;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }

        .header p {
            font-size: 1.5rem;
            margin-bottom: 30px;
        }

        .cta-button {
            display: inline-block;
            padding: 15px 30px;
            background-color: #ff6b6b;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-size: 1.2rem;
            transition: background-color 0.3s;
        }

        .cta-button:hover {
            background-color: #ff5252;
        }

        .attractions {
            padding: 80px 20px;
            background-color: #f9f9f9;
        }

        .attractions h2 {
            text-align: center;
            margin-bottom: 40px;
            font-size: 2.5rem;
            color: #333;
        }

        .attractions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .attraction-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }

        .attraction-card:hover {
            transform: translateY(-5px);
        }

        .attraction-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .attraction-card h3 {
            padding: 20px;
            color: #333;
        }

        .attraction-card p {
            padding: 0 20px 20px;
            color: #666;
        }

        .contact-form {
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .contact-form h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .form-group textarea {
            height: 150px;
        }

        .submit-btn {
            background-color: #ff6b6b;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        .submit-btn:hover {
            background-color: #ff5252;
        }

        .message {
            text-align: center;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
            background-color: #d4edda;
            color: #155724;
        }

        .footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 40px 20px;
        }

        .footer p {
            margin-bottom: 10px;
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2.5rem;
            }
            
            .header p {
                font-size: 1.2rem;
            }
        }

        .consent-form {
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .consent-form h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
        }

        .checkbox-group label {
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <header class="header">
        <h1>Adventure World Theme Park</h1>
        <p>Experience the thrill of a lifetime!</p>
        <a href="#attractions" class="cta-button">Explore Attractions</a>
    </header>

    <section id="attractions" class="attractions">
        <h2>Our Amazing Attractions</h2>
        <div class="attractions-grid">
            <?php if (!empty($attractions)): ?>
                <?php foreach ($attractions as $attraction): ?>
                    <div class="attraction-card">
                        <img src="<?php echo htmlspecialchars($attraction['image_url']); ?>" alt="<?php echo htmlspecialchars($attraction['name']); ?>">
                        <h3><?php echo htmlspecialchars($attraction['name']); ?></h3>
                        <p><?php echo htmlspecialchars($attraction['description']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="attraction-card">
                    <img src="../images/pic1.jpg" alt="Thunder Mountain">
                    <h3>Thunder Mountain</h3>
                    <p>Experience the ultimate thrill with our signature roller coaster featuring multiple loops and drops.</p>
                </div>
                <div class="attraction-card">
                    <img src="https://images.unsplash.com/photo-1565800452-f2d14754b919?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Splash World">
                    <h3>Splash World</h3>
                    <p>Cool off in our massive water park with slides, wave pools, and lazy rivers.</p>
                </div>
                <div class="attraction-card">
                    <img src="https://images.unsplash.com/photo-1565800452-f2d14754b919?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Family Fun Zone">
                    <h3>Family Fun Zone</h3>
                    <p>Perfect for the whole family with gentle rides and interactive attractions.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="consent-form">
        <h2>Visitor Consent Form</h2>
        <?php if ($consent_message): ?>
            <div class="message"><?php echo htmlspecialchars($consent_message); ?></div>
        <?php endif; ?>
        <form method="POST" action="" id="consentForm">
            <div class="form-group">
                <label for="visitor_name">Full Name:</label>
                <input type="text" id="visitor_name" name="visitor_name" required>
            </div>
            <div class="form-group">
                <label for="age">Age:</label>
                <input type="number" id="age" name="age" required min="0" max="120">
            </div>
            <div class="form-group">
                <label for="emergency_contact">Emergency Contact:</label>
                <input type="tel" id="emergency_contact" name="emergency_contact" required>
            </div>
            <div class="form-group">
                <label for="medical_conditions">Medical Conditions (if any):</label>
                <textarea id="medical_conditions" name="medical_conditions"></textarea>
            </div>
            <div class="form-group checkbox-group">
                <input type="checkbox" id="terms_agree" name="terms_agree" required>
                <label for="terms_agree">I agree to the park's terms and conditions</label>
            </div>
            <div class="form-group checkbox-group">
                <input type="checkbox" id="liability_agree" name="liability_agree" required>
                <label for="liability_agree">I understand and accept the liability waiver</label>
            </div>
            <button type="submit" class="submit-btn">Submit Consent Form</button>
        </form>
    </section>

    <section class="contact-form">
        <h2>Contact Us</h2>
        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="message">Message:</label>
                <textarea id="message" name="message" required></textarea>
            </div>
            <button type="submit" class="submit-btn">Send Message</button>
        </form>
    </section>

    <footer class="footer">
        <p>Adventure World Theme Park</p>
        <p>123 Park Avenue, Fun City, FC 12345</p>
        <p>Phone: (555) 123-4567</p>
        <p>Email: info@adventureworld.com</p>
    </footer>
</body>
</html>
<?php
$conn->close();
?> 
