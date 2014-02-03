<?php
/**
 * TidyJSON
 *
 * A simple class for cleaning up poorly formatted JSON strings. No validation
 * is performed; if you pass in bogus data, you will get bogus output.
 */

class TidyJSON {
  protected static $default_config = array(
    'indent'    =>  '  ',
    'space'     =>  ' ',
  );

  protected static $string_chars = array('"', "'");
  protected static $esc_string_chars = array("\\\"", "\\'");
  protected static $white_chars = array(" ", "\t", "\n", "\r");
  protected static $newline_chars = array("\r", "\n");
  protected static $esc_newline_chars = array('', '\\n');

  /**
   * tidy
   * @param string $json JSON-formatted string you'd like to tidy
   * @param array $config Optional configuration values
   */
  public static function tidy($json, $config = null) {
    $config = self::get_config($config);
    $out = '';
    $level = 0;
    $strchr = null;
    $c = null;
    for ($x = 0; $x < strlen($json); $x++) {
      $lc = $c;
      $c = substr($json, $x, 1);
      if ($strchr === null) {
        if (in_array($c, self::$white_chars))
          continue;
        if (in_array($c, self::$string_chars)) {
          $strchr = $c;
        } else {
          if ($c == '{' || $c == '[') {
            $eol = true;
            $level++;
          } elseif ($c == '}' || $c == ']') {
            $level--;
            $out .= "\n" . self::indent($level, $config);
          } elseif ($c == ',') {
            $eol = true;
          } elseif ($c == ':') {
            $c .= $config['space'];
          }
        }
      } else {
        if ($c === $strchr && !in_array($lc.$c, self::$esc_string_chars)) {
          $strchr = null;
        }
        if(in_array($c, self::$newline_chars)) {
          $c = str_replace(self::$newline_chars, self::$esc_newline_chars, $c);
        }
      }
      $out .= $c;
      if ($eol) {
        $eol = false;
        $out .= "\n" . self::indent($level, $config);
      }
    }

    // Remove trailing whitespace
    while (in_array(substr($out, -1), self::$white_chars)) {
      $out = substr($out, 0, -1);
    }

    return $out;
  }

  protected static function indent($level, $config) {
    $out = '';
    for ($x = 0; $x < $level; $x++) $out .= $config['indent'];
    return $out;
  }

  protected static function get_config($config = null) {
    return is_array($config) ? array_merge(self::$default_config, $config) : self::$default_config;
  }


  /*! JSON.repair()
    v0.1 (c) Brant Wedel
    MIT License
  */
  public static function repair($json)
  {
    $pattern = '/,\s*}/';
    $replacement = '}';
    $json = preg_replace($pattern, $replacement, $json);
    $pattern = '/,\s*]/';
    $replacement = ']';
    $json = preg_replace($pattern, $replacement, $json);
    $pattern = '/,\s*$/';
    $replacement = '';
    $json = preg_replace($pattern, $replacement, $json);
    return $json;
  }

  public static function minify($json)
  {
    return TidyJSON::json_minify($json);
  }

  /*! JSON.minify()
    v0.1 (c) Kyle Simpson
    MIT License
  */

  private static function json_minify($json) {
    $tokenizer = "/\"|(\/\*)|(\*\/)|(\/\/)|\n|\r/";
    $in_string = false;
    $in_multiline_comment = false;
    $in_singleline_comment = false;
    $tmp; $tmp2; $new_str = array(); $ns = 0; $from = 0; $lc; $rc; $lastIndex = 0;

    while (preg_match($tokenizer,$json,$tmp,PREG_OFFSET_CAPTURE,$lastIndex)) {
      $tmp = $tmp[0];
      $lastIndex = $tmp[1] + strlen($tmp[0]);
      $lc = substr($json,0,$lastIndex - strlen($tmp[0]));
      $rc = substr($json,$lastIndex);
      if (!$in_multiline_comment && !$in_singleline_comment) {
        $tmp2 = substr($lc,$from);
        if (!$in_string) {
          $tmp2 = preg_replace("/(\n|\r|\s)*/","",$tmp2);
        }
        $new_str[] = $tmp2;
      }
      $from = $lastIndex;

      if ($tmp[0] == "\"" && !$in_multiline_comment && !$in_singleline_comment) {
        preg_match("/(\\\\)*$/",$lc,$tmp2);
        if (!$in_string || !$tmp2 || (strlen($tmp2[0]) % 2) == 0) { // start of string with ", or unescaped " character found to end string
          $in_string = !$in_string;
        }
        $from--; // include " character in next catch
        $rc = substr($json,$from);
      }
      else if ($tmp[0] == "/*" && !$in_string && !$in_multiline_comment && !$in_singleline_comment) {
        $in_multiline_comment = true;
      }
      else if ($tmp[0] == "*/" && !$in_string && $in_multiline_comment && !$in_singleline_comment) {
        $in_multiline_comment = false;
      }
      else if ($tmp[0] == "//" && !$in_string && !$in_multiline_comment && !$in_singleline_comment) {
        $in_singleline_comment = true;
      }
      else if (($tmp[0] == "\n" || $tmp[0] == "\r") && !$in_string && !$in_multiline_comment && $in_singleline_comment) {
        $in_singleline_comment = false;
      }
      else if (!$in_multiline_comment && !$in_singleline_comment && !(preg_match("/\n|\r|\s/",$tmp[0]))) {
        $new_str[] = $tmp[0];
      }
    }
    $new_str[] = $rc;
    return implode("",$new_str);
  }

}
