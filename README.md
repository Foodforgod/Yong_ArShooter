# Web-Based AR Shooter Game (AR Game Shooter)

A complete, high-performance, responsive **Web-Based AR Shooter Game** built from scratch using PHP 8.3+, Vanilla JavaScript, Three.js, and MindAR Image Tracking. This project allows users to scan a custom printed or displayed image target with their smartphone's rear camera to play an immersive first-person AR shooting game complete with waves, animations, enemy scaling, shooting mechanics, procedural audio, and customizable GLB models.

---

## 🚀 Features

* **WebAR Image Tracking:** Powered by MindAR and Three.js for stable image-based AR tracking.
* **First-Person Shooter Mechanics:** Crosshair aiming, raycasting hit detection, ammo system, reload controls, recoil, muzzle flashes, and procedural audio effects via the Web Audio API.
* **Dynamic Enemy Waves:** Automatic scaling of enemy speed and health per wave, with support for fixed or randomized GLB enemy models and bounding-box auto-scaling.
* **Custom Target Management:** Built-in target image upload (JPG, PNG, WEBP) and an in-browser MindAR target compiler (`.mind` generation) requiring no Node.js.
* **Model Management & Uploads:** Secure GLB model uploading and configuration for both enemies and weapons, stored via JSON backends.
* **Responsive Mobile Design:** Designed specifically for mobile browsers (Android Chrome, iOS Safari) with touch-optimized overlay buttons, secure fullscreen locking, and responsive tactical UI.
* **Automatic LAN & Public URL Detection:** QR code generation pointing directly to your local or public game instance.

---

## 💻 System Requirements

* **PHP:** Version 8.3 or higher (Native PHP, no framework required).
* **Web Server:** Apache (XAMPP for local development) or cPanel shared hosting.
* **Browser:** Modern mobile or desktop browser (Google Chrome recommended).
* **HTTPS:** Required for mobile camera access on public servers (localhost is exempted for testing).

---

## 📦 Project Structure

```text
AR-Shooter/
│
├── index.php
├── game.php
├── models.php
├── compile-target.php
├── upload-target.php
├── save-target.php
├── save-site.php
│
├── api/
│   ├── models.php
│   ├── models_lib.php
│   └── site_lib.php
│
├── assets/
│   ├── css/
│   │   └── game.css
│   │
│   ├── js/
│   │   ├── main.js
│   │   ├── game.js
│   │   ├── enemy.js
│   │   ├── weapon.js
│   │   ├── effects.js
│   │   └── audio.js
│   │
│   ├── models/
│   │   ├── enemy GLB files
│   │   ├── weapon GLB files
│   │   └── uploaded GLB files
│   │
│   ├── targets/
│   │   ├── picture.jpg
│   │   └── targets.mind
│   │
│   └── config/
│       ├── models.json
│       └── site.json
│
└── README.md
🛠️ Installation & Local Development (XAMPP)
Clone or Copy the project folder into your XAMPP htdocs directory (e.g., C:/xampp/htdocs/AR-Shooter/).

Start Apache from your XAMPP Control Panel (ensure PHP 8.3+ is active).

Open the Home Page in your browser:

Plaintext
http://localhost/AR-Shooter/index.php
Upload a Target Image: Go to the target section, upload a high-contrast image (JPG/PNG), and click Compile Target to generate the required targets.mind file.

Play: Scan the generated QR code with your smartphone connected to the same Wi-Fi network, or click Start Game for desktop testing.

🌐 cPanel Deployment
Compress your project folder into a .zip archive.

Log in to your cPanel account and open the File Manager.

Navigate to your target directory (usually public_html/ar-shooter/) and upload the .zip archive.

Extract the files directly into the directory.

Ensure that PHP version 8.3+ is selected via cPanel's Select PHP Version tool.

Verify write permissions (755 or 644) for the assets/config/ and assets/targets/ directories so configuration and compiled targets save properly.

Access your live website via https://yourdomain.com/ar-shooter/ (HTTPS is mandatory for mobile browser camera access).

📱 Camera & Mobile Testing Requirements
HTTPS Protocol: Modern browsers restrict camera hardware access on insecure HTTP connections. Always use https:// on production/cPanel servers (local testing on http://localhost is natively permitted by browsers).

Target Display: Do not display the target image on the same smartphone you are using to play the game. Display the target on a computer monitor, tablet, or print it out on paper.

Lighting: Ensure adequate room lighting so MindAR can reliably lock onto and track the image target.