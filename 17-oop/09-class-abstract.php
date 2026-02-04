<?php
$title       = '09- Class Abstract';
$description = 'A class that cannot be instantiated, only extended from.';

include __DIR__ . '/template/header.php';

echo "<section>";

abstract class ConexionDB
{

    private $host;
    private $bd;
    private $usuario;
    private $clave;

    protected $db;

    public function __construct(
        $host = 'localhost',
        $bd = 'pokeadso_a',
        $usuario = 'root',
        $clave = ''
    ) {
        $this->host    = $host;
        $this->bd      = $bd;
        $this->usuario = $usuario;
        $this->clave   = $clave;

        $this->abrirConexion();
    }

    protected function abrirConexion()
    {
        $cadena = "mysql:host={$this->host};dbname={$this->bd};charset=utf8";

        try {
            $this->db = new PDO($cadena, $this->usuario, $this->clave);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $err) {
            die("🔴 No se pudo conectar: " . $err->getMessage());
        }
    }

    protected function cerrarConexion()
    {
        $this->db = null;
    }

    
    public function consultar() {}
}

class CatalogoPokemon extends ConexionDB
{

    public function obtenerTabla()
    {

        $this->abrirConexion();

        try {
            $peticion = "SELECT id, name AS nombre, type FROM pokemons ORDER BY id";
            $resultado = $this->db->query($peticion);
            $lista = $resultado->fetchAll(PDO::FETCH_ASSOC);

            $html  = "<table class='pokemon-table'>";
            $html .= "<thead><tr>
                        <th>Código</th>
                        <th>Pokémon</th>
                        <th>Elemento</th>
                      </tr></thead>";
            $html .= "<tbody>";

            foreach ($lista as $fila) {
                $html .= "<tr>
                            <td class='col-id'>{$fila['id']}</td>
                            <td class='col-name'>{$fila['nombre']}</td>
                            <td class='col-type'>{$fila['type']}</td>
                          </tr>";
            }

            $html .= "</tbody></table>";

            return $html;
        } catch (PDOException $e) {
            return "<p class='error'>Error: " . $e->getMessage() . "</p>";
        } finally {
            $this->cerrarConexion();
        }
    }
}

$pokemones = new CatalogoPokemon();
echo $pokemones->obtenerTabla();

include_once __DIR__ . '/template/footer.php';
