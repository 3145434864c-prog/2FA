            <footer>
                <p class="small tm-copyright-text">Copyright &copy; <span class="tm-current-year">2020</span> Nuva
                    S.A.S. .

            </footer>
        </div> <!-- .tm-main-content -->
    </div>
    <!-- load JS -->
  <script src="vistas/recursos/equipo_recursos/js/jquery-3.2.1.slim.min.js"></script>
<script src="vistas/recursos/equipo_recursos/slick/slick.min.js"></script>
<script src="vistas/recursos/equipo_recursos/js/anime.min.js"></script>
<script src="vistas/recursos/equipo_recursos/js/main.js"></script>

            
    <script>

        function setupFooter() {
            var pageHeight = $('.tm-site-header-container').height() + $('footer').height() + 100;

            var main = $('.tm-main-content');

            if ($(window).height() < pageHeight) {
                main.addClass('tm-footer-relative');
            }
            else {
                main.removeClass('tm-footer-relative');
            }
        }

        /* DOM is ready
        ------------------------------------------------*/
        $(function () {

            setupFooter();

            $(window).resize(function () {
                setupFooter();
            });

            $('.tm-current-year').text(new Date().getFullYear());  // Update year in copyright           
        });

    </script>

</body>

</html>