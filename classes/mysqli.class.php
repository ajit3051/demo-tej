<?php

class mysqli_ext extends mysqli
{
    private string $class_name = "MySqli";
    private string $class_version = "1.2.1";
    private string $class_author = "Wolf Software";
    private string $class_source = "http://www.wolf-software.com/Downloads/mysqli_class";

    private bool $cacheHandles = false;
    private array $prepareHandle = [];
    private $result = null;
    private ?mysqli_stmt $stmt = null;
    private int $debug = 1;
    private bool $connection_failed = false;

    public function class_name(): string
    {
        return $this->class_name;
    }

    public function class_version(): string
    {
        return $this->class_version;
    }

    public function class_author(): string
    {
        return $this->class_author;
    }

    public function class_source(): string
    {
        return $this->class_source;
    }

    public function __construct(string $hostname, string $username, string $password, string $database, int $cachePrepare = 0)
    {
        if ($cachePrepare) {
            $this->cacheHandles = true;
        }

        try {
            parent::__construct($hostname, $username, $password, $database);
            $this->set_charset('utf8mb4');  // Set the charset for the connection
        } catch (mysqli_sql_exception $e) {
            echo 'There was an error with your connection: ' . $e->getMessage();
            exit;
        }
        
    }

    #[\ReturnTypeWillChange]
    public function close()
    {
        return parent::close();
    }

    public function prepare(string $sql): mysqli_stmt
    {
        if ($this->cacheHandles) {
            if (!isset($this->prepareHandle[$sql])) {
                $this->prepareHandle[$sql] = new stmt_ext($this, $sql, true);
            }
            $stmt = $this->prepareHandle[$sql];
        } else {
            $stmt = new stmt_ext($this, $sql, false);
        }

        if ($this->error) {
            $this->error("Prepare Failed", $this->error, $sql);
        }

        return $stmt;
    }

    public function select_first_row(string $sql, ?string $paramtypes = null)
    {
        if (!stristr($sql, 'select') || !stristr($sql, 'from')) {
            $this->error("Incorrect SQL", $this->error, $sql);
        }

        $stmt = $this->prepare($sql);

        if ($paramtypes !== null) {
            $params = [$paramtypes];
            $paramcount = func_num_args();

            for ($i = 2; $i < $paramcount; $i++) {
                $tmp = func_get_arg($i);
                if (is_array($tmp)) {
                    foreach ($tmp as $v) {
                        $params[] = $v;
                    }
                } else {
                    $params[] = $tmp;
                }
            }

            $object_params = [];
            foreach ($params as $k => $v) {
                $object_params[$k] = &$params[$k];
            }

            if (!call_user_func_array([$stmt, 'bind_param'], $object_params)) {
                $this->error("Binding Failed", $this->error, $sql);
            }
        }

        $stmt->execute();

        if ($this->error) {
            $this->error("Execute Error", $this->error, $sql);
        }

        $stmt->store_result();

        if ($stmt->num_rows()) {
            $obj = $stmt->fetch_object();
            $result = new stdClass();
            foreach ($obj as $key => $val) {
                $result->$key = $val;
            }
        } else {
            $result = null;
        }

        $stmt->close();
        return $result;
    }

    public function select(string $sql, ?string $paramtypes = null): mysqli_stmt
    {
        if (!stristr($sql, 'select') || !stristr($sql, 'from')) {
            $this->error("Incorrect SQL", $this->error, $sql);
        }
        $stmt = $this->prepare($sql);
        if (!$stmt) {
            die("Prepare failed: " . $this->error);
        }
        if ($paramtypes !== null) {
            $params = [$paramtypes];
            $paramcount = func_num_args();

            for ($i = 2; $i < $paramcount; $i++) {
                $tmp = func_get_arg($i);
                if (is_array($tmp)) {
                    foreach ($tmp as $v) {
                        $params[] = $v;
                    }
                } else {
                    $params[] = $tmp;
                }
            }

            $object_params = [];
            foreach ($params as $k => $v) {
                $object_params[$k] = &$params[$k];
            }

            if (!call_user_func_array([$stmt, 'bind_param'], $object_params)) {
                $this->error("Binding Failed", $stmt->error, $sql);
            }
        }

        $stmt->execute();

        if ($this->error) {
            $this->error("Execute Error", $this->error, $sql);
        }

        $stmt->store_result();
        return $stmt;
    }

