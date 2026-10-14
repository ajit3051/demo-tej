<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expandable Modal Form</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Trigger Button */
        .open-modal-btn {
            padding: 15px 30px;
            font-size: 18px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }

        .open-modal-btn:hover {
            background: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }

        /* Modal Overlay */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            backdrop-filter: blur(5px);
            transition: all 0.3s ease;
        }

        .modal.active {
            display: block;
        }

        /* Modal Content - Default State */
        .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 40px;
            border-radius: 15px;
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }

        /* Modal Content - Full Screen State */
        .modal.fullscreen .modal-content {
            width: 100%;
            height: 120%;
            max-width: none;
            max-height: none;
            border-radius: 0;
            top: 0;
            left: 0;
            transform: none;
            padding: 60px;
        }

        /* Close Button */
        .close-btn {
            position: absolute;
            top: 20px;
            right: 25px;
            font-size: 35px;
            font-weight: bold;
            color: #666;
            cursor: pointer;
            transition: color 0.3s;
            z-index: 1001;
        }

        .close-btn:hover {
            color: #000;
        }

        /* Full Screen Toggle Button */
        .fullscreen-toggle {
            position: absolute;
            top: 20px;
            right: 80px;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #666;
            transition: color 0.3s;
            z-index: 1001;
        }

        .fullscreen-toggle:hover {
            color: #000;
        }

        /* Form Header */
        .modal h2 {
            margin-bottom: 30px;
            color: #333;
            font-size: 28px;
            text-align: center;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 15px;
        }

        /* Form Groups */
        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #4CAF50;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        /* Checkbox and Radio Styles */
        .checkbox-group,
        .radio-group {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .checkbox-group label,
        .radio-group label {
            display: flex;
            align-items: center;
            gap: 5px;
            font-weight: normal;
            text-transform: none;
            cursor: pointer;
        }

        .checkbox-group input[type="checkbox"],
        .radio-group input[type="radio"] {
            width: auto;
            margin-right: 5px;
        }

        /* Submit Button */
        .submit-btn {
            width: 100%;
            padding: 15px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 20px;
        }

        .submit-btn:hover {
            background: #45a049;
        }

        /* Success Message */
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
            text-align: center;
        }

        /* Error Message */
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
            text-align: center;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .modal.fullscreen .modal-content {
                padding: 40px 20px;
            }
            
            .close-btn {
                top: 10px;
                right: 15px;
                font-size: 30px;
            }
            
            .fullscreen-toggle {
                top: 10px;
                right: 60px;
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <button class="open-modal-btn" onclick="openModal()">Open Registration Form</button>

    <?php
    // Initialize variables
    $showModal = false;
    $success = false;
    $error = false;
    $formData = [];

    // Process form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_form'])) {
        $showModal = true;
        
        // Validate inputs
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $country = $_POST['country'] ?? '';
        $gender = $_POST['gender'] ?? '';
        $interests = $_POST['interests'] ?? [];
        $message = trim($_POST['message'] ?? '');

        // Basic validation
        $errors = [];
        
        if (empty($name)) {
            $errors[] = "Name is required";
        }
        
        if (empty($email)) {
            $errors[] = "Email is required";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format";
        }
        
        if (empty($phone)) {
            $errors[] = "Phone number is required";
        }
        
        if (empty($country)) {
            $errors[] = "Please select a country";
        }
        
        if (empty($gender)) {
            $errors[] = "Please select your gender";
        }
        
        if (empty($message)) {
            $errors[] = "Message is required";
        }

        if (empty($errors)) {
            // Process the form (save to database, send email, etc.)
            $success = true;
            
            // Here you would typically insert into database
            // For demonstration, we'll just store the data
            $formData = [
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'country' => $country,
                'gender' => $gender,
                'interests' => $interests,
                'message' => $message
            ];
            
            // You can add database insertion code here
            /*
            $conn = new mysqli("localhost", "username", "password", "database");
            $stmt = $conn->prepare("INSERT INTO users (name, email, phone, country, gender, message) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $name, $email, $phone, $country, $gender, $message);
            $stmt->execute();
            $stmt->close();
            $conn->close();
            */
        } else {
            $error = true;
        }
    }
    ?>

    <!-- Modal -->
    <div id="myModal" class="modal <?php echo $showModal ? 'active' : ''; ?>">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <button class="fullscreen-toggle" onclick="toggleFullScreen()" title="Toggle Full Screen">
                ⛶
            </button>
            
            <h2>Registration Form</h2>
            
            <?php if ($success): ?>
                <div class="success-message">
                    <strong>Success!</strong> Your registration has been submitted successfully!<br>
                    <small>Thank you for registering, <?php echo htmlspecialchars($formData['name']); ?>!</small>
                </div>
                
                <?php if (!empty($formData)): ?>
                    <div style="background: #e9ecef; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                        <h3 style="margin-bottom: 10px;">Submitted Information:</h3>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($formData['name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($formData['email']); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($formData['phone']); ?></p>
                        <p><strong>Country:</strong> <?php echo htmlspecialchars($formData['country']); ?></p>
                        <p><strong>Gender:</strong> <?php echo htmlspecialchars($formData['gender']); ?></p>
                        <?php if (!empty($formData['interests'])): ?>
                            <p><strong>Interests:</strong> <?php echo htmlspecialchars(implode(', ', $formData['interests'])); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
            <?php elseif ($error && !empty($errors)): ?>
                <div class="error-message">
                    <strong>Error!</strong> Please fix the following issues:<br>
                    <?php foreach ($errors as $err): ?>
                        - <?php echo htmlspecialchars($err); ?><br>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="name">Full Name *</label>
                    <input type="text" id="name" name="name" required 
                           value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>"
                           placeholder="Enter your full name">
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" required 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                           placeholder="Enter your email">
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number *</label>
                    <input type="tel" id="phone" name="phone" required 
                           value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                           placeholder="Enter your phone number">
                </div>
                
                <div class="form-group">
                    <label for="country">Country *</label>
                    <select id="country" name="country" required>
                        <option value="">Select your country</option>
                        <option value="USA" <?php echo (isset($_POST['country']) && $_POST['country'] == 'USA') ? 'selected' : ''; ?>>United States</option>
                        <option value="Canada" <?php echo (isset($_POST['country']) && $_POST['country'] == 'Canada') ? 'selected' : ''; ?>>Canada</option>
                        <option value="UK" <?php echo (isset($_POST['country']) && $_POST['country'] == 'UK') ? 'selected' : ''; ?>>United Kingdom</option>
                        <option value="Australia" <?php echo (isset($_POST['country']) && $_POST['country'] == 'Australia') ? 'selected' : ''; ?>>Australia</option>
                        <option value="Germany" <?php echo (isset($_POST['country']) && $_POST['country'] == 'Germany') ? 'selected' : ''; ?>>Germany</option>
                        <option value="France" <?php echo (isset($_POST['country']) && $_POST['country'] == 'France') ? 'selected' : ''; ?>>France</option>
                        <option value="Japan" <?php echo (isset($_POST['country']) && $_POST['country'] == 'Japan') ? 'selected' : ''; ?>>Japan</option>
                        <option value="Brazil" <?php echo (isset($_POST['country']) && $_POST['country'] == 'Brazil') ? 'selected' : ''; ?>>Brazil</option>
                        <option value="India" <?php echo (isset($_POST['country']) && $_POST['country'] == 'India') ? 'selected' : ''; ?>>India</option>
                        <option value="Other" <?php echo (isset($_POST['country']) && $_POST['country'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Gender *</label>
                    <div class="radio-group">
                        <label>
                            <input type="radio" name="gender" value="Male" required
                                   <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Male') ? 'checked' : ''; ?>> Male
                        </label>
                        <label>
                            <input type="radio" name="gender" value="Female" required
                                   <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Female') ? 'checked' : ''; ?>> Female
                        </label>
                        <label>
                            <input type="radio" name="gender" value="Other" required
                                   <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Other') ? 'checked' : ''; ?>> Other
                        </label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Interests (Optional)</label>
                    <div class="checkbox-group">
                        <label>
                            <input type="checkbox" name="interests[]" value="Technology"
                                   <?php echo (isset($_POST['interests']) && in_array('Technology', $_POST['interests'])) ? 'checked' : ''; ?>> Technology
                        </label>
                        <label>
                            <input type="checkbox" name="interests[]" value="Sports"
                                   <?php echo (isset($_POST['interests']) && in_array('Sports', $_POST['interests'])) ? 'checked' : ''; ?>> Sports
                        </label>
                        <label>
                            <input type="checkbox" name="interests[]" value="Music"
                                   <?php echo (isset($_POST['interests']) && in_array('Music', $_POST['interests'])) ? 'checked' : ''; ?>> Music
                        </label>
                        <label>
                            <input type="checkbox" name="interests[]" value="Art"
                                   <?php echo (isset($_POST['interests']) && in_array('Art', $_POST['interests'])) ? 'checked' : ''; ?>> Art
                        </label>
                        <label>
                            <input type="checkbox" name="interests[]" value="Travel"
                                   <?php echo (isset($_POST['interests']) && in_array('Travel', $_POST['interests'])) ? 'checked' : ''; ?>> Travel
                        </label>
                        <label>
                            <input type="checkbox" name="interests[]" value="Food"
                                   <?php echo (isset($_POST['interests']) && in_array('Food', $_POST['interests'])) ? 'checked' : ''; ?>> Food
                        </label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="message">Message *</label>
                    <textarea id="message" name="message" required 
                              placeholder="Enter your message here..."><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                </div>
                
                <button type="submit" name="submit_form" class="submit-btn">Submit Registration</button>
            </form>
            
            <div style="margin-top: 20px; text-align: center; color: #999; font-size: 12px;">
                * Required fields
            </div>
        </div>
    </div>

    <script>
        // Function to open modal
        function openModal() {
            document.getElementById('myModal').classList.add('active');
        }

        // Function to close modal
        function closeModal() {
            document.getElementById('myModal').classList.remove('active');
            // Remove fullscreen class when closing
            document.getElementById('myModal').classList.remove('fullscreen');
        }

        // Function to toggle full screen
        function toggleFullScreen() {
            document.getElementById('myModal').classList.toggle('fullscreen');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('myModal');
            if (event.target == modal) {
                closeModal();
            }
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const modal = document.getElementById('myModal');
                if (modal.classList.contains('active')) {
                    closeModal();
                }
            }
        });

        // Optional: Auto-open modal if there was a form submission
        <?php if ($showModal && !$success): ?>
        window.onload = function() {
            openModal();
        };
        <?php endif; ?>

        // Auto-hide success message after 5 seconds (optional)
        <?php if ($success): ?>
        setTimeout(function() {
            const successMsg = document.querySelector('.success-message');
            if (successMsg) {
                successMsg.style.transition = 'opacity 0.5s';
                successMsg.style.opacity = '0';
                setTimeout(function() {
                    successMsg.style.display = 'none';
                }, 500);
            }
        }, 5000);
        <?php endif; ?>
    </script>
</body>
</html>