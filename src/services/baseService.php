<?php
abstract class BaseService
{
    protected $connect;
    protected $tableName = '';

    public function __construct()
    {
        $this->connect = Database::getConnection();
    }

    public function create(array $data)
    {

        try {
            $columns = implode(', ', array_keys($data));
            $placeholders = ':' . implode(', :', array_keys($data));

            $sql = "INSERT INTO {$this->tableName} ($columns) VALUES ($placeholders)";
            $stmt = $this->connect->prepare($sql);

            foreach ($data as $key => $value) {
                $paramType = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
                $stmt->bindValue(":$key", $value, $paramType);
            }

            return $stmt->execute();

        } catch (PDOException $e) {
            echo json_encode(['error' => 'Database Error: ' . $e->getMessage()]);
            return false;
        }
    }

    public function count()
    {
        $query = "SELECT COUNT(*) as total FROM {$this->tableName}";
        $params = [];

        $stmt = $this->connect->prepare($query);
        $stmt->execute($params);

        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function update($id, array $data)
    {
        if (!$this->getById($id)) {
            return false;
        }

        $fields = implode(', ', array_map(fn($col) => "$col = :$col", array_keys($data)));
        $sql = "UPDATE {$this->tableName} SET $fields WHERE id = :id";
        $stmt = $this->connect->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    public function get()
    {
        $sql = "SELECT * FROM {$this->tableName}";
        $stmt = $this->connect->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->tableName} WHERE id = :id LIMIT 1";
        $stmt = $this->connect->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function delete($id)
    {
        if (!$this->getById($id)) {
            return false;
        }

        $sql = "DELETE FROM {$this->tableName} WHERE id = :id";
        $stmt = $this->connect->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
?>