    public function run_query(string $sql, ?string $paramtypes = null): int
    {
        $stmt = $this->prepare($sql);

        if ($paramtypes !== null) {
            $params = [$paramtypes];
            $paramcount = func_num_args();

            for ($i = 2; $i < $paramcount; $i++) {
                $tmp = func_get_arg($i);
                if (is_array($tmp)) {
                    foreach ($tmp as $v) {
                        $params[] = $v;
                    }
                } else {
                    $params[] = $tmp;
                }
            }

            $object_params = [];
            foreach ($params as $k => $v) {
                $object_params[$k] = &$params[$k];
            }

            if (!call_user_func_array([$stmt, 'bind_param'], $object_params)) {
                $this->error("Binding Error", $this->error, $sql);
            }
        }

        $stmt->execute();

        if ($this->error) {
            $this->error("Execute Error", $this->error, $sql);
        }

        $stmt->store_result();

        return (stristr($sql, 'select') && stristr($sql, 'from')) ? $stmt->num_rows : $stmt->affected_rows;
    }

    public function raw_query(string $sql): void
    {
        $this->query($sql);

        if ($this->error) {
            $this->error("Execute Error", $this->error, $sql);
        }
    }

    public function insert(string $sql, ?string $paramtypes = null): ?int
    {
        if (!stristr($sql, 'insert') || !stristr($sql, 'into')) {
            $this->error("Incorrect SQL", $this->error, $sql);
        }

        $stmt = $this->prepare($sql);

        if ($paramtypes !== null) {
            $params = [$paramtypes];
            $paramcount = func_num_args();

            for ($i = 2; $i < $paramcount; $i++) {
                $tmp = func_get_arg($i);
                if (is_array($tmp)) {
                    foreach ($tmp as $v) {
                        $params[] = $v;
                    }
                } else {
                    $params[] = $tmp;
                }
            }

            $object_params = [];
            foreach ($params as $k => $v) {
                $object_params[$k] = &$params[$k];
            }

            if (!call_user_func_array([$stmt, 'bind_param'], $object_params)) {
                $this->error("Binding Error", $this->error, $sql);
            }
        }

        $stmt->execute();

        if ($this->error) {
            $this->error("Execute Error", $this->error, $sql);
        }

        $stmt->store_result();

        return $stmt->insert_id > 0 ? $stmt->insert_id : null;
    }

    public function update(string $sql, ?string $paramtypes = null): ?int
    {
        if (!stristr($sql, 'update') || !stristr($sql, 'set')) {
            $this->error("Incorrect SQL", $this->error, $sql);
        }

        $stmt = $this->prepare($sql);

        if ($paramtypes !== null) {
            $params = [$paramtypes];
            $paramcount = func_num_args();

            for ($i = 2; $i < $paramcount; $i++) {
                $tmp = func_get_arg($i);
                if (is_array($tmp)) {
                    foreach ($tmp as $v) {
                        $params[] = $v;
                    }
                } else {
                    $params[] = $tmp;
                }
            }

            $object_params = [];
            foreach ($params as $k => $v) {
                $object_params[$k] = &$params[$k];
            }

            if (!call_user_func_array([$stmt, 'bind_param'], $object_params)) {
                $this->error("Binding Error", $stmt->error, $sql);
            }
        }

        $stmt->execute();

        if ($this->error) {
            $this->error("Execute Error", $this->error, $sql);
        }

        $stmt->store_result();

        return $stmt->affected_rows > 0 ? $stmt->affected_rows : null;
    }

