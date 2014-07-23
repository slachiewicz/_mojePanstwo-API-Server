<?

/**
 * ApiRoute
 *
 * Allows to skip pattern checking in route parameters. Used in path generation for swagger
 * @see SwaggerController
 */
class ApiRoute extends CakeRoute {
    protected $skipPatterns = false;
    /**
     * Saves any skipPatterns flag
     *
     * @param array $url
     * @param array $params
     * @return array|void
     */
    public function persistParams($url, $params) {
        if (isset($url['skipPatterns']) && $url['skipPatterns']) {
            $this->skipPatterns = true;
            unset($url['skipPatterns']);
        }

        return parent::persistParams($url, $params);
    }

    /**
     * Skips pattern checking if invoked with Router::url(array(..., 'skipPatterns' => true));
     *
     * @param array $url
     * @return mixed|void
     */
    public function match($url) {
        if ($this->skipPatterns) {
            $originalOptions = $this->options;
            $this->options = array();

            $res = parent::match($url);

            $this->options = $originalOptions;
            return $res;

        } else {
            return parent::match($url);
        }

    }
}