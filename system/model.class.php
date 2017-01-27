<?php
/**
 * Created by PhpStorm.
 * User: dusty
 * Date: 20.01.17
 * Time: 20:22
 */

abstract class Model
{
    private static $db;

    protected static $table;

    protected static $behaviours;
    protected static $fields;
    protected static $field_types;

    protected $values;
    protected $related_models = [];

    protected static function get_db()
    {
        if (self::$db === NULL)
        {
            require 'db.php';
            /**
             * @var $host string
             * @var $username string
             * @var $password string
             * @var $db_name string
             */
            self::$db = mysqli_connect($host, $username, $password, $db_name);

            mysqli_set_charset(self::$db, 'utf8');
        }

        return self::$db;
    }

    public function __construct($id = NULL)
    {
        if ($id !== NULL)
        {
            // TODO не грузит id
            $this->one($id);

            $this->id = $id;
        }
        else
        {
            foreach(static::get_fields() as $field)
            {
                // TODO инициализация в соответствии с protected static $field_types;
                $this->values[$field] = NULL;
            }
        }
    }

    public function __get($name)
    {
        if (in_array($name, static::get_fields()))
        {
            return $this->values[$name];
        }
        else
        {
            if (in_array($name, array_keys(static::$behaviours)))
            {
                // TODO описать one, many
                if (isset($this->related_models[$name]))
                {
                    return $this->related_models[$name];
                }

                if (static::$behaviours[$name]['type'] === 'one')
                {
                    if (isset(static::$behaviours[$name]['key']))
                    {
                        $id = static::$behaviours[$name]['key'];
                        $class_name = static::$behaviours[$name]['system'];

                        $object = new $class_name($id);
                        $this->related_models[$name] = $object;
                        return $object;
                    }
                }
                else // ко многим
                {
                    if (is_string(static::$behaviours[$name]['key'])) // отношение один ко многим
                    {
                        $id = static::$behaviours[$name]['key'];
                        $class_name = static::$behaviours[$name]['system'];

                        $list = $class_name::all("`$id` = '{$this->id}'");
                        $this->related_models[$name] = $list;
                        return $list;
                    }
                    if (is_array(static::$behaviours[$name]['key'])) // отношение много ко многим
                    {
                        foreach (static::$behaviours[$name]['key'] as $self_key => $foreign_key)
                        {
                        }

                        $table = static::$behaviours[$name]['table'];

                        $query = "SELECT `{$table}`.`{$foreign_key}` FROM `{$table}` WHERE `{$table}`.`{$self_key}` = '{$this->id}'";
                        echo $query;
                        $result = mysqli_query(self::get_db(), $query);

                        $ids = [];
                        while ($row = mysqli_fetch_assoc($result))
                        {
                            $ids[] = $row[$foreign_key];
                        }

                        $class_name = static::$behaviours[$name]['system'];

                        echo $class_name;

                        $list = $class_name::all(['id' => $ids]);
                        $this->related_models[$name] = $list;
                        return $list;
                    }
                }
            }
            throw new ErrorException('Model field does not exists: '. $name);
        }
    }

    public function __set($name, $value)
    {
        if (in_array($name, static::get_fields()))
        {
            if (static::get_field_types()[$name] === 'int')
            {
                $value = (int) $value;
            }
            else
            {
                $value = mysqli_real_escape_string(self::get_db(), $value);
            }

            $this->values[$name] = $value;

        }
        else
        {
            throw new ErrorException('Model field does not exists: '. $name);
        }
    }

    protected function one($id)
    {
        $query = "SELECT " . static::get_query_fields_list(true) . " FROM `" . static::$table .
            "` WHERE `id` = '{$id}'";

        $result = mysqli_query(self::get_db(), $query);
        if ($row = mysqli_fetch_assoc($result))
        {
            $this->load($row);
        }
    }

    public function load($data) // ['id' => 3];
    {
        foreach($data as $key => $value)
        {
            $this->$key = $value;
        }
    }

    public function save()
    {
        if ($this->id === NULL)
        {
            return $this->create();
        }
        else
        {
            return $this->update();
        }
    }

