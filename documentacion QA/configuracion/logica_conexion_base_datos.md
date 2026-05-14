**Archivo:** `config/database.php`  
**Clase:** `Database`

Clase con propiedades: host='127.0.0.1', port='3320', db_name='its-fashion', username='root', password=''. Método `conectar()` construye DSN con charset=utf8mb4. Try-catch: crea PDO, configura ATTR_ERRMODE=EXCEPTION y ATTR_DEFAULT_FETCH_MODE=FETCH_ASSOC. Retorna objeto PDO. Catch muestra error y ejecuta die(). Controladores instancian Database, llaman conectar() y pasan conexión al modelo. Modelo usa `$this->conn->prepare()` para prepared statements.
