    </main>
    
    <!-- Footer -->
    <footer class="bg-gray-800 text-white pt-12 pb-6">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
                <!-- About Us -->
                <div>
                    <h3 class="text-lg font-bold mb-4">Sobre Nosotros</h3>
                    <p class="text-gray-400 text-sm mb-4">Requejo Fashion Lab es tu tienda de moda deportiva de confianza con las mejores marcas y precios.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-bold mb-4">Enlaces Rápidos</h3>
                    <ul class="space-y-2">
                        <li><a href="index.php" class="text-gray-400 hover:text-white transition-colors">Inicio</a></li>
                        <li><a href="ofertas.php" class="text-gray-400 hover:text-white transition-colors">Ofertas</a></li>
                        <li><a href="categorias.php" class="text-gray-400 hover:text-white transition-colors">Categorías</a></li>
                        <li><a href="nuevo.php" class="text-gray-400 hover:text-white transition-colors">Nuevo</a></li>
                        <li><a href="contacto.php" class="text-gray-400 hover:text-white transition-colors">Contacto</a></li>
                    </ul>
                </div>
                
                <!-- My Account -->
                <div>
                    <h3 class="text-lg font-bold mb-4">Mi Cuenta</h3>
                    <ul class="space-y-2">
                        <li><a href="perfil.php" class="text-gray-400 hover:text-white transition-colors">Mi Perfil</a></li>
                        <li><a href="pedidos.php" class="text-gray-400 hover:text-white transition-colors">Mis Pedidos</a></li>
                        <li><a href="lista-deseos.php" class="text-gray-400 hover:text-white transition-colors">Lista de Deseos</a></li>
                        <li><a href="carrito.php" class="text-gray-400 hover:text-white transition-colors">Carrito</a></li>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div>
                    <h3 class="text-lg font-bold mb-4">Contacto</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-3"></i>
                            <span>Av. Principal 123, Lima, Perú</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone-alt mr-3"></i>
                            <span>+51 123 456 789</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-3"></i>
                            <span>info@requejofashionlab.com</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Bottom Bar -->
            <div class="border-t border-gray-700 pt-6">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-sm text-gray-400 mb-4 md:mb-0">&copy; <?php echo date('Y'); ?> Requejo Fashion Lab. Todos los derechos reservados.</p>
                    <div class="flex space-x-6">
                        <a href="#" class="text-gray-400 hover:text-white text-sm">Términos y Condiciones</a>
                        <a href="#" class="text-gray-400 hover:text-white text-sm">Política de Privacidad</a>
                        <a href="#" class="text-gray-400 hover:text-white text-sm">Política de Envíos</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Back to Top Button -->
    <button id="back-to-top" class="fixed bottom-6 right-6 bg-blue-600 text-white p-3 rounded-full shadow-lg opacity-0 invisible transition-all duration-300">
        <i class="fas fa-arrow-up"></i>
    </button>
    
    <script>
        // Back to top button
        const backToTopButton = document.getElementById('back-to-top');
        
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                backToTopButton.classList.remove('opacity-0', 'invisible');
                backToTopButton.classList.add('opacity-100', 'visible');
            } else {
                backToTopButton.classList.remove('opacity-100', 'visible');
                backToTopButton.classList.add('opacity-0', 'invisible');
            }
        });
        
        backToTopButton.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    </script>
</body>
</html>
