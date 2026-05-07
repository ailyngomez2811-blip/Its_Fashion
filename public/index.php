<!DOCTYPE html>
<html lang="es" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Its Fashion - Sistema integral de gestión de inventario, ventas y control de caja para boutiques y tiendas de moda">
    <title>Its Fashion | Gestión Premium para Boutiques</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">
    <link rel="icon" href="../img/icono head .png" type="image/png">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                        serif: ['Playfair Display', 'serif'],
                    },
                    colors: {
                        brand: {
                            dark: '#0f172a',
                            light: '#f8fafc',
                            accent: '#2563eb',
                            /* blue-600 */
                            muted: '#64748b'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .glass-nav {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
        }

        .slide {
            opacity: 0;
            transition: opacity 1s ease-in-out, transform 1s ease-in-out;
            position: absolute;
            inset: 0;
            z-index: 0;
            transform: scale(1.05);
        }

        .slide.active {
            opacity: 1;
            z-index: 10;
            transform: scale(1);
        }

        .hero-overlay {
            background: linear-gradient(to right, rgba(15, 23, 42, 0.9) 0%, rgba(15, 23, 42, 0.4) 100%);
        }

        .hover-lift {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .hover-lift:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
    </style>
</head>

<body class="bg-brand-light text-brand-dark antialiased">

    <!-- Navigation -->
    <nav class="glass-nav fixed w-full top-0 z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center gap-3">
                    <img src="../img/logo en nombre.png" alt="Its Fashion Logo" class="h-10 w-auto object-contain">
                    <span class="font-serif text-2xl font-bold tracking-wide">Its <span class="text-brand-accent">Fashion</span></span>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#inicio" class="text-sm font-medium text-brand-muted hover:text-brand-accent transition-colors">Inicio</a>
                    <a href="#soluciones" class="text-sm font-medium text-brand-muted hover:text-brand-accent transition-colors">Soluciones</a>
                    <a href="#nosotros" class="text-sm font-medium text-brand-muted hover:text-brand-accent transition-colors">Nosotros</a>
                    <a href="../views/auth/login.php" class="inline-flex items-center justify-center px-6 py-2.5 border border-transparent text-sm font-medium rounded-full text-white bg-brand-dark hover:bg-brand-accent transition-all duration-300 shadow-md">
                        Acceder <i class="fas fa-arrow-right ml-2 text-xs"></i>
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <button id="mobile-btn" class="md:hidden text-brand-dark hover:text-brand-accent focus:outline-none">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden bg-white border-t border-gray-100 absolute w-full shadow-lg">
            <div class="px-4 py-6 space-y-4 flex flex-col">
                <a href="#inicio" class="text-brand-dark font-medium hover:text-brand-accent">Inicio</a>
                <a href="#soluciones" class="text-brand-dark font-medium hover:text-brand-accent">Soluciones</a>
                <a href="#nosotros" class="text-brand-dark font-medium hover:text-brand-accent">Nosotros</a>
                <a href="../views/auth/login.php" class="bg-brand-dark text-white px-6 py-3 rounded-md text-center font-medium">Acceder</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="inicio" class="relative h-screen min-h-[600px] flex items-center overflow-hidden bg-brand-dark pt-20">
        <!-- Slides -->
        <div class="slide active">
            <img src="https://images.unsplash.com/photo-1441984904996-e0b6ba687e04?q=80&w=2070&auto=format&fit=crop" class="w-full h-full object-cover" alt="Boutique">
            <div class="absolute inset-0 hero-overlay"></div>
        </div>
        <div class="slide">
            <img src="https://images.unsplash.com/photo-1558769132-cb1fac084092?q=80&w=2069&auto=format&fit=crop" class="w-full h-full object-cover" alt="Inventory">
            <div class="absolute inset-0 hero-overlay"></div>
        </div>

        <div class="relative z-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 text-white/90 text-xs font-semibold tracking-wider uppercase mb-6">
                    <span class="w-2 h-2 rounded-full bg-brand-accent"></span>
                    Software para Boutiques
                </div>
                <h1 class="text-5xl md:text-7xl font-serif font-bold text-white leading-tight mb-6">
                    El arte de <br><span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-300 to-brand-accent">gestionar tu moda</span>
                </h1>
                <p class="text-lg md:text-xl text-gray-300 font-light mb-10 max-w-xl leading-relaxed">
                    Control absoluto de tu inventario, tallas, colores y caja. Diseñado para tiendas de ropa que buscan elegancia y precisión en sus operaciones.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="#soluciones" class="px-8 py-4 bg-white text-brand-dark font-medium rounded-full hover:bg-gray-100 transition-colors shadow-lg">
                        Descubrir más
                    </a>
                    <a href="../views/auth/login.php" class="px-8 py-4 bg-brand-accent text-white font-medium rounded-full hover:bg-blue-700 transition-colors flex items-center gap-2 shadow-lg shadow-blue-600/30">
                        Ingresar al Sistema <i class="fas fa-arrow-right text-sm"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Carousel Controls -->
        <div class="absolute bottom-10 right-10 z-20 flex gap-3">
            <button onclick="nextSlide(-1)" class="w-12 h-12 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white hover:bg-white hover:text-brand-dark transition-all flex items-center justify-center">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button onclick="nextSlide(1)" class="w-12 h-12 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white hover:bg-white hover:text-brand-dark transition-all flex items-center justify-center">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </section>

    <!-- Soluciones (Eficacia Operativa) -->
    <section id="soluciones" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-20">
                <h2 class="font-serif text-4xl md:text-5xl font-bold text-brand-dark mb-6">Control total, en cada detalle</h2>
                <p class="text-lg text-brand-muted font-light">
                    Transformamos la complejidad del inventario de moda en una experiencia intuitiva. Olvídate de los desajustes de stock y enfócate en lo que importa: vender.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div class="bg-brand-light p-10 rounded-2xl border border-gray-100 hover-lift group">
                    <div class="w-14 h-14 bg-white rounded-xl shadow-sm flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
                        <i class="fas fa-tags text-2xl text-brand-accent"></i>
                    </div>
                    <h3 class="text-xl font-bold text-brand-dark mb-4">Gestión por Variantes</h3>
                    <p class="text-brand-muted font-light leading-relaxed">
                        Control exhaustivo por prenda, color y talla. Conoce exactamente cuántas unidades tienes de cada variación en tiempo real.
                    </p>
                </div>
                <!-- Card 2 -->
                <div class="bg-brand-dark p-10 rounded-2xl border border-gray-800 hover-lift group relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-brand-accent/20 rounded-bl-full -z-10 transition-transform group-hover:scale-150"></div>
                    <div class="w-14 h-14 bg-white/10 backdrop-blur-sm rounded-xl flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
                        <i class="fas fa-cash-register text-2xl text-blue-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-4">Punto de Venta Ágil</h3>
                    <p class="text-gray-400 font-light leading-relaxed">
                        Sistema de caja integrado. Registra ventas, aplica descuentos y genera recibos de manera rápida para no hacer esperar a tus clientes.
                    </p>
                </div>
                <!-- Card 3 -->
                <div class="bg-brand-light p-10 rounded-2xl border border-gray-100 hover-lift group">
                    <div class="w-14 h-14 bg-white rounded-xl shadow-sm flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
                        <i class="fas fa-chart-pie text-2xl text-brand-accent"></i>
                    </div>
                    <h3 class="text-xl font-bold text-brand-dark mb-4">Reportes Inteligentes</h3>
                    <p class="text-brand-muted font-light leading-relaxed">
                        Toma decisiones basadas en datos. Visualiza qué prendas se venden más, controla tus márgenes de ganancia y optimiza tus compras.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Nosotros / Stats -->
    <section id="nosotros" class="py-24 bg-brand-light border-y border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-16 items-center">
                <div class="relative">
                    <div class="aspect-[4/5] rounded-2xl overflow-hidden shadow-2xl">
                        <img src="https://images.unsplash.com/photo-1567401893414-76b7b1e5a7a5?q=80&w=2070&auto=format&fit=crop" class="w-full h-full object-cover" alt="Fashion Boutique">
                    </div>
                    <div class="absolute -bottom-8 -right-8 bg-white p-8 rounded-2xl shadow-xl max-w-xs hidden md:block border border-gray-100">
                        <div class="text-4xl font-serif font-bold text-brand-accent mb-2">100%</div>
                        <div class="text-sm font-medium text-brand-dark">Precisión en el control de tu stock de moda.</div>
                    </div>
                </div>
                <div>
                    <h2 class="font-serif text-4xl md:text-5xl font-bold text-brand-dark mb-6">Nuestra Misión</h2>
                    <p class="text-lg text-brand-muted font-light mb-8 leading-relaxed">
                        Empoderar a los dueños de boutiques y tiendas de ropa con una herramienta tecnológica que elimine el caos administrativo. Creemos que la gestión del inventario debe ser tan elegante y fluida como las prendas que vendes.
                    </p>

                    <div class="space-y-6">
                        <div class="flex gap-4">
                            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-check text-brand-accent"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-brand-dark">Diseño Intuitivo</h4>
                                <p class="text-brand-muted font-light">Una interfaz fácil de usar que no requiere semanas de capacitación.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-shield-alt text-brand-accent"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-brand-dark">Seguridad de Datos</h4>
                                <p class="text-brand-muted font-light">Tu información financiera y de stock, protegida y respaldada en todo momento.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-brand-dark text-white pt-20 pb-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-12 mb-16">
                <div class="col-span-2">
                    <div class="flex items-center gap-3 mb-6">
                        <img src="../img/logo en blanco.png" alt="Its Fashion Logo" class="h-10 w-auto object-contain">
                        <span class="font-serif text-2xl font-bold tracking-wide">Its <span class="text-brand-accent">Fashion</span></span>
                    </div>
                    <p class="text-gray-400 font-light max-w-sm leading-relaxed mb-8">
                        Elevando el estándar de la gestión para boutiques. Simplificamos tus operaciones para que enfoques tu energía en hacer crecer tu negocio.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-brand-accent transition-colors">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-brand-accent transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-brand-accent transition-colors">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>

                <div>
                    <h4 class="font-bold text-lg mb-6">Enlaces Rápidos</h4>
                    <ul class="space-y-4">
                        <li><a href="#inicio" class="text-gray-400 hover:text-white transition-colors">Inicio</a></li>
                        <li><a href="#soluciones" class="text-gray-400 hover:text-white transition-colors">Soluciones</a></li>
                        <li><a href="#nosotros" class="text-gray-400 hover:text-white transition-colors">Nosotros</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-lg mb-6">Contacto</h4>
                    <ul class="space-y-4 text-gray-400 font-light">
                        <li class="flex items-center gap-3">
                            <i class="fas fa-map-marker-alt text-brand-accent"></i> Bogotá, Colombia
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-envelope text-brand-accent"></i> info@itsfashion.com
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-phone text-brand-accent"></i> +57 300 123 4567
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-gray-500 text-sm">
                    &copy; 2026 Its Fashion. Todos los derechos reservados.
                </p>
                <div class="flex gap-6 text-sm text-gray-500">
                    <a href="#" class="hover:text-white transition-colors">Términos de Servicio</a>
                    <a href="#" class="hover:text-white transition-colors">Política de Privacidad</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        const btn = document.getElementById('mobile-btn');
        const menu = document.getElementById('mobile-menu');
        btn.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });

        // Carousel logic
        let currentSlide = 0;
        const slides = document.querySelectorAll('.slide');

        function showSlide(index) {
            slides.forEach(s => s.classList.remove('active'));
            slides[index].classList.add('active');
        }

        function nextSlide(dir) {
            currentSlide = (currentSlide + dir + slides.length) % slides.length;
            showSlide(currentSlide);
        }

        setInterval(() => nextSlide(1), 5000);

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                menu.classList.add('hidden'); // hide menu if open
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Glass nav effect on scroll
        const nav = document.querySelector('nav');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 20) {
                nav.classList.add('shadow-md');
            } else {
                nav.classList.remove('shadow-md');
            }
        });
    </script>
</body>

</html>