<?php

namespace app\models;

use \PDO;

if (file_exists(__DIR__ . "/../../config/server.php")) {
    require_once __DIR__ . "/../../config/server.php";
}

class mainModel
{
    private $server = DB_SERVER;
    private $db = DB_NAME;
    private $user = DB_USER;
    private $pass = DB_PASS;

    /*----------  Funcion conectar a BD  ----------*/
    protected function conectar()
    {
        $conexion = new PDO("mysql:host=" . $this->server . ";dbname=" . $this->db, $this->user, $this->pass);
        $conexion->exec("SET CHARACTER SET utf8");
        return $conexion;
    }

    /*----------  Funcion ejecutar consultas  ----------*/
    protected function ejecutarConsulta($consulta)
    {
        $sql = $this->conectar()->prepare($consulta);
        $sql->execute();
        return $sql;
    }

    /*----------  Funcion limpiar cadenas  ----------*/
    public function limpiarCadena($cadena)
    {
        $palabras = ["<script>", "</script>", "<script src", "<script type=", "SELECT * FROM", "DELETE FROM", "INSERT INTO", "DROP TABLE", "DROP DATABASE", "TRUNCATE TABLE", "SHOW TABLES", "SHOW DATABASE", "<?PHP", "?>", "--", "^", "<", ">", "==", "=", ";", "::"];
        $cadena = trim($cadena); //Elimina esspacios en blancos
        $cadena = stripslashes($cadena); //Borra las barras de un string

        foreach ($palabras as $palabra) {
            $cadena = str_ireplace($palabra, "", $cadena);
        }

        $cadena = trim($cadena); //Elimina esspacios en blancos
        $cadena = stripslashes($cadena); //Borra las barras de un string

        return $cadena;
    }

    /*---------- Funcion verificar datos (expresion regular) ----------*/
    protected function verificarDatos($filtro, $cadena)
    {
        if (preg_match("/^" . $filtro . "$/", $cadena)) {
            return false;
        } else {
            return true;
        }
    }

    /*----------  Funcion para ejecutar una consulta INSERT preparada  ----------*/
    protected function guardarDatos($tabla, $datos)
    {
        $query = "INSERT INTO $tabla (";

        $c = 0;
        foreach ($datos as $clave) {
            if ($c >= 1) {
                $query .= ",";
            }
            $query .= $clave["campo_nombre"];
            $c++;
        }

        $query .= ") VALUES (";

        $c = 0;
        foreach ($datos as $clave) {
            if ($c >= 1) {
                $query .= ",";
            }
            $query .= $clave["campo_marcador"];
            $c++;
        }

        $query .= ")";

        $sql = $this->conectar()->prepare($query);

        foreach ($datos as $clave) {
            $sql->bindParam($clave["campo_marcador"], $clave["campo_valor"]);
        }

        $sql->execute();

        return $sql;
    }

    /*---------- Funcion seleccionar datos ----------*/
    public function seleccionarDatos($tipo, $tabla, $campo, $id)
    {
        $tipo = $this->limpiarCadena($tipo);
        $tabla = $this->limpiarCadena($tabla);
        $campo = $this->limpiarCadena($campo);
        $id = $this->limpiarCadena($id);

        if ($tipo == "Unico") {
            $sql = $this->conectar()->prepare("SELECT * FROM $tabla WHERE $campo = :ID");
            $sql->bindParam(":ID", $id);
        } elseif ($tipo == "Normal") {
            $sql = $this->conectar()->prepare("SELECT $campo FROM $tabla");
        }

        $sql->execute();

        return $sql;
    }

    /*----------  Funcion para ejecutar una consulta UPDATE preparada  ----------*/
    protected function actualizarDatos($tabla, $datos, $condicion)
    {
        $query = "UPDATE $tabla SET ";

        $c = 0;
        foreach ($datos as $clave) {
            if ($c >= 1) {
                $query .= ",";
            }
            $query .= $clave["campo_nombre"] . "=" . $clave["campo_marcador"];
            $c++;
        }

        $query .= " WHERE " . $condicion["condicion_campo"] . "=" . $condicion["condicion_marcador"];

        $sql = $this->conectar()->prepare($query);

        foreach ($datos as $clave) {
            $sql->bindParam($clave["campo_marcador"], $clave["campo_valor"]);
        }

        $sql->bindParam($condicion["condicion_marcador"], $condicion["condicion_valor"]);

        $sql->execute();

        return $sql;
    }

    protected function eliminarDatos($tabla, $campo, $id)
    {
        $sql = $this->conectar()->prepare("DELETE FROM $tabla WHERE $campo = :ID");

        $sql->bindParam(":ID", $id);

        $sql->execute();

        return $sql;
    }

    protected function paginadorTablas($pagina, $numeroPaginas, $url, $botones)
    {
        $tabla = '
            <nav aria-label="Page navigation" class="d-flex justify-content-center">
                <ul class="pagination">
        ';

        if ($pagina <= 1) {
            $tabla .= '
                <li class="page-item disabled">
                    <a class="page-link" tabindex="-1" aria-disabled="true">Anterior</a>
                </li>
            ';
        } else {
            $tabla .= '
                <li class="page-item">
                    <a class="page-link" href="' . $url . ($pagina - 1) . '">Anterior</a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="' . $url . '1/">1</a>
                </li>
                <li class="page-item"><span class="page-link">&hellip;</span></li>
            ';
        }

        $ci = 0;
        for ($i = $pagina; $i <= $numeroPaginas; $i++) {
            if ($ci >= $botones) {
                break;
            }
            if ($pagina == $i) {
                $tabla .= '
                    <li class="page-item active" aria-current="page">
                        <a class="page-link" href="' . $url . $i . '/">' . $i . '</a>
                    </li>               
                ';
            } else {
                $tabla .= '
                    <li class="page-item active">
                        <a class="page-link" href="' . $url . $i . '/">' . $i . '</a>
                    </li>
                ';
            }
            $ci++;
        }

        if ($pagina == $numeroPaginas) {
            $tabla .= '
                <li class="page-item disabled">
                    <a class="page-link" tabindex="-1" aria-disabled="true">Siguiente</a>
                </li>
            ';
        } else {
            $tabla .= '
                <li class="page-item"><span class="page-link">&hellip;</span></li>
                <li class="page-item active">
                    <a class="page-link" href="' . $numeroPaginas . '/">' . $numeroPaginas . '</a>
                </li>
                <li>
                    <a class="pagination-link" href="' . $url .  $numeroPaginas . '/">' . $numeroPaginas . '</a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="' . $url . ($pagina + 1) . '">Siguiente</a>
                </li>
            ';
        }

        $tabla .= '
                </ul>
            </nav>
        ';
        return $tabla;
    }
}
