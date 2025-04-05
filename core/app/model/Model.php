<?php
class Model {
    public static function one($query, $object) {
        if ($query) {
            $row = $query->fetch_array();
            if ($row) {
                foreach ($row as $key => $value) {
                    if (property_exists($object, $key)) {
                        $object->$key = $value;
                    }
                }
            }
        }
        return $object;
    }

    public static function many($query, $object) {
        $array = array();
        if ($query) {
            while ($row = $query->fetch_array()) {
                $new_object = new $object();
                foreach ($row as $key => $value) {
                    if (property_exists($new_object, $key)) {
                        $new_object->$key = $value;
                    }
                }
                array_push($array, $new_object);
            }
        }
        return $array;
    }
}
?> 