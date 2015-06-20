<?php

App::uses('S3', 'Vendor');

class S3Component extends Component
{
    /**
     * @var S3
     */
    protected $s3 = null;
    /**
     * @var Controller
     */
    protected static $s3_static = null;
    protected $controller = null;

    public function initialize(Controller $controller)
    {
        $this->s3 = new S3(S3_LOGIN, S3_SECRET, null, S3_ENDPOINT);
    }

    /**
     * Magic wrapper for S3
     * @param $func
     * @param $args
     * @return mixed
     */
    public function __call($func, $args)
    {
        if (is_null($this->s3)) {
            $this->s3 = new S3(S3_LOGIN, S3_SECRET, null, S3_ENDPOINT);
        }
        $obj = array('s3', $func);
        return call_user_func_array($obj, $args);
    }

    public static function __callStatic($func, $args)
    {
        if (is_null(self::$s3_static)) {
            self::$s3_static = new S3(S3_LOGIN, S3_SECRET, null, S3_ENDPOINT);
        }
        $obj = array('s3', $func);
        return call_user_func_array($obj, $args);
    }
} 