<?php

//destruimos las sesiones que estén habilitadas

session_destroy();

//direccionamos al login para volver a loguear

echo '<script>
    window.location = "ingreso"
</script>';