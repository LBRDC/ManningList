<?php

class Connection
{

    static $instance;

    static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new mysqli('localhost', 'root', '', database: 'manninglist');
        }
        return self::$instance;
    }

    static function _executePostQuery($query, ...$params)
    {
        try {
            $stmt = self::getInstance()->prepare($query);
            if (!empty($params)) {
                $stmt->bind_param(str_repeat('s', count($params[0])), ...$params[0]);
            }
            $stmt->execute();
            return ['Error' => false, 'result' => $params];
        } catch (Exception $e) {
            return ['Error' => true, 'message' => $e->getMessage()];
        }
    }


    static function _executeQuery($query, ...$params)
    {
        try {
            $stmt = self::getInstance()->prepare($query);
            if (!empty($params)) {
                $stmt->bind_param(str_repeat('s', count($params[0])), ...$params[0]);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            return ["Error" => false, "data" => $result->fetch_all(MYSQLI_ASSOC), 'count' => $result->num_rows];
        } catch (Exception $e) {
            return ["Error" => true, "message" => $e->getMessage()];

        }
    }
}
