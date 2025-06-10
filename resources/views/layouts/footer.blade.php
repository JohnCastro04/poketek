<footer class="w-100 p-4 text-center mt-8">
    <div class="container mx-auto">
        <p>&copy; {{ date('Y') }} <span class="author-link" id="authorName">John Castro</span>. Todos los derechos reservados. Última actualización: 10/06/2025</p>
        <p>Imágenes y contenido de Pokémon &copy; 1995-2023 Nintendo/Game Freak.</p>
    </div>
    
    <!-- Modal Overlay -->
    <div class="modal-overlay" id="modalOverlay">
        <div class="author-modal">
            <a href="javascript:void(0)" class="closebtn" id="closeModal">&times;</a>
            <img src="{{ asset('images/profile/1.png') }}" alt="John Castro" class="author-image">
            <h3>John Castro</h3>
            <p>Desarrollador Web Full Stack</p>
            <div class="social-links">
                <a href="https://linkedin.com/in/johncastro" target="_blank" title="LinkedIn">
                    <i class="bi bi-linkedin"></i>
                </a>
                <a href="https://github.com/johncastro" target="_blank" title="GitHub">
                    <i class="bi bi-github"></i>
                </a>
            </div>
        </div>
    </div>
</footer>

<style>
    .author-link {
        color: var(--golden-bloom);
        cursor: pointer;
        font-weight: 600;
    }
    
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(18, 22, 33, 0.6);
        backdrop-filter: blur(10px);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }
    
    .author-modal {
        background: var(--midnight-abyss);
        border: 2px solid var(--golden-bloom);
        border-radius: var(--card-border-radius);
        padding: 2rem;
        width: 90%;
        max-width: 350px;
        text-align: center;
        position: relative;
    }
    
    .closebtn {
        position: absolute;
        top: 10px;
        right: 15px;
        color: var(--golden-bloom);
        font-size: 28px;
        text-decoration: none;
    }
    
    .author-image {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        border: 3px solid var(--golden-bloom);
        margin-bottom: 1rem;
        object-fit: cover;
    }
    
    .author-modal h3 {
        color: var(--golden-bloom);
        margin-bottom: 0.5rem;
    }
    
    .author-modal p {
        color: var(--white);
        margin-bottom: 1.5rem;
    }
    
    .social-links {
        display: flex;
        justify-content: center;
        gap: 1rem;
    }
    
    .social-links a {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--midnight-steel);
        color: var(--white);
        font-size: 1.2rem;
        transition: var(--transition-time);
    }
    
    .social-links a:hover {
        background: var(--golden-bloom);
        color: var(--midnight-abyss);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const authorName = document.getElementById('authorName');
        const modalOverlay = document.getElementById('modalOverlay');
        const closeModal = document.getElementById('closeModal');
        
        authorName.addEventListener('click', function() {
            modalOverlay.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        });
        
        function closeModalFunction() {
            modalOverlay.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        
        closeModal.addEventListener('click', closeModalFunction);
        
        modalOverlay.addEventListener('click', function(e) {
            if (e.target === modalOverlay) {
                closeModalFunction();
            }
        });
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modalOverlay.style.display === 'flex') {
                closeModalFunction();
            }
        });
    });
</script>