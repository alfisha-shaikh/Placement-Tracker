<?php
session_start();

$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&amp;family=Inter:wght@400;500;600&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "on-tertiary-fixed": "#001d32",
                        "on-tertiary-fixed-variant": "#004b74",
                        "primary": "#00236f",
                        "background": "#f7f9fb",
                        "tertiary-fixed-dim": "#94ccff",
                        "on-secondary-fixed-variant": "#005049",
                        "on-error-container": "#93000a",
                        "surface-dim": "#d8dadc",
                        "primary-fixed": "#dce1ff",
                        "on-background": "#191c1e",
                        "surface-variant": "#e0e3e5",
                        "on-primary-fixed-variant": "#264191",
                        "on-surface": "#191c1e",
                        "primary-container": "#1e3a8a",
                        "inverse-surface": "#2d3133",
                        "surface-container-high": "#e6e8ea",
                        "on-secondary-fixed": "#00201d",
                        "tertiary-fixed": "#cde5ff",
                        "surface-container": "#eceef0",
                        "tertiary-container": "#00446b",
                        "surface-tint": "#4059aa",
                        "on-tertiary-container": "#6ab3ef",
                        "surface-bright": "#f7f9fb",
                        "on-tertiary": "#ffffff",
                        "on-primary": "#ffffff",
                        "on-surface-variant": "#444651",
                        "outline-variant": "#c5c5d3",
                        "surface-container-highest": "#e0e3e5",
                        "secondary-fixed-dim": "#6bd8cb",
                        "surface-container-lowest": "#ffffff",
                        "inverse-on-surface": "#eff1f3",
                        "on-primary-fixed": "#00164e",
                        "tertiary": "#002d49",
                        "secondary-container": "#86f2e4",
                        "on-secondary": "#ffffff",
                        "surface": "#f7f9fb",
                        "inverse-primary": "#b6c4ff",
                        "on-primary-container": "#90a8ff",
                        "primary-fixed-dim": "#b6c4ff",
                        "outline": "#757682",
                        "secondary": "#006a61",
                        "error": "#ba1a1a",
                        "secondary-fixed": "#89f5e7",
                        "surface-container-low": "#f2f4f6",
                        "on-error": "#ffffff",
                        "error-container": "#ffdad6",
                        "on-secondary-container": "#006f66"
                    },
                    fontFamily: {
                        "headline": ["Manrope"],
                        "body": ["Inter"],
                        "label": ["Inter"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
    <style>
        html {
            scroll-behavior: smooth;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
        }

        .primary-gradient {
            background: linear-gradient(135deg, #00236f 0%, #1e3a8a 100%);
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
    </style>
</head>

<body class="bg-surface font-body text-on-surface min-h-screen flex items-center justify-center p-6 selection:bg-secondary-container selection:text-on-secondary-container">
    <!-- Auth Container -->
    <main class="w-full max-w-5xl grid grid-cols-1 lg:grid-cols-2 bg-surface-container-lowest rounded-xl overflow-hidden shadow-sm">
        <!-- Branding & Illustration Side -->
        <section class="hidden lg:flex flex-col justify-between p-12 primary-gradient relative overflow-hidden">
            <!-- Decorative Elements -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-secondary/10 rounded-full -mr-20 -mt-20 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-tertiary-fixed-dim/5 rounded-full -ml-32 -mb-32 blur-3xl"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-12">

                    <span class="font-headline text-2xl font-extrabold text-white tracking-tighter">Placement Tracker</span>
                </div>
                <h1 class="font-headline text-4xl font-bold text-white mb-6 leading-tight">
                    Design your career with <span class="text-secondary-fixed">architectural precision.</span>
                </h1>
                <p class="text-white/80 text-lg max-w-md font-body leading-relaxed">
                    Manage your applications, track interview cycles, and secure your next role through our curated executive dashboard.
                </p>
            </div>
            <div class="relative z-10 mt-12 bg-white/5 p-6 rounded-xl border border-white/10 backdrop-blur-sm">
                <div class="flex items-center gap-4 mb-4">
                    <img alt="Success Story" class="w-12 h-12 rounded-full object-cover border-2 border-secondary" data-alt="professional woman in corporate attire smiling confidently, portrait with soft office background lighting" src="https://lh3.googleusercontent.com/aida-public/AB6AXuD0kBVnOAGnKITvatp1kQPBjyY6pR1xcdUP0sWbjFZxzEiRbh3TVVl0foxJQDywpKHC-Wk_jt9Ax-KFM-QAxg6n0hPlKTIMHuBxzCfD1FB-uIYO9eh-OtIBPFc4Pi92ydM3Xw3qx4yhqFPt1AHE0VZabZpw9X5qblI-nJwJqv3i2is9r5uBxYRcuNq45SU7Zv6Y_GZPWbkYdRYdYsH1qpY6AUjXRup5ptofYf149-vdxL1XXOyWalnxaaFtA0Po-6NDiA4oo5R-quA" />
                    <div>
                        <p class="text-white font-semibold text-sm">Elena Rodriguez</p>
                        <p class="text-white/60 text-xs">Placed at Global Arch Solutions</p>
                    </div>
                </div>
                <p class="text-white/90 italic text-sm">"The tracker turned my chaotic job hunt into a streamlined narrative. I felt in control of every step."</p>
            </div>
        </section>
        <!-- Form Side -->
        <section class="p-8 md:p-16 flex flex-col justify-center">
            <!-- Tab Toggle -->
            <div class="flex gap-8 mb-10">
                <button class="relative pb-2 font-headline font-bold text-lg text-primary transition-all" onclick="window.location.href='login.php'">
                    Login
                    <div class="absolute bottom-0 left-0 w-full h-1 bg-secondary rounded-full"></div>
                </button>
                <button class="pb-2 font-headline font-medium text-lg text-on-surface-variant hover:text-primary transition-all" onclick="window.location.href='register.php'">
                    Register
                </button>
            </div>
            <header class="mb-10">
                <h2 class="font-headline text-2xl font-bold text-on-surface tracking-tight mb-2">Welcome Back</h2>
                <p class="text-on-surface-variant font-body">Please enter your credentials to access your dashboard.</p>
            </header>
            <!-- Social Login (Optional Premium Feel) -->



            <form class="space-y-4" action="login_process.php" method="post">
                <!-- Field: Email -->
                <div class="space-y-2">
                    <label class="block font-label text-sm font-medium text-on-surface-variant" for="email">Email Address</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-on-surface-variant/50 group-focus-within:text-secondary transition-colors">
                            <span class="material-symbols-outlined text-[20px]" data-icon="mail">mail</span>
                        </div>
                        <input class="w-full pl-10 pr-4 py-3 bg-surface-container-low border-0 border-b-2 border-outline-variant/30 focus:border-secondary focus:ring-0 text-on-surface font-body transition-all placeholder:text-on-surface-variant/30 rounded-t-lg" id="email" name="email" placeholder="name@company.com" type="email" />
                    </div>
                </div>
                <!-- Field: Password -->
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <label class="block font-label text-sm font-medium text-on-surface-variant" for="password">Password</label>
                        <a class="text-xs font-semibold text-secondary hover:underline" href="#">Forgot password?</a>
                    </div>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-on-surface-variant/50 group-focus-within:text-secondary transition-colors">
                            <span class="material-symbols-outlined text-[20px]" data-icon="lock">lock</span>
                        </div>
                        <input class="w-full pl-10 pr-12 py-3 bg-surface-container-low border-0 border-b-2 border-outline-variant/30 focus:border-secondary focus:ring-0 text-on-surface font-body transition-all placeholder:text-on-surface-variant/30 rounded-t-lg" id="password" name="password" placeholder="••••••••" type="password" />
                        <button class="absolute inset-y-0 right-0 pr-3 flex items-center text-on-surface-variant/50 hover:text-on-surface" type="button">
                            <span class="material-symbols-outlined text-[20px]" data-icon="visibility">visibility</span>
                        </button>
                    </div>
                </div>

                <?php if ($error): ?>
                    <div style="color:red; margin-bottom:5px; font-size:14px;">
                        <?= $error ?>
                    </div>
                <?php endif; ?>


                <!-- Remember Me -->
                <div class="flex items-center gap-3">
                    <input class="w-4 h-4 rounded text-secondary focus:ring-secondary border-outline-variant bg-surface-container-low" id="remember" type="checkbox" />
                    <label class="text-sm font-label text-on-surface-variant select-none" for="remember">Keep me signed in for 30 days</label>
                </div>
                <!-- Action Button -->
                <button class="w-full bg-gradient-to-br from-primary to-primary-container text-white py-3 rounded-full font-headline text-sm font-semibold shadow-lg shadow-primary/10 flex items-center justify-center gap-2 active:scale-95 transition-all" type="submit">
                    Sign Into Dashboard
                    <span class="material-symbols-outlined text-[18px]" data-icon="arrow_forward">arrow_forward</span>
                </button>
            </form>
            <footer class="mt-12 text-center">
                <p class="text-sm font-body text-on-surface-variant">
                    Don't have an account yet?
                    <a class="text-primary font-bold hover:text-secondary transition-colors" href="register.php">Start for free today</a>
                </p>
            </footer>
        </section>
    </main>
    <!-- Footer Meta -->

</body>

</html>