<?php
namespace hiperesp\server\storage;

abstract class Storage {

    protected abstract function _select(string $collection, array $where, ?int $limit): array;
    protected abstract function _insert(string $collection, array $document): void;
    protected abstract function _update(string $collection, array $where, array $document, ?int $limit): bool;

    protected abstract function _lastInsertId(): int;
    protected abstract function createCollection(string $collection): bool;
    protected abstract function dropCollection(string $collection): bool;

    protected string $dateFormat = "Y-m-d";
    protected string $dateTimeFormat = "Y-m-d H:i:s";

    final public function select(string $collection, array $where, ?int $limit = 1): array {
        $where['_isDeleted'] = 0;

        $data = $this->_select($collection, $where, $limit);

        foreach($data as $key => $document) {
            unset($document['_isDeleted']);
            $data[$key] = $document;
        }
        return $data;
    }

    final public function insert(string $collection, array $document): array {
        foreach(CollectionSetup::getStructure($collection) as $key => $definitions) {
            if(\in_array('CREATED_DATETIME', $definitions)) {
                $document[$key] = \date($this->dateTimeFormat);
                break;
            }
            if(\in_array('UPDATED_DATETIME', $definitions)) {
                $document[$key] = \date($this->dateTimeFormat);
                break;
            }
            if(isset($document[$key])) {
                if(\in_array('DATETIME', $definitions)) {
                    $document[$key] = \date($this->dateTimeFormat, \strtotime($document[$key]));
                    break;
                }
                if(\in_array('DATE', $definitions)) {
                    $document[$key] = \date($this->dateFormat, \strtotime($document[$key]));
                    break;
                }
            }
        }

        $this->_insert($collection, $document);

        $where = [];
        foreach(CollectionSetup::getStructure($collection) as $key => $definitions) {
            if(\in_array('PRIMARY_KEY', $definitions)) {
                if(isset($document[$key])) {
                    $where[$key] = $document[$key];
                    break;
                }
                $where[$key] = $this->_lastInsertId();
                break;
            }
        }

        $data = $this->select($collection, $where)[0];
        unset($data['_isDeleted']);

        return $data;
    }

    final public function update(string $collection, array $document): bool {
        $where = [];
        $where['_isDeleted'] = 0;

        $newFields = [];
        foreach(CollectionSetup::getStructure($collection) as $key => $definitions) {
            if(\in_array('UPDATED_DATETIME', $definitions)) {
                $document[$key] = \date($this->dateFormat);
            }
            if(isset($document[$key])) {
                if(\in_array('PRIMARY_KEY', $definitions)) {
                    $where[$key] = $document[$key];
                    continue;
                }
                if(\in_array('DATETIME', $definitions)) {
                    $document[$key] = \date($this->dateTimeFormat, \strtotime($document[$key]));
                }
                if(\in_array('DATE', $definitions)) {
                    $document[$key] = \date($this->dateFormat, \strtotime($document[$key]));
                }
                $newFields[$key] = $document[$key];
            }
        }
        if(\count($where) === 0) {
            throw new \Exception("No primary key found in update document");
        }
        return $this->_update($collection, $where, $newFields, 1);
    }
    public function delete(string $collection, array $document): bool {
        $where = [];
        foreach(CollectionSetup::getStructure($collection) as $key => $definitions) {
            if(\in_array('PRIMARY_KEY', $definitions)) {
                $where[$key] = $document[$key];
                break;
            }
        }
        if(\count($where) === 0) {
            throw new \Exception("No primary key found in delete document");
        }

        $updateFields = [ '_isDeleted' => $document[\array_keys($where)[0]] ];

        return $this->_update($collection, $where, $updateFields, 1);
    }

    private static Storage $instance;
    public static function getStorage(): Storage {
        $driver = \getenv("DB_DRIVER");
        $options = \json_decode(\getenv("DB_OPTIONS"), true);

        if(!isset(self::$instance)) {
            self::$instance = new $driver($options);
        }
        return self::$instance;
    }

    final public function setup(): void {
        foreach(CollectionSetup::getCollections() as $collection) {
            if(!$this->createCollection($collection)) {
                throw new \Exception("Setup error: Failed to create collection {$collection}");
            }

            foreach(CollectionSetup::getData($collection) as $data) {
                try {
                    $this->insert($collection, $data);
                } catch(\Exception $e) {
                    $this->dropCollection($collection);
                    throw new \Exception("Setup error: Failed to insert data into collection {$collection}: {$e->getMessage()}");
                }
            }
        }
    }
}