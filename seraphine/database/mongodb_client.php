<?php

require_once __DIR__."/../tools/snow_id/snow_id.php";

use MongoDB\Driver\Manager;
use MongoDB\Driver\Query;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Command;

/**
 * MongoDB 客户端封装类
 * 基于 MongoDB 底层驱动 (ext-mongodb) 实现
 */
class MongoDB_Client
{
    private Manager $manager;
    private string $database;
    private array $config;

    /**
     * 构造函数 - 初始化 MongoDB 连接
     *
     * @param string $configFile 配置文件名（不含 .yml 后缀），默认为 'db'
     * @throws Exception 当 MongoDB 连接失败时抛出异常
     */
    public function __construct($configFile = 'db')
    {
        $this->config = ReadConfig::read_yml($configFile)['mongodb'];

        $username = $this->config['username'];
        $password = $this->config['password'];
        $dbname = $this->config['database'];
        $host = $this->config['host'];
        $port = $this->config['port'];
        $authSource = $this->config['auth_source'] ?? 'admin';

        $mongo_uri = "mongodb://$username:$password@$host:$port/$dbname?authSource=$authSource&maxPoolSize=100";

        try {
            $this->manager = new Manager($mongo_uri);
            $this->database = $dbname;
        } catch (Exception $e) {
            throw new Exception("MongoDB 连接失败: " . $e->getMessage());
        }
    }