    protected function create()
    {
        $query = "INSERT INTO `" . static::$table . "` (" . static::get_query_fields_list() .
            ") VALUES (" . $this->get_query_values_list() . ")";

        $result = mysqli_query(self::get_db(), $query);

        if ($result)
        {
            $this->id = mysqli_insert_id(self::get_db());
            return $this->id;
        }
        else
        {
            throw new ErrorException('Data insert failed: '.$query);
        }
    }

    protected function update()
    {
        $query = "UPDATE `" . static::$table . "` SET " . $this->get_update_query_list() .
            " WHERE `id` = '{$this->id}'";

        $result = mysqli_query(self::get_db(), $query);

        if ($result)
        {
            return true;
        }
        else
        {
            throw new ErrorException('Data update failed: '.$query);
        }
    }

    public function delete()
    {
        $query = "DELETE FROM `" . static::$table . "` WHERE `id` = '{$this->id}'";

        $result = mysqli_query(self::get_db(), $query);

        if ($result)
        {
            return true;
        }
        else
        {
            throw new ErrorException('Data update failed: '.$query);
        }
    }

    public static function get_table()
    {
        return static::$table;
    }

    private static function type_cleaner($type)
    {
        return preg_replace("|\(.*\)|", "", $type);
    }

    protected static function describe_fields()
    {
        $query = 'DESCRIBE `' . static::$table . '`';
        $result = mysqli_query(self::get_db(), $query);

        static::$fields = [];
        static::$field_types = [];

        while ($row = mysqli_fetch_assoc($result))
        {
            static::$fields[] = $row['Field'];
            static::$field_types[$row['Field']] = self::type_cleaner($row['Type']);
        }
    }

    public static function get_fields()
    {
        if (static::$fields === NULL) static::describe_fields();

        return static::$fields;
    }

    public static function get_field_types()
    {
        if (static::$field_types === NULL) static::describe_fields();

        return static::$field_types;
    }

    protected static function get_query_fields_list($with_table = false)
    {
        $result = '';

        foreach (static::get_fields() as $field)
        {
            if ($result !== '')
            {
                $result .= ', ';
            }
            if ($with_table)
            {
                $result .= '`' . static::$table . '`.';
            }

            $result .= '`' . $field .'`';
        }

        return $result;
    }

    protected function get_query_values_list()
    {
        $result = '';

        foreach ($this->values as $value)
        {
            if ($result !== '') {
                $result .= ', ';
            }

            if ($value !== NULL)
            {
                $result .= "'{$value}'";
            }
            else
            {
                $result .= 'NULL';
            }

        }

        return $result;
    }

    protected function get_update_query_list()
    {
        $result = '';

        foreach (static::get_fields() as $field)
        {
            if ($field === 'id') continue;
            if ($result !== '')
            {
                $result .= ', ';
            }

            $result .= '`' . $field ."` = '{$this->values[$field]}'";
        }

        return $result;
    }

    // TODO $value check
    private static function where_generator($where = [])
    {
        $result = '';
        //['id' => 5, ['AND','caption' => 'hello','>']]
        foreach ($where as $key => $value)
        {
            if ($result !== '') $result .= ' AND ';
            if (!is_array($value))
            {
                $result .= "(`{$key}` = '{$value}')";
            }
            else
            {
                $value = "'".implode("', '", $value)."'";
                $result .= "(`{$key}` IN ({$value}))";
            }
        }

        return $result;
    }

    public static function all($where = '1', $with = [])
    {
        if (is_array($where))
        {
            echo '<br/><br/>';
            var_dump($where);
            echo '<br/><br/>';
            $where = self::where_generator($where);
        }

        $query = "SELECT " . static::get_query_fields_list(true) . " FROM `" . static::$table .
            "` WHERE {$where}";

        $result = mysqli_query(self::get_db(), $query);
        $all = [];

        $class_name = static::class;
        while ($row = mysqli_fetch_assoc($result))
        {
            $item = new $class_name();
            $item->load($row);

            $all[] = $item;
        }

        return $all;
    }

}
