<?php
class EpiSession_Apc implements EpiSessionInterface
{
  private $key  = null;
  private $store= null;

  public function end()
  {
    apc_delete($this->key);
    setcookie(EpiSession::COOKIE, null, time()-86400);
  }

  public function get($key = null)
  {
    if(empty($key) || !isset($this->store[$key]))
      return false;

    return $this->store[$key];
  }

  public function getAll()
  {
    return apc_fetch($this->key);
  }

  public function set($key = null, $value = null)
  {
    if(empty($key))
      return false;
    
    $this->store[$key] = $value;
    apc_store($this->key, $this->store);
    return $value;
  }

  public function __construct($params = null)
  {
    if(!empty($params))
      $key = array_shift($params);

    if(empty($key) && empty($_COOKIE[EpiSession::COOKIE]))
    {
      $uid = md5(uniqid(rand(), true));
      setcookie(EpiSession::COOKIE, $uid, time()+1209600, '/');
      $_COOKIE[EpiSession::COOKIE] = $uid;
    }

    $this->key = empty($key) ? $_COOKIE[EpiSession::COOKIE] : $key;
    $this->store = $this->getAll();
  }
}
