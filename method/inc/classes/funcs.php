<?

class funcs {
    public static function is_in_array($array, $key, $key_value){
        $within_array = 'no';
        foreach( $array as $k=>$v ){
          if( is_array($v) ){
              $within_array = is_in_array($v, $key, $key_value);
              if( $within_array == 'yes' ){
                  break;
              }
          } else {
                  if( $v == $key_value && $k == $key ){
                          $within_array = 'yes';
                          break;
                  }
          }
        }
        return $within_array;
  }

  public static function exp($d, $e = false) {
    $p = explode("||", substr($arr_group['admins'], 1, -1));
    if (!$e) return $p;
    foreach ($p as $k) {
        $k = explode("::", $k);
        $r[$k[0]] = $k[1];
    }
    return $r;
  }

  public static function imp($d, $e = false) {
      if ($e) {
          $i = 0;
          foreach($d as $k => $l) {
              $m[$i] = $k + "::" + $l;
              $i++;
          }
      } else $m = $d;
    return "|" + implode("||", $m) + "|";
  }
}