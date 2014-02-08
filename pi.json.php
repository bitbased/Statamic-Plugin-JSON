<?php

require_once('lib/tidyjson-extended.php');

class Plugin_json extends Plugin {

  var $meta = array(
    'name'       => 'JSON Builder',
    'version'    => '0.2.1',
    'author'     => 'Brant Wedel',
    'author_url' => 'http://bitbsed.net'
  );

  public function  hash()  { return $this->json("hash"); }
  public function  hash0() { return $this->json("hash"); }
  public function array0() { return $this->json("array"); }

  public function  hash1() { return $this->json("hash"); }
  public function array1() { return $this->json("array"); }
  public function  hash2() { return $this->json("hash"); }
  public function array2() { return $this->json("array"); }
  public function  hash3() { return $this->json("hash"); }
  public function array3() { return $this->json("array"); }
  public function  hash4() { return $this->jsno("hash"); }
  public function array4() { return $this->jsno("array"); }
  public function  hash5() { return $this->jsno("hash"); }
  public function array5() { return $this->jsno("array"); }
  public function  hash6() { return $this->json("hash"); }
  public function array6() { return $this->json("array"); }
  public function  hash7() { return $this->json("hash"); }
  public function array7() { return $this->json("array"); }
  public function  hash8() { return $this->json("hash"); }
  public function array8() { return $this->json("array"); }
  public function  hash9() { return $this->json("hash"); }
  public function array9() { return $this->json("array"); }

  public function pretty()
  {
    $json = Parse::template($this->content,array());
    return TidyJSON::tidy(TidyJSON::repair($json));
  }

  public function ugly()
  {
    $json = Parse::template($this->content,array());
    return TidyJSON::minify(TidyJSON::repair($json));
  }

  public function escape()
  {
    return htmlspecialchars($this->ugly());
  }

  public function index()
  {
    return $this->json("value");
  }

  public function json($type = "root")
  {
    $name = $this->fetch('name');
    $value = $this->fetch('value', null, false, false, false);
    if($name)
    {
      if ($value) return "\"$name\": ".json_encode($value).",";        
      $matches = array();
      if (preg_match("/{{\s?([A-Za-z_]+).*}}/i", $this->content, $matches) && $type == "array")
        return "\"$name\": [".$this->content."],";
      return "\"$name\": ".json_encode($this->content).",";
    }else{
      $matches = array();
      if (preg_match("/^\s?{{\s?([a-z_]+)\s?}}\s?$/i", $this->content, $matches))
      {
        $name = $matches[1];
        return "\"$name\": ".json_encode(Parse::template(trim($this->content),array())).",";
      }
      if ($value) return json_encode($value).",";        
      if($type=="hash") return "{".$this->content."},";
      if($type=="array") return "[".$this->content."],";
      return json_encode($this->content).",";
    }
  }

  public function encode()
  {
    return json_encode(Parse::template(trim($this->content),array()));
  }

}
