<?php
class Hscurl {
	protected $_post = array();
	protected $_retry = 0;
	protected $_ch = null;
    protected $_errno = 0;
    protected $_error = '';
    protected $_option = array();
	public function __construct(){
		$this->_option = array(
            'CURLOPT_TIMEOUT'        => 30,
            'CURLOPT_ENCODING'       => '',
            'CURLOPT_IPRESOLVE'      => 1,
            'CURLOPT_RETURNTRANSFER' => true,
            'CURLOPT_SSL_VERIFYPEER' => false,
            'CURLOPT_CONNECTTIMEOUT' => 10,
        );
	}
    /**
     * 出错自动重试N次
     * @param type $times
     * @return \curl
     */
    public function retry( $times = 0 ){
        $this->_retry = intval( $times );
        return $this;
    }
    /**
     * 
     * @param type $url
     */
    public function get( $url = '' ){
        return $this->set('CURLOPT_URL', $url)->_exec();
    }
    
    /**
     * 设置curl属性
     * @param type $item
     * @param type $value
     * @return \curl
     */
    public function set( $item , $value = '' ){
        if(is_array( $item )){
            foreach( $item as $key => $value){
                $this->_option[$key] = $value;
            }
        } else {
            $this->_option[$item] = $value;
        }
        return $this;
    }
    /**
     * 发送一个常规的POST请求
     * 类型为：application/x-www-form-urlencoded，就像表单提交的一样
     * @param type $url
     */
    public function submit( $url = '' ){
        return $this->get($url);
    }
    /**
     * 设置POST数据
     * @param type $data
     * @param type $value
     * @return \curl
     */
    public function post( $data , $value = ''){
        if( is_array($data) ){
            foreach( $data as $key => $value){
                $this->_post[$key] = $value;
            }
        } else {
            $this->_post[$data] = $value ;
        }
        return $this;
    }
    /**
     * 
     * @param type $name
     * @param type $arguments
     */
	public function __call($name, $arguments) {
        //;
    }
    /**
     * 执行curl操作
     * @return type
     */
	protected function _exec(){
		//初始化句柄
		$this->_ch = curl_init();
		//配置选项
		foreach($this->_option as $_k => $_v){
			curl_setopt($this->_ch,constant(strtoupper($_k)),$_v);
		}
		//POST选项
        if(!empty($this->_post)){
            curl_setopt($this->_ch, CURLOPT_POST, true);
            curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $this->_post);
        }
		//运行句柄
		$body = curl_exec($this->_ch);
		$info = curl_getinfo($this->_ch);
		//检查错误
        $this->_errno = curl_errno($this->_ch);
		//注销句柄
		curl_close($this->_ch);
		//自动重试
        if($this->_errno && $this->_retry){
            $this->_error = curl_error($this->_ch);
            $this->_exec();
        }
		//返回结果
		return $body;
	}
    /**
     * 返回错误信息
     * @return array array[0]:错误号 , array[1]:错误信息
     */
    public function getLastError(){
        return array($this->_errno, $this->_error);
    }
	public function __destruct() {
        $this->_ch = null;
        $this->_post = null;
        $this->_retry = null;
    }
}