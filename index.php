<?php
include 'seguridad.php';
 ?>
<!DOCTYPE html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Sistema Préstamos</title>
        <link rel="stylesheet" href="css/bootstrap.css">
        <link rel="stylesheet" href="css/dashboard.css">
        <link rel="stylesheet" href="css/font-awesome.min.css">
        <script src="js/jquery-3.2.1.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/buscarPrestamo.js"></script>
    </head>
    <body>
        <nav class="navbar navbar-inverse navbar-fixed-top">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="#">SISTEMA PRÉSTAMOS</a>
                </div>
                <ul class="nav navbar-nav navbar-right">
                  <li><a href="logout.php"><?php echo $_SESSION["NombreCompleto"]. " "; ?><i class="fa fa-sign-out"></i>  Salir</a></li>
                </ul>
            </div>
        </nav>
       <div class="container-fluid">
            <div class="row">
                <div class="col-sm-3 col-md-2 sidebar" id="sidebar">
                    <ul class="nav nav-sidebar">
                        <li>
                            <a href="index.php" class="w3-bar-item w3-button"><i class="fa fa-home"></i> Principal</a>
                        </li>
                    </ul>
                    <ul class="nav nav-sidebar">
                        <li>
                            <a href="webClientes.php" class="w3-bar-item w3-button"><i class="fa fa-user"></i> Clientes</a>
                        </li>
                    </ul>
                    <ul class="nav nav-sidebar">
                        <li>
                            <a href="webPrestamos.php" class="w3-bar-item w3-button"><i class="fa fa-list-alt"></i> Prestamos</a>
                        </li>
                    </ul>
                    <?php
                    if ($_SESSION['rol'] == 'A') {
                      echo '<ul class="nav nav-sidebar">
                        <li>
                          <a href="webParametros.php" class="w3 bar-item w3-button"><i class="fa fa-cog"></i> Configuracion </a>
                        </li>
                      </ul>
                      <ul class="nav nav-sidebar">
                        <li>
                        <a href="webUsers.php" class="w3 bar-item w3-button"><i class="fa fa-users"></i> Usuarios </a>
                        </li>
                      </ul>
                      <ul class="nav nav-sidebar">
                        <li>
                          <a href="reporteEstadosFinancieros.php" target="_blank" class="w3 bar-item w3-button"><i class="fa fa-university"></i> Estados Financieros </a>
                        </li>
                      </ul>
                      <ul class="nav nav-sidebar">
                        <li>
                          <a href="modificarPlantillaContrato.php" class="w3 bar-item w3-button"><i class="fa fa-file-text"></i> Editar Contrato </a>
                        </li>
                      </ul>
                      <ul class="nav nav-sidebar">
                        <li>
                          <a href="webBitacora.php" class="w3 bar-item w3-button"><i class="fa fa-address-book"></i> Bitacora</a>
                        </li>
                      </ul> ';
                    }
                      ?>
                </div>

        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
        <div class="container">
            <h1 class="page-header">Préstamos</h1>
            <div class="col-md-8">
                <input type="text" class="form-control" name="busqueda" autocomplete="off" id="busqueda" onkeyup="buscarPrestamos();" placeholder="Buscar...">
            </div>
            <a href="formNuevoPrestamo.php" class="btn btn-default"><i class="fa fa-plus"></i> Nuevo préstamo</a>
        </div>
        <br>
        <table class="table table-condensed" id="resultadoBusqueda">
            <thead>
              <tr>
              <th>ID</th>
              <th>DUI</th>
              <th>Nombre</th>
              <th>Monto</th>
              <th>Saldo</th>
              <th>Interés</th>
              <th>Cancelado</th>
              <th>Opciones</th>
              </tr>
            </thead>
            <tbody>
              <?php
              require_once 'ControladorPrestamo.php';
              require_once 'Conexion.php';
              $cPrestamo = new ControladorPrestamo();
              $prestamos = $cPrestamo->obtenerActivos();
              $numPrestamos = count($prestamos);
            for ($i=0; $i <$numPrestamos ; $i++) {
                $conn = new Conexion();
                $stmn = "SELECT MAX(num_cuota) FROM cuota WHERE ID_prestamo='" . $prestamos[$i]->id_prestamo . "'";
                $resultado = $conn->execQueryO($stmn);
                $max_cuota = $resultado->fetch_assoc();
                $porcentaje = (100-($prestamos[$i]->saldo / $prestamos[$i]->monto)*100);
                $cu = $max_cuota['MAX(num_cuota)'];
                echo '<tr>';
                echo '<td>' . $prestamos[$i]->id_prestamo. '</td>';
                echo '<td>' . $prestamos[$i]->cliente->dui . '</td>';
                echo '<td>' . $prestamos[$i]->cliente->nombres . ' ' . $prestamos[$i]->cliente->apellidos . '</td>';
                echo '<td>$' . $prestamos[$i]->monto. '</td>';
                echo '<td>$' . $prestamos[$i]->saldo . '</td>';
                echo '<td>' . $prestamos[$i]->tasa_interes. '%</td>';
                echo '<td>' . round($porcentaje, 2) . '%</td>';
                echo '<td>'.'<a href="detallePrestamo.php?id_prestamo='.$prestamos[$i]->id_prestamo.'" data-toggle="tooltip" data-placement="bottom" title="Detalle"><button class="btn btn-sm btn-info"><i class="fa fa-eye"></i></button></a>';
                echo '<a data-toggle="tooltip" data-placement="bottom" title="Nuevo pago" href="formNuevoPago.php?id_prestamo='.$prestamos[$i]->id_prestamo.'&fecha_ultimo_pago='.$prestamos[$i]->fecha_ultimo_pago.'&nombres='.$prestamos[$i]->cliente->nombres.'&apellidos='.$prestamos[$i]->cliente->apellidos.'&monto='.$prestamos[$i]->monto.'&tasa_interes='.$prestamos[$i]->tasa_interes.'&cantidad_cuotas='.$prestamos[$i]->cantidad_cuotas.'&valor_cuota='.$prestamos[$i]->valor_cuota.'" ><button class="btn btn-sm btn-info"><i class="fa fa-usd"></i></button></a>';
                echo '<a target="_blank" href="contrato.php?id_prestamo='.$prestamos[$i]->id_prestamo.'&dui='.$prestamos[$i]->cliente->dui.'" data-toggle="tooltip" data-placement="bottom" title="Contrato"><button class="btn btn-sm btn-info"><i class="fa fa-file"></i></button></a></tr>';
            }
               ?>
            </tbody>
        </table>
        </div>
        </div>
        </div>
        <script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
});
</script>

    </body>
</html>
