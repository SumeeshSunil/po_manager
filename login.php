<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if ($password === $user['password']) {

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "User not found";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PO Manager Login</title>

    <link rel="icon" type="image/svg+xml" href="assets/images/favicon.svg">
    <link rel="shortcut icon" href="assets/images/favicon.svg">

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/three@0.160.0/build/three.min.js"></script>

    <style>
        /* REPLACE your entire <style> section with this */

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            min-height: 100vh;
            overflow: hidden;

            display: flex;
            align-items: center;
            justify-content: center;

            background:
                linear-gradient(135deg, #f4f7fb, #eef3ff);
        }

        /* WEBGL CANVAS */

        #webgl-bg {
            position: fixed;
            inset: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        /* SOFT WHITE OVERLAY */

        .bg-overlay {
            position: fixed;
            inset: 0;
            z-index: 1;

            background:
                radial-gradient(circle at top left,
                    rgba(0, 119, 255, 0.08),
                    transparent 30%),

                radial-gradient(circle at bottom right,
                    rgba(0, 180, 255, 0.08),
                    transparent 30%),

                linear-gradient(to bottom,
                    rgba(255, 255, 255, 0.45),
                    rgba(255, 255, 255, 0.25));

            pointer-events: none;
        }

        /* MAIN CONTAINER */

        .login-shell {
            width: 100%;
            max-width: 450px;
            padding: 24px;

            position: relative;
            z-index: 2;
        }

        /* BRAND */

        .login-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;

            margin-bottom: 24px;
        }

        .login-brand-icon {
            width: 60px;
            height: 60px;

            border-radius: 18px;

            background: rgba(255, 255, 255, 0.7);

            backdrop-filter: blur(12px);

            border: 1px solid rgba(255, 255, 255, 0.6);

            display: flex;
            align-items: center;
            justify-content: center;

            box-shadow:
                0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .login-brand-icon svg {
            width: 28px;
            height: 28px;

            stroke: #2563eb;
            fill: none;
            stroke-width: 2;
        }

        .login-brand-text h1 {
            font-size: 32px;
            font-weight: 700;

            color: #1e293b;

            letter-spacing: -1px;
        }

        .login-brand-text p {
            margin-top: 4px;

            color: #64748b;
            font-size: 13px;
        }

        /* CARD */

        .login-card {

            background: rgba(255, 255, 255, 0.72);

            backdrop-filter: blur(18px);

            border: 1px solid rgba(255, 255, 255, 0.6);

            border-radius: 28px;

            overflow: hidden;

            box-shadow:
                0 20px 50px rgba(15, 23, 42, 0.08);
        }

        .login-card-head {

            padding: 22px 24px;

            border-bottom: 1px solid rgba(226, 232, 240, 0.8);

            display: flex;
            align-items: center;
            gap: 10px;
        }

        .login-card-head .dot {

            width: 10px;
            height: 10px;

            border-radius: 50%;

            background: #2563eb;
        }

        .login-card-head span {

            color: #1e293b;

            font-weight: 600;
            font-size: 15px;
        }

        .login-card-body {
            padding: 28px;
        }

        /* ERROR */

        .error-box {

            margin-bottom: 18px;

            padding: 13px 14px;

            border-radius: 14px;

            background: #fff1f2;

            border: 1px solid #fecdd3;

            color: #e11d48;

            font-size: 13px;
            font-weight: 500;
        }

        /* INPUTS */

        .field-group {
            margin-bottom: 18px;
        }

        .field-group label {

            display: block;

            margin-bottom: 8px;

            color: #334155;

            font-size: 13px;
            font-weight: 600;
        }

        .field-group input {

            width: 100%;
            height: 54px;

            border: 1px solid #dbe4f0;

            border-radius: 14px;

            background: rgba(255, 255, 255, 0.8);

            padding: 0 16px;

            color: #0f172a;

            font-size: 14px;

            outline: none;

            transition: all .25s ease;
        }

        .field-group input::placeholder {
            color: #94a3b8;
        }

        .field-group input:focus {

            border-color: #3b82f6;

            background: #fff;

            box-shadow:
                0 0 0 4px rgba(59, 130, 246, 0.12);
        }

        /* BUTTON */

        .login-btn {

            width: 100%;
            height: 54px;

            border: none;

            border-radius: 14px;

            background:
                linear-gradient(135deg, #3b82f6, #2563eb);

            color: white;

            font-size: 15px;
            font-weight: 700;

            cursor: pointer;

            transition: all .25s ease;

            margin-top: 8px;

            box-shadow:
                0 10px 20px rgba(37, 99, 235, 0.18);
        }

        .login-btn:hover {

            transform: translateY(-2px);

            box-shadow:
                0 14px 28px rgba(37, 99, 235, 0.25);
        }

        /* FOOTER */

        .login-footer {

            margin-top: 18px;

            text-align: center;

            color: #64748b;

            font-size: 12px;
        }

        /* MOBILE */

        @media(max-width:520px) {

            .login-shell {
                padding: 18px;
            }

            .login-brand {
                justify-content: flex-start;
            }

            .login-brand-text h1 {
                font-size: 26px;
            }

            .login-card-body {
                padding: 22px;
            }
        }
    </style>
</head>

<body>

    <canvas id="webgl-bg"></canvas>

    <div class="bg-overlay"></div>

    <div class="login-shell">

        <div class="login-brand">

            <div class="login-brand-icon">
                <svg viewBox="0 0 24 24">
                    <path d="M12 3l8 4v5c0 5-3.5 8-8 9-4.5-1-8-4-8-9V7l8-4z"></path>
                    <path d="M9 12l2 2 4-4"></path>
                </svg>
            </div>

            <div class="login-brand-text">
                <h1>PO Manager</h1>
                <p>Secure dashboard access</p>
            </div>

        </div>

        <div class="login-card">

            <div class="login-card-head">
                <div class="dot"></div>
                <span>Login</span>
            </div>

            <div class="login-card-body">

                <?php if (isset($error)): ?>
                    <div class="error-box">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST">

                    <div class="field-group">
                        <label>Username</label>
                        <input
                            type="text"
                            name="username"
                            required
                            placeholder="Enter your username">
                    </div>

                    <div class="field-group">
                        <label>Password</label>
                        <input
                            type="password"
                            name="password"
                            required
                            placeholder="Enter your password">
                    </div>

                    <button type="submit" class="login-btn">
                        Login
                    </button>

                </form>

                <div class="login-footer">
                    Authorized users only
                </div>

            </div>

        </div>

    </div>

    <script>
        const canvas = document.getElementById('webgl-bg');

        const scene = new THREE.Scene();

        const camera = new THREE.PerspectiveCamera(
            75,
            window.innerWidth / window.innerHeight,
            0.1,
            1000
        );

        const renderer = new THREE.WebGLRenderer({
            canvas: canvas,
            antialias: true,
            alpha: true
        });

        renderer.setSize(window.innerWidth, window.innerHeight);

        renderer.setPixelRatio(window.devicePixelRatio);

        camera.position.z = 30;

        // PARTICLES

        const particlesGeometry = new THREE.BufferGeometry();

        const particlesCount = 2500;

        const posArray = new Float32Array(particlesCount * 3);

        for (let i = 0; i < particlesCount * 3; i++) {

            posArray[i] = (Math.random() - 0.5) * 120;

        }

        particlesGeometry.setAttribute(
            'position',
            new THREE.BufferAttribute(posArray, 3)
        );

        const particlesMaterial = new THREE.PointsMaterial({
            size: 0.18,
            color: 0x60a5fa, // soft blue instead of neon cyan
            transparent: true,
            opacity: 0.7
        });

        const particlesMesh = new THREE.Points(
            particlesGeometry,
            particlesMaterial
        );

        scene.add(particlesMesh);

        // MOUSE EFFECT

        let mouseX = 0;
        let mouseY = 0;

        document.addEventListener('mousemove', (event) => {

            mouseX = (event.clientX / window.innerWidth) - 0.5;
            mouseY = (event.clientY / window.innerHeight) - 0.5;

        });

        // ANIMATION

        const clock = new THREE.Clock();

        const animate = () => {

            requestAnimationFrame(animate);

            const elapsedTime = clock.getElapsedTime();

            particlesMesh.rotation.y = elapsedTime * 0.03;
            particlesMesh.rotation.x = elapsedTime * 0.015;

            camera.position.x += (mouseX * 8 - camera.position.x) * 0.03;
            camera.position.y += (-mouseY * 8 - camera.position.y) * 0.03;

            camera.lookAt(scene.position);

            renderer.render(scene, camera);

        };

        animate();

        // RESIZE

        window.addEventListener('resize', () => {

            camera.aspect = window.innerWidth / window.innerHeight;

            camera.updateProjectionMatrix();

            renderer.setSize(
                window.innerWidth,
                window.innerHeight
            );

        });
    </script>

</body>

</html>