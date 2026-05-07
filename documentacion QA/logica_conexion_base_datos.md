**Archivo:** `config/database.php`
**Clase:** `Database`

1. Archivo define clase Database con propiedades privadas: host='127.0.0.1', port='3320', db_name='its-fashion', username='root', password=''.
2. Método `conectar()` crea DSN string con formato "mysql:host={$host};port={$port};dbname={$db_name};charset=utf8mb4".
3. Try-catch: intenta crear nueva instancia PDO con DSN, username, password. Configura atributos: PDO::ATTR_ERRMODE = PDO::ERRMODE_EXCEPTION para lanzar excepciones en errores, PDO::ATTR_DEFAULT_FETCH_MODE = PDO::FETCH_ASSOC para retornar arrays asociativos.
4. If conexión exitosa retorna objeto PDO. Catch captura PDOException, hace echo del mensaje de error y die() para detener ejecución.
5. Cada controlador que necesita BD hace: require_once database.php, instancia Database, llama conectar(), pasa conexión al modelo.
