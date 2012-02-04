<?php

namespace ircmaxell\com\Sources;

use ircmaxell\com\Sources\MySQLi\Stmt;

class MySQLi extends \MySQLi {
    
    public function __construct(array $settings) {
        $settings += array(
            'username' => null,
            'password' => null,
            'database' => null,
            'host' => 'localhost',
            'charset' => 'utf8'
        );
        parent::__construct(
            $settings['host'], 
            $settings['username'], 
            $settings['password'],
            $settings['database']
        );
        $this->set_charset($settings['charset']);
    }

    public function query($query, array $params = array()) {
        $stmt = $this->prepare($query);
        if (!$stmt) {
            $this->throwError($query);
        }
        $values = array();
        $params = $this->prepareParams($params);
        foreach ($params as &$value) {
            $values[] = &$value;
        }
        if (!empty($values) && !call_user_func_array(array($stmt, 'bind_param'), $values)) {
            $this->throwError($query);
        }
        if (!$stmt->execute()) {
            $this->throwError($query);
        }
        return $stmt->get_result();
    }
    
    protected function prepareParams(array $params = array()) {
        $result = array('');
        foreach ($params as $param) {
            switch (strtolower(getType($param))) {
                case 'integer':
                case 'boolean':
                case 'resource':
                    $param = (int) $param;
                    $result[] = $param;
                    $result[0] .= 'i';
                    break;
                case 'double':
                case 'float':
                    $result[] = $param;
                    $result[0] .= 'd';
                    break;
                case 'object':
                case 'string':
                    $param = (string) $param;
                    $result[] = $param;
                    $result[0] .= 's';
                    break;
                case 'null':
                    $result[] = $param;
                    $result[0] .= 's';
                    break;
                default:
                    throw new \InvalidArgumentException('Invalid Param Passed: ' . getType($param));
            }
        }
        return $result;
    }
    
    protected function throwError($query) {
        throw new \RuntimeException(
            sprintf(
                'MySQL Error [%d] %s : %s',
                $this->errno,
                $this->error,
                $query
            )
        );
    }
}