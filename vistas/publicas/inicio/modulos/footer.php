<!-- Footer -->
<footer id="footer">
    
    <!-- Formulario de Mensaje -->
    <div class="footer-form">
        <h3>Contáctanos</h3>
        <form id="footer-contact-form">
  <input type="text" name="nombre" placeholder="Tu nombre" required>
<input type="email" name="email" placeholder="Tu correo" required>
<textarea name="mensaje" placeholder="Tu mensaje" rows="3" required></textarea>

    <button type="submit">Enviar mensaje</button>
</form>

    </div>

    <!-- Redes Sociales -->
    <ul class="icons">
        <li><a href="https://twitter.com/nuva_sas" class="icon brands alt fa-twitter"><span class="label">Twitter</span></a></li>
        <li><a href="https://www.facebook.com/nuva.sas" class="icon brands alt fa-facebook-f"><span class="label">Facebook</span></a></li>
        <li><a href="https://www.linkedin.com/company/nuva-sas" class="icon brands alt fa-linkedin-in"><span class="label">LinkedIn</span></a></li>
        <li><a href="https://www.instagram.com/nuva.sas" class="icon brands alt fa-instagram"><span class="label">Instagram</span></a></li>
        <li><a href="mailto:contacto@nuva.com" class="icon solid alt fa-envelope"><span class="label">Email</span></a></li>
    </ul>

    <ul class="copyright">
        <li>&copy; 2025 Nuva S.A.S. Todos los derechos reservados.</li>
    </ul>

</footer>



		</div>

	
<script src="vistas/recursos/landing/js/jquery.min.js"></script>
<script src="vistas/recursos/landing/js/jquery.scrolly.min.js"></script>
<script src="vistas/recursos/landing/js/jquery.dropotron.min.js"></script>
<script src="vistas/recursos/landing/js/jquery.scrollex.min.js"></script>
<script src="vistas/recursos/landing/js/browser.min.js"></script>
<script src="vistas/recursos/landing/js/breakpoints.min.js"></script>
<script src="vistas/recursos/landing/js/util.js"></script>
<script src="vistas/recursos/landing/js/main.js"></script>

 
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Animate.css para animaciones -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const footerForm = document.getElementById("footer-contact-form");

    footerForm.addEventListener("submit", function(e) {
        e.preventDefault();

        const formData = new FormData(footerForm);

        fetch("index.php?ruta=contacto_enviar", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            Swal.fire({
                title: data.titulo,
                text: data.mensaje,
                confirmButtonText: "Aceptar",
                confirmButtonColor: data.status === "success" ? "#1e90ff" : "#ff4d4d",
                background: "#ffffff",
                color: "#222222",
                iconColor: data.status === "success" ? "#28a745" : "#dc3545",
                showClass: { popup: 'animate__animated animate__zoomIn' },
                hideClass: { popup: 'animate__animated animate__fadeOut' },
                width: 400,
                padding: '1.5em',
                timer: data.status === "success" ? 3000 : undefined,
                timerProgressBar: data.status === "success"
            });

            // Limpiar el formulario solo si fue exitoso
            if(data.status === "success") footerForm.reset();
        })
        .catch(err => {
            Swal.fire({
                title: "Error",
                text: "Ocurrió un error al enviar el mensaje",
                confirmButtonColor: "#ff4d4d",
                background: "#ffffff",
                color: "#222222",
                iconColor: "#dc3545",
                showClass: { popup: 'animate__animated animate__shakeX' },
                hideClass: { popup: 'animate__animated animate__fadeOut' }
            });
        });
    });
});
</script>



	</body>
</html>