Here is the complete, tutorial-style README.md file for your project, designed to serve as both documentation and a comprehensive step-by-step guide for developers, students, and server administrators.

Markdown
# Web-Based AR Shooter Game (AR Game Shooter)

A fully functional, professional **Web-Based AR Shooter Game** built from scratch using PHP 8.3+, Vanilla JavaScript, Three.js, and MindAR Image Tracking. This project enables users to scan a printed or displayed image target with their smartphone's rear camera to play a first-person augmented reality shooter complete with custom enemy waves, raycasting hit detection, animations, scoring, health management, procedural audio, and customizable GLB 3D models.

---

## 📋 Table of Contents
1. [Features](#-features)
2. [System Requirements](#-system-requirements)
3. [Project Directory Structure](#-project-directory-structure)
4. [Step 1: Local Installation & XAMPP Setup](#-step-1-local-installation--xampp-setup)
5. [Step 2: Target Upload & In-Browser Compilation](#-step-2-target-upload--in-browser-compilation)
6. [Step 3: Model Management & GLB Uploads](#-step-3-model-management--glb-uploads)
7. [Step 4: Playing the Game & Mobile Access](#-step-4-playing-the-game--mobile-access)
8. [Step 5: Production Deployment on cPanel](#-step-5-production-deployment-on-cpanel)
9. [Troubleshooting & FAQ](#-troubleshooting--faq)

---

## 🚀 Features

* **Real WebAR Image Tracking:** Powered by MindAR and Three.js for stable marker tracking.
* **First-Person Shooter Mechanics:** Crosshair aiming, raycasting hit detection, ammo tracking, reload mechanics, recoil, muzzle flashes, and procedural audio effects via the Web Audio API.
* **Dynamic Enemy Waves:** Automatic health and speed scaling per wave, supporting fixed or randomized GLB enemy models with dynamic bounding-box scaling.
* **In-Browser Target Compiler:** Upload target images (JPG, PNG, WEBP) and compile `.mind` files directly in the browser without needing Node.js.
* **Secure Model Management:** Upload `.glb` files with binary magic-number validation (`glTF`) to prevent unauthorized file execution.
* **Responsive Mobile Controls:** Designed for mobile browsers (Android Chrome, iOS Safari) with touch-optimized buttons and full-screen layout locks.

---

## 💻 System Requirements

* **PHP:** Version 8.3 or higher (Native PHP, no frameworks required).
* **Web Server:** Apache (XAMPP for local development, cPanel shared hosting for production).
* **Browser:** Google Chrome (Recommended for mobile and desktop testing).
* **HTTPS Protocol:** Mandatory on public servers for mobile camera hardware access (localhost is exempted during local development).

---

## 📦 Project Directory Structure

🛠️ Step 1: Local Installation & XAMPP Setup
<img width="1917" height="1140" alt="image" src="https://github.com/user-attachments/assets/7c362ff4-09cb-4e14-b3c6-a18022124d0b" />

Download & Place Files: Copy the entire AR-Shooter/ folder into your XAMPP installation directory:

Plaintext
C:/xampp/htdocs/AR-Shooter/
Start Apache: Open your XAMPP Control Panel and start the Apache service, ensuring PHP 8.3+ is enabled.

Open the Homepage: Launch your web browser and navigate to:

Plaintext
http://localhost/AR-Shooter/index.php
🎯 Step 2: Target Upload & In-Browser Compilation
<img width="1917" height="1142" alt="image" src="https://github.com/user-attachments/assets/eadeabd3-59e4-4fa8-8649-0a59437c4094" />

MindAR requires a compiled target file (targets.mind) to recognize your tracking image.

On the home page, navigate to the Target Section.

Upload a clear, high-contrast image (JPG, PNG, or WEBP up to 12 MB). The system will automatically process and save it as assets/targets/picture.jpg.

Click Compile Target. The browser will run the MindAR image compiler client-side, showing progress from 0% to 100%.

Once completed, the binary target data is automatically saved to assets/targets/targets.mind.

🦖 Step 3: Model Management & GLB Uploads
<img width="1917" height="1198" alt="image" src="https://github.com/user-attachments/assets/76dbb175-59e6-41c5-b4e2-380729a7347f" />

Navigate to the Models page (models.php) from the top navigation bar.

Choose between Random enemy selection or pick a specific .glb enemy model.

Choose your preferred FPS weapon rig (e.g., fps-akm.glb).

You can also upload new .glb models (up to 40 MB). The system validates the binary glTF file signature to ensure security.

Save your changes; configurations are instantly written to assets/config/models.json.

📱 Step 4: Playing the Game & Mobile Access
<img width="1916" height="1130" alt="image" src="https://github.com/user-attachments/assets/a7c155da-aef1-42b0-81d2-22ebf8e4dee0" />

Accessing via Smartphone:

On the home page, use the generated QR Code which points dynamically to your game URL (game.php). Ensure your phone is connected to the same Wi-Fi network as your XAMPP server.

Target Setup:

Display picture.jpg on a computer monitor, tablet, or print it out on paper. Do not display the target on the same phone you are using to play.

Gameplay Controls:

Camera Permission: Allow camera access when prompted.

Aiming: Center the white crosshair over the 3D enemy spawned on the tracked target.

Firing: Tap or hold the bottom-right FIRE button (or press Space/F / click on desktop).

Reloading: Tap the bottom-left RELOAD button (or press R on desktop) when ammo is low.

🌐 Step 5: Production Deployment on cPanel
Archive: Compress your project folder into a .zip file.

Upload: Log into cPanel, open the File Manager, navigate to your target public directory (e.g., public_html/ar-shooter/), and upload the .zip file.

Extract: Extract the contents directly into the folder.

PHP Version: Go to cPanel's Select PHP Version tool and ensure PHP 8.3 or higher is active.

Permissions: Ensure the assets/config/ and assets/targets/ folders have proper write permissions (755 or 644) so configurations and compiled targets save successfully.

Secure URL: Access your deployed game via https://yourdomain.com/ar-shooter/.

🔧 Troubleshooting & FAQ
Camera Access Denied / Black Screen: Modern browsers require an HTTPS connection to access mobile camera hardware (except on localhost). Ensure your production server uses a valid SSL certificate.

Target Not Detected ("TARGET LOST"): Make sure you are in a well-lit room, the target image is completely flat and visible in the camera frame, and you are not displaying the target on the gaming phone itself.

Model Loading Errors: Verify that uploaded .glb files are valid and not corrupted. Check the browser's Developer Console (F12) for specific JSON or fetch errors.