    public function delete($sql, $paramtypes = null)
    {
        $stmt = $this->prepare($sql);  // Use the prepare function to get the statement handle

        if (isset($paramtypes)) {
            $params = [$paramtypes];  // Short array syntax
            $paramcount = func_num_args();

            for ($i = 2; $i < $paramcount; $i++) {
                $tmp = func_get_arg($i);

                if (is_array($tmp)) {
                    $params = array_merge($params, $tmp);  // Append array values
                } else {
                    $params[] = $tmp;
                }
            }

            $object_params = [];
            foreach ($params as $k => $v) {
                $object_params[$k] = &$params[$k];  // Pass by reference for compatibility
            }

            if (!call_user_func_array([$stmt, 'bind_param'], $object_params)) {
                $error = mysqli_error($this);
                $this->error("Binding Error", $error, $sql);
            }
        }

        $stmt->execute();  // Execute the statement

        $error = mysqli_error($this);  // Check for errors

        if ($error) {
            $this->error("Execute Error", $error, $sql);
        }

        $stmt->store_result();

        // Get affected rows
        $result = $stmt->affected_rows;

        $stmt->close();  // Close the statement
        return $result;  // Return the affected rows count
    }


    public function error(string $str, string $error = "", string $sql = ""): void
    {
       
        if (!empty($str)) {
            echo $message = htmlspecialchars($str, ENT_QUOTES, 'UTF-8').'<br>';
           echo $details = htmlspecialchars($error, ENT_QUOTES, 'UTF-8')."<br>";
            echo $sql = htmlspecialchars($sql, ENT_QUOTES, 'UTF-8');

            die;
        }
    }
}

class stmt_ext extends mysqli_stmt
{
    private bool $varsBound = false;
    private bool $cacheHandles = false;
    private array $results = [];

    public function __construct($link, string $query, bool $cachePrepare = false)
    {
        if ($cachePrepare) {
            $this->cacheHandles = true;
        }

        parent::__construct($link, $query);
    }

    public function fetch_object(): ?stdClass
    {
        if (!$this->varsBound) {
            $meta = $this->result_metadata();
            while ($column = $meta->fetch_field()) {
                $bindVarArray[] = &$this->results[$column->name];
            }
            call_user_func_array([$this, 'bind_result'], $bindVarArray);
            $this->varsBound = true;
        }

        if ($this->fetch()) {
            $result = new stdClass();
            foreach ($this->results as $key => $value) {
                $result->$key = $value;
            }
            return $result;
        } else {
            return null;
        }
    }

    public function fetch_assoc()
    {
        if (!$this->varsBound) {
            $meta = $this->result_metadata();

            $seen = array();
            while ($column = $meta->fetch_field()) {
                $columnName = $column->name;

                $columnName = str_replace(' ', '_', $columnName);
                $columnName = str_replace('(*)', '', $columnName);
                $columnName = str_replace('(', '_', $columnName);
                $columnName = str_replace(')', '', $columnName);

                if (in_array($columnName, $seen)) {
                    $count = 2;
                    $test_name = $columnName . "_" . $count;

                    while (in_array($test_name, $seen)) {
                        $count = $count + 1;
                        $test_name = $columnName . "_" . $count;
                    }
                    $columnName = $test_name;
                }
                array_push($seen, $columnName);

                $bindVarArray[] = &$this->results[$columnName];
            }
            call_user_func_array(array($this, 'bind_result'), $bindVarArray);
            $this->varsBound = true;
        }
        if ($this->fetch() != null) {
            foreach ($this->results as $k => $v) {
                $results[$k] = $v;
            }
            return $results;
        } else {
            return null;
        }
    }

    public function error($str, $error = "")
    {
        if (strlen($str) > 0) {
            if (strlen($error) > 0) {
                echo "<BR><BR><font color=\"#ff0000\"><B>Database Error: $str</b></font><BR><BR>\n";
            } else {
                echo "<BR><BR><font color=\"#ff0000\"><B>Database Error: $str - $error</b></font><BR><BR>\n";
            }
        }
        exit;
    }
}

?>