    /**
     * 插入单条文档
     *
     * @param string $collection 集合名称
     * @param array $data 要插入的数据数组
     * @return array 返回操作结果 ['success' => bool, 'inserted_id' => mixed, 'matched_count' => int] 或 ['success' => false, 'error' => string]
     */
    public function insertOne(string $collection, array $data): array
    {
        try {
            $sid = new UniquenessId("INSERT_");
            $data['openid'] = $sid ->getId();

            $bulk = new BulkWrite();
            $insertedId = $bulk->insert($data);

            $result = $this->manager->executeBulkWrite(
                "{$this->database}.{$collection}",
                $bulk
            );

            return [
                'success' => true,
                'inserted_id' => $insertedId,
                'matched_count' => $result->getInsertedCount()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * 批量插入多条文档
     *
     * @param string $collection 集合名称
     * @param array $dataList 要插入的数据数组列表
     * @return array 返回操作结果 ['success' => bool, 'inserted_count' => int] 或 ['success' => false, 'error' => string]
     */
    public function insertMany(string $collection, array $dataList): array
    {
        try {
            $bulk = new BulkWrite();
            foreach ($dataList as $data) {
                $sid = new UniquenessId("INSERT-MANY_");
                $data['openid'] = $sid ->getId();

                $bulk->insert($data);
            }

            $result = $this->manager->executeBulkWrite(
                "{$this->database}.{$collection}",
                $bulk
            );

            return [
                'success' => true,
                'inserted_count' => $result->getInsertedCount()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * 查询单条文档
     *
     * @param string $collection 集合名称
     * @param array $filter 查询条件，默认为空数组（查询第一条）
     * @param array $options 查询选项（如 sort, projection 等）
     * @return array|null 返回找到的文档数组，未找到则返回 null
     */
    public function findOne(string $collection, array $filter = [], array $options = []): ?array
    {
        try {
            $options['limit'] = 1;
            $query = new Query($filter, $options);
            $cursor = $this->manager->executeQuery(
                "{$this->database}.{$collection}",
                $query
            );

            foreach ($cursor as $document) {
                return (array)$document;
            }

            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * 查询多条文档
     *
     * @param string $collection 集合名称
     * @param array $filter 查询条件，默认为空数组（查询所有）
     * @param array $options 查询选项（如 sort, limit, skip, projection 等）
     * @return array 返回文档数组列表，未找到则返回空数组
     */
    public function find(string $collection, array $filter = [], array $options = []): array
    {
        try {
            $query = new Query($filter, $options);
            $cursor = $this->manager->executeQuery(
                "{$this->database}.{$collection}",
                $query
            );

            $results = [];
            foreach ($cursor as $document) {
                $results[] = (array)$document;
            }
            return $results;
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * 更新单条文档
     *
     * @param string $collection 集合名称
     * @param array $filter 查询条件
     * @param array $update 更新操作（如 ['$set' => [...]]）
     * @param array $options 更新选项
     * @return array 返回操作结果 ['success' => bool, 'matched_count' => int, 'modified_count' => int] 或 ['success' => false, 'error' => string]
     */
    public function updateOne(string $collection, array $filter, array $update, array $options = []): array
    {
        try {
            if(!isset($update['openid'])){
                $sid = new UniquenessId("UPDATE_");
                $update['openid'] = $sid ->getId();
            }
            $bulk = new BulkWrite();
            $bulk->update($filter, $update, array_merge(['multi' => false], $options));

            $result = $this->manager->executeBulkWrite(
                "{$this->database}.{$collection}",
                $bulk
            );

            return [
                'success' => true,
                'matched_count' => $result->getMatchedCount(),
                'modified_count' => $result->getModifiedCount()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * 更新多条文档
     *
     * @param string $collection 集合名称
     * @param array $filter 查询条件
     * @param array $update 更新操作（如 ['$set' => [...]]）
     * @param array $options 更新选项
     * @return array 返回操作结果 ['success' => bool, 'matched_count' => int, 'modified_count' => int] 或 ['success' => false, 'error' => string]
     */
    public function updateMany(string $collection, array $filter, array $update, array $options = []): array
    {
        try {
            $bulk = new BulkWrite();
            $bulk->update($filter, $update, array_merge(['multi' => true], $options));

            $result = $this->manager->executeBulkWrite(
                "{$this->database}.{$collection}",
                $bulk
            );

            return [
                'success' => true,
                'matched_count' => $result->getMatchedCount(),
                'modified_count' => $result->getModifiedCount()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * 删除单条文档
     *
     * @param string $collection 集合名称
     * @param array $filter 查询条件
     * @return array 返回操作结果 ['success' => bool, 'deleted_count' => int] 或 ['success' => false, 'error' => string]
     */
    public function deleteOne(string $collection, array $filter): array
    {
        try {
            $bulk = new BulkWrite();
            $bulk->delete($filter, ['limit' => 1]);

            $result = $this->manager->executeBulkWrite(
                "{$this->database}.{$collection}",
                $bulk
            );

            return [
                'success' => true,
                'deleted_count' => $result->getDeletedCount()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * 删除多条文档
     *
     * @param string $collection 集合名称
     * @param array $filter 查询条件
     * @return array 返回操作结果 ['success' => bool, 'deleted_count' => int] 或 ['success' => false, 'error' => string]
     */
    public function deleteMany(string $collection, array $filter): array
    {
        try {
            $bulk = new BulkWrite();
            $bulk->delete($filter, ['limit' => 0]);

            $result = $this->manager->executeBulkWrite(
                "{$this->database}.{$collection}",
                $bulk
            );

            return [
                'success' => true,
                'deleted_count' => $result->getDeletedCount()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * 统计文档数量
     *
     * @param string $collection 集合名称
     * @param array $filter 查询条件，默认为空数组（统计所有）
     * @return int 返回符合条件的文档数量，出错则返回 0
     */
    public function countDocuments(string $collection, array $filter = []): int
    {
        try {
            $command = new Command([
                'count' => $collection,
                'query' => $filter
            ]);

            $result = $this->manager->executeCommand($this->database, $command);
            $response = current($result->toArray());

            return (int)$response->n;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * 聚合查询
     *
     * @param string $collection 集合名称
     * @param array $pipeline 聚合管道数组
     * @return array 返回聚合结果数组，出错则返回空数组
     */
    public function aggregate(string $collection, array $pipeline): array
    {
        try {
            $command = new Command([
                'aggregate' => $collection,
                'pipeline' => $pipeline,
                'cursor' => new stdClass()
            ]);

            $result = $this->manager->executeCommand($this->database, $command);
            $cursor = $result->getCursor();

            $results = [];
            foreach ($cursor as $document) {
                $results[] = (array)$document;
            }
            return $results;
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * 创建索引
     *
     * @param string $collection 集合名称
     * @param array $keys 索引键数组（如 ['username' => 1]）
     * @param array $options 索引选项（如 ['name' => 'idx_username', 'unique' => true]）
     * @return array 返回操作结果 ['success' => bool, 'index_name' => string] 或 ['success' => false, 'error' => string]
     */
    public function createIndex(string $collection, array $keys, array $options = []): array
    {
        try {
            $indexKeys = [];
            foreach ($keys as $field => $order) {
                $indexKeys[$field] = $order;
            }

            $command = new Command([
                'createIndexes' => $collection,
                'indexes' => [[
                    'key' => $indexKeys,
                    'name' => $options['name'] ?? null
                ]]
            ]);

            $this->manager->executeCommand($this->database, $command);

            return [
                'success' => true,
                'index_name' => $options['name'] ?? implode('_', array_keys($keys))
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * 删除集合
     *
     * @param string $collection 集合名称
     * @return array 返回操作结果 ['success' => bool] 或 ['success' => false, 'error' => string]
     */
    public function dropCollection(string $collection): array
    {
        try {
            $command = new Command(['drop' => $collection]);
            $this->manager->executeCommand($this->database, $command);

            return ['success' => true];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * 获取 MongoDB Manager 实例
     *
     * @return Manager 返回 MongoDB Driver Manager 对象
     */
    public function getManager(): Manager
    {
        return $this->manager;
    }

    /**
     * 获取当前数据库名称
     *
     * @return string 返回数据库名称
     */
    public function getDatabaseName(): string
    {
        return $this->database;
    }
}
