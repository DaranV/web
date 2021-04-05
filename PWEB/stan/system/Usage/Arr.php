<?php
namespace Usage;

class Arr
{
    /**
     * Array Set
     *
     * @param array $array
     * @param string $path
     * @param string|array $value
     */
    public static function set(array &$array, string $path, $value)
    {
        $segments = explode('.', $path);
        while (count($segments) > 1) {
            $segment = array_shift($segments);
            if (!isset($array[$segment]) || !is_array($array[$segment])) {
                $array[$segment] = [];
            } //!isset($array[$segment]) || !is_array($array[$segment])
            $array =& $array[$segment];
        } //count($segments) > 1
        $array[array_shift($segments)] = $value;
    }

    /**
     * Array Has
     *
     * @param array $array
     * @param string $path
     * @return bool
     */

    public static function has(array $array, string $path) : bool
    {
        $segments = explode('.', $path);
        foreach ($segments as $segment) {
            if (!is_array($array) || !isset($array[$segment])) {
                return false;
            } //!is_array($array) || !isset($array[$segment])
            $array = $array[$segment];
        } //$segments as $segment
        
        return true;
    }

    /**
     * Array Get
     *
     * @param array $array
     * @param string $path
     * @param null|array|string $default
     * @return array|mixed|null
     */

    public static function get(array $array, string $path, $default = null)
    {
        $segments = explode('.', $path);
        foreach ($segments as $segment) {
            if (!is_array($array) || !isset($array[$segment])) {
                return $default;
            } //!is_array($array) || !isset($array[$segment])
            $array = $array[$segment];
        } //$segments as $segment
        
        return $array;
    }

    /**
     * Array Remove
     *
     * @param array $array
     * @param string $path
     * @return bool
     */

    public static function remove(array &$array, string $path) : bool
    {
        $segments = explode('.', $path);
        while (count($segments) > 1) {
            $segment = array_shift($segments);
            if (!isset($array[$segment]) || !is_array($array[$segment])) {
                return false;
            } //!isset($array[$segment]) || !is_array($array[$segment])
            $array =& $array[$segment];
        } //count($segments) > 1
        unset($array[array_shift($segments)]);
        
        return true;
    }

    /**
     * Array Rand
     *
     * @param array $array
     * @return mixed
     */

    public static function rand(array $array)
    {
        return $array[array_rand($array)];
    }

    /**
     * Array Is Assoc
     *
     * @param array $array
     * @return bool
     */

    public static function isAssoc(array $array) : bool
    {
        return count(array_filter(array_keys($array), 'is_string')) === count($array);
    }

    /**
     * Array Value
     *
     * @param array $array
     * @param string $key
     * @return array
     */

    public static function value(array $array, string $key) : array
    {
        return array_map(function($value) use ($key)
        {
            return is_object($value) ? $value->$key : $value[$key];
        }, $array);
    }

    /**
     * Array Is Empty
     *
     * @param array $array
     * @return bool
     */

    public static function isEmpty(array $array) : bool {
        foreach($array as $key => $value){
            if(empty($value)){
                return false;
            }
        }   
        return true;
    }

    /**
     * Array Merge
     *
     * @param array $array
     * @param array $arrays
     * @return array
     */

    public static function merge(array $array, array $arrays) : array{
        return array_merge($array, $arrays);
    }

    /**
     * Array Probability
     *
     * @return string
     */

    public static function probability()
    {
       $arg_list = func_get_args();
       $tablo = array();
       $start = 0;

       foreach($arg_list as $arg_curr)
       {
          if(!is_array($arg_curr)){return (false);}
          $tempo=array();
          list($tempo['name'], $tempo['probability']) = $arg_curr;
          $tempo['start']=$start;
          $tempo['end']=($start + $tempo['probability']);
          $start += $tempo['probability'];
          array_push($tablo, $tempo);
       }

       $result = mt_rand(0, 1000)/1000;
       foreach($tablo as $once)
       {
          if($result >= $once['start'] && $result <= $once['end'])
          {
             return($once['name']);
          }
       }
    }
}
