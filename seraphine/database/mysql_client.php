<?php

require_once __DIR__."/../tools/snow_id/snow_id.php";

/**
 * MySQL 客户端封装类
 * 基于 PDO 实现
 */
class MySQLClient {
    private PDO $pdo;
    private array $config;

    /**
     * 构造函数 - 初始化 MySQL 连接
     *
     * @param string $configFile 配置文件名（不含 .yml 后缀），默认为 'db'
     * @throws Exception 当 MySQL 连接失败时抛出异常
     */
    public function __construct($configFile = 'db')
    {
        $this->config = ReadConfig::read_yml($configFile)['mysql'];

        $username = $this->config['username'];
        $password = $this->config['password'];
        $dbname = $this->config['database'];
        $host = $this->config['host'];
        $port = $this->config['port'];
        $charset = $this->config['charset'] ?? 'utf8mb4';
        $time_zone = $this->config['time_zone'] ?? '+08:00';

        $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";

        try {
            $this->pdo = new PDO($dsn, $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            // 设置时区
            $this->pdo->exec("SET time_zone = '{$time_zone}'");
        } catch (PDOException $e) {
            throw new Exception("MySQL 连接失败: " . $e->getMessage());
        }
    }

    /**
     * 插入单条记录
     *
     * @param string $table 表名
     * @param array $data 要插入的数据数组
     * @return array 返回操作结果 ['success' => bool, 'insert_id' => mixed] 或 ['success' => false, 'error' => string]
     */
    public function insertOne(string $table, array $data): array
    {
        try {
            $sid = new UniquenessId("INSERT_");
            $data['openid'] = $sid->getId();

            $columns = implode(', ', array_keys($data));
            $placeholders = ':' . implode(', :', array_keys($data));

            $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($data);

            return [
                'success' => true,
                'insert_id' => $this->pdo->lastInsertId()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * 批量插入多条记录
     *
     * @param string $table 表名
     * @param array $dataList 要插入的数据数组列表
     * @return array 返回操作结果 ['success' => bool, 'insert_count' => int] 或 ['success' => false, 'error' => string]
     */
    public function insertMany(string $table, array $dataList): array
    {
        try {
            if (empty($dataList)) {
                return [
                    'success' => false,
                    'error' => '数据列表不能为空'
                ];
            }

            // 为每条数据添加 openid
            foreach ($dataList as &$data) {
                $sid = new UniquenessId("INSERT-MANY_");
                $data['openid'] = $sid->getId();
            }
            unset($data);

            $columns = implode(', ', array_keys($dataList[0]));
            $placeholders = [];
            $values = [];

            foreach ($dataList as $index => $data) {
                $rowPlaceholders = [];
                foreach ($data as $key => $value) {
                    $paramName = ":col{$index}_{$key}";
                    $rowPlaceholders[] = $paramName;
                    $values[$paramName] = $value;
                }
                $placeholders[] = '(' . implode(', ', $rowPlaceholders) . ')';
            }

            $sql = "INSERT INTO {$table} ({$columns}) VALUES " . implode(', ', $placeholders);
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($values);

            return [
                'success' => true,
                'insert_count' => $stmt->rowCount()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * 查询单条记录
     *
     * @param string $table 表名
     * @param array $conditions 查询条件数组
     * @param string $orderBy 排序字段，默认为空
     * @return array|null 返回找到的记录数组，未找到则返回 null
     */
    public function findOne(string $table, array $conditions = [], string $orderBy = ''): ?array
    {
        try {
            $whereClause = '';
            $params = [];

            if (!empty($conditions)) {
                $whereParts = [];
                foreach ($conditions as $key => $value) {
                    $whereParts[] = "{$key} = :{$key}";
                    $params[":{$key}"] = $value;
                }
                $whereClause = 'WHERE ' . implode(' AND ', $whereParts);
            }

            $orderClause = '';
            if (!empty($orderBy)) {
                $orderClause = "ORDER BY {$orderBy}";
            }

            $sql = "SELECT * FROM {$table} {$whereClause} {$orderClause} LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            $result = $stmt->fetch();
            return $result ?: null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * 查询多条记录
     *
     * @param string $table 表名
     * @param array $conditions 查询条件数组
     * @param string $orderBy 排序字段，默认为空
     * @param int $limit 限制数量，默认为 0（不限制）
     * @param int $offset 偏移量，默认为 0
     * @return array 返回记录数组列表，未找到则返回空数组
     */
    public function find(string $table, array $conditions = [], string $orderBy = '', int $limit = 0, int $offset = 0): array
    {
        try {
            $whereClause = '';
            $params = [];

            if (!empty($conditions)) {
                $whereParts = [];
                foreach ($conditions as $key => $value) {
                    $whereParts[] = "{$key} = :{$key}";
                    $params[":{$key}"] = $value;
                }
                $whereClause = 'WHERE ' . implode(' AND ', $whereParts);
            }

            $orderClause = '';
            if (!empty($orderBy)) {
                $orderClause = "ORDER BY {$orderBy}";
            }

            $limitClause = '';
            if ($limit > 0) {
                $limitClause = "LIMIT {$limit}";
                if ($offset > 0) {
                    $limitClause .= " OFFSET {$offset}";
                }
            }

            $sql = "SELECT * FROM {$table} {$whereClause} {$orderClause} {$limitClause}";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * 更新记录
     *
     * @param string $table 表名
     * @param array $data 要更新的数据
     * @param array $conditions 更新条件
     * @return array 返回操作结果 ['success' => bool, 'affected_rows' => int] 或 ['success' => false, 'error' => string]
     */
    public function update(string $table, array $data, array $conditions): array
    {
        try {
            if (empty($data)) {
                return [
                    'success' => false,
                    'error' => '更新数据不能为空'
                ];
            }

            if (empty($conditions)) {
                return [
                    'success' => false,
                    'error' => '更新条件不能为空'
                ];
            }

            // 添加 openid
            $sid = new UniquenessId("UPDATE_");
            $data['openid'] = $sid->getId();

            $setParts = [];
            $params = [];

            foreach ($data as $key => $value) {
                $setParts[] = "{$key} = :set_{$key}";
                $params[":set_{$key}"] = $value;
            }

            $whereParts = [];
            foreach ($conditions as $key => $value) {
                $whereParts[] = "{$key} = :cond_{$key}";
                $params[":cond_{$key}"] = $value;
            }

            $sql = "UPDATE {$table} SET " . implode(', ', $setParts) . ' WHERE ' . implode(' AND ', $whereParts);
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            return [
                'success' => true,
                'affected_rows' => $stmt->rowCount()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * 删除记录
     *
     * @param string $table 表名
     * @param array $conditions 删除条件
     * @param int $limit 限制删除数量，默认为 0（不限制）
     * @return array 返回操作结果 ['success' => bool, 'affected_rows' => int] 或 ['success' => false, 'error' => string]
     */
    public function delete(string $table, array $conditions, int $limit = 0): array
    {
        try {
            if (empty($conditions)) {
                return [
                    'success' => false,
                    'error' => '删除条件不能为空'
                ];
            }

            $whereParts = [];
            $params = [];

            foreach ($conditions as $key => $value) {
                $whereParts[] = "{$key} = :{$key}";
                $params[":{$key}"] = $value;
            }

            $limitClause = '';
            if ($limit > 0) {
                $limitClause = "LIMIT {$limit}";
            }

            $sql = "DELETE FROM {$table} WHERE " . implode(' AND ', $whereParts) . " {$limitClause}";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            return [
                'success' => true,
                'affected_rows' => $stmt->rowCount()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * 统计记录数量
     *
     * @param string $table 表名
     * @param array $conditions 查询条件，默认为空数组（统计所有）
     * @return int 返回符合条件的记录数量，出错则返回 0
     */
    public function count(string $table, array $conditions = []): int
    {
        try {
            $whereClause = '';
            $params = [];

            if (!empty($conditions)) {
                $whereParts = [];
                foreach ($conditions as $key => $value) {
                    $whereParts[] = "{$key} = :{$key}";
                    $params[":{$key}"] = $value;
                }
                $whereClause = 'WHERE ' . implode(' AND ', $whereParts);
            }

            $sql = "SELECT COUNT(*) as total FROM {$table} {$whereClause}";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            $result = $stmt->fetch();
            return (int)$result['total'];
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * 执行自定义 SQL 查询
     *
     * @param string $sql SQL 语句
     * @param array $params 参数数组
     * @return array 返回查询结果数组，出错则返回空数组
     */
    public function query(string $sql, array $params = []): array
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * 执行自定义 SQL 命令（INSERT, UPDATE, DELETE）
     *
     * @param string $sql SQL 语句
     * @param array $params 参数数组
     * @return array 返回操作结果 ['success' => bool, 'affected_rows' => int] 或 ['success' => false, 'error' => string]
     */
    public function execute(string $sql, array $params = []): array
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            return [
                'success' => true,
                'affected_rows' => $stmt->rowCount()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * 开始事务
     *
     * @return bool 成功返回 true，失败返回 false
     */
    public function beginTransaction(): bool
    {
        try {
            return $this->pdo->beginTransaction();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 提交事务
     *
     * @return bool 成功返回 true，失败返回 false
     */
    public function commit(): bool
    {
        try {
            return $this->pdo->commit();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 回滚事务
     *
     * @return bool 成功返回 true，失败返回 false
     */
    public function rollback(): bool
    {
        try {
            return $this->pdo->rollBack();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 获取 PDO 实例
     *
     * @return PDO 返回 PDO 对象
     */
    public function getPDO(): PDO
    {
        return $this->pdo;
    }
}
