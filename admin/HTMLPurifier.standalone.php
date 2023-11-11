<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

if (!defined("HTMLPURIFIER_PREFIX")) {
    define("HTMLPURIFIER_PREFIX", dirname(__FILE__) . "/standalone");
    set_include_path(HTMLPURIFIER_PREFIX . PATH_SEPARATOR . get_include_path());
}
if (!defined("PHP_EOL")) {
    strtoupper(substr(PHP_OS, 0, 3));
    switch (strtoupper(substr(PHP_OS, 0, 3))) {
        case "WIN":
            define("PHP_EOL", "\r\n");
            break;
        case "DAR":
            define("PHP_EOL", "\r");
            break;
        default:
            define("PHP_EOL", "\n");
    }
}
class HTMLPurifier_Exception extends Exception
{
}
class HTMLPurifier_PropertyListIterator extends FilterIterator
{
    protected $l = NULL;
    protected $filter = NULL;
    public function __construct(Iterator $iterator, $filter = NULL)
    {
        parent::__construct($iterator);
        $this->l = strlen($filter);
        $this->filter = $filter;
    }
    public function accept()
    {
        $key = $this->getInnerIterator()->key();
        if (strncmp($key, $this->filter, $this->l) !== 0) {
            return false;
        }
        return true;
    }
}
class HTMLPurifier_StringHash extends ArrayObject
{
    protected $accessed = [];
    public function offsetGet($index)
    {
        $this->accessed[$index] = true;
        return parent::offsetGet($index);
    }
    public function getAccessed()
    {
        return $this->accessed;
    }
    public function resetAccessed()
    {
        $this->accessed = [];
    }
}
class HTMLPurifier_VarParserException extends HTMLPurifier_Exception
{
}
class HTMLPurifier
{
    public $version = "4.12.0";
    public $config = NULL;
    private $filters = [];
    private static $instance = NULL;
    protected $strategy = NULL;
    protected $generator = NULL;
    public $context = NULL;
    const VERSION = "4.12.0";
    public function __construct($config = NULL)
    {
        $this->config = HTMLPurifier_Config::create($config);
        $this->strategy = new HTMLPurifier_Strategy_Core();
    }
    public function addFilter($filter)
    {
        trigger_error("HTMLPurifier->addFilter() is deprecated, use configuration directives in the Filter namespace or Filter.Custom", 512);
        $this->filters[] = $filter;
    }
    public function purify($html, $config = NULL)
    {
        $config = $config ? HTMLPurifier_Config::create($config) : $this->config;
        $lexer = HTMLPurifier_Lexer::create($config);
        $context = new HTMLPurifier_Context();
        $this->generator = new HTMLPurifier_Generator($config, $context);
        $context->register("Generator", $this->generator);
        if ($config->get("Core.CollectErrors")) {
            $language_factory = HTMLPurifier_LanguageFactory::instance();
            $language = $language_factory->create($config, $context);
            $context->register("Locale", $language);
            $error_collector = new HTMLPurifier_ErrorCollector($context);
            $context->register("ErrorCollector", $error_collector);
        }
        $id_accumulator = HTMLPurifier_IDAccumulator::build($config, $context);
        $context->register("IDAccumulator", $id_accumulator);
        $html = HTMLPurifier_Encoder::convertToUTF8($html, $config, $context);
        $filter_flags = $config->getBatch("Filter");
        $custom_filters = $filter_flags["Custom"];
        unset($filter_flags["Custom"]);
        $filters = [];
        foreach ($filter_flags as $filter => $flag) {
            if ($flag) {
                if (strpos($filter, ".") === false) {
                    $class = "HTMLPurifier_Filter_" . $filter;
                    $filters[] = new $class();
                }
            }
        }
        foreach ($custom_filters as $filter) {
            $filters[] = $filter;
        }
        $filters = array_merge($filters, $this->filters);
        $i = 0;
        for ($filter_size = count($filters); $i < $filter_size; $i++) {
            $html = $filters[$i]->preFilter($html, $config, $context);
        }
        $html = $this->generator->generateFromTokens($this->strategy->execute($lexer->tokenizeHTML($html, $config, $context), $config, $context));
        for ($i = $filter_size - 1; 0 <= $i; $i--) {
            $html = $filters[$i]->postFilter($html, $config, $context);
        }
        $html = HTMLPurifier_Encoder::convertFromUTF8($html, $config, $context);
        $this->context =& $context;
        return $html;
    }
    public function purifyArray($array_of_html, $config = NULL)
    {
        $context_array = [];
        foreach ($array_of_html as $key => $value) {
            if (is_array($value)) {
                $array[$key] = $this->purifyArray($value, $config);
            } else {
                $array[$key] = $this->purify($value, $config);
            }
            $context_array[$key] = $this->context;
        }
        $this->context = $context_array;
        return $array;
    }
    public static function instance($prototype = NULL)
    {
        if (!self::$instance || $prototype) {
            if ($prototype instanceof HTMLPurifier) {
                self::$instance = $prototype;
            } else {
                if ($prototype) {
                    self::$instance = new HTMLPurifier($prototype);
                } else {
                    self::$instance = new HTMLPurifier();
                }
            }
        }
        return self::$instance;
    }
    public static function getInstance($prototype = NULL)
    {
        return HTMLPurifier::instance($prototype);
    }
}
class HTMLPurifier_Arborize
{
    public static function arborize($tokens, $config, $context)
    {
        $definition = $config->getHTMLDefinition();
        $parent = new HTMLPurifier_Token_Start($definition->info_parent);
        $stack = [$parent->toNode()];
        foreach ($tokens as $token) {
            $token->skip = NULL;
            $token->carryover = NULL;
            if ($token instanceof HTMLPurifier_Token_End) {
                $token->start = NULL;
                $r = array_pop($stack);
                $r->endCol = $token->col;
                $r->endLine = $token->line;
                $r->endArmor = $token->armor;
            } else {
                $node = $token->toNode();
                $stack[count($stack) - 1]->children[] = $node;
                if ($token instanceof HTMLPurifier_Token_Start) {
                    $stack[] = $node;
                }
            }
        }
        return $stack[0];
    }
    public static function flatten($node, $config, $context)
    {
        $level = 0;
        $nodes = [$level => new HTMLPurifier_Queue([$node])];
        $closingTokens = [];
        do {
            $tokens = [];
            while (!$nodes[$level]->isEmpty()) {
                $node = $nodes[$level]->shift();
                list($start, $end) = $node->toTokenPair();
                if (0 < $level) {
                    $tokens[] = $start;
                }
                if ($end !== NULL) {
                    $closingTokens[$level][] = $end;
                }
                if ($node instanceof HTMLPurifier_Node_Element) {
                    $level++;
                    $nodes[$level] = new HTMLPurifier_Queue();
                    foreach ($node->children as $childNode) {
                        $nodes[$level]->push($childNode);
                    }
                }
            }
            $level--;
            if ($level && isset($closingTokens[$level])) {
                while ($token = array_pop($closingTokens[$level])) {
                    $tokens[] = $token;
                }
            }
        } while (0 >= $level);
        return $tokens;
    }
}
class HTMLPurifier_AttrCollections
{
    public $info = [];
    public function __construct($attr_types, $modules)
    {
        $this->doConstruct($attr_types, $modules);
    }
    public function doConstruct($attr_types, $modules)
    {
        foreach ($modules as $module) {
            foreach ($module->attr_collections as $coll_i => $coll) {
                if (!isset($this->info[$coll_i])) {
                    $this->info[$coll_i] = [];
                }
                foreach ($coll as $attr_i => $attr) {
                    if ($attr_i === 0 && isset($this->info[$coll_i][$attr_i])) {
                        $this->info[$coll_i][$attr_i] = array_merge($this->info[$coll_i][$attr_i], $attr);
                    } else {
                        $this->info[$coll_i][$attr_i] = $attr;
                    }
                }
            }
        }
        foreach ($this->info as $name => $attr) {
            $this->performInclusions($this->info[$name]);
            $this->expandIdentifiers($this->info[$name], $attr_types);
        }
    }
    public function performInclusions(&$attr)
    {
        if (!isset($attr[0])) {
            return NULL;
        }
        $merge = $attr[0];
        $seen = [];
        for ($i = 0; isset($merge[$i]); $i++) {
            if (!isset($seen[$merge[$i]])) {
                $seen[$merge[$i]] = true;
                if (isset($this->info[$merge[$i]])) {
                    foreach ($this->info[$merge[$i]] as $key => $value) {
                        if (!isset($attr[$key])) {
                            $attr[$key] = $value;
                        }
                    }
                    if (isset($this->info[$merge[$i]][0])) {
                        $merge = array_merge($merge, $this->info[$merge[$i]][0]);
                    }
                }
            }
        }
        unset($attr[0]);
    }
    public function expandIdentifiers(&$attr, $attr_types)
    {
        $processed = [];
        foreach ($attr as $def_i => $def) {
            if ($def_i !== 0) {
                if (!isset($processed[$def_i])) {
                    if ($required = strpos($def_i, "*") !== false) {
                        unset($attr[$def_i]);
                        $def_i = trim($def_i, "*");
                        $attr[$def_i] = $def;
                    }
                    $processed[$def_i] = true;
                    if (is_object($def)) {
                        $attr[$def_i]->required = $required || $attr[$def_i]->required;
                    } else {
                        if ($def === false) {
                            unset($attr[$def_i]);
                        } else {
                            if ($t = $attr_types->get($def)) {
                                $attr[$def_i] = $t;
                                $attr[$def_i]->required = $required;
                            } else {
                                unset($attr[$def_i]);
                            }
                        }
                    }
                }
            }
        }
    }
}
abstract class HTMLPurifier_AttrDef
{
    public $minimized = false;
    public $required = false;
    public abstract function validate($string, $config, $context);
    public function parseCDATA($string)
    {
        $string = trim($string);
        $string = str_replace(["\n", "\t", "\r"], " ", $string);
        return $string;
    }
    public function make($string)
    {
        return $this;
    }
    protected function mungeRgb($string)
    {
        $p = "\\s*(\\d+(\\.\\d+)?([%]?))\\s*";
        if (preg_match("/(rgba|hsla)\\(/", $string)) {
            return preg_replace("/(rgba|hsla)\\(" . $p . "," . $p . "," . $p . "," . $p . "\\)/", "\\1(\\2,\\5,\\8,\\11)", $string);
        }
        return preg_replace("/(rgb|hsl)\\(" . $p . "," . $p . "," . $p . "\\)/", "\\1(\\2,\\5,\\8)", $string);
    }
    protected function expandCSSEscape($string)
    {
        $ret = "";
        $i = 0;
        $c = strlen($string);
        while ($i < $c) {
            if ($string[$i] === "\\") {
                $i++;
                if ($c <= $i) {
                    $ret .= "\\";
                } else {
                    if (ctype_xdigit($string[$i])) {
                        $code = $string[$i];
                        $a = 1;
                        $i++;
                        while ($i < $c && $a < 6) {
                            if (ctype_xdigit($string[$i])) {
                                $code .= $string[$i];
                                $i++;
                                $a++;
                            }
                        }
                        $char = HTMLPurifier_Encoder::unichr(hexdec($code));
                        if (HTMLPurifier_Encoder::cleanUTF8($char) !== "") {
                            $ret .= $char;
                            if ($i < $c && trim($string[$i]) !== "") {
                                $i--;
                            }
                        }
                    } else {
                        if ($string[$i] !== "\n") {
                        }
                    }
                    $i++;
                }
            }
            $ret .= $string[$i];
        }
        return $ret;
    }
}
abstract class HTMLPurifier_AttrTransform
{
    public abstract function transform($attr, $config, $context);
    public function prependCSS(&$attr, $css)
    {
        $attr["style"] = isset($attr["style"]) ? $attr["style"] : "";
        $attr["style"] = $css . $attr["style"];
    }
    public function confiscateAttr(&$attr, $key)
    {
        if (!isset($attr[$key])) {
            return NULL;
        }
        $value = $attr[$key];
        unset($attr[$key]);
        return $value;
    }
}
class HTMLPurifier_AttrTypes
{
    protected $info = [];
    public function __construct()
    {
        $this->info["Enum"] = new HTMLPurifier_AttrDef_Enum();
        $this->info["Bool"] = new HTMLPurifier_AttrDef_HTML_Bool();
        $this->info["CDATA"] = new HTMLPurifier_AttrDef_Text();
        $this->info["ID"] = new HTMLPurifier_AttrDef_HTML_ID();
        $this->info["Length"] = new HTMLPurifier_AttrDef_HTML_Length();
        $this->info["MultiLength"] = new HTMLPurifier_AttrDef_HTML_MultiLength();
        $this->info["NMTOKENS"] = new HTMLPurifier_AttrDef_HTML_Nmtokens();
        $this->info["Pixels"] = new HTMLPurifier_AttrDef_HTML_Pixels();
        $this->info["Text"] = new HTMLPurifier_AttrDef_Text();
        $this->info["URI"] = new HTMLPurifier_AttrDef_URI();
        $this->info["LanguageCode"] = new HTMLPurifier_AttrDef_Lang();
        $this->info["Color"] = new HTMLPurifier_AttrDef_HTML_Color();
        $this->info["IAlign"] = self::makeEnum("top,middle,bottom,left,right");
        $this->info["LAlign"] = self::makeEnum("top,bottom,left,right");
        $this->info["FrameTarget"] = new HTMLPurifier_AttrDef_HTML_FrameTarget();
        $this->info["ContentType"] = new HTMLPurifier_AttrDef_Text();
        $this->info["ContentTypes"] = new HTMLPurifier_AttrDef_Text();
        $this->info["Charsets"] = new HTMLPurifier_AttrDef_Text();
        $this->info["Character"] = new HTMLPurifier_AttrDef_Text();
        $this->info["Class"] = new HTMLPurifier_AttrDef_HTML_Class();
        $this->info["Number"] = new HTMLPurifier_AttrDef_Integer(false, false, true);
    }
    private static function makeEnum($in)
    {
        return new HTMLPurifier_AttrDef_Clone(new HTMLPurifier_AttrDef_Enum(explode(",", $in)));
    }
    public function get($type)
    {
        if (strpos($type, "#") !== false) {
            list($type, $string) = explode("#", $type, 2);
        } else {
            $string = "";
        }
        if (!isset($this->info[$type])) {
            trigger_error("Cannot retrieve undefined attribute type " . $type, 256);
        } else {
            return $this->info[$type]->make($string);
        }
    }
    public function set($type, $impl)
    {
        $this->info[$type] = $impl;
    }
}
class HTMLPurifier_AttrValidator
{
    public function validateToken($token, $config, $context)
    {
        $definition = $config->getHTMLDefinition();
        $e =& $context->get("ErrorCollector", true);
        $ok =& $context->get("IDAccumulator", true);
        if (!$ok) {
            $id_accumulator = HTMLPurifier_IDAccumulator::build($config, $context);
            $context->register("IDAccumulator", $id_accumulator);
        }
        $current_token =& $context->get("CurrentToken", true);
        if (!$current_token) {
            $context->register("CurrentToken", $token);
        }
        if (!$token instanceof HTMLPurifier_Token_Start && !$token instanceof HTMLPurifier_Token_Empty) {
            return NULL;
        }
        $d_defs = $definition->info_global_attr;
        $attr = $token->attr;
        foreach ($definition->info_attr_transform_pre as $transform) {
            $attr = $transform->transform($o = $attr, $config, $context);
            if ($e && $attr != $o) {
                $e->send(8, "AttrValidator: Attributes transformed", $o, $attr);
            }
        }
        foreach ($definition->info[$token->name]->attr_transform_pre as $transform) {
            $attr = $transform->transform($o = $attr, $config, $context);
            if ($e && $attr != $o) {
                $e->send(8, "AttrValidator: Attributes transformed", $o, $attr);
            }
        }
        $defs = $definition->info[$token->name]->attr;
        $attr_key = false;
        $context->register("CurrentAttr", $attr_key);
        foreach ($attr as $attr_key => $value) {
            if (isset($defs[$attr_key])) {
                if ($defs[$attr_key] === false) {
                    $result = false;
                } else {
                    $result = $defs[$attr_key]->validate($value, $config, $context);
                }
            } else {
                if (isset($d_defs[$attr_key])) {
                    $result = $d_defs[$attr_key]->validate($value, $config, $context);
                } else {
                    $result = false;
                }
            }
            if ($result === false || $result === NULL) {
                if ($e) {
                    $e->send(1, "AttrValidator: Attribute removed");
                }
                unset($attr[$attr_key]);
            } else {
                if (is_string($result)) {
                    $attr[$attr_key] = $result;
                }
            }
        }
        $context->destroy("CurrentAttr");
        foreach ($definition->info_attr_transform_post as $transform) {
            $attr = $transform->transform($o = $attr, $config, $context);
            if ($e && $attr != $o) {
                $e->send(8, "AttrValidator: Attributes transformed", $o, $attr);
            }
        }
        foreach ($definition->info[$token->name]->attr_transform_post as $transform) {
            $attr = $transform->transform($o = $attr, $config, $context);
            if ($e && $attr != $o) {
                $e->send(8, "AttrValidator: Attributes transformed", $o, $attr);
            }
        }
        $token->attr = $attr;
        if (!$current_token) {
            $context->destroy("CurrentToken");
        }
    }
}
class HTMLPurifier_Bootstrap
{
    public static function autoload($class)
    {
        $file = HTMLPurifier_Bootstrap::getPath($class);
        if (!$file) {
            return false;
        }
        require_once HTMLPURIFIER_PREFIX . "/" . $file;
        return true;
    }
    public static function getPath($class)
    {
        if (strncmp("HTMLPurifier", $class, 12) !== 0) {
            return false;
        }
        if (strncmp("HTMLPurifier_Language_", $class, 22) === 0) {
            $code = str_replace("_", "-", substr($class, 22));
            $file = "HTMLPurifier/Language/classes/" . $code . ".php";
        } else {
            $file = str_replace("_", "/", $class) . ".php";
        }
        if (!file_exists(HTMLPURIFIER_PREFIX . "/" . $file)) {
            return false;
        }
        return $file;
    }
    public static function registerAutoload()
    {
        $autoload = ["HTMLPurifier_Bootstrap", "autoload"];
        if (($funcs = spl_autoload_functions()) === false) {
            spl_autoload_register($autoload);
        } else {
            if (function_exists("spl_autoload_unregister")) {
                if (version_compare(PHP_VERSION, "5.3.0", ">=")) {
                    spl_autoload_register($autoload, true, true);
                } else {
                    $buggy = version_compare(PHP_VERSION, "5.2.11", "<");
                    $compat = version_compare(PHP_VERSION, "5.1.2", "<=") && version_compare(PHP_VERSION, "5.1.0", ">=");
                    foreach ($funcs as $func) {
                        if ($buggy && is_array($func)) {
                            $reflector = new ReflectionMethod($func[0], $func[1]);
                            if (!$reflector->isStatic()) {
                                throw new Exception("HTML Purifier autoloader registrar is not compatible\r\n                                with non-static object methods due to PHP Bug #44144;\r\n                                Please do not use HTMLPurifier.autoload.php (or any\r\n                                file that includes this file); instead, place the code:\r\n                                spl_autoload_register(array('HTMLPurifier_Bootstrap', 'autoload'))\r\n                                after your own autoloaders.");
                            }
                            if ($compat) {
                                $func = implode("::", $func);
                            }
                        }
                        spl_autoload_unregister($func);
                    }
                    spl_autoload_register($autoload);
                    foreach ($funcs as $func) {
                        spl_autoload_register($func);
                    }
                }
            }
        }
    }
}
abstract class HTMLPurifier_Definition
{
    public $setup = false;
    public $optimized = NULL;
    public $type = NULL;
    protected abstract function doSetup($config);
    public function setup($config)
    {
        if ($this->setup) {
            return NULL;
        }
        $this->setup = true;
        $this->doSetup($config);
    }
}
class HTMLPurifier_CSSDefinition extends HTMLPurifier_Definition
{
    public $type = "CSS";
    public $info = [];
    protected function doSetup($config)
    {
        $this->info["text-align"] = new HTMLPurifier_AttrDef_Enum(["left", "right", "center", "justify"], false);
        $this->info["border-top-style"] = new HTMLPurifier_AttrDef_Enum(["none", "hidden", "dotted", "dashed", "solid", "double", "groove", "ridge", "inset", "outset"], false);
        $this->info["border-left-style"] = $this->info["border-top-style"];
        $this->info["border-right-style"] = $this->info["border-left-style"];
        $this->info["border-bottom-style"] = $this->info["border-right-style"];
        $border_style = $this->info["border-bottom-style"];
        $this->info["border-style"] = new HTMLPurifier_AttrDef_CSS_Multiple($border_style);
        $this->info["clear"] = new HTMLPurifier_AttrDef_Enum(["none", "left", "right", "both"], false);
        $this->info["float"] = new HTMLPurifier_AttrDef_Enum(["none", "left", "right"], false);
        $this->info["font-style"] = new HTMLPurifier_AttrDef_Enum(["normal", "italic", "oblique"], false);
        $this->info["font-variant"] = new HTMLPurifier_AttrDef_Enum(["normal", "small-caps"], false);
        $uri_or_none = new HTMLPurifier_AttrDef_CSS_Composite([new HTMLPurifier_AttrDef_Enum(["none"]), new HTMLPurifier_AttrDef_CSS_URI()]);
        $this->info["list-style-position"] = new HTMLPurifier_AttrDef_Enum(["inside", "outside"], false);
        $this->info["list-style-type"] = new HTMLPurifier_AttrDef_Enum(["disc", "circle", "square", "decimal", "lower-roman", "upper-roman", "lower-alpha", "upper-alpha", "none"], false);
        $this->info["list-style-image"] = $uri_or_none;
        $this->info["list-style"] = new HTMLPurifier_AttrDef_CSS_ListStyle($config);
        $this->info["text-transform"] = new HTMLPurifier_AttrDef_Enum(["capitalize", "uppercase", "lowercase", "none"], false);
        $this->info["color"] = new HTMLPurifier_AttrDef_CSS_Color();
        $this->info["background-image"] = $uri_or_none;
        $this->info["background-repeat"] = new HTMLPurifier_AttrDef_Enum(["repeat", "repeat-x", "repeat-y", "no-repeat"]);
        $this->info["background-attachment"] = new HTMLPurifier_AttrDef_Enum(["scroll", "fixed"]);
        $this->info["background-position"] = new HTMLPurifier_AttrDef_CSS_BackgroundPosition();
        $this->info["background-color"] = new HTMLPurifier_AttrDef_CSS_Composite([new HTMLPurifier_AttrDef_Enum(["transparent"]), new HTMLPurifier_AttrDef_CSS_Color()]);
        $this->info["border-right-color"] = $this->info["background-color"];
        $this->info["border-left-color"] = $this->info["border-right-color"];
        $this->info["border-bottom-color"] = $this->info["border-left-color"];
        $this->info["border-top-color"] = $this->info["border-bottom-color"];
        $border_color = $this->info["border-top-color"];
        $this->info["background"] = new HTMLPurifier_AttrDef_CSS_Background($config);
        $this->info["border-color"] = new HTMLPurifier_AttrDef_CSS_Multiple($border_color);
        $this->info["border-right-width"] = new HTMLPurifier_AttrDef_CSS_Composite([new HTMLPurifier_AttrDef_Enum(["thin", "medium", "thick"]), new HTMLPurifier_AttrDef_CSS_Length("0")]);
        $this->info["border-left-width"] = $this->info["border-right-width"];
        $this->info["border-bottom-width"] = $this->info["border-left-width"];
        $this->info["border-top-width"] = $this->info["border-bottom-width"];
        $border_width = $this->info["border-top-width"];
        $this->info["border-width"] = new HTMLPurifier_AttrDef_CSS_Multiple($border_width);
        $this->info["letter-spacing"] = new HTMLPurifier_AttrDef_CSS_Composite([new HTMLPurifier_AttrDef_Enum(["normal"]), new HTMLPurifier_AttrDef_CSS_Length()]);
        $this->info["word-spacing"] = new HTMLPurifier_AttrDef_CSS_Composite([new HTMLPurifier_AttrDef_Enum(["normal"]), new HTMLPurifier_AttrDef_CSS_Length()]);
        $this->info["font-size"] = new HTMLPurifier_AttrDef_CSS_Composite([new HTMLPurifier_AttrDef_Enum(["xx-small", "x-small", "small", "medium", "large", "x-large", "xx-large", "larger", "smaller"]), new HTMLPurifier_AttrDef_CSS_Percentage(), new HTMLPurifier_AttrDef_CSS_Length()]);
        $this->info["line-height"] = new HTMLPurifier_AttrDef_CSS_Composite([new HTMLPurifier_AttrDef_Enum(["normal"]), new HTMLPurifier_AttrDef_CSS_Number(true), new HTMLPurifier_AttrDef_CSS_Length("0"), new HTMLPurifier_AttrDef_CSS_Percentage(true)]);
        $this->info["margin-right"] = new HTMLPurifier_AttrDef_CSS_Composite([new HTMLPurifier_AttrDef_CSS_Length(), new HTMLPurifier_AttrDef_CSS_Percentage(), new HTMLPurifier_AttrDef_Enum(["auto"])]);
        $this->info["margin-left"] = $this->info["margin-right"];
        $this->info["margin-bottom"] = $this->info["margin-left"];
        $this->info["margin-top"] = $this->info["margin-bottom"];
        $margin = $this->info["margin-top"];
        $this->info["margin"] = new HTMLPurifier_AttrDef_CSS_Multiple($margin);
        $this->info["padding-right"] = new HTMLPurifier_AttrDef_CSS_Composite([new HTMLPurifier_AttrDef_CSS_Length("0"), new HTMLPurifier_AttrDef_CSS_Percentage(true)]);
        $this->info["padding-left"] = $this->info["padding-right"];
        $this->info["padding-bottom"] = $this->info["padding-left"];
        $this->info["padding-top"] = $this->info["padding-bottom"];
        $padding = $this->info["padding-top"];
        $this->info["padding"] = new HTMLPurifier_AttrDef_CSS_Multiple($padding);
        $this->info["text-indent"] = new HTMLPurifier_AttrDef_CSS_Composite([new HTMLPurifier_AttrDef_CSS_Length(), new HTMLPurifier_AttrDef_CSS_Percentage()]);
        $trusted_wh = new HTMLPurifier_AttrDef_CSS_Composite([new HTMLPurifier_AttrDef_CSS_Length("0"), new HTMLPurifier_AttrDef_CSS_Percentage(true), new HTMLPurifier_AttrDef_Enum(["auto", "initial", "inherit"])]);
        $trusted_min_wh = new HTMLPurifier_AttrDef_CSS_Composite([new HTMLPurifier_AttrDef_CSS_Length("0"), new HTMLPurifier_AttrDef_CSS_Percentage(true), new HTMLPurifier_AttrDef_Enum(["initial", "inherit"])]);
        $trusted_max_wh = new HTMLPurifier_AttrDef_CSS_Composite([new HTMLPurifier_AttrDef_CSS_Length("0"), new HTMLPurifier_AttrDef_CSS_Percentage(true), new HTMLPurifier_AttrDef_Enum(["none", "initial", "inherit"])]);
        $max = $config->get("CSS.MaxImgLength");
        $this->info["height"] = $max === NULL ? $trusted_wh : new HTMLPurifier_AttrDef_Switch("img", new HTMLPurifier_AttrDef_CSS_Composite([new HTMLPurifier_AttrDef_CSS_Length("0", $max), new HTMLPurifier_AttrDef_Enum(["auto"])]), $trusted_wh);
        $this->info["width"] = $this->info["height"];
        $this->info["min-height"] = $max === NULL ? $trusted_min_wh : new HTMLPurifier_AttrDef_Switch("img", new HTMLPurifier_AttrDef_CSS_Composite([new HTMLPurifier_AttrDef_CSS_Length("0", $max), new HTMLPurifier_AttrDef_Enum(["initial", "inherit"])]), $trusted_min_wh);
        $this->info["min-width"] = $this->info["min-height"];
        $this->info["max-height"] = $max === NULL ? $trusted_max_wh : new HTMLPurifier_AttrDef_Switch("img", new HTMLPurifier_AttrDef_CSS_Composite([new HTMLPurifier_AttrDef_CSS_Length("0", $max), new HTMLPurifier_AttrDef_Enum(["none", "initial", "inherit"])]), $trusted_max_wh);
        $this->info["max-width"] = $this->info["max-height"];
        $this->info["text-decoration"] = new HTMLPurifier_AttrDef_CSS_TextDecoration();
        $this->info["font-family"] = new HTMLPurifier_AttrDef_CSS_FontFamily();
        $this->info["font-weight"] = new HTMLPurifier_AttrDef_Enum(["normal", "bold", "bolder", "lighter", "100", "200", "300", "400", "500", "600", "700", "800", "900"], false);
        $this->info["font"] = new HTMLPurifier_AttrDef_CSS_Font($config);
        $this->info["border-right"] = new HTMLPurifier_AttrDef_CSS_Border($config);
        $this->info["border-left"] = $this->info["border-right"];
        $this->info["border-top"] = $this->info["border-left"];
        $this->info["border-bottom"] = $this->info["border-top"];
        $this->info["border"] = $this->info["border-bottom"];
        $this->info["border-collapse"] = new HTMLPurifier_AttrDef_Enum(["collapse", "separate"]);
        $this->info["caption-side"] = new HTMLPurifier_AttrDef_Enum(["top", "bottom"]);
        $this->info["table-layout"] = new HTMLPurifier_AttrDef_Enum(["auto", "fixed"]);
        $this->info["vertical-align"] = new HTMLPurifier_AttrDef_CSS_Composite([new HTMLPurifier_AttrDef_Enum(["baseline", "sub", "super", "top", "text-top", "middle", "bottom", "text-bottom"]), new HTMLPurifier_AttrDef_CSS_Length(), new HTMLPurifier_AttrDef_CSS_Percentage()]);
        $this->info["border-spacing"] = new HTMLPurifier_AttrDef_CSS_Multiple(new HTMLPurifier_AttrDef_CSS_Length(), 2);
        $this->info["white-space"] = new HTMLPurifier_AttrDef_Enum(["nowrap", "normal", "pre", "pre-wrap", "pre-line"]);
        if ($config->get("CSS.Proprietary")) {
            $this->doSetupProprietary($config);
        }
        if ($config->get("CSS.AllowTricky")) {
            $this->doSetupTricky($config);
        }
        if ($config->get("CSS.Trusted")) {
            $this->doSetupTrusted($config);
        }
        $allow_important = $config->get("CSS.AllowImportant");
        foreach ($this->info as $k => $v) {
            $this->info[$k] = new HTMLPurifier_AttrDef_CSS_ImportantDecorator($v, $allow_important);
        }
        $this->setupConfigStuff($config);
    }
    protected function doSetupProprietary($config)
    {
        $this->info["scrollbar-arrow-color"] = new HTMLPurifier_AttrDef_CSS_Color();
        $this->info["scrollbar-base-color"] = new HTMLPurifier_AttrDef_CSS_Color();
        $this->info["scrollbar-darkshadow-color"] = new HTMLPurifier_AttrDef_CSS_Color();
        $this->info["scrollbar-face-color"] = new HTMLPurifier_AttrDef_CSS_Color();
        $this->info["scrollbar-highlight-color"] = new HTMLPurifier_AttrDef_CSS_Color();
        $this->info["scrollbar-shadow-color"] = new HTMLPurifier_AttrDef_CSS_Color();
        $this->info["-moz-opacity"] = new HTMLPurifier_AttrDef_CSS_AlphaValue();
        $this->info["-khtml-opacity"] = new HTMLPurifier_AttrDef_CSS_AlphaValue();
        $this->info["filter"] = new HTMLPurifier_AttrDef_CSS_Filter();
        $this->info["page-break-before"] = new HTMLPurifier_AttrDef_Enum(["auto", "always", "avoid", "left", "right"]);
        $this->info["page-break-after"] = $this->info["page-break-before"];
        $this->info["page-break-inside"] = new HTMLPurifier_AttrDef_Enum(["auto", "avoid"]);
        $border_radius = new HTMLPurifier_AttrDef_CSS_Composite([new HTMLPurifier_AttrDef_CSS_Percentage(true), new HTMLPurifier_AttrDef_CSS_Length("0")]);
        $this->info["border-bottom-left-radius"] = new HTMLPurifier_AttrDef_CSS_Multiple($border_radius, 2);
        $this->info["border-bottom-right-radius"] = $this->info["border-bottom-left-radius"];
        $this->info["border-top-right-radius"] = $this->info["border-bottom-right-radius"];
        $this->info["border-top-left-radius"] = $this->info["border-top-right-radius"];
        $this->info["border-radius"] = new HTMLPurifier_AttrDef_CSS_Multiple($border_radius, 4);
    }
    protected function doSetupTricky($config)
    {
        $this->info["display"] = new HTMLPurifier_AttrDef_Enum(["inline", "block", "list-item", "run-in", "compact", "marker", "table", "inline-block", "inline-table", "table-row-group", "table-header-group", "table-footer-group", "table-row", "table-column-group", "table-column", "table-cell", "table-caption", "none"]);
        $this->info["visibility"] = new HTMLPurifier_AttrDef_Enum(["visible", "hidden", "collapse"]);
        $this->info["overflow"] = new HTMLPurifier_AttrDef_Enum(["visible", "hidden", "auto", "scroll"]);
        $this->info["opacity"] = new HTMLPurifier_AttrDef_CSS_AlphaValue();
    }
    protected function doSetupTrusted($config)
    {
        $this->info["position"] = new HTMLPurifier_AttrDef_Enum(["static", "relative", "absolute", "fixed"]);
        $this->info["bottom"] = new HTMLPurifier_AttrDef_CSS_Composite([new HTMLPurifier_AttrDef_CSS_Length(), new HTMLPurifier_AttrDef_CSS_Percentage(), new HTMLPurifier_AttrDef_Enum(["auto"])]);
        $this->info["right"] = $this->info["bottom"];
        $this->info["left"] = $this->info["right"];
        $this->info["top"] = $this->info["left"];
        $this->info["z-index"] = new HTMLPurifier_AttrDef_CSS_Composite([new HTMLPurifier_AttrDef_Integer(), new HTMLPurifier_AttrDef_Enum(["auto"])]);
    }
    protected function setupConfigStuff($config)
    {
        $support = "(for information on implementing this, see the support forums) ";
        $allowed_properties = $config->get("CSS.AllowedProperties");
        if ($allowed_properties !== NULL) {
            foreach ($this->info as $name => $d) {
                if (!isset($allowed_properties[$name])) {
                    unset($this->info[$name]);
                }
                unset($allowed_properties[$name]);
            }
            foreach ($allowed_properties as $name => $d) {
                $name = htmlspecialchars($name);
                trigger_error("Style attribute '" . $name . "' is not supported " . $support, 512);
            }
        }
        $forbidden_properties = $config->get("CSS.ForbiddenProperties");
        if ($forbidden_properties !== NULL) {
            foreach ($this->info as $name => $d) {
                if (isset($forbidden_properties[$name])) {
                    unset($this->info[$name]);
                }
            }
        }
    }
}
abstract class HTMLPurifier_ChildDef
{
    public $type = NULL;
    public $allow_empty = NULL;
    public $elements = [];
    public function getAllowedElements($config)
    {
        return $this->elements;
    }
    public abstract function validateChildren($children, $config, $context);
}
class HTMLPurifier_Config
{
    public $version = "4.12.0";
    public $autoFinalize = true;
    protected $serials = [];
    protected $serial = NULL;
    protected $parser = NULL;
    public $def = NULL;
    protected $definitions = NULL;
    protected $finalized = false;
    protected $plist = NULL;
    private $aliasMode = NULL;
    public $chatty = true;
    private $lock = NULL;
    public function __construct($definition, $parent = NULL)
    {
        $parent = $parent ? $parent : $definition->defaultPlist;
        $this->plist = new HTMLPurifier_PropertyList($parent);
        $this->def = $definition;
        $this->parser = new HTMLPurifier_VarParser_Flexible();
    }
    public static function create($config, $schema = NULL)
    {
        if ($config instanceof HTMLPurifier_Config) {
            return $config;
        }
        if (!$schema) {
            $ret = HTMLPurifier_Config::createDefault();
        } else {
            $ret = new HTMLPurifier_Config($schema);
        }
        if (is_string($config)) {
            $ret->loadIni($config);
        } else {
            if (is_array($config)) {
                $ret->loadArray($config);
            }
        }
        return $ret;
    }
    public static function inherit(HTMLPurifier_Config $config)
    {
        return new HTMLPurifier_Config($config->def, $config->plist);
    }
    public static function createDefault()
    {
        $definition = HTMLPurifier_ConfigSchema::instance();
        $config = new HTMLPurifier_Config($definition);
        return $config;
    }
    public function get($key, $a = NULL)
    {
        if ($a !== NULL) {
            $this->triggerError("Using deprecated API: use \$config->get('" . $key . "." . $a . "') instead", 512);
            $key = $key . "." . $a;
        }
        if (!$this->finalized) {
            $this->autoFinalize();
        }
        if (!isset($this->def->info[$key])) {
            $this->triggerError("Cannot retrieve value of undefined directive " . htmlspecialchars($key), 512);
        } else {
            if (isset($this->def->info[$key]->isAlias)) {
                $d = $this->def->info[$key];
                $this->triggerError("Cannot get value from aliased directive, use real name " . $d->key, 256);
            } else {
                if ($this->lock) {
                    list($ns) = explode(".", $key);
                    if ($ns !== $this->lock) {
                        $this->triggerError("Cannot get value of namespace " . $ns . " when lock for " . $this->lock . " is active, this probably indicates a Definition setup method " . "is accessing directives that are not within its namespace", 256);
                        return NULL;
                    }
                }
                return $this->plist->get($key);
            }
        }
    }
    public function getBatch($namespace)
    {
        if (!$this->finalized) {
            $this->autoFinalize();
        }
        $full = $this->getAll();
        if (!isset($full[$namespace])) {
            $this->triggerError("Cannot retrieve undefined namespace " . htmlspecialchars($namespace), 512);
        } else {
            return $full[$namespace];
        }
    }
    public function getBatchSerial($namespace)
    {
        if (empty($this->serials[$namespace])) {
            $batch = $this->getBatch($namespace);
            unset($batch["DefinitionRev"]);
            $this->serials[$namespace] = sha1(serialize($batch));
        }
        return $this->serials[$namespace];
    }
    public function getSerial()
    {
        if (empty($this->serial)) {
            $this->serial = sha1(serialize($this->getAll()));
        }
        return $this->serial;
    }
    public function getAll()
    {
        if (!$this->finalized) {
            $this->autoFinalize();
        }
        $ret = [];
        foreach ($this->plist->squash() as $name => $value) {
            list($ns, $key) = explode(".", $name, 2);
            $ret[$ns][$key] = $value;
        }
        return $ret;
    }
    public function set($key, $value, $a = NULL)
    {
        if (strpos($key, ".") === false) {
            $namespace = $key;
            $directive = $value;
            $value = $a;
            $key = $key . "." . $directive;
            $this->triggerError("Using deprecated API: use \$config->set('" . $key . "', ...) instead", 1024);
        } else {
            list($namespace) = explode(".", $key);
        }
        if ($this->isFinalized("Cannot set directive after finalization")) {
            return NULL;
        }
        if (!isset($this->def->info[$key])) {
            $this->triggerError("Cannot set undefined directive " . htmlspecialchars($key) . " to value", 512);
        } else {
            $def = $this->def->info[$key];
            if (isset($def->isAlias)) {
                if ($this->aliasMode) {
                    $this->triggerError("Double-aliases not allowed, please fix ConfigSchema bug with" . $key, 256);
                } else {
                    $this->aliasMode = true;
                    $this->set($def->key, $value);
                    $this->aliasMode = false;
                    $this->triggerError($key . " is an alias, preferred directive name is " . $def->key, 1024);
                }
            } else {
                $rtype = is_int($def) ? $def : $def->type;
                if ($rtype < 0) {
                    $type = -1 * $rtype;
                    $allow_null = true;
                } else {
                    $type = $rtype;
                    $allow_null = isset($def->allow_null);
                }
                try {
                    $value = $this->parser->parse($value, $type, $allow_null);
                } catch (HTMLPurifier_VarParserException $e) {
                    $this->triggerError("Value for " . $key . " is of invalid type, should be " . HTMLPurifier_VarParser::getTypeName($type), 512);
                    return NULL;
                }
                if (is_string($value) && is_object($def)) {
                    if (isset($def->aliases[$value])) {
                        $value = $def->aliases[$value];
                    }
                    if (isset($def->allowed) && !isset($def->allowed[$value])) {
                        $this->triggerError("Value not supported, valid values are: " . $this->_listify($def->allowed), 512);
                        return NULL;
                    }
                }
                $this->plist->set($key, $value);
                if ($namespace == "HTML" || $namespace == "CSS" || $namespace == "URI") {
                    $this->definitions[$namespace] = NULL;
                }
                $this->serials[$namespace] = false;
            }
        }
    }
    private function _listify($lookup)
    {
        $list = [];
        foreach ($lookup as $name => $b) {
            $list[] = $name;
        }
        return implode(", ", $list);
    }
    public function getHTMLDefinition($raw = false, $optimized = false)
    {
        return $this->getDefinition("HTML", $raw, $optimized);
    }
    public function getCSSDefinition($raw = false, $optimized = false)
    {
        return $this->getDefinition("CSS", $raw, $optimized);
    }
    public function getURIDefinition($raw = false, $optimized = false)
    {
        return $this->getDefinition("URI", $raw, $optimized);
    }
    public function getDefinition($type, $raw = false, $optimized = false)
    {
        if ($optimized && !$raw) {
            throw new HTMLPurifier_Exception("Cannot set optimized = true when raw = false");
        }
        if (!$this->finalized) {
            $this->autoFinalize();
        }
        $lock = $this->lock;
        $this->lock = NULL;
        $factory = HTMLPurifier_DefinitionCacheFactory::instance();
        $cache = $factory->create($type, $this);
        $this->lock = $lock;
        if (!$raw) {
            if (!empty($this->definitions[$type])) {
                $def = $this->definitions[$type];
                if ($def->setup) {
                    return $def;
                }
                $def->setup($this);
                if ($def->optimized) {
                    $cache->add($def, $this);
                }
                return $def;
            }
            $def = $cache->get($this);
            if ($def) {
                $this->definitions[$type] = $def;
                return $def;
            }
            $def = $this->initDefinition($type);
            $this->lock = $type;
            $def->setup($this);
            $this->lock = NULL;
            $cache->add($def, $this);
            return $def;
        }
        $def = NULL;
        if ($optimized && is_null($this->get($type . ".DefinitionID"))) {
            throw new HTMLPurifier_Exception("Cannot retrieve raw version without specifying %" . $type . ".DefinitionID");
        }
        if (!empty($this->definitions[$type])) {
            $def = $this->definitions[$type];
            if ($def->setup && !$optimized) {
                $extra = $this->chatty ? " (try moving this code block earlier in your initialization)" : "";
                throw new HTMLPurifier_Exception("Cannot retrieve raw definition after it has already been setup" . $extra);
            }
            if ($def->optimized === NULL) {
                $extra = $this->chatty ? " (try flushing your cache)" : "";
                throw new HTMLPurifier_Exception("Optimization status of definition is unknown" . $extra);
            }
            if ($def->optimized !== $optimized) {
                $msg = $optimized ? "optimized" : "unoptimized";
                $extra = $this->chatty ? " (this backtrace is for the first inconsistent call, which was for a " . $msg . " raw definition)" : "";
                throw new HTMLPurifier_Exception("Inconsistent use of optimized and unoptimized raw definition retrievals" . $extra);
            }
        }
        if ($def) {
            if ($def->setup) {
                return NULL;
            }
            return $def;
        }
        if ($optimized) {
            $def = $cache->get($this);
            if ($def) {
                $this->definitions[$type] = $def;
                return NULL;
            }
        }
        if (!$optimized && !is_null($this->get($type . ".DefinitionID"))) {
            if ($this->chatty) {
                $this->triggerError("Due to a documentation error in previous version of HTML Purifier, your definitions are not being cached.  If this is OK, you can remove the %\$type.DefinitionRev and %\$type.DefinitionID declaration.  Otherwise, modify your code to use maybeGetRawDefinition, and test if the returned value is null before making any edits (if it is null, that means that a cached version is available, and no raw operations are necessary).  See <a href=\"http://htmlpurifier.org/docs/enduser-customize.html#optimized\">Customize</a> for more details", 512);
            } else {
                $this->triggerError("Useless DefinitionID declaration", 512);
            }
        }
        $def = $this->initDefinition($type);
        $def->optimized = $optimized;
        return $def;
    }
    private function initDefinition($type)
    {
        if ($type == "HTML") {
            $def = new HTMLPurifier_HTMLDefinition();
        } else {
            if ($type == "CSS") {
                $def = new HTMLPurifier_CSSDefinition();
            } else {
                if ($type == "URI") {
                    $def = new HTMLPurifier_URIDefinition();
                } else {
                    throw new HTMLPurifier_Exception("Definition of " . $type . " type not supported");
                }
            }
        }
        $this->definitions[$type] = $def;
        return $def;
    }
    public function maybeGetRawDefinition($name)
    {
        return $this->getDefinition($name, true, true);
    }
    public function maybeGetRawHTMLDefinition()
    {
        return $this->getDefinition("HTML", true, true);
    }
    public function maybeGetRawCSSDefinition()
    {
        return $this->getDefinition("CSS", true, true);
    }
    public function maybeGetRawURIDefinition()
    {
        return $this->getDefinition("URI", true, true);
    }
    public function loadArray($config_array)
    {
        if ($this->isFinalized("Cannot load directives after finalization")) {
            return NULL;
        }
        foreach ($config_array as $key => $value) {
            $key = str_replace("_", ".", $key);
            if (strpos($key, ".") !== false) {
                $this->set($key, $value);
            } else {
                $namespace = $key;
                $namespace_values = $value;
                foreach ($namespace_values as $directive => $value2) {
                    $this->set($namespace . "." . $directive, $value2);
                }
            }
        }
    }
    public static function getAllowedDirectivesForForm($allowed, $schema = NULL)
    {
        if (!$schema) {
            $schema = HTMLPurifier_ConfigSchema::instance();
        }
        if ($allowed !== true) {
            if (is_string($allowed)) {
                $allowed = [$allowed];
            }
            $allowed_ns = [];
            $allowed_directives = [];
            $blacklisted_directives = [];
            foreach ($allowed as $ns_or_directive) {
                if (strpos($ns_or_directive, ".") !== false) {
                    if ($ns_or_directive[0] == "-") {
                        $blacklisted_directives[substr($ns_or_directive, 1)] = true;
                    } else {
                        $allowed_directives[$ns_or_directive] = true;
                    }
                } else {
                    $allowed_ns[$ns_or_directive] = true;
                }
            }
        }
        $ret = [];
        foreach ($schema->info as $key => $def) {
            list($ns, $directive) = explode(".", $key, 2);
            if ($allowed !== true) {
                if (!isset($blacklisted_directives[$ns . "." . $directive])) {
                    if (isset($allowed_directives[$ns . "." . $directive]) || isset($allowed_ns[$ns])) {
                    }
                }
            }
            if (!isset($def->isAlias)) {
                if (!($directive == "DefinitionID" || $directive == "DefinitionRev")) {
                    $ret[] = [$ns, $directive];
                }
            }
        }
        return $ret;
    }
    public static function loadArrayFromForm($array, $index = false, $allowed = true, $mq_fix = true, $schema = NULL)
    {
        $ret = HTMLPurifier_Config::prepareArrayFromForm($array, $index, $allowed, $mq_fix, $schema);
        $config = HTMLPurifier_Config::create($ret, $schema);
        return $config;
    }
    public function mergeArrayFromForm($array, $index = false, $allowed = true, $mq_fix = true)
    {
        $ret = HTMLPurifier_Config::prepareArrayFromForm($array, $index, $allowed, $mq_fix, $this->def);
        $this->loadArray($ret);
    }
    public static function prepareArrayFromForm($array, $index = false, $allowed = true, $mq_fix = true, $schema = NULL)
    {
        if ($index !== false) {
            $array = isset($array[$index]) && is_array($array[$index]) ? $array[$index] : [];
        }
        $mq = $mq_fix && function_exists("get_magic_quotes_gpc") && get_magic_quotes_gpc();
        $allowed = HTMLPurifier_Config::getAllowedDirectivesForForm($allowed, $schema);
        $ret = [];
        foreach ($allowed as $key) {
            list($ns, $directive) = $key;
            $skey = $ns . "." . $directive;
            if (!empty($array["Null_" . $skey])) {
                $ret[$ns][$directive] = NULL;
            } else {
                if (isset($array[$skey])) {
                    $value = $mq ? stripslashes($array[$skey]) : $array[$skey];
                    $ret[$ns][$directive] = $value;
                }
            }
        }
        return $ret;
    }
    public function loadIni($filename)
    {
        if ($this->isFinalized("Cannot load directives after finalization")) {
            return NULL;
        }
        $array = parse_ini_file($filename, true);
        $this->loadArray($array);
    }
    public function isFinalized($error = false)
    {
        if ($this->finalized && $error) {
            $this->triggerError($error, 256);
        }
        return $this->finalized;
    }
    public function autoFinalize()
    {
        if ($this->autoFinalize) {
            $this->finalize();
        } else {
            $this->plist->squash(true);
        }
    }
    public function finalize()
    {
        $this->finalized = true;
        $this->parser = NULL;
    }
    protected function triggerError($msg, $no)
    {
        $extra = "";
        if ($this->chatty) {
            $trace = debug_backtrace();
            $i = 0;
            $c = count($trace);
            while ($i < $c - 1) {
                if (isset($trace[$i + 1]["class"]) && $trace[$i + 1]["class"] === "HTMLPurifier_Config") {
                    $i++;
                } else {
                    $frame = $trace[$i];
                    $extra = " invoked on line " . $frame["line"] . " in file " . $frame["file"];
                }
            }
        }
        trigger_error($msg . $extra, $no);
    }
    public function serialize()
    {
        $this->getDefinition("HTML");
        $this->getDefinition("CSS");
        $this->getDefinition("URI");
        return serialize($this);
    }
}
class HTMLPurifier_ConfigSchema
{
    public $defaults = [];
    public $defaultPlist = NULL;
    public $info = [];
    protected static $singleton = NULL;
    public function __construct()
    {
        $this->defaultPlist = new HTMLPurifier_PropertyList();
    }
    public static function makeFromSerial()
    {
        $contents = file_get_contents(HTMLPURIFIER_PREFIX . "/HTMLPurifier/ConfigSchema/schema.ser");
        $r = unserialize($contents);
        if (!$r) {
            $hash = sha1($contents);
            trigger_error("Unserialization of configuration schema failed, sha1 of file was " . $hash, 256);
        }
        return $r;
    }
    public static function instance($prototype = NULL)
    {
        if ($prototype !== NULL) {
            HTMLPurifier_ConfigSchema::$singleton = $prototype;
        } else {
            if (HTMLPurifier_ConfigSchema::$singleton === NULL || $prototype === true) {
                HTMLPurifier_ConfigSchema::$singleton = HTMLPurifier_ConfigSchema::makeFromSerial();
            }
        }
        return HTMLPurifier_ConfigSchema::$singleton;
    }
    public function add($key, $default, $type, $allow_null)
    {
        $obj = new stdClass();
        $obj->type = is_int($type) ? $type : HTMLPurifier_VarParser::$types[$type];
        if ($allow_null) {
            $obj->allow_null = true;
        }
        $this->info[$key] = $obj;
        $this->defaults[$key] = $default;
        $this->defaultPlist->set($key, $default);
    }
    public function addValueAliases($key, $aliases)
    {
        if (!isset($this->info[$key]->aliases)) {
            $this->info[$key]->aliases = [];
        }
        foreach ($aliases as $alias => $real) {
            $this->info[$key]->aliases[$alias] = $real;
        }
    }
    public function addAllowedValues($key, $allowed)
    {
        $this->info[$key]->allowed = $allowed;
    }
    public function addAlias($key, $new_key)
    {
        $obj = new stdClass();
        $obj->key = $new_key;
        $obj->isAlias = true;
        $this->info[$key] = $obj;
    }
    public function postProcess()
    {
        foreach ($this->info as $key => $v) {
            if (count((array) $v) == 1) {
                $this->info[$key] = $v->type;
            } else {
                if (count((array) $v) == 2 && isset($v->allow_null)) {
                    $this->info[$key] = -1 * $v->type;
                }
            }
        }
    }
}
class HTMLPurifier_ContentSets
{
    public $info = [];
    public $lookup = [];
    protected $keys = [];
    protected $values = [];
    public function __construct($modules)
    {
        if (!is_array($modules)) {
            $modules = [$modules];
        }
        foreach ($modules as $module) {
            foreach ($module->content_sets as $key => $value) {
                $temp = $this->convertToLookup($value);
                if (isset($this->lookup[$key])) {
                    $this->lookup[$key] = array_merge($this->lookup[$key], $temp);
                } else {
                    $this->lookup[$key] = $temp;
                }
            }
        }
        $old_lookup = false;
        while ($old_lookup !== $this->lookup) {
            $old_lookup = $this->lookup;
            foreach ($this->lookup as $i => $set) {
                $add = [];
                foreach ($set as $element => $x) {
                    if (isset($this->lookup[$element])) {
                        $add += $this->lookup[$element];
                        unset($this->lookup[$i][$element]);
                    }
                }
                $this->lookup[$i] += $add;
            }
        }
        foreach ($this->lookup as $key => $lookup) {
            $this->info[$key] = implode(" | ", array_keys($lookup));
        }
        $this->keys = array_keys($this->info);
        $this->values = array_values($this->info);
    }
    public function generateChildDef(&$def, $module)
    {
        if (!empty($def->child)) {
            return NULL;
        }
        $content_model = $def->content_model;
        if (is_string($content_model)) {
            $def->content_model = preg_replace_callback("/\\b(" . implode("|", $this->keys) . ")\\b/", [$this, "generateChildDefCallback"], $content_model);
        }
        $def->child = $this->getChildDef($def, $module);
    }
    public function generateChildDefCallback($matches)
    {
        return $this->info[$matches[0]];
    }
    public function getChildDef($def, $module)
    {
        $value = $def->content_model;
        if (is_object($value)) {
            trigger_error("Literal object child definitions should be stored in ElementDef->child not ElementDef->content_model", 1024);
            return $value;
        }
        switch ($def->content_model_type) {
            case "required":
                return new HTMLPurifier_ChildDef_Required($value);
                break;
            case "optional":
                return new HTMLPurifier_ChildDef_Optional($value);
                break;
            case "empty":
                return new HTMLPurifier_ChildDef_Empty();
                break;
            case "custom":
                return new HTMLPurifier_ChildDef_Custom($value);
                break;
            default:
                $return = false;
                if ($module->defines_child_def) {
                    $return = $module->getChildDef($def);
                }
                if ($return !== false) {
                    return $return;
                }
                trigger_error("Could not determine which ChildDef class to instantiate", 256);
                return false;
        }
    }
    protected function convertToLookup($string)
    {
        $array = explode("|", str_replace(" ", "", $string));
        $ret = [];
        foreach ($array as $k) {
            $ret[$k] = true;
        }
        return $ret;
    }
}
class HTMLPurifier_Context
{
    private $_storage = [];
    public function register($name, &$ref)
    {
        if (array_key_exists($name, $this->_storage)) {
            trigger_error("Name " . $name . " produces collision, cannot re-register", 256);
        } else {
            $this->_storage[$name] =& $ref;
        }
    }
    public function &get($name, $ignore_error = false)
    {
        if (!array_key_exists($name, $this->_storage)) {
            if (!$ignore_error) {
                trigger_error("Attempted to retrieve non-existent variable " . $name, 256);
            }
            $var = NULL;
            return $var;
        }
        return $this->_storage[$name];
    }
    public function destroy($name)
    {
        if (!array_key_exists($name, $this->_storage)) {
            trigger_error("Attempted to destroy non-existent variable " . $name, 256);
        } else {
            unset($this->_storage[$name]);
        }
    }
    public function exists($name)
    {
        return array_key_exists($name, $this->_storage);
    }
    public function loadArray($context_array)
    {
        foreach ($context_array as $key => $discard) {
            $this->register($key, $context_array[$key]);
        }
    }
}
abstract class HTMLPurifier_DefinitionCache
{
    public $type = NULL;
    public function __construct($type)
    {
        $this->type = $type;
    }
    public function generateKey($config)
    {
        return $config->version . "," . $config->getBatchSerial($this->type) . "," . $config->get($this->type . ".DefinitionRev");
    }
    public function isOld($key, $config)
    {
        if (substr_count($key, ",") < 2) {
            return true;
        }
        list($version, $hash, $revision) = explode(",", $key, 3);
        $compare = version_compare($version, $config->version);
        if ($compare != 0) {
            return true;
        }
        if ($hash == $config->getBatchSerial($this->type) && $revision < $config->get($this->type . ".DefinitionRev")) {
            return true;
        }
        return false;
    }
    public function checkDefType($def)
    {
        if ($def->type !== $this->type) {
            trigger_error("Cannot use definition of type " . $def->type . " in cache for " . $this->type);
            return false;
        }
        return true;
    }
    public abstract function add($def, $config);
    public abstract function set($def, $config);
    public abstract function replace($def, $config);
    public abstract function get($config);
    public abstract function remove($config);
    public abstract function flush($config);
    public abstract function cleanup($config);
}
class HTMLPurifier_DefinitionCacheFactory
{
    protected $caches = ["Serializer" => []];
    protected $implementations = [];
    protected $decorators = [];
    public function setup()
    {
        $this->addDecorator("Cleanup");
    }
    public static function instance($prototype = NULL)
    {
        if ($prototype !== NULL) {
            $instance = $prototype;
        } else {
            if ($instance === NULL || $prototype === true) {
                $instance = new HTMLPurifier_DefinitionCacheFactory();
                $instance->setup();
            }
        }
        return $instance;
    }
    public function register($short, $long)
    {
        $this->implementations[$short] = $long;
    }
    public function create($type, $config)
    {
        $method = $config->get("Cache.DefinitionImpl");
        if ($method === NULL) {
            return new HTMLPurifier_DefinitionCache_Null($type);
        }
        if (!empty($this->caches[$method][$type])) {
            return $this->caches[$method][$type];
        }
        if (isset($this->implementations[$method]) && class_exists($class = $this->implementations[$method], false)) {
            $cache = new $class($type);
        } else {
            if ($method != "Serializer") {
                trigger_error("Unrecognized DefinitionCache " . $method . ", using Serializer instead", 512);
            }
            $cache = new HTMLPurifier_DefinitionCache_Serializer($type);
        }
        foreach ($this->decorators as $decorator) {
            $new_cache = $decorator->decorate($cache);
            unset($cache);
            $cache = $new_cache;
        }
        $this->caches[$method][$type] = $cache;
        return $this->caches[$method][$type];
    }
    public function addDecorator($decorator)
    {
        if (is_string($decorator)) {
            $class = "HTMLPurifier_DefinitionCache_Decorator_" . $decorator;
            $decorator = new $class();
        }
        $this->decorators[$decorator->name] = $decorator;
    }
}
class HTMLPurifier_Doctype
{
    public $name = NULL;
    public $modules = [];
    public $tidyModules = [];
    public $xml = true;
    public $aliases = [];
    public $dtdPublic = NULL;
    public $dtdSystem = NULL;
    public function __construct($name = NULL, $xml = true, $modules = [], $tidyModules = [], $aliases = [], $dtd_public = NULL, $dtd_system = NULL)
    {
        $this->name = $name;
        $this->xml = $xml;
        $this->modules = $modules;
        $this->tidyModules = $tidyModules;
        $this->aliases = $aliases;
        $this->dtdPublic = $dtd_public;
        $this->dtdSystem = $dtd_system;
    }
}
class HTMLPurifier_DoctypeRegistry
{
    protected $doctypes = NULL;
    protected $aliases = NULL;
    public function register($doctype, $xml = true, $modules = [], $tidy_modules = [], $aliases = [], $dtd_public = NULL, $dtd_system = NULL)
    {
        if (!is_array($modules)) {
            $modules = [$modules];
        }
        if (!is_array($tidy_modules)) {
            $tidy_modules = [$tidy_modules];
        }
        if (!is_array($aliases)) {
            $aliases = [$aliases];
        }
        if (!is_object($doctype)) {
            $doctype = new HTMLPurifier_Doctype($doctype, $xml, $modules, $tidy_modules, $aliases, $dtd_public, $dtd_system);
        }
        $this->doctypes[$doctype->name] = $doctype;
        $name = $doctype->name;
        foreach ($doctype->aliases as $alias) {
            if (!isset($this->doctypes[$alias])) {
                $this->aliases[$alias] = $name;
            }
        }
        if (isset($this->aliases[$name])) {
            unset($this->aliases[$name]);
        }
        return $doctype;
    }
    public function get($doctype)
    {
        if (isset($this->aliases[$doctype])) {
            $doctype = $this->aliases[$doctype];
        }
        if (!isset($this->doctypes[$doctype])) {
            trigger_error("Doctype " . htmlspecialchars($doctype) . " does not exist", 256);
            $anon = new HTMLPurifier_Doctype($doctype);
            return $anon;
        }
        return $this->doctypes[$doctype];
    }
    public function make($config)
    {
        return clone $this->get($this->getDoctypeFromConfig($config));
    }
    public function getDoctypeFromConfig($config)
    {
        $doctype = $config->get("HTML.Doctype");
        if (!empty($doctype)) {
            return $doctype;
        }
        $doctype = $config->get("HTML.CustomDoctype");
        if (!empty($doctype)) {
            return $doctype;
        }
        if ($config->get("HTML.XHTML")) {
            $doctype = "XHTML 1.0";
        } else {
            $doctype = "HTML 4.01";
        }
        if ($config->get("HTML.Strict")) {
            $doctype .= " Strict";
        } else {
            $doctype .= " Transitional";
        }
        return $doctype;
    }
}
class HTMLPurifier_ElementDef
{
    public $standalone = true;
    public $attr = [];
    public $attr_transform_pre = [];
    public $attr_transform_post = [];
    public $child = NULL;
    public $content_model = NULL;
    public $content_model_type = NULL;
    public $descendants_are_inline = false;
    public $required_attr = [];
    public $excludes = [];
    public $autoclose = [];
    public $wrap = NULL;
    public $formatting = NULL;
    public static function create($content_model, $content_model_type, $attr)
    {
        $def = new HTMLPurifier_ElementDef();
        $def->content_model = $content_model;
        $def->content_model_type = $content_model_type;
        $def->attr = $attr;
        return $def;
    }
    public function mergeIn($def)
    {
        foreach ($def->attr as $k => $v) {
            if ($k === 0) {
                foreach ($v as $v2) {
                    $this->attr[0][] = $v2;
                }
            } else {
                if ($v === false) {
                    if (isset($this->attr[$k])) {
                        unset($this->attr[$k]);
                    }
                } else {
                    $this->attr[$k] = $v;
                }
            }
        }
        $this->_mergeAssocArray($this->excludes, $def->excludes);
        $this->attr_transform_pre = array_merge($this->attr_transform_pre, $def->attr_transform_pre);
        $this->attr_transform_post = array_merge($this->attr_transform_post, $def->attr_transform_post);
        if (!empty($def->content_model)) {
            $this->content_model = str_replace("#SUPER", $this->content_model, $def->content_model);
            $this->child = false;
        }
        if (!empty($def->content_model_type)) {
            $this->content_model_type = $def->content_model_type;
            $this->child = false;
        }
        if (!is_null($def->child)) {
            $this->child = $def->child;
        }
        if (!is_null($def->formatting)) {
            $this->formatting = $def->formatting;
        }
        if ($def->descendants_are_inline) {
            $this->descendants_are_inline = $def->descendants_are_inline;
        }
    }
    private function _mergeAssocArray(&$a1, $a2)
    {
        foreach ($a2 as $k => $v) {
            if ($v === false) {
                if (isset($a1[$k])) {
                    unset($a1[$k]);
                }
            } else {
                $a1[$k] = $v;
            }
        }
    }
}
class HTMLPurifier_Encoder
{
    const ICONV_OK = 0;
    const ICONV_TRUNCATES = 1;
    const ICONV_UNUSABLE = 2;
    private function __construct()
    {
        trigger_error("Cannot instantiate encoder, call methods statically", 256);
    }
    public static function muteErrorHandler()
    {
    }
    public static function unsafeIconv($in, $out, $text)
    {
        set_error_handler(["HTMLPurifier_Encoder", "muteErrorHandler"]);
        $r = iconv($in, $out, $text);
        restore_error_handler();
        return $r;
    }
    public static function iconv($in, $out, $text, $max_chunk_size = 8000)
    {
        $code = self::testIconvTruncateBug();
        if ($code == self::ICONV_OK) {
            return self::unsafeIconv($in, $out, $text);
        }
        if ($code == self::ICONV_TRUNCATES) {
            if ($in == "utf-8") {
                if ($max_chunk_size < 4) {
                    trigger_error("max_chunk_size is too small", 512);
                    return false;
                }
                if (($c = strlen($text)) <= $max_chunk_size) {
                    return self::unsafeIconv($in, $out, $text);
                }
                $r = "";
                $i = 0;
                while (true) {
                    if ($c <= $i + $max_chunk_size) {
                        $r .= self::unsafeIconv($in, $out, substr($text, $i));
                    } else {
                        if (128 != (192 & ord($text[$i + $max_chunk_size]))) {
                            $chunk_size = $max_chunk_size;
                        } else {
                            if (128 != (192 & ord($text[$i + $max_chunk_size - 1]))) {
                                $chunk_size = $max_chunk_size - 1;
                            } else {
                                if (128 != (192 & ord($text[$i + $max_chunk_size - 2]))) {
                                    $chunk_size = $max_chunk_size - 2;
                                } else {
                                    if (128 != (192 & ord($text[$i + $max_chunk_size - 3]))) {
                                        $chunk_size = $max_chunk_size - 3;
                                    } else {
                                        return false;
                                    }
                                }
                            }
                        }
                        $chunk = substr($text, $i, $chunk_size);
                        $r .= self::unsafeIconv($in, $out, $chunk);
                        $i += $chunk_size;
                    }
                }
                return $r;
            }
            return false;
        }
        return false;
    }
    public static function cleanUTF8($str, $force_php = false)
    {
        if (preg_match("/^[\\x{9}\\x{A}\\x{D}\\x{20}-\\x{7E}\\x{A0}-\\x{D7FF}\\x{E000}-\\x{FFFD}\\x{10000}-\\x{10FFFF}]*\$/Du", $str)) {
            return $str;
        }
        $mState = 0;
        $mUcs4 = 0;
        $mBytes = 1;
        $out = "";
        $char = "";
        $len = strlen($str);
        for ($i = 0; $i < $len; $i++) {
            $in = ord($str[$i]);
            $char .= $str[$i];
            if (0 == $mState) {
                if (0 == (128 & $in)) {
                    if (!($in <= 31 || $in == 127) || ($in == 9 || $in == 13 || $in == 10)) {
                        $out .= $char;
                    }
                    $char = "";
                    $mBytes = 1;
                } else {
                    if (192 == (224 & $in)) {
                        $mUcs4 = $in;
                        $mUcs4 = ($mUcs4 & 31) << 6;
                        $mState = 1;
                        $mBytes = 2;
                    } else {
                        if (224 == (240 & $in)) {
                            $mUcs4 = $in;
                            $mUcs4 = ($mUcs4 & 15) << 12;
                            $mState = 2;
                            $mBytes = 3;
                        } else {
                            if (240 == (248 & $in)) {
                                $mUcs4 = $in;
                                $mUcs4 = ($mUcs4 & 7) << 18;
                                $mState = 3;
                                $mBytes = 4;
                            } else {
                                if (248 == (252 & $in)) {
                                    $mUcs4 = $in;
                                    $mUcs4 = ($mUcs4 & 3) << 24;
                                    $mState = 4;
                                    $mBytes = 5;
                                } else {
                                    if (252 == (254 & $in)) {
                                        $mUcs4 = $in;
                                        $mUcs4 = ($mUcs4 & 1) << 30;
                                        $mState = 5;
                                        $mBytes = 6;
                                    } else {
                                        $mState = 0;
                                        $mUcs4 = 0;
                                        $mBytes = 1;
                                        $char = "";
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                if (128 == (192 & $in)) {
                    $shift = ($mState - 1) * 6;
                    $tmp = $in;
                    $tmp = ($tmp & 63) << $shift;
                    $mUcs4 |= $tmp;
                    if (0 == --$mState) {
                        if (!(2 == $mBytes && $mUcs4 < 128 || 3 == $mBytes && $mUcs4 < 2048 || 4 == $mBytes && $mUcs4 < 65536 || 4 < $mBytes || ($mUcs4 & 0) == 55296 || 1114111 < $mUcs4)) {
                            if (65279 != $mUcs4 && (9 == $mUcs4 || 10 == $mUcs4 || 13 == $mUcs4 || 32 <= $mUcs4 && $mUcs4 <= 126 || 160 <= $mUcs4 && $mUcs4 <= 55295 || 57344 <= $mUcs4 && $mUcs4 <= 65533 || 65536 <= $mUcs4 && $mUcs4 <= 1114111)) {
                                $out .= $char;
                            }
                        }
                        $mState = 0;
                        $mUcs4 = 0;
                        $mBytes = 1;
                        $char = "";
                    }
                } else {
                    $mState = 0;
                    $mUcs4 = 0;
                    $mBytes = 1;
                    $char = "";
                }
            }
        }
        return $out;
    }
    public static function unichr($code)
    {
        if (1114111 < $code || $code < 0 || 55296 <= $code && $code <= 57343) {
            return "";
        }
        $x = $y = $z = $w = 0;
        if ($code < 128) {
            $x = $code;
        } else {
            $x = $code & 63 | 128;
            if ($code < 2048) {
                $y = ($code & 2047) >> 6 | 192;
            } else {
                $y = ($code & 4032) >> 6 | 128;
                if ($code < 65536) {
                    $z = $code >> 12 & 15 | 224;
                } else {
                    $z = $code >> 12 & 63 | 128;
                    $w = $code >> 18 & 7 | 240;
                }
            }
        }
        $ret = "";
        if ($w) {
            $ret .= chr($w);
        }
        if ($z) {
            $ret .= chr($z);
        }
        if ($y) {
            $ret .= chr($y);
        }
        $ret .= chr($x);
        return $ret;
    }
    public static function iconvAvailable()
    {
        if ($iconv === NULL) {
            $iconv = function_exists("iconv") && self::testIconvTruncateBug() != self::ICONV_UNUSABLE;
        }
        return $iconv;
    }
    public static function convertToUTF8($str, $config, $context)
    {
        $encoding = $config->get("Core.Encoding");
        if ($encoding === "utf-8") {
            return $str;
        }
        if ($iconv === NULL) {
            $iconv = self::iconvAvailable();
        }
        if ($iconv && !$config->get("Test.ForceNoIconv")) {
            $str = self::unsafeIconv($encoding, "utf-8//IGNORE", $str);
            if ($str === false) {
                trigger_error("Invalid encoding " . $encoding, 256);
                return "";
            }
            $str = strtr($str, self::testEncodingSupportsASCII($encoding));
            return $str;
        }
        if ($encoding === "iso-8859-1") {
            $str = utf8_encode($str);
            return $str;
        }
        $bug = HTMLPurifier_Encoder::testIconvTruncateBug();
        if ($bug == self::ICONV_OK) {
            trigger_error("Encoding not supported, please install iconv", 256);
        } else {
            trigger_error("You have a buggy version of iconv, see https://bugs.php.net/bug.php?id=48147 and http://sourceware.org/bugzilla/show_bug.cgi?id=13541", 256);
        }
    }
    public static function convertFromUTF8($str, $config, $context)
    {
        $encoding = $config->get("Core.Encoding");
        if ($escape = $config->get("Core.EscapeNonASCIICharacters")) {
            $str = self::convertToASCIIDumbLossless($str);
        }
        if ($encoding === "utf-8") {
            return $str;
        }
        if ($iconv === NULL) {
            $iconv = self::iconvAvailable();
        }
        if ($iconv && !$config->get("Test.ForceNoIconv")) {
            $ascii_fix = self::testEncodingSupportsASCII($encoding);
            if (!$escape && !empty($ascii_fix)) {
                $clear_fix = [];
                foreach ($ascii_fix as $utf8 => $native) {
                    $clear_fix[$utf8] = "";
                }
                $str = strtr($str, $clear_fix);
            }
            $str = strtr($str, array_flip($ascii_fix));
            $str = self::iconv("utf-8", $encoding . "//IGNORE", $str);
            return $str;
        } else {
            if ($encoding === "iso-8859-1") {
                $str = utf8_decode($str);
                return $str;
            }
            trigger_error("Encoding not supported", 256);
        }
    }
    public static function convertToASCIIDumbLossless($str)
    {
        $bytesleft = 0;
        $result = "";
        $working = 0;
        $len = strlen($str);
        for ($i = 0; $i < $len; $i++) {
            $bytevalue = ord($str[$i]);
            if ($bytevalue <= 127) {
                $result .= chr($bytevalue);
                $bytesleft = 0;
            } else {
                if ($bytevalue <= 191) {
                    $working = $working << 6;
                    $working += $bytevalue & 63;
                    $bytesleft--;
                    if ($bytesleft <= 0) {
                        $result .= "&#" . $working . ";";
                    }
                } else {
                    if ($bytevalue <= 223) {
                        $working = $bytevalue & 31;
                        $bytesleft = 1;
                    } else {
                        if ($bytevalue <= 239) {
                            $working = $bytevalue & 15;
                            $bytesleft = 2;
                        } else {
                            $working = $bytevalue & 7;
                            $bytesleft = 3;
                        }
                    }
                }
            }
        }
        return $result;
    }
    public static function testIconvTruncateBug()
    {
        if ($code === NULL) {
            $r = self::unsafeIconv("utf-8", "ascii//IGNORE", "α" . str_repeat("a", 9000));
            if ($r === false) {
                $code = 2;
            } else {
                if (($c = strlen($r)) < 9000) {
                    $code = 1;
                } else {
                    if (9000 < $c) {
                        trigger_error("Your copy of iconv is extremely buggy. Please notify HTML Purifier maintainers: include your iconv version as per phpversion()", 256);
                    } else {
                        $code = 0;
                    }
                }
            }
        }
        return $code;
    }
    public static function testEncodingSupportsASCII($encoding, $bypass = false)
    {
        if (!$bypass) {
            if (isset($encodings[$encoding])) {
                return $encodings[$encoding];
            }
            $lenc = strtolower($encoding);
            switch ($lenc) {
                case "shift_jis":
                    return ["¥" => "\\", "‾" => "~"];
                    break;
                case "johab":
                    return ["₩" => "\\"];
                    break;
                default:
                    if (strpos($lenc, "iso-8859-") === 0) {
                        return [];
                    }
            }
        }
        $ret = [];
        if (self::unsafeIconv("UTF-8", $encoding, "a") === false) {
            return false;
        }
        for ($i = 32; $i <= 126; $i++) {
            $c = chr($i);
            $r = self::unsafeIconv("UTF-8", $encoding . "//IGNORE", $c);
            if ($r === "" || $r === $c && self::unsafeIconv($encoding, "UTF-8//IGNORE", $r) !== $c) {
                $ret[self::unsafeIconv($encoding, "UTF-8//IGNORE", $c)] = $c;
            }
        }
        $encodings[$encoding] = $ret;
        return $ret;
    }
}
class HTMLPurifier_EntityLookup
{
    public $table = NULL;
    public function setup($file = false)
    {
        if (!$file) {
            $file = HTMLPURIFIER_PREFIX . "/HTMLPurifier/EntityLookup/entities.ser";
        }
        $this->table = unserialize(file_get_contents($file));
    }
    public static function instance($prototype = false)
    {
        if ($prototype) {
            $instance = $prototype;
        } else {
            if (!$instance) {
                $instance = new HTMLPurifier_EntityLookup();
                $instance->setup();
            }
        }
        return $instance;
    }
}
class HTMLPurifier_EntityParser
{
    protected $_entity_lookup = NULL;
    protected $_textEntitiesRegex = NULL;
    protected $_attrEntitiesRegex = NULL;
    protected $_semiOptionalPrefixRegex = NULL;
    protected $_substituteEntitiesRegex = "/&(?:[#]x([a-fA-F0-9]+)|[#]0*(\\d+)|([A-Za-z_:][A-Za-z0-9.\\-_:]*));?/";
    protected $_special_dec2str = ["34" => "\"", "38" => "&", "39" => "'", "60" => "<", "62" => ">"];
    protected $_special_ent2dec = ["quot" => 34, "amp" => 38, "lt" => 60, "gt" => 62];
    public function __construct()
    {
        $semi_optional = "quot|QUOT|lt|LT|gt|GT|amp|AMP|AElig|Aacute|Acirc|Agrave|Aring|Atilde|Auml|COPY|Ccedil|ETH|Eacute|Ecirc|Egrave|Euml|Iacute|Icirc|Igrave|Iuml|Ntilde|Oacute|Ocirc|Ograve|Oslash|Otilde|Ouml|REG|THORN|Uacute|Ucirc|Ugrave|Uuml|Yacute|aacute|acirc|acute|aelig|agrave|aring|atilde|auml|brvbar|ccedil|cedil|cent|copy|curren|deg|divide|eacute|ecirc|egrave|eth|euml|frac12|frac14|frac34|iacute|icirc|iexcl|igrave|iquest|iuml|laquo|macr|micro|middot|nbsp|not|ntilde|oacute|ocirc|ograve|ordf|ordm|oslash|otilde|ouml|para|plusmn|pound|raquo|reg|sect|shy|sup1|sup2|sup3|szlig|thorn|times|uacute|ucirc|ugrave|uml|uuml|yacute|yen|yuml";
        $this->_semiOptionalPrefixRegex = "/&()()()(" . $semi_optional . ")/";
        $this->_textEntitiesRegex = "/&(?:[#]x([a-fA-F0-9]+);?|[#]0*(\\d+);?|([A-Za-z_:][A-Za-z0-9.\\-_:]*);|" . "(" . $semi_optional . ")" . ")/";
        $this->_attrEntitiesRegex = "/&(?:[#]x([a-fA-F0-9]+);?|[#]0*(\\d+);?|([A-Za-z_:][A-Za-z0-9.\\-_:]*);|" . "(" . $semi_optional . ")(?![=;A-Za-z0-9])" . ")/";
    }
    public function substituteTextEntities($string)
    {
        return preg_replace_callback($this->_textEntitiesRegex, [$this, "entityCallback"], $string);
    }
    public function substituteAttrEntities($string)
    {
        return preg_replace_callback($this->_attrEntitiesRegex, [$this, "entityCallback"], $string);
    }
    protected function entityCallback($matches)
    {
        list($entity, $hex_part, $dec_part) = $matches;
        $named_part = empty($matches[3]) ? empty($matches[4]) ? "" : $matches[4] : $matches[3];
        if ($hex_part !== NULL && $hex_part !== "") {
            return HTMLPurifier_Encoder::unichr(hexdec($hex_part));
        }
        if ($dec_part !== NULL && $dec_part !== "") {
            return HTMLPurifier_Encoder::unichr((int) $dec_part);
        }
        if (!$this->_entity_lookup) {
            $this->_entity_lookup = HTMLPurifier_EntityLookup::instance();
        }
        if (isset($this->_entity_lookup->table[$named_part])) {
            return $this->_entity_lookup->table[$named_part];
        }
        if (!empty($matches[3])) {
            return preg_replace_callback($this->_semiOptionalPrefixRegex, [$this, "entityCallback"], $entity);
        }
        return $entity;
    }
    public function substituteNonSpecialEntities($string)
    {
        return preg_replace_callback($this->_substituteEntitiesRegex, [$this, "nonSpecialEntityCallback"], $string);
    }
    protected function nonSpecialEntityCallback($matches)
    {
        $entity = $matches[0];
        $is_num = $matches[0][1] === "#";
        if ($is_num) {
            $is_hex = $entity[2] === "x";
            $code = $is_hex ? hexdec($matches[1]) : (int) $matches[2];
            if (isset($this->_special_dec2str[$code])) {
                return $entity;
            }
            return HTMLPurifier_Encoder::unichr($code);
        }
        if (isset($this->_special_ent2dec[$matches[3]])) {
            return $entity;
        }
        if (!$this->_entity_lookup) {
            $this->_entity_lookup = HTMLPurifier_EntityLookup::instance();
        }
        if (isset($this->_entity_lookup->table[$matches[3]])) {
            return $this->_entity_lookup->table[$matches[3]];
        }
        return $entity;
    }
    public function substituteSpecialEntities($string)
    {
        return preg_replace_callback($this->_substituteEntitiesRegex, [$this, "specialEntityCallback"], $string);
    }
    protected function specialEntityCallback($matches)
    {
        $entity = $matches[0];
        $is_num = $matches[0][1] === "#";
        if ($is_num) {
            $is_hex = $entity[2] === "x";
            $int = $is_hex ? hexdec($matches[1]) : (int) $matches[2];
            return isset($this->_special_dec2str[$int]) ? $this->_special_dec2str[$int] : $entity;
        }
        return isset($this->_special_ent2dec[$matches[3]]) ? $this->_special_dec2str[$this->_special_ent2dec[$matches[3]]] : $entity;
    }
}
class HTMLPurifier_ErrorCollector
{
    protected $errors = NULL;
    protected $_current = NULL;
    protected $_stacks = [[]];
    protected $locale = NULL;
    protected $generator = NULL;
    protected $context = NULL;
    protected $lines = [];
    const LINENO = 0;
    const SEVERITY = 1;
    const MESSAGE = 2;
    const CHILDREN = 3;
    public function __construct($context)
    {
        $this->locale =& $context->get("Locale");
        $this->context = $context;
        $this->_current =& $this->_stacks[0];
        $this->errors =& $this->_stacks[0];
    }
    public function send($severity, $msg)
    {
        $args = [];
        if (2 < func_num_args()) {
            $args = func_get_args();
            array_shift($args);
            unset($args[0]);
        }
        $token = $this->context->get("CurrentToken", true);
        $line = $token ? $token->line : $this->context->get("CurrentLine", true);
        $col = $token ? $token->col : $this->context->get("CurrentCol", true);
        $attr = $this->context->get("CurrentAttr", true);
        $subst = [];
        if (!is_null($token)) {
            $args["CurrentToken"] = $token;
        }
        if (!is_null($attr)) {
            $subst["\$CurrentAttr.Name"] = $attr;
            if (isset($token->attr[$attr])) {
                $subst["\$CurrentAttr.Value"] = $token->attr[$attr];
            }
        }
        if (empty($args)) {
            $msg = $this->locale->getMessage($msg);
        } else {
            $msg = $this->locale->formatMessage($msg, $args);
        }
        if (!empty($subst)) {
            $msg = strtr($msg, $subst);
        }
        $error = [$line, $severity, $msg, []];
        $this->_current[] = $error;
        $new_struct = new HTMLPurifier_ErrorStruct();
        $new_struct->type = HTMLPurifier_ErrorStruct::TOKEN;
        if ($token) {
            $new_struct->value = clone $token;
        }
        if (is_int($line) && is_int($col)) {
            if (isset($this->lines[$line][$col])) {
                $struct = $this->lines[$line][$col];
            } else {
                $this->lines[$line][$col] = $new_struct;
                $struct = $this->lines[$line][$col];
            }
            ksort($this->lines[$line], SORT_NUMERIC);
        } else {
            if (isset($this->lines[-1])) {
                $struct = $this->lines[-1];
            } else {
                $this->lines[-1] = $new_struct;
                $struct = $this->lines[-1];
            }
        }
        ksort($this->lines, SORT_NUMERIC);
        if (!empty($attr)) {
            $struct = $struct->getChild(HTMLPurifier_ErrorStruct::ATTR, $attr);
            if (!$struct->value) {
                $struct->value = [$attr, "PUT VALUE HERE"];
            }
        }
        if (!empty($cssprop)) {
            $struct = $struct->getChild(HTMLPurifier_ErrorStruct::CSSPROP, $cssprop);
            if (!$struct->value) {
                $struct->value = [$cssprop, "PUT VALUE HERE"];
            }
        }
        $struct->addError($severity, $msg);
    }
    public function getRaw()
    {
        return $this->errors;
    }
    public function getHTMLFormatted($config, $errors = NULL)
    {
        $ret = [];
        $this->generator = new HTMLPurifier_Generator($config, $this->context);
        if ($errors === NULL) {
            $errors = $this->errors;
        }
        foreach ($this->lines as $line => $col_array) {
            if ($line != -1) {
                foreach ($col_array as $col => $struct) {
                    $this->_renderStruct($ret, $struct, $line, $col);
                }
            }
        }
        if (isset($this->lines[-1])) {
            $this->_renderStruct($ret, $this->lines[-1]);
        }
        if (empty($errors)) {
            return "<p>" . $this->locale->getMessage("ErrorCollector: No errors") . "</p>";
        }
        return "<ul><li>" . implode("</li><li>", $ret) . "</li></ul>";
    }
    private function _renderStruct(&$ret, $struct, $line = NULL, $col = NULL)
    {
        $stack = [$struct];
        $context_stack = [[]];
        while ($current = array_pop($stack)) {
            $context = array_pop($context_stack);
            foreach ($current->errors as $error) {
                list($severity, $msg) = $error;
                $string = "";
                $string .= "<div>";
                $error = $this->locale->getErrorName($severity);
                $string .= "<span class=\"error e" . $severity . "\"><strong>" . $error . "</strong></span> ";
                if (!is_null($line) && !is_null($col)) {
                    $string .= "<em class=\"location\">Line " . $line . ", Column " . $col . ": </em> ";
                } else {
                    $string .= "<em class=\"location\">End of Document: </em> ";
                }
                $string .= "<strong class=\"description\">" . $this->generator->escape($msg) . "</strong> ";
                $string .= "</div>";
                $ret[] = $string;
            }
            foreach ($current->children as $array) {
                $context[] = $current;
                $stack = array_merge($stack, array_reverse($array, true));
                for ($i = count($array); 0 < $i; $i--) {
                    $context_stack[] = $context;
                }
            }
        }
    }
}
class HTMLPurifier_ErrorStruct
{
    public $type = NULL;
    public $value = NULL;
    public $errors = [];
    public $children = [];
    const TOKEN = 0;
    const ATTR = 1;
    const CSSPROP = 2;
    public function getChild($type, $id)
    {
        if (!isset($this->children[$type][$id])) {
            $this->children[$type][$id] = new HTMLPurifier_ErrorStruct();
            $this->children[$type][$id]->type = $type;
        }
        return $this->children[$type][$id];
    }
    public function addError($severity, $message)
    {
        $this->errors[] = [$severity, $message];
    }
}
class HTMLPurifier_Filter
{
    public $name = NULL;
    public function preFilter($html, $config, $context)
    {
        return $html;
    }
    public function postFilter($html, $config, $context)
    {
        return $html;
    }
}
class HTMLPurifier_Generator
{
    private $_xhtml = true;
    private $_scriptFix = false;
    private $_def = NULL;
    private $_sortAttr = NULL;
    private $_flashCompat = NULL;
    private $_innerHTMLFix = NULL;
    private $_flashStack = [];
    protected $config = NULL;
    public function __construct($config, $context)
    {
        $this->config = $config;
        $this->_scriptFix = $config->get("Output.CommentScriptContents");
        $this->_innerHTMLFix = $config->get("Output.FixInnerHTML");
        $this->_sortAttr = $config->get("Output.SortAttr");
        $this->_flashCompat = $config->get("Output.FlashCompat");
        $this->_def = $config->getHTMLDefinition();
        $this->_xhtml = $this->_def->doctype->xml;
    }
    public function generateFromTokens($tokens)
    {
        if (!$tokens) {
            return "";
        }
        $html = "";
        $i = 0;
        for ($size = count($tokens); $i < $size; $i++) {
            if ($this->_scriptFix && $tokens[$i]->name === "script" && $i + 2 < $size && $tokens[$i + 2] instanceof HTMLPurifier_Token_End) {
                $html .= $this->generateFromToken($tokens[$i++]);
                $html .= $this->generateScriptFromToken($tokens[$i++]);
            }
            $html .= $this->generateFromToken($tokens[$i]);
        }
        if (extension_loaded("tidy") && $this->config->get("Output.TidyFormat")) {
            $tidy = new Tidy();
            $tidy->parseString($html, ["indent" => true, "output-xhtml" => $this->_xhtml, "show-body-only" => true, "indent-spaces" => 2, "wrap" => 68], "utf8");
            $tidy->cleanRepair();
            $html = (string) $tidy;
        }
        if ($this->config->get("Core.NormalizeNewlines")) {
            $nl = $this->config->get("Output.Newline");
            if ($nl === NULL) {
                $nl = PHP_EOL;
            }
            if ($nl !== "\n") {
                $html = str_replace("\n", $nl, $html);
            }
        }
        return $html;
    }
    public function generateFromToken($token)
    {
        if (!$token instanceof HTMLPurifier_Token) {
            trigger_error("Cannot generate HTML from non-HTMLPurifier_Token object", 512);
            return "";
        }
        if ($token instanceof HTMLPurifier_Token_Start) {
            $attr = $this->generateAttributes($token->attr, $token->name);
            if ($this->_flashCompat && $token->name == "object") {
                $flash = new stdClass();
                $flash->attr = $token->attr;
                $flash->param = [];
                $this->_flashStack[] = $flash;
            }
            return "<" . $token->name . ($attr ? " " : "") . $attr . ">";
        }
        if ($token instanceof HTMLPurifier_Token_End) {
            $_extra = "";
            if (!$this->_flashCompat || $token->name == "object" && !empty($this->_flashStack)) {
            }
            return $_extra . "</" . $token->name . ">";
        }
        if ($token instanceof HTMLPurifier_Token_Empty) {
            if ($this->_flashCompat && $token->name == "param" && !empty($this->_flashStack)) {
                $this->_flashStack[count($this->_flashStack) - 1]->param[$token->attr["name"]] = $token->attr["value"];
            }
            $attr = $this->generateAttributes($token->attr, $token->name);
            return "<" . $token->name . ($attr ? " " : "") . $attr . ($this->_xhtml ? " /" : "") . ">";
        }
        if ($token instanceof HTMLPurifier_Token_Text) {
            return $this->escape($token->data, ENT_NOQUOTES);
        }
        if ($token instanceof HTMLPurifier_Token_Comment) {
            return "<!--" . $token->data . "-->";
        }
        return "";
    }
    public function generateScriptFromToken($token)
    {
        if (!$token instanceof HTMLPurifier_Token_Text) {
            return $this->generateFromToken($token);
        }
        $data = preg_replace("#//\\s*\$#", "", $token->data);
        return "<!--//--><![CDATA[//><!--\n" . trim($data) . "\n" . "//--><!]]>";
    }
    public function generateAttributes($assoc_array_of_attributes, $element = "")
    {
        $html = "";
        if ($this->_sortAttr) {
            ksort($assoc_array_of_attributes);
        }
        foreach ($assoc_array_of_attributes as $key => $value) {
            if (!$this->_xhtml) {
                if (strpos($key, ":") === false) {
                    if ($element && !empty($this->_def->info[$element]->attr[$key]->minimized)) {
                        $html .= $key . " ";
                    }
                }
            }
            if ($this->_innerHTMLFix && strpos($value, "`") !== false && strcspn($value, "\"' <>") === strlen($value)) {
                $value .= " ";
            }
            $html .= $key . "=\"" . $this->escape($value) . "\" ";
        }
        return rtrim($html);
    }
    public function escape($string, $quote = NULL)
    {
        if ($quote === NULL) {
            $quote = ENT_COMPAT;
        }
        return htmlspecialchars($string, $quote, "UTF-8");
    }
}
class HTMLPurifier_HTMLDefinition extends HTMLPurifier_Definition
{
    public $info = [];
    public $info_global_attr = [];
    public $info_parent = "div";
    public $info_parent_def = NULL;
    public $info_block_wrapper = "p";
    public $info_tag_transform = [];
    public $info_attr_transform_pre = [];
    public $info_attr_transform_post = [];
    public $info_content_sets = [];
    public $info_injector = [];
    public $doctype = NULL;
    private $_anonModule = NULL;
    public $type = "HTML";
    public $manager = NULL;
    public function addAttribute($element_name, $attr_name, $def)
    {
        $module = $this->getAnonymousModule();
        if (!isset($module->info[$element_name])) {
            $element = $module->addBlankElement($element_name);
        } else {
            $element = $module->info[$element_name];
        }
        $element->attr[$attr_name] = $def;
    }
    public function addElement($element_name, $type, $contents, $attr_collections, $attributes = [])
    {
        $module = $this->getAnonymousModule();
        $element = $module->addElement($element_name, $type, $contents, $attr_collections, $attributes);
        return $element;
    }
    public function addBlankElement($element_name)
    {
        $module = $this->getAnonymousModule();
        $element = $module->addBlankElement($element_name);
        return $element;
    }
    public function getAnonymousModule()
    {
        if (!$this->_anonModule) {
            $this->_anonModule = new HTMLPurifier_HTMLModule();
            $this->_anonModule->name = "Anonymous";
        }
        return $this->_anonModule;
    }
    public function __construct()
    {
        $this->manager = new HTMLPurifier_HTMLModuleManager();
    }
    protected function doSetup($config)
    {
        $this->processModules($config);
        $this->setupConfigStuff($config);
        unset($this->manager);
        foreach ($this->info as $k => $v) {
            unset($this->info[$k]->content_model);
            unset($this->info[$k]->content_model_type);
        }
    }
    protected function processModules($config)
    {
        if ($this->_anonModule) {
            $this->manager->addModule($this->_anonModule);
            unset($this->_anonModule);
        }
        $this->manager->setup($config);
        $this->doctype = $this->manager->doctype;
        foreach ($this->manager->modules as $module) {
            foreach ($module->info_tag_transform as $k => $v) {
                if ($v === false) {
                    unset($this->info_tag_transform[$k]);
                } else {
                    $this->info_tag_transform[$k] = $v;
                }
            }
            foreach ($module->info_attr_transform_pre as $k => $v) {
                if ($v === false) {
                    unset($this->info_attr_transform_pre[$k]);
                } else {
                    $this->info_attr_transform_pre[$k] = $v;
                }
            }
            foreach ($module->info_attr_transform_post as $k => $v) {
                if ($v === false) {
                    unset($this->info_attr_transform_post[$k]);
                } else {
                    $this->info_attr_transform_post[$k] = $v;
                }
            }
            foreach ($module->info_injector as $k => $v) {
                if ($v === false) {
                    unset($this->info_injector[$k]);
                } else {
                    $this->info_injector[$k] = $v;
                }
            }
        }
        $this->info = $this->manager->getElements();
        $this->info_content_sets = $this->manager->contentSets->lookup;
    }
    protected function setupConfigStuff($config)
    {
        $block_wrapper = $config->get("HTML.BlockWrapper");
        if (isset($this->info_content_sets["Block"][$block_wrapper])) {
            $this->info_block_wrapper = $block_wrapper;
        } else {
            trigger_error("Cannot use non-block element as block wrapper", 256);
        }
        $parent = $config->get("HTML.Parent");
        $def = $this->manager->getElement($parent, true);
        if ($def) {
            $this->info_parent = $parent;
            $this->info_parent_def = $def;
        } else {
            trigger_error("Cannot use unrecognized element as parent", 256);
            $this->info_parent_def = $this->manager->getElement($this->info_parent, true);
        }
        $support = "(for information on implementing this, see the support forums) ";
        $allowed_elements = $config->get("HTML.AllowedElements");
        $allowed_attributes = $config->get("HTML.AllowedAttributes");
        if (!is_array($allowed_elements) && !is_array($allowed_attributes)) {
            $allowed = $config->get("HTML.Allowed");
            if (is_string($allowed)) {
                list($allowed_elements, $allowed_attributes) = $this->parseTinyMCEAllowedList($allowed);
            }
        }
        if (is_array($allowed_elements)) {
            foreach ($this->info as $name => $d) {
                if (!isset($allowed_elements[$name])) {
                    unset($this->info[$name]);
                }
                unset($allowed_elements[$name]);
            }
            foreach ($allowed_elements as $element => $d) {
                $element = htmlspecialchars($element);
                trigger_error("Element '" . $element . "' is not supported " . $support, 512);
            }
        }
        $allowed_attributes_mutable = $allowed_attributes;
        if (is_array($allowed_attributes)) {
            foreach ($this->info_global_attr as $attr => $x) {
                $keys = [$attr, "*@" . $attr, "*." . $attr];
                $delete = true;
                foreach ($keys as $key) {
                    if ($delete && isset($allowed_attributes[$key])) {
                        $delete = false;
                    }
                    if (isset($allowed_attributes_mutable[$key])) {
                        unset($allowed_attributes_mutable[$key]);
                    }
                }
                if ($delete) {
                    unset($this->info_global_attr[$attr]);
                }
            }
            foreach ($this->info as $tag => $info) {
                foreach ($info->attr as $attr => $x) {
                    $keys = [$tag . "@" . $attr, $attr, "*@" . $attr, $tag . "." . $attr, "*." . $attr];
                    $delete = true;
                    foreach ($keys as $key) {
                        if ($delete && isset($allowed_attributes[$key])) {
                            $delete = false;
                        }
                        if (isset($allowed_attributes_mutable[$key])) {
                            unset($allowed_attributes_mutable[$key]);
                        }
                    }
                    if ($delete) {
                        if ($this->info[$tag]->attr[$attr]->required) {
                            trigger_error("Required attribute '" . $attr . "' in element '" . $tag . "' " . "was not allowed, which means '" . $tag . "' will not be allowed either", 512);
                        }
                        unset($this->info[$tag]->attr[$attr]);
                    }
                }
            }
            foreach ($allowed_attributes_mutable as $elattr => $d) {
                $bits = preg_split("/[.@]/", $elattr, 2);
                $c = count($bits);
                switch ($c) {
                    case 2:
                        if ($bits[0] !== "*") {
                            $element = htmlspecialchars($bits[0]);
                            $attribute = htmlspecialchars($bits[1]);
                            if (!isset($this->info[$element])) {
                                trigger_error("Cannot allow attribute '" . $attribute . "' if element " . "'" . $element . "' is not allowed/supported " . $support);
                            } else {
                                trigger_error("Attribute '" . $attribute . "' in element '" . $element . "' not supported " . $support, 512);
                            }
                        }
                        break;
                    case 1:
                        $attribute = htmlspecialchars($bits[0]);
                        trigger_error("Global attribute '" . $attribute . "' is not " . "supported in any elements " . $support, 512);
                        break;
                }
            }
        }
        $forbidden_elements = $config->get("HTML.ForbiddenElements");
        $forbidden_attributes = $config->get("HTML.ForbiddenAttributes");
        foreach ($this->info as $tag => $info) {
            if (isset($forbidden_elements[$tag])) {
                unset($this->info[$tag]);
            } else {
                foreach ($info->attr as $attr => $x) {
                    if (isset($forbidden_attributes[$tag . "@" . $attr]) || isset($forbidden_attributes["*@" . $attr]) || isset($forbidden_attributes[$attr])) {
                        unset($this->info[$tag]->attr[$attr]);
                    } else {
                        if (isset($forbidden_attributes[$tag . "." . $attr])) {
                            trigger_error("Error with " . $tag . "." . $attr . ": tag.attr syntax not supported for " . "HTML.ForbiddenAttributes; use tag@attr instead", 512);
                        }
                    }
                }
            }
        }
        foreach ($forbidden_attributes as $key => $v) {
            if (strlen($key) >= 2) {
                if ($key[0] == "*") {
                    if ($key[1] == ".") {
                        trigger_error("Error with " . $key . ": *.attr syntax not supported for HTML.ForbiddenAttributes; use attr instead", 512);
                    }
                }
            }
        }
        foreach ($this->info_injector as $i => $injector) {
            if ($injector->checkNeeded($config) !== false) {
                unset($this->info_injector[$i]);
            }
        }
    }
    public function parseTinyMCEAllowedList($list)
    {
        $list = str_replace([" ", "\t"], "", $list);
        $elements = [];
        $attributes = [];
        $chunks = preg_split("/(,|[\\n\\r]+)/", $list);
        foreach ($chunks as $chunk) {
            if (!empty($chunk)) {
                if (!strpos($chunk, "[")) {
                    $element = $chunk;
                    $attr = false;
                } else {
                    list($element, $attr) = explode("[", $chunk);
                }
                if ($element !== "*") {
                    $elements[$element] = true;
                }
                if ($attr) {
                    $attr = substr($attr, 0, strlen($attr) - 1);
                    $attr = explode("|", $attr);
                    foreach ($attr as $key) {
                        $attributes[$element . "." . $key] = true;
                    }
                }
            }
        }
        return [$elements, $attributes];
    }
}
class HTMLPurifier_HTMLModule
{
    public $name = NULL;
    public $elements = [];
    public $info = [];
    public $content_sets = [];
    public $attr_collections = [];
    public $info_tag_transform = [];
    public $info_attr_transform_pre = [];
    public $info_attr_transform_post = [];
    public $info_injector = [];
    public $defines_child_def = false;
    public $safe = true;
    public function getChildDef($def)
    {
        return false;
    }
    public function addElement($element, $type, $contents, $attr_includes = [], $attr = [])
    {
        $this->elements[] = $element;
        list($content_model_type, $content_model) = $this->parseContents($contents);
        $this->mergeInAttrIncludes($attr, $attr_includes);
        if ($type) {
            $this->addElementToContentSet($element, $type);
        }
        $this->info[$element] = HTMLPurifier_ElementDef::create($content_model, $content_model_type, $attr);
        if (!is_string($contents)) {
            $this->info[$element]->child = $contents;
        }
        return $this->info[$element];
    }
    public function addBlankElement($element)
    {
        if (!isset($this->info[$element])) {
            $this->elements[] = $element;
            $this->info[$element] = new HTMLPurifier_ElementDef();
            $this->info[$element]->standalone = false;
        } else {
            trigger_error("Definition for " . $element . " already exists in module, cannot redefine");
        }
        return $this->info[$element];
    }
    public function addElementToContentSet($element, $type)
    {
        if (!isset($this->content_sets[$type])) {
            $this->content_sets[$type] = "";
        } else {
            $this->content_sets[$type] .= " | ";
        }
        $this->content_sets[$type] .= $element;
    }
    public function parseContents($contents)
    {
        if (!is_string($contents)) {
            return [NULL, NULL];
        }
        switch ($contents) {
            case "Empty":
                return ["empty", ""];
                break;
            case "Inline":
                return ["optional", "Inline | #PCDATA"];
                break;
            case "Flow":
                return ["optional", "Flow | #PCDATA"];
                break;
            default:
                list($content_model_type, $content_model) = explode(":", $contents);
                $content_model_type = strtolower(trim($content_model_type));
                $content_model = trim($content_model);
                return [$content_model_type, $content_model];
        }
    }
    public function mergeInAttrIncludes(&$attr, $attr_includes)
    {
        if (!is_array($attr_includes)) {
            if (empty($attr_includes)) {
                $attr_includes = [];
            } else {
                $attr_includes = [$attr_includes];
            }
        }
        $attr[0] = $attr_includes;
    }
    public function makeLookup($list)
    {
        if (is_string($list)) {
            $list = func_get_args();
        }
        $ret = [];
        foreach ($list as $value) {
            if (!is_null($value)) {
                $ret[$value] = true;
            }
        }
        return $ret;
    }
    public function setup($config)
    {
    }
}
class HTMLPurifier_HTMLModuleManager
{
    public $doctypes = NULL;
    public $doctype = NULL;
    public $attrTypes = NULL;
    public $modules = [];
    public $registeredModules = [];
    public $userModules = [];
    public $elementLookup = [];
    public $prefixes = ["HTMLPurifier_HTMLModule_"];
    public $contentSets = NULL;
    public $attrCollections = NULL;
    public $trusted = false;
    public function __construct()
    {
        $this->attrTypes = new HTMLPurifier_AttrTypes();
        $this->doctypes = new HTMLPurifier_DoctypeRegistry();
        $common = ["CommonAttributes", "Text", "Hypertext", "List", "Presentation", "Edit", "Bdo", "Tables", "Image", "StyleAttribute", "Scripting", "Object", "Forms", "Name"];
        $transitional = ["Legacy", "Target", "Iframe"];
        $xml = ["XMLCommonAttributes"];
        $non_xml = ["NonXMLCommonAttributes"];
        $this->doctypes->register("HTML 4.01 Transitional", false, array_merge($common, $transitional, $non_xml), ["Tidy_Transitional", "Tidy_Proprietary"], [], "-//W3C//DTD HTML 4.01 Transitional//EN", "http://www.w3.org/TR/html4/loose.dtd");
        $this->doctypes->register("HTML 4.01 Strict", false, array_merge($common, $non_xml), ["Tidy_Strict", "Tidy_Proprietary", "Tidy_Name"], [], "-//W3C//DTD HTML 4.01//EN", "http://www.w3.org/TR/html4/strict.dtd");
        $this->doctypes->register("XHTML 1.0 Transitional", true, array_merge($common, $transitional, $xml, $non_xml), ["Tidy_Transitional", "Tidy_XHTML", "Tidy_Proprietary", "Tidy_Name"], [], "-//W3C//DTD XHTML 1.0 Transitional//EN", "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd");
        $this->doctypes->register("XHTML 1.0 Strict", true, array_merge($common, $xml, $non_xml), ["Tidy_Strict", "Tidy_XHTML", "Tidy_Strict", "Tidy_Proprietary", "Tidy_Name"], [], "-//W3C//DTD XHTML 1.0 Strict//EN", "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd");
        $this->doctypes->register("XHTML 1.1", true, array_merge($common, $xml, ["Ruby", "Iframe"]), ["Tidy_Strict", "Tidy_XHTML", "Tidy_Proprietary", "Tidy_Strict", "Tidy_Name"], [], "-//W3C//DTD XHTML 1.1//EN", "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd");
    }
    public function registerModule($module, $overload = false)
    {
        if (is_string($module)) {
            $original_module = $module;
            $ok = false;
            foreach ($this->prefixes as $prefix) {
                $module = $prefix . $original_module;
                if (class_exists($module)) {
                    $ok = true;
                    if (!$ok) {
                        $module = $original_module;
                        if (!class_exists($module)) {
                            trigger_error($original_module . " module does not exist", 256);
                            return NULL;
                        }
                    }
                    $module = new $module();
                }
            }
        }
        if (empty($module->name)) {
            trigger_error("Module instance of " . get_class($module) . " must have name");
        } else {
            if (!$overload && isset($this->registeredModules[$module->name])) {
                trigger_error("Overloading " . $module->name . " without explicit overload parameter", 512);
            }
            $this->registeredModules[$module->name] = $module;
        }
    }
    public function addModule($module)
    {
        $this->registerModule($module);
        if (is_object($module)) {
            $module = $module->name;
        }
        $this->userModules[] = $module;
    }
    public function addPrefix($prefix)
    {
        $this->prefixes[] = $prefix;
    }
    public function setup($config)
    {
        $this->trusted = $config->get("HTML.Trusted");
        $this->doctype = $this->doctypes->make($config);
        $modules = $this->doctype->modules;
        $lookup = $config->get("HTML.AllowedModules");
        $special_cases = $config->get("HTML.CoreModules");
        if (is_array($lookup)) {
            foreach ($modules as $k => $m) {
                if (!isset($special_cases[$m])) {
                    if (!isset($lookup[$m])) {
                        unset($modules[$k]);
                    }
                }
            }
        }
        if ($config->get("HTML.Proprietary")) {
            $modules[] = "Proprietary";
        }
        if ($config->get("HTML.SafeObject")) {
            $modules[] = "SafeObject";
        }
        if ($config->get("HTML.SafeEmbed")) {
            $modules[] = "SafeEmbed";
        }
        if ($config->get("HTML.SafeScripting") !== []) {
            $modules[] = "SafeScripting";
        }
        if ($config->get("HTML.Nofollow")) {
            $modules[] = "Nofollow";
        }
        if ($config->get("HTML.TargetBlank")) {
            $modules[] = "TargetBlank";
        }
        if ($config->get("HTML.TargetNoreferrer")) {
            $modules[] = "TargetNoreferrer";
        }
        if ($config->get("HTML.TargetNoopener")) {
            $modules[] = "TargetNoopener";
        }
        $modules = array_merge($modules, $this->userModules);
        foreach ($modules as $module) {
            $this->processModule($module);
            $this->modules[$module]->setup($config);
        }
        foreach ($this->doctype->tidyModules as $module) {
            $this->processModule($module);
            $this->modules[$module]->setup($config);
        }
        foreach ($this->modules as $module) {
            $n = [];
            foreach ($module->info_injector as $injector) {
                if (!is_object($injector)) {
                    $class = "HTMLPurifier_Injector_" . $injector;
                    $injector = new $class();
                }
                $n[$injector->name] = $injector;
            }
            $module->info_injector = $n;
        }
        foreach ($this->modules as $module) {
            foreach ($module->info as $name => $def) {
                if (!isset($this->elementLookup[$name])) {
                    $this->elementLookup[$name] = [];
                }
                $this->elementLookup[$name][] = $module->name;
            }
        }
        $this->contentSets = new HTMLPurifier_ContentSets($this->modules);
        $this->attrCollections = new HTMLPurifier_AttrCollections($this->attrTypes, $this->modules);
    }
    public function processModule($module)
    {
        if (!isset($this->registeredModules[$module]) || is_object($module)) {
            $this->registerModule($module);
        }
        $this->modules[$module] = $this->registeredModules[$module];
    }
    public function getElements()
    {
        $elements = [];
        foreach ($this->modules as $module) {
            if ($this->trusted || $module->safe) {
                foreach ($module->info as $name => $v) {
                    if (!isset($elements[$name])) {
                        $elements[$name] = $this->getElement($name);
                    }
                }
            }
        }
        foreach ($elements as $n => $v) {
            if ($v === false) {
                unset($elements[$n]);
            }
        }
        return $elements;
    }
    public function getElement($name, $trusted = NULL)
    {
        if (!isset($this->elementLookup[$name])) {
            return false;
        }
        $def = false;
        if ($trusted === NULL) {
            $trusted = $this->trusted;
        }
        foreach ($this->elementLookup[$name] as $module_name) {
            $module = $this->modules[$module_name];
            if ($trusted || $module->safe) {
                $new_def = clone $module->info[$name];
                if (!$def && $new_def->standalone) {
                    $def = $new_def;
                } else {
                    if ($def) {
                        $def->mergeIn($new_def);
                    }
                }
                $this->attrCollections->performInclusions($def->attr);
                $this->attrCollections->expandIdentifiers($def->attr, $this->attrTypes);
                if (is_string($def->content_model) && strpos($def->content_model, "Inline") !== false && $name != "del" && $name != "ins") {
                    $def->descendants_are_inline = true;
                }
                $this->contentSets->generateChildDef($def, $module);
            }
        }
        if (!$def) {
            return false;
        }
        foreach ($def->attr as $attr_name => $attr_def) {
            if ($attr_def->required) {
                $def->required_attr[] = $attr_name;
            }
        }
        return $def;
    }
}
class HTMLPurifier_IDAccumulator
{
    public $ids = [];
    public static function build($config, $context)
    {
        $id_accumulator = new HTMLPurifier_IDAccumulator();
        $id_accumulator->load($config->get("Attr.IDBlacklist"));
        return $id_accumulator;
    }
    public function add($id)
    {
        if (isset($this->ids[$id])) {
            return false;
        }
        $this->ids[$id] = true;
        return $this->ids[$id];
    }
    public function load($array_of_ids)
    {
        foreach ($array_of_ids as $id) {
            $this->ids[$id] = true;
        }
    }
}
abstract class HTMLPurifier_Injector
{
    public $name = NULL;
    protected $htmlDefinition = NULL;
    protected $currentNesting = NULL;
    protected $currentToken = NULL;
    protected $inputZipper = NULL;
    public $needed = [];
    protected $rewindOffset = false;
    public function rewindOffset($offset)
    {
        $this->rewindOffset = $offset;
    }
    public function getRewindOffset()
    {
        $r = $this->rewindOffset;
        $this->rewindOffset = false;
        return $r;
    }
    public function prepare($config, $context)
    {
        $this->htmlDefinition = $config->getHTMLDefinition();
        $result = $this->checkNeeded($config);
        if ($result !== false) {
            return $result;
        }
        $this->currentNesting =& $context->get("CurrentNesting");
        $this->currentToken =& $context->get("CurrentToken");
        $this->inputZipper =& $context->get("InputZipper");
        return false;
    }
    public function checkNeeded($config)
    {
        $def = $config->getHTMLDefinition();
        foreach ($this->needed as $element => $attributes) {
            if (is_int($element)) {
                $element = $attributes;
            }
            if (!isset($def->info[$element])) {
                return $element;
            }
            if (is_array($attributes)) {
                foreach ($attributes as $name) {
                    if (!isset($def->info[$element]->attr[$name])) {
                        return $element . "." . $name;
                    }
                }
            }
        }
        return false;
    }
    public function allowsElement($name)
    {
        if (!empty($this->currentNesting)) {
            $parent_token = array_pop($this->currentNesting);
            $this->currentNesting[] = $parent_token;
            $parent = $this->htmlDefinition->info[$parent_token->name];
        } else {
            $parent = $this->htmlDefinition->info_parent_def;
        }
        if (!isset($parent->child->elements[$name]) || isset($parent->excludes[$name])) {
            return false;
        }
        if (!empty($this->currentNesting)) {
            for ($i = count($this->currentNesting) - 2; 0 <= $i; $i--) {
                $node = $this->currentNesting[$i];
                $def = $this->htmlDefinition->info[$node->name];
                if (isset($def->excludes[$name])) {
                    return false;
                }
            }
        }
        return true;
    }
    protected function forward(&$i, &$current)
    {
        if ($i === NULL) {
            $i = count($this->inputZipper->back) - 1;
        } else {
            $i--;
        }
        if ($i < 0) {
            return false;
        }
        $current = $this->inputZipper->back[$i];
        return true;
    }
    protected function forwardUntilEndToken(&$i, &$current, &$nesting)
    {
        $result = $this->forward($i, $current);
        if (!$result) {
            return false;
        }
        if ($nesting === NULL) {
            $nesting = 0;
        }
        if ($current instanceof HTMLPurifier_Token_Start) {
            $nesting++;
        } else {
            if ($current instanceof HTMLPurifier_Token_End) {
                if ($nesting <= 0) {
                    return false;
                }
                $nesting--;
            }
        }
        return true;
    }
    protected function backward(&$i, &$current)
    {
        if ($i === NULL) {
            $i = count($this->inputZipper->front) - 1;
        } else {
            $i--;
        }
        if ($i < 0) {
            return false;
        }
        $current = $this->inputZipper->front[$i];
        return true;
    }
    public function handleText(&$token)
    {
    }
    public function handleElement(&$token)
    {
    }
    public function handleEnd(&$token)
    {
        $this->notifyEnd($token);
    }
    public function notifyEnd($token)
    {
    }
}
class HTMLPurifier_Language
{
    public $code = "en";
    public $fallback = false;
    public $messages = [];
    public $errorNames = [];
    public $error = false;
    public $_loaded = false;
    protected $config = NULL;
    protected $context = NULL;
    public function __construct($config, $context)
    {
        $this->config = $config;
        $this->context = $context;
    }
    public function load()
    {
        if ($this->_loaded) {
            return NULL;
        }
        $factory = HTMLPurifier_LanguageFactory::instance();
        $factory->loadLanguage($this->code);
        foreach ($factory->keys as $key) {
            $this->{$key} = $factory->cache[$this->code][$key];
        }
        $this->_loaded = true;
    }
    public function getMessage($key)
    {
        if (!$this->_loaded) {
            $this->load();
        }
        if (!isset($this->messages[$key])) {
            return "[" . $key . "]";
        }
        return $this->messages[$key];
    }
    public function getErrorName($int)
    {
        if (!$this->_loaded) {
            $this->load();
        }
        if (!isset($this->errorNames[$int])) {
            return "[Error: " . $int . "]";
        }
        return $this->errorNames[$int];
    }
    public function listify($array)
    {
        $sep = $this->getMessage("Item separator");
        $sep_last = $this->getMessage("Item separator last");
        $ret = "";
        $i = 0;
        for ($c = count($array); $i < $c; $i++) {
            if ($i != 0) {
                if ($i + 1 < $c) {
                    $ret .= $sep;
                } else {
                    $ret .= $sep_last;
                }
            }
            $ret .= $array[$i];
        }
        return $ret;
    }
    public function formatMessage($key, $args = [])
    {
        if (!$this->_loaded) {
            $this->load();
        }
        if (!isset($this->messages[$key])) {
            return "[" . $key . "]";
        }
        $raw = $this->messages[$key];
        $subst = [];
        $generator = false;
        foreach ($args as $i => $value) {
            if (is_object($value)) {
                if ($value instanceof HTMLPurifier_Token) {
                    if (!$generator) {
                        $generator = $this->context->get("Generator");
                    }
                    if (isset($value->name)) {
                        $subst["\$" . $i . ".Name"] = $value->name;
                    }
                    if (isset($value->data)) {
                        $subst["\$" . $i . ".Data"] = $value->data;
                    }
                    $subst["\$" . $i . ".Serialized"] = $generator->generateFromToken($value);
                    $subst["\$" . $i . ".Compact"] = $subst["\$" . $i . ".Serialized"];
                    if (!empty($value->attr)) {
                        $stripped_token = clone $value;
                        $stripped_token->attr = [];
                        $subst["\$" . $i . ".Compact"] = $generator->generateFromToken($stripped_token);
                    }
                    $subst["\$" . $i . ".Line"] = $value->line ? $value->line : "unknown";
                }
            } else {
                if (is_array($value)) {
                    $keys = array_keys($value);
                    if (array_keys($keys) === $keys) {
                        $subst["\$" . $i] = $this->listify($value);
                    } else {
                        $subst["\$" . $i . ".Keys"] = $this->listify($keys);
                        $subst["\$" . $i . ".Values"] = $this->listify(array_values($value));
                    }
                } else {
                    $subst["\$" . $i] = $value;
                }
            }
        }
        return strtr($raw, $subst);
    }
}
class HTMLPurifier_LanguageFactory
{
    public $cache = NULL;
    public $keys = ["fallback", "messages", "errorNames"];
    protected $validator = NULL;
    protected $dir = NULL;
    protected $mergeable_keys_map = ["messages" => true, "errorNames" => true];
    protected $mergeable_keys_list = [];
    public static function instance($prototype = NULL)
    {
        if ($prototype !== NULL) {
            $instance = $prototype;
        } else {
            if ($instance === NULL || $prototype) {
                $instance = new HTMLPurifier_LanguageFactory();
                $instance->setup();
            }
        }
        return $instance;
    }
    public function setup()
    {
        $this->validator = new HTMLPurifier_AttrDef_Lang();
        $this->dir = HTMLPURIFIER_PREFIX . "/HTMLPurifier";
    }
    public function create($config, $context, $code = false)
    {
        if ($code === false) {
            $code = $this->validator->validate($config->get("Core.Language"), $config, $context);
        } else {
            $code = $this->validator->validate($code, $config, $context);
        }
        if ($code === false) {
            $code = "en";
        }
        $pcode = str_replace("-", "_", $code);
        if ($code == "en") {
            $lang = new HTMLPurifier_Language($config, $context);
        } else {
            $class = "HTMLPurifier_Language_" . $pcode;
            $file = $this->dir . "/Language/classes/" . $code . ".php";
            if (file_exists($file) || class_exists($class, false)) {
                $lang = new $class($config, $context);
            } else {
                $raw_fallback = $this->getFallbackFor($code);
                $fallback = $raw_fallback ? $raw_fallback : "en";
                $depth++;
                $lang = $this->create($config, $context, $fallback);
                if (!$raw_fallback) {
                    $lang->error = true;
                }
                $depth--;
            }
        }
        $lang->code = $code;
        return $lang;
    }
    public function getFallbackFor($code)
    {
        $this->loadLanguage($code);
        return $this->cache[$code]["fallback"];
    }
    public function loadLanguage($code)
    {
        if (isset($this->cache[$code])) {
            return NULL;
        }
        $filename = $this->dir . "/Language/messages/" . $code . ".php";
        $fallback = $code != "en" ? "en" : false;
        if (!file_exists($filename)) {
            $filename = $this->dir . "/Language/messages/en.php";
            $cache = [];
        } else {
            include $filename;
            $cache = compact($this->keys);
        }
        if (!empty($fallback)) {
            if (isset($languages_seen[$code])) {
                trigger_error("Circular fallback reference in language " . $code, 256);
                $fallback = "en";
            }
            $language_seen[$code] = true;
            $this->loadLanguage($fallback);
            $fallback_cache = $this->cache[$fallback];
            foreach ($this->keys as $key) {
                if (isset($cache[$key]) && isset($fallback_cache[$key])) {
                    if (isset($this->mergeable_keys_map[$key])) {
                        $cache[$key] = $cache[$key] + $fallback_cache[$key];
                    } else {
                        if (isset($this->mergeable_keys_list[$key])) {
                            $cache[$key] = array_merge($fallback_cache[$key], $cache[$key]);
                        }
                    }
                } else {
                    $cache[$key] = $fallback_cache[$key];
                }
            }
        }
        $this->cache[$code] = $cache;
        return NULL;
    }
}
class HTMLPurifier_Length
{
    protected $n = NULL;
    protected $unit = NULL;
    protected $isValid = NULL;
    protected static $allowedUnits = ["em" => true, "ex" => true, "px" => true, "in" => true, "cm" => true, "mm" => true, "pt" => true, "pc" => true, "ch" => true, "rem" => true, "vw" => true, "vh" => true, "vmin" => true, "vmax" => true];
    public function __construct($n = "0", $u = false)
    {
        $this->n = (string) $n;
        $this->unit = $u !== false ? (string) $u : false;
    }
    public static function make($s)
    {
        if ($s instanceof HTMLPurifier_Length) {
            return $s;
        }
        $n_length = strspn($s, "1234567890.+-");
        $n = substr($s, 0, $n_length);
        $unit = substr($s, $n_length);
        if ($unit === "") {
            $unit = false;
        }
        return new HTMLPurifier_Length($n, $unit);
    }
    protected function validate()
    {
        if ($this->n === "+0" || $this->n === "-0") {
            $this->n = "0";
        }
        if ($this->n === "0" && $this->unit === false) {
            return true;
        }
        if (!ctype_lower($this->unit)) {
            $this->unit = strtolower($this->unit);
        }
        if (!isset(HTMLPurifier_Length::$allowedUnits[$this->unit])) {
            return false;
        }
        $def = new HTMLPurifier_AttrDef_CSS_Number();
        $result = $def->validate($this->n, false, false);
        if ($result === false) {
            return false;
        }
        $this->n = $result;
        return true;
    }
    public function toString()
    {
        if (!$this->isValid()) {
            return false;
        }
        return $this->n . $this->unit;
    }
    public function getN()
    {
        return $this->n;
    }
    public function getUnit()
    {
        return $this->unit;
    }
    public function isValid()
    {
        if ($this->isValid === NULL) {
            $this->isValid = $this->validate();
        }
        return $this->isValid;
    }
    public function compareTo($l)
    {
        if ($l === false) {
            return false;
        }
        if ($l->unit !== $this->unit) {
            $converter = new HTMLPurifier_UnitConverter();
            $l = $converter->convert($l, $this->unit);
            if ($l === false) {
                return false;
            }
        }
        return $this->n - $l->n;
    }
}
class HTMLPurifier_Lexer
{
    public $tracksLineNumbers = false;
    protected $_special_entity2str = ["&quot;" => "\"", "&amp;" => "&", "&lt;" => "<", "&gt;" => ">", "&#39;" => "'", "&#039;" => "'", "&#x27;" => "'"];
    public static function create($config)
    {
        if (!$config instanceof HTMLPurifier_Config) {
            $lexer = $config;
            trigger_error("Passing a prototype to\r\n                HTMLPurifier_Lexer::create() is deprecated, please instead\r\n                use %Core.LexerImpl", 512);
        } else {
            $lexer = $config->get("Core.LexerImpl");
        }
        $needs_tracking = $config->get("Core.MaintainLineNumbers") || $config->get("Core.CollectErrors");
        $inst = NULL;
        if (is_object($lexer)) {
            $inst = $lexer;
        } else {
            if (is_null($lexer)) {
                while ($needs_tracking) {
                    if (class_exists("DOMDocument", false) && method_exists("DOMDocument", "loadHTML") && !extension_loaded("domxml")) {
                        $lexer = "DOMLex";
                    } else {
                        $lexer = "DirectLex";
                    }
                    if (0) {
                    }
                    $lexer = "DirectLex";
                }
            }
            switch ($lexer) {
                case "DOMLex":
                    $inst = new HTMLPurifier_Lexer_DOMLex();
                    break;
                case "DirectLex":
                    $inst = new HTMLPurifier_Lexer_DirectLex();
                    break;
                case "PH5P":
                    $inst = new HTMLPurifier_Lexer_PH5P();
                    break;
                default:
                    throw new HTMLPurifier_Exception("Cannot instantiate unrecognized Lexer type " . htmlspecialchars($lexer));
            }
        }
        if (!$inst) {
            throw new HTMLPurifier_Exception("No lexer was instantiated");
        }
        if ($needs_tracking && !$inst->tracksLineNumbers) {
            throw new HTMLPurifier_Exception("Cannot use lexer that does not support line numbers with Core.MaintainLineNumbers or Core.CollectErrors (use DirectLex instead)");
        }
        return $inst;
    }
    public function __construct()
    {
        $this->_entity_parser = new HTMLPurifier_EntityParser();
    }
    public function parseText($string, $config)
    {
        return $this->parseData($string, false, $config);
    }
    public function parseAttr($string, $config)
    {
        return $this->parseData($string, true, $config);
    }
    public function parseData($string, $is_attr, $config)
    {
        if ($string === "") {
            return "";
        }
        $num_amp = substr_count($string, "&") - substr_count($string, "& ") - ($string[strlen($string) - 1] === "&" ? 1 : 0);
        if (!$num_amp) {
            return $string;
        }
        $num_esc_amp = substr_count($string, "&amp;");
        $string = strtr($string, $this->_special_entity2str);
        $num_amp_2 = substr_count($string, "&") - substr_count($string, "& ") - ($string[strlen($string) - 1] === "&" ? 1 : 0);
        if ($num_amp_2 <= $num_esc_amp) {
            return $string;
        }
        if ($config->get("Core.LegacyEntityDecoder")) {
            $string = $this->_entity_parser->substituteSpecialEntities($string);
        } else {
            if ($is_attr) {
                $string = $this->_entity_parser->substituteAttrEntities($string);
            } else {
                $string = $this->_entity_parser->substituteTextEntities($string);
            }
        }
        return $string;
    }
    public function tokenizeHTML($string, $config, $context)
    {
        trigger_error("Call to abstract class", 256);
    }
    protected static function escapeCDATA($string)
    {
        return preg_replace_callback("/<!\\[CDATA\\[(.+?)\\]\\]>/s", ["HTMLPurifier_Lexer", "CDATACallback"], $string);
    }
    protected static function escapeCommentedCDATA($string)
    {
        return preg_replace_callback("#<!--//--><!\\[CDATA\\[//><!--(.+?)//--><!\\]\\]>#s", ["HTMLPurifier_Lexer", "CDATACallback"], $string);
    }
    protected static function removeIEConditional($string)
    {
        return preg_replace("#<!--\\[if [^>]+\\]>.*?<!\\[endif\\]-->#si", "", $string);
    }
    protected static function CDATACallback($matches)
    {
        return htmlspecialchars($matches[1], ENT_COMPAT, "UTF-8");
    }
    public function normalize($html, $config, $context)
    {
        if ($config->get("Core.NormalizeNewlines")) {
            $html = str_replace("\r\n", "\n", $html);
            $html = str_replace("\r", "\n", $html);
        }
        if ($config->get("HTML.Trusted")) {
            $html = $this->escapeCommentedCDATA($html);
        }
        $html = $this->escapeCDATA($html);
        $html = $this->removeIEConditional($html);
        if ($config->get("Core.ConvertDocumentToFragment")) {
            $e = false;
            if ($config->get("Core.CollectErrors")) {
                $e =& $context->get("ErrorCollector");
            }
            $new_html = $this->extractBody($html);
            if ($e && $new_html != $html) {
                $e->send(2, "Lexer: Extracted body");
            }
            $html = $new_html;
        }
        if ($config->get("Core.LegacyEntityDecoder")) {
            $html = $this->_entity_parser->substituteNonSpecialEntities($html);
        }
        $html = HTMLPurifier_Encoder::cleanUTF8($html);
        if ($config->get("Core.RemoveProcessingInstructions")) {
            $html = preg_replace("#<\\?.+?\\?>#s", "", $html);
        }
        $hidden_elements = $config->get("Core.HiddenElements");
        if ($config->get("Core.AggressivelyRemoveScript") && !($config->get("HTML.Trusted") || !$config->get("Core.RemoveScriptContents") || empty($hidden_elements["script"]))) {
            $html = preg_replace("#<script[^>]*>.*?</script>#i", "", $html);
        }
        return $html;
    }
    public function extractBody($html)
    {
        $matches = [];
        $result = preg_match("|(.*?)<body[^>]*>(.*)</body>|is", $html, $matches);
        if ($result) {
            $comment_start = strrpos($matches[1], "<!--");
            $comment_end = strrpos($matches[1], "-->");
            if ($comment_start === false || $comment_end !== false && $comment_start < $comment_end) {
                return $matches[2];
            }
        }
        return $html;
    }
}
abstract class HTMLPurifier_Node
{
    public $line = NULL;
    public $col = NULL;
    public $armor = [];
    public $dead = false;
    public abstract function toTokenPair();
}
class HTMLPurifier_PercentEncoder
{
    protected $preserve = [];
    public function __construct($preserve = false)
    {
        for ($i = 48; $i <= 57; $i++) {
            $this->preserve[$i] = true;
        }
        for ($i = 65; $i <= 90; $i++) {
            $this->preserve[$i] = true;
        }
        for ($i = 97; $i <= 122; $i++) {
            $this->preserve[$i] = true;
        }
        $this->preserve[45] = true;
        $this->preserve[46] = true;
        $this->preserve[95] = true;
        $this->preserve[126] = true;
        if ($preserve !== false) {
            $i = 0;
            for ($c = strlen($preserve); $i < $c; $i++) {
                $this->preserve[ord($preserve[$i])] = true;
            }
        }
    }
    public function encode($string)
    {
        $ret = "";
        $i = 0;
        for ($c = strlen($string); $i < $c; $i++) {
            if ($string[$i] !== "%" && !isset($this->preserve[$int = ord($string[$i])])) {
                $ret .= "%" . sprintf("%02X", $int);
            } else {
                $ret .= $string[$i];
            }
        }
        return $ret;
    }
    public function normalize($string)
    {
        if ($string == "") {
            return "";
        }
        $parts = explode("%", $string);
        $ret = array_shift($parts);
        foreach ($parts as $part) {
            $length = strlen($part);
            if ($length < 2) {
                $ret .= "%25" . $part;
            } else {
                $encoding = substr($part, 0, 2);
                $text = substr($part, 2);
                if (!ctype_xdigit($encoding)) {
                    $ret .= "%25" . $part;
                } else {
                    $int = hexdec($encoding);
                    if (isset($this->preserve[$int])) {
                        $ret .= chr($int) . $text;
                    } else {
                        $encoding = strtoupper($encoding);
                        $ret .= "%" . $encoding . $text;
                    }
                }
            }
        }
        return $ret;
    }
}
class HTMLPurifier_PropertyList
{
    protected $data = [];
    protected $parent = NULL;
    protected $cache = NULL;
    public function __construct($parent = NULL)
    {
        $this->parent = $parent;
    }
    public function get($name)
    {
        if ($this->has($name)) {
            return $this->data[$name];
        }
        if ($this->parent) {
            return $this->parent->get($name);
        }
        throw new HTMLPurifier_Exception("Key '" . $name . "' not found");
    }
    public function set($name, $value)
    {
        $this->data[$name] = $value;
    }
    public function has($name)
    {
        return array_key_exists($name, $this->data);
    }
    public function reset($name = NULL)
    {
        if ($name == NULL) {
            $this->data = [];
        } else {
            unset($this->data[$name]);
        }
    }
    public function squash($force = false)
    {
        if ($this->cache !== NULL && !$force) {
            return $this->cache;
        }
        if ($this->parent) {
            return $this->cache = array_merge($this->parent->squash($force), $this->data);
        }
        return $this->cache = $this->data;
    }
    public function getParent()
    {
        return $this->parent;
    }
    public function setParent($plist)
    {
        $this->parent = $plist;
    }
}
class HTMLPurifier_Queue
{
    private $input = NULL;
    private $output = NULL;
    public function __construct($input = [])
    {
        $this->input = $input;
        $this->output = [];
    }
    public function shift()
    {
        if (empty($this->output)) {
            $this->output = array_reverse($this->input);
            $this->input = [];
        }
        if (empty($this->output)) {
            return NULL;
        }
        return array_pop($this->output);
    }
    public function push($x)
    {
        array_push($this->input, $x);
    }
    public function isEmpty()
    {
        return empty($this->input) && empty($this->output);
    }
}
abstract class HTMLPurifier_Strategy
{
    public abstract function execute($tokens, $config, $context);
}
class HTMLPurifier_StringHashParser
{
    public $default = "ID";
    public function parseFile($file)
    {
        if (!file_exists($file)) {
            return false;
        }
        $fh = fopen($file, "r");
        if (!$fh) {
            return false;
        }
        $ret = $this->parseHandle($fh);
        fclose($fh);
        return $ret;
    }
    public function parseMultiFile($file)
    {
        if (!file_exists($file)) {
            return false;
        }
        $ret = [];
        $fh = fopen($file, "r");
        if (!$fh) {
            return false;
        }
        while (!feof($fh)) {
            $ret[] = $this->parseHandle($fh);
        }
        fclose($fh);
        return $ret;
    }
    protected function parseHandle($fh)
    {
        $state = false;
        $single = false;
        $ret = [];
        $line = fgets($fh);
        if ($line !== false) {
            $line = rtrim($line, "\n\r");
            if ($state || $line !== "") {
                if ($line !== "----") {
                    if (strncmp("--#", $line, 3) !== 0) {
                        if (strncmp("--", $line, 2) === 0) {
                            $state = trim($line, "- ");
                            if (!isset($ret[$state])) {
                                $ret[$state] = "";
                            }
                        } else {
                            if (!$state) {
                                $single = true;
                                if (strpos($line, ":") !== false) {
                                    list($state, $line) = explode(":", $line, 2);
                                    $line = trim($line);
                                } else {
                                    $state = $this->default;
                                }
                            }
                            if ($single) {
                                $ret[$state] = $line;
                                $single = false;
                                $state = false;
                            } else {
                                $ret[$state] .= $line . "\n";
                            }
                        }
                    }
                }
            }
            if (!feof($fh)) {
            }
        }
        return $ret;
    }
}
abstract class HTMLPurifier_TagTransform
{
    public $transform_to = NULL;
    public abstract function transform($tag, $config, $context);
    protected function prependCSS(&$attr, $css)
    {
        $attr["style"] = isset($attr["style"]) ? $attr["style"] : "";
        $attr["style"] = $css . $attr["style"];
    }
}
abstract class HTMLPurifier_Token
{
    public $line = NULL;
    public $col = NULL;
    public $armor = [];
    public $skip = NULL;
    public $rewind = NULL;
    public $carryover = NULL;
    public function __get($n)
    {
        if ($n === "type") {
            trigger_error("Deprecated type property called; use instanceof", 1024);
            get_class($this);
            switch (get_class($this)) {
                case "HTMLPurifier_Token_Start":
                    return "start";
                    break;
                case "HTMLPurifier_Token_Empty":
                    return "empty";
                    break;
                case "HTMLPurifier_Token_End":
                    return "end";
                    break;
                case "HTMLPurifier_Token_Text":
                    return "text";
                    break;
                case "HTMLPurifier_Token_Comment":
                    return "comment";
                    break;
            }
        }
    }
    public function position($l = NULL, $c = NULL)
    {
        $this->line = $l;
        $this->col = $c;
    }
    public function rawPosition($l, $c)
    {
        if ($c === -1) {
            $l++;
        }
        $this->line = $l;
        $this->col = $c;
    }
    public abstract function toNode();
}
class HTMLPurifier_TokenFactory
{
    private $p_start = NULL;
    private $p_end = NULL;
    private $p_empty = NULL;
    private $p_text = NULL;
    private $p_comment = NULL;
    public function __construct()
    {
        $this->p_start = new HTMLPurifier_Token_Start("", []);
        $this->p_end = new HTMLPurifier_Token_End("");
        $this->p_empty = new HTMLPurifier_Token_Empty("", []);
        $this->p_text = new HTMLPurifier_Token_Text("");
        $this->p_comment = new HTMLPurifier_Token_Comment("");
    }
    public function createStart($name, $attr = [])
    {
        $p = clone $this->p_start;
        $p->__construct($name, $attr);
        return $p;
    }
    public function createEnd($name)
    {
        $p = clone $this->p_end;
        $p->__construct($name);
        return $p;
    }
    public function createEmpty($name, $attr = [])
    {
        $p = clone $this->p_empty;
        $p->__construct($name, $attr);
        return $p;
    }
    public function createText($data)
    {
        $p = clone $this->p_text;
        $p->__construct($data);
        return $p;
    }
    public function createComment($data)
    {
        $p = clone $this->p_comment;
        $p->__construct($data);
        return $p;
    }
}
class HTMLPurifier_URI
{
    public $scheme = NULL;
    public $userinfo = NULL;
    public $host = NULL;
    public $port = NULL;
    public $path = NULL;
    public $query = NULL;
    public $fragment = NULL;
    public function __construct($scheme, $userinfo, $host, $port, $path, $query, $fragment)
    {
        $this->scheme = is_null($scheme) || ctype_lower($scheme) ? $scheme : strtolower($scheme);
        $this->userinfo = $userinfo;
        $this->host = $host;
        $this->port = is_null($port) ? $port : (int) $port;
        $this->path = $path;
        $this->query = $query;
        $this->fragment = $fragment;
    }
    public function getSchemeObj($config, $context)
    {
        $registry = HTMLPurifier_URISchemeRegistry::instance();
        if ($this->scheme !== NULL) {
            $scheme_obj = $registry->getScheme($this->scheme, $config, $context);
            if (!$scheme_obj) {
                return false;
            }
        } else {
            $def = $config->getDefinition("URI");
            $scheme_obj = $def->getDefaultScheme($config, $context);
            if (!$scheme_obj) {
                if ($def->defaultScheme !== NULL) {
                    trigger_error("Default scheme object \"" . $def->defaultScheme . "\" was not readable", 512);
                }
                return false;
            }
        }
        return $scheme_obj;
    }
    public function validate($config, $context)
    {
        $chars_sub_delims = "!\$&'()*+,;=";
        $chars_gen_delims = ":/?#[]@";
        $chars_pchar = $chars_sub_delims . ":@";
        if (!is_null($this->host)) {
            $host_def = new HTMLPurifier_AttrDef_URI_Host();
            $this->host = $host_def->validate($this->host, $config, $context);
            if ($this->host === false) {
                $this->host = NULL;
            }
        }
        if (!is_null($this->scheme) && is_null($this->host) || $this->host === "") {
            $def = $config->getDefinition("URI");
            if ($def->defaultScheme === $this->scheme) {
                $this->scheme = NULL;
            }
        }
        if (!is_null($this->userinfo)) {
            $encoder = new HTMLPurifier_PercentEncoder($chars_sub_delims . ":");
            $this->userinfo = $encoder->encode($this->userinfo);
        }
        if (!is_null($this->port) && ($this->port < 1 || 65535 < $this->port)) {
            $this->port = NULL;
        }
        $segments_encoder = new HTMLPurifier_PercentEncoder($chars_pchar . "/");
        if (!is_null($this->host)) {
            $this->path = $segments_encoder->encode($this->path);
        } else {
            if ($this->path !== "") {
                if ($this->path[0] === "/") {
                    if (2 <= strlen($this->path) && $this->path[1] === "/") {
                        $this->path = "";
                    } else {
                        $this->path = $segments_encoder->encode($this->path);
                    }
                } else {
                    if (!is_null($this->scheme)) {
                        $this->path = $segments_encoder->encode($this->path);
                    } else {
                        $segment_nc_encoder = new HTMLPurifier_PercentEncoder($chars_sub_delims . "@");
                        $c = strpos($this->path, "/");
                        if ($c !== false) {
                            $this->path = $segment_nc_encoder->encode(substr($this->path, 0, $c)) . $segments_encoder->encode(substr($this->path, $c));
                        } else {
                            $this->path = $segment_nc_encoder->encode($this->path);
                        }
                    }
                }
            } else {
                $this->path = "";
            }
        }
        $qf_encoder = new HTMLPurifier_PercentEncoder($chars_pchar . "/?");
        if (!is_null($this->query)) {
            $this->query = $qf_encoder->encode($this->query);
        }
        if (!is_null($this->fragment)) {
            $this->fragment = $qf_encoder->encode($this->fragment);
        }
        return true;
    }
    public function toString()
    {
        $authority = NULL;
        if (!is_null($this->host)) {
            $authority = "";
            if (!is_null($this->userinfo)) {
                $authority .= $this->userinfo . "@";
            }
            $authority .= $this->host;
            if (!is_null($this->port)) {
                $authority .= ":" . $this->port;
            }
        }
        $result = "";
        if (!is_null($this->scheme)) {
            $result .= $this->scheme . ":";
        }
        if (!is_null($authority)) {
            $result .= "//" . $authority;
        }
        $result .= $this->path;
        if (!is_null($this->query)) {
            $result .= "?" . $this->query;
        }
        if (!is_null($this->fragment)) {
            $result .= "#" . $this->fragment;
        }
        return $result;
    }
    public function isLocal($config, $context)
    {
        if ($this->host === NULL) {
            return true;
        }
        $uri_def = $config->getDefinition("URI");
        if ($uri_def->host === $this->host) {
            return true;
        }
        return false;
    }
    public function isBenign($config, $context)
    {
        if (!$this->isLocal($config, $context)) {
            return false;
        }
        $scheme_obj = $this->getSchemeObj($config, $context);
        if (!$scheme_obj) {
            return false;
        }
        $current_scheme_obj = $config->getDefinition("URI")->getDefaultScheme($config, $context);
        if ($current_scheme_obj->secure && !$scheme_obj->secure) {
            return false;
        }
        return true;
    }
}
class HTMLPurifier_URIDefinition extends HTMLPurifier_Definition
{
    public $type = "URI";
    protected $filters = [];
    protected $postFilters = [];
    protected $registeredFilters = [];
    public $base = NULL;
    public $host = NULL;
    public $defaultScheme = NULL;
    public function __construct()
    {
        $this->registerFilter(new HTMLPurifier_URIFilter_DisableExternal());
        $this->registerFilter(new HTMLPurifier_URIFilter_DisableExternalResources());
        $this->registerFilter(new HTMLPurifier_URIFilter_DisableResources());
        $this->registerFilter(new HTMLPurifier_URIFilter_HostBlacklist());
        $this->registerFilter(new HTMLPurifier_URIFilter_SafeIframe());
        $this->registerFilter(new HTMLPurifier_URIFilter_MakeAbsolute());
        $this->registerFilter(new HTMLPurifier_URIFilter_Munge());
    }
    public function registerFilter($filter)
    {
        $this->registeredFilters[$filter->name] = $filter;
    }
    public function addFilter($filter, $config)
    {
        $r = $filter->prepare($config);
        if ($r === false) {
            return NULL;
        }
        if ($filter->post) {
            $this->postFilters[$filter->name] = $filter;
        } else {
            $this->filters[$filter->name] = $filter;
        }
    }
    protected function doSetup($config)
    {
        $this->setupMemberVariables($config);
        $this->setupFilters($config);
    }
    protected function setupFilters($config)
    {
        foreach ($this->registeredFilters as $name => $filter) {
            if ($filter->always_load) {
                $this->addFilter($filter, $config);
            } else {
                $conf = $config->get("URI." . $name);
                if ($conf !== false && $conf !== NULL) {
                    $this->addFilter($filter, $config);
                }
            }
        }
        unset($this->registeredFilters);
    }
    protected function setupMemberVariables($config)
    {
        $this->host = $config->get("URI.Host");
        $base_uri = $config->get("URI.Base");
        if (!is_null($base_uri)) {
            $parser = new HTMLPurifier_URIParser();
            $this->base = $parser->parse($base_uri);
            $this->defaultScheme = $this->base->scheme;
            if (is_null($this->host)) {
                $this->host = $this->base->host;
            }
        }
        if (is_null($this->defaultScheme)) {
            $this->defaultScheme = $config->get("URI.DefaultScheme");
        }
    }
    public function getDefaultScheme($config, $context)
    {
        return HTMLPurifier_URISchemeRegistry::instance()->getScheme($this->defaultScheme, $config, $context);
    }
    public function filter(&$uri, $config, $context)
    {
        foreach ($this->filters as $name => $f) {
            $result = $f->filter($uri, $config, $context);
            if (!$result) {
                return false;
            }
        }
        return true;
    }
    public function postFilter(&$uri, $config, $context)
    {
        foreach ($this->postFilters as $name => $f) {
            $result = $f->filter($uri, $config, $context);
            if (!$result) {
                return false;
            }
        }
        return true;
    }
}
abstract class HTMLPurifier_URIFilter
{
    public $name = NULL;
    public $post = false;
    public $always_load = false;
    public function prepare($config)
    {
        return true;
    }
    public abstract function filter(&$uri, $config, $context);
}
class HTMLPurifier_URIParser
{
    protected $percentEncoder = NULL;
    public function __construct()
    {
        $this->percentEncoder = new HTMLPurifier_PercentEncoder();
    }
    public function parse($uri)
    {
        $uri = $this->percentEncoder->normalize($uri);
        $r_URI = "!(([a-zA-Z0-9\\.\\+\\-]+):)?(//([^/?#\"<>]*))?([^?#\"<>]*)(\\?([^#\"<>]*))?(#([^\"<>]*))?!";
        $matches = [];
        $result = preg_match($r_URI, $uri, $matches);
        if (!$result) {
            return false;
        }
        $scheme = !empty($matches[1]) ? $matches[2] : NULL;
        $authority = !empty($matches[3]) ? $matches[4] : NULL;
        $path = $matches[5];
        $query = !empty($matches[6]) ? $matches[7] : NULL;
        $fragment = !empty($matches[8]) ? $matches[9] : NULL;
        if ($authority !== NULL) {
            $r_authority = "/^((.+?)@)?(\\[[^\\]]+\\]|[^:]*)(:(\\d*))?/";
            $matches = [];
            preg_match($r_authority, $authority, $matches);
            $userinfo = !empty($matches[1]) ? $matches[2] : NULL;
            $host = !empty($matches[3]) ? $matches[3] : "";
            $port = !empty($matches[4]) ? (int) $matches[5] : NULL;
        } else {
            $port = $host = $userinfo = NULL;
        }
        return new HTMLPurifier_URI($scheme, $userinfo, $host, $port, $path, $query, $fragment);
    }
}
abstract class HTMLPurifier_URIScheme
{
    public $default_port = NULL;
    public $browsable = false;
    public $secure = false;
    public $hierarchical = false;
    public $may_omit_host = false;
    public abstract function doValidate(&$uri, $config, $context);
    public function validate(&$uri, $config, $context)
    {
        if ($this->default_port == $uri->port) {
            $uri->port = NULL;
        }
        if (!$this->may_omit_host && !is_null($uri->scheme) && ($uri->host === "" || is_null($uri->host)) || is_null($uri->scheme) && $uri->host === "") {
            while (is_null($uri->scheme) && substr($uri->path, 0, 2) != "//") {
                $host = $config->get("URI.Host");
                if (!is_null($host)) {
                    $uri->host = $host;
                    if (false) {
                    }
                } else {
                    return false;
                }
                $uri->host = NULL;
            }
        }
        return $this->doValidate($uri, $config, $context);
    }
}
class HTMLPurifier_URISchemeRegistry
{
    protected $schemes = [];
    public static function instance($prototype = NULL)
    {
        if ($prototype !== NULL) {
            $instance = $prototype;
        } else {
            if ($instance === NULL || $prototype) {
                $instance = new HTMLPurifier_URISchemeRegistry();
            }
        }
        return $instance;
    }
    public function getScheme($scheme, $config, $context)
    {
        if (!$config) {
            $config = HTMLPurifier_Config::createDefault();
        }
        $allowed_schemes = $config->get("URI.AllowedSchemes");
        if (!$config->get("URI.OverrideAllowedSchemes") && !isset($allowed_schemes[$scheme])) {
            return NULL;
        }
        if (isset($this->schemes[$scheme])) {
            return $this->schemes[$scheme];
        }
        if (!isset($allowed_schemes[$scheme])) {
            return NULL;
        }
        $class = "HTMLPurifier_URIScheme_" . $scheme;
        if (!class_exists($class)) {
            return NULL;
        }
        $this->schemes[$scheme] = new $class();
        return $this->schemes[$scheme];
    }
    public function register($scheme, $scheme_obj)
    {
        $this->schemes[$scheme] = $scheme_obj;
    }
}
class HTMLPurifier_UnitConverter
{
    protected $outputPrecision = NULL;
    protected $internalPrecision = NULL;
    private $bcmath = NULL;
    protected static $units = ["1" => ["px" => 3, "pt" => 4, "pc" => 48, "in" => 288, "2" => ["pt", "0.352777778", "mm"]], "2" => ["mm" => 1, "cm" => 10, "1" => ["mm", "2.83464567", "pt"]]];
    const ENGLISH = 1;
    const METRIC = 2;
    const DIGITAL = 3;
    public function __construct($output_precision = 4, $internal_precision = 10, $force_no_bcmath = false)
    {
        $this->outputPrecision = $output_precision;
        $this->internalPrecision = $internal_precision;
        $this->bcmath = !$force_no_bcmath && function_exists("bcmul");
    }
    public function convert($length, $to_unit)
    {
        if (!$length->isValid()) {
            return false;
        }
        $n = $length->getN();
        $unit = $length->getUnit();
        if ($n === "0" || $unit === false) {
            return new HTMLPurifier_Length("0", false);
        }
        $state = $dest_state = false;
        foreach (self::$units as $k => $x) {
            if (isset($x[$unit])) {
                $state = $k;
            }
            if (isset($x[$to_unit])) {
                $dest_state = $k;
            }
        }
        if (!$state || !$dest_state) {
            return false;
        }
        $sigfigs = $this->getSigFigs($n);
        if ($sigfigs < $this->outputPrecision) {
            $sigfigs = $this->outputPrecision;
        }
        $log = (int) floor(log(abs($n), 10));
        $cp = $log < 0 ? $this->internalPrecision - $log : $this->internalPrecision;
        $i = 0;
        while ($i < 2) {
            if ($dest_state === $state) {
                $dest_unit = $to_unit;
            } else {
                $dest_unit = self::$units[$state][$dest_state][0];
            }
            if ($dest_unit !== $unit) {
                $factor = $this->div(self::$units[$state][$unit], self::$units[$state][$dest_unit], $cp);
                $n = $this->mul($n, $factor, $cp);
                $unit = $dest_unit;
            }
            if ($n === "") {
                $n = "0";
                $unit = $to_unit;
            } else {
                if ($dest_state !== $state) {
                    if ($i !== 0) {
                        return false;
                    }
                    $n = $this->mul($n, self::$units[$state][$dest_state][1], $cp);
                    $unit = self::$units[$state][$dest_state][2];
                    $state = $dest_state;
                    $i++;
                }
            }
        }
        if ($unit !== $to_unit) {
            return false;
        }
        $n = $this->round($n, $sigfigs);
        if (strpos($n, ".") !== false) {
            $n = rtrim($n, "0");
        }
        $n = rtrim($n, ".");
        return new HTMLPurifier_Length($n, $unit);
    }
    public function getSigFigs($n)
    {
        $n = ltrim($n, "0+-");
        $dp = strpos($n, ".");
        if ($dp === false) {
            $sigfigs = strlen(rtrim($n, "0"));
        } else {
            $sigfigs = strlen(ltrim($n, "0."));
            if ($dp !== 0) {
                $sigfigs--;
            }
        }
        return $sigfigs;
    }
    private function add($s1, $s2, $scale)
    {
        if ($this->bcmath) {
            return bcadd($s1, $s2, $scale);
        }
        return $this->scale((double) $s1 + (double) $s2, $scale);
    }
    private function mul($s1, $s2, $scale)
    {
        if ($this->bcmath) {
            return bcmul($s1, $s2, $scale);
        }
        return $this->scale((double) $s1 * (double) $s2, $scale);
    }
    private function div($s1, $s2, $scale)
    {
        if ($this->bcmath) {
            return bcdiv($s1, $s2, $scale);
        }
        return $this->scale((double) $s1 / (double) $s2, $scale);
    }
    private function round($n, $sigfigs)
    {
        $new_log = (int) floor(log(abs($n), 10));
        $rp = $sigfigs - $new_log - 1;
        $neg = $n < 0 ? "-" : "";
        if ($this->bcmath) {
            if (0 <= $rp) {
                $n = bcadd($n, $neg . "0." . str_repeat("0", $rp) . "5", $rp + 1);
                $n = bcdiv($n, "1", $rp);
            } else {
                $n = bcadd($n, $neg . "5" . str_repeat("0", $new_log - $sigfigs), 0);
                $n = substr($n, 0, $sigfigs + strlen($neg)) . str_repeat("0", $new_log - $sigfigs + 1);
            }
            return $n;
        }
        return $this->scale(round($n, $sigfigs - $new_log - 1), $rp + 1);
    }
    private function scale($r, $scale)
    {
        if ($scale < 0) {
            $r = sprintf("%.0f", (double) $r);
            $precise = (string) round(substr($r, 0, strlen($r) + $scale), -1);
            return substr($precise, 0, -1) . str_repeat("0", -1 * $scale + 1);
        }
        return sprintf("%." . $scale . "f", (double) $r);
    }
}
class HTMLPurifier_VarParser
{
    public static $stringTypes = ["1" => true, "2" => true, "3" => true, "4" => true];
    public static $types = ["string" => 1, "istring" => 2, "text" => 3, "itext" => 4, "int" => 5, "float" => 6, "bool" => 7, "lookup" => 8, "list" => 9, "hash" => 10, "mixed" => 11];
    const C_STRING = 1;
    const ISTRING = 2;
    const TEXT = 3;
    const ITEXT = 4;
    const C_INT = 5;
    const C_FLOAT = 6;
    const C_BOOL = 7;
    const LOOKUP = 8;
    const ALIST = 9;
    const HASH = 10;
    const C_MIXED = 11;
    public final function parse($var, $type, $allow_null = false)
    {
        if (is_string($type)) {
            if (!isset(HTMLPurifier_VarParser::$types[$type])) {
                throw new HTMLPurifier_VarParserException("Invalid type '" . $type . "'");
            }
            $type = HTMLPurifier_VarParser::$types[$type];
        }
        $var = $this->parseImplementation($var, $type, $allow_null);
        if ($allow_null && $var === NULL) {
            return NULL;
        }
        switch ($type) {
            case 1:
            case 2:
            case 3:
            case 4:
                if (is_string($var)) {
                    if ($type == 2 || $type == 4) {
                        $var = strtolower($var);
                    }
                    return $var;
                }
                break;
            case 5:
                if (is_int($var)) {
                    return $var;
                }
                break;
            case 6:
                if (is_float($var)) {
                    return $var;
                }
                break;
            case 7:
                if (is_bool($var)) {
                    return $var;
                }
                break;
            case 8:
            case 9:
            case 10:
                if (is_array($var)) {
                    if ($type === 8) {
                        foreach ($var as $k) {
                            if ($k !== true) {
                                $this->error("Lookup table contains value other than true");
                            }
                        }
                    } else {
                        if ($type === 9) {
                            $keys = array_keys($var);
                            if (array_keys($keys) !== $keys) {
                                $this->error("Indices for list are not uniform");
                            }
                        }
                    }
                    return $var;
                }
                break;
            case 11:
                return $var;
                break;
            default:
                $this->errorInconsistent(get_class($this), $type);
                $this->errorGeneric($var, $type);
        }
    }
    protected function parseImplementation($var, $type, $allow_null)
    {
        return $var;
    }
    protected function error($msg)
    {
        throw new HTMLPurifier_VarParserException($msg);
    }
    protected function errorInconsistent($class, $type)
    {
        throw new HTMLPurifier_Exception("Inconsistency in " . $class . ": " . HTMLPurifier_VarParser::getTypeName($type) . " not implemented");
    }
    protected function errorGeneric($var, $type)
    {
        $vtype = gettype($var);
        $this->error("Expected type " . HTMLPurifier_VarParser::getTypeName($type) . ", got " . $vtype);
    }
    public static function getTypeName($type)
    {
        if (!$lookup) {
            $lookup = array_flip(HTMLPurifier_VarParser::$types);
        }
        if (!isset($lookup[$type])) {
            return "unknown";
        }
        return $lookup[$type];
    }
}
class HTMLPurifier_Zipper
{
    public $front = NULL;
    public $back = NULL;
    public function __construct($front, $back)
    {
        $this->front = $front;
        $this->back = $back;
    }
    public static function fromArray($array)
    {
        $z = new self([], array_reverse($array));
        $t = $z->delete();
        return [$z, $t];
    }
    public function toArray($t = NULL)
    {
        $a = $this->front;
        if ($t !== NULL) {
            $a[] = $t;
        }
        for ($i = count($this->back) - 1; 0 <= $i; $i--) {
            $a[] = $this->back[$i];
        }
        return $a;
    }
    public function next($t)
    {
        if ($t !== NULL) {
            array_push($this->front, $t);
        }
        return empty($this->back) ? NULL : array_pop($this->back);
    }
    public function advance($t, $n)
    {
        for ($i = 0; $i < $n; $i++) {
            $t = $this->next($t);
        }
        return $t;
    }
    public function prev($t)
    {
        if ($t !== NULL) {
            array_push($this->back, $t);
        }
        return empty($this->front) ? NULL : array_pop($this->front);
    }
    public function delete()
    {
        return empty($this->back) ? NULL : array_pop($this->back);
    }
    public function done()
    {
        return empty($this->back);
    }
    public function insertBefore($t)
    {
        if ($t !== NULL) {
            array_push($this->front, $t);
        }
    }
    public function insertAfter($t)
    {
        if ($t !== NULL) {
            array_push($this->back, $t);
        }
    }
    public function splice($t, $delete, $replacement)
    {
        $old = [];
        $r = $t;
        for ($i = $delete; 0 < $i; $i--) {
            $old[] = $r;
            $r = $this->delete();
        }
        for ($i = count($replacement) - 1; 0 <= $i; $i--) {
            $this->insertAfter($r);
            $r = $replacement[$i];
        }
        return [$old, $r];
    }
}
class HTMLPurifier_AttrDef_CSS extends HTMLPurifier_AttrDef
{
    public function validate($css, $config, $context)
    {
        $css = $this->parseCDATA($css);
        $definition = $config->getCSSDefinition();
        $allow_duplicates = $config->get("CSS.AllowDuplicates");
        $len = strlen($css);
        $accum = "";
        $declarations = [];
        $quoted = false;
        $i = 0;
        while ($i < $len) {
            $c = strcspn($css, ";'\"", $i);
            $accum .= substr($css, $i, $c);
            $i += $c;
            if ($i != $len) {
                $d = $css[$i];
                if ($quoted) {
                    $accum .= $d;
                    if ($d == $quoted) {
                        $quoted = false;
                    }
                } else {
                    if ($d == ";") {
                        $declarations[] = $accum;
                        $accum = "";
                    } else {
                        $accum .= $d;
                        $quoted = $d;
                    }
                }
                $i++;
            }
        }
        if ($accum != "") {
            $declarations[] = $accum;
        }
        $propvalues = [];
        $new_declarations = "";
        $property = false;
        $context->register("CurrentCSSProperty", $property);
        foreach ($declarations as $declaration) {
            if ($declaration) {
                if (strpos($declaration, ":")) {
                    list($property, $value) = explode(":", $declaration, 2);
                    $property = trim($property);
                    $value = trim($value);
                    $ok = false;
                    if (isset($definition->info[$property])) {
                        $ok = true;
                    } else {
                        if (!ctype_lower($property)) {
                            $property = strtolower($property);
                            if (isset($definition->info[$property])) {
                                $ok = true;
                            } else {
                                if (0) {
                                }
                            }
                        }
                    }
                    if ($ok) {
                        if (strtolower(trim($value)) !== "inherit") {
                            $result = $definition->info[$property]->validate($value, $config, $context);
                        } else {
                            $result = "inherit";
                        }
                        if ($result !== false) {
                            if ($allow_duplicates) {
                                $new_declarations .= $property . ":" . $result . ";";
                            } else {
                                $propvalues[$property] = $result;
                            }
                        }
                    }
                }
            }
        }
        $context->destroy("CurrentCSSProperty");
        foreach ($propvalues as $prop => $value) {
            $new_declarations .= $prop . ":" . $value . ";";
        }
        return $new_declarations ? $new_declarations : false;
    }
}
class HTMLPurifier_AttrDef_Clone extends HTMLPurifier_AttrDef
{
    protected $clone = NULL;
    public function __construct($clone)
    {
        $this->clone = $clone;
    }
    public function validate($v, $config, $context)
    {
        return $this->clone->validate($v, $config, $context);
    }
    public function make($string)
    {
        return clone $this->clone;
    }
}
class HTMLPurifier_AttrDef_Enum extends HTMLPurifier_AttrDef
{
    public $valid_values = [];
    protected $case_sensitive = false;
    public function __construct($valid_values = [], $case_sensitive = false)
    {
        $this->valid_values = array_flip($valid_values);
        $this->case_sensitive = $case_sensitive;
    }
    public function validate($string, $config, $context)
    {
        $string = trim($string);
        if (!$this->case_sensitive) {
            $string = ctype_lower($string) ? $string : strtolower($string);
        }
        $result = isset($this->valid_values[$string]);
        return $result ? $string : false;
    }
    public function make($string)
    {
        if (2 < strlen($string) && $string[0] == "s" && $string[1] == ":") {
            $string = substr($string, 2);
            $sensitive = true;
        } else {
            $sensitive = false;
        }
        $values = explode(",", $string);
        return new HTMLPurifier_AttrDef_Enum($values, $sensitive);
    }
}
class HTMLPurifier_AttrDef_Integer extends HTMLPurifier_AttrDef
{
    protected $negative = true;
    protected $zero = true;
    protected $positive = true;
    public function __construct($negative = true, $zero = true, $positive = true)
    {
        $this->negative = $negative;
        $this->zero = $zero;
        $this->positive = $positive;
    }
    public function validate($integer, $config, $context)
    {
        $integer = $this->parseCDATA($integer);
        if ($integer === "") {
            return false;
        }
        if ($this->negative && $integer[0] === "-") {
            $digits = substr($integer, 1);
            if ($digits === "0") {
                $integer = "0";
            }
        } else {
            if ($this->positive && $integer[0] === "+") {
                $digits = $integer = substr($integer, 1);
            } else {
                $digits = $integer;
            }
        }
        if (!ctype_digit($digits)) {
            return false;
        }
        if (!$this->zero && $integer == 0) {
            return false;
        }
        if (!$this->positive && 0 < $integer) {
            return false;
        }
        if (!$this->negative && $integer < 0) {
            return false;
        }
        return $integer;
    }
}
class HTMLPurifier_AttrDef_Lang extends HTMLPurifier_AttrDef
{
    public function validate($string, $config, $context)
    {
        $string = trim($string);
        if (!$string) {
            return false;
        }
        $subtags = explode("-", $string);
        $num_subtags = count($subtags);
        if ($num_subtags == 0) {
            return false;
        }
        $length = strlen($subtags[0]);
        switch ($length) {
            case 0:
                return false;
                break;
            case 1:
                if (!($subtags[0] == "x" || $subtags[0] == "i")) {
                    return false;
                }
                break;
            case 2:
            case 3:
                if (!ctype_alpha($subtags[0])) {
                    return false;
                }
                if (!ctype_lower($subtags[0])) {
                    $subtags[0] = strtolower($subtags[0]);
                }
                $new_string = $subtags[0];
                if ($num_subtags == 1) {
                    return $new_string;
                }
                $length = strlen($subtags[1]);
                if ($length == 0 || $length == 1 && $subtags[1] != "x" || 8 < $length || !ctype_alnum($subtags[1])) {
                    return $new_string;
                }
                if (!ctype_lower($subtags[1])) {
                    $subtags[1] = strtolower($subtags[1]);
                }
                $new_string .= "-" . $subtags[1];
                if ($num_subtags == 2) {
                    return $new_string;
                }
                for ($i = 2; $i < $num_subtags; $i++) {
                    $length = strlen($subtags[$i]);
                    if ($length == 0 || 8 < $length || !ctype_alnum($subtags[$i])) {
                        return $new_string;
                    }
                    if (!ctype_lower($subtags[$i])) {
                        $subtags[$i] = strtolower($subtags[$i]);
                    }
                    $new_string .= "-" . $subtags[$i];
                }
                return $new_string;
                break;
            default:
                return false;
        }
    }
}
class HTMLPurifier_AttrDef_Switch
{
    protected $tag = NULL;
    protected $withTag = NULL;
    protected $withoutTag = NULL;
    public function __construct($tag, $with_tag, $without_tag)
    {
        $this->tag = $tag;
        $this->withTag = $with_tag;
        $this->withoutTag = $without_tag;
    }
    public function validate($string, $config, $context)
    {
        $token = $context->get("CurrentToken", true);
        if (!$token || $token->name !== $this->tag) {
            return $this->withoutTag->validate($string, $config, $context);
        }
        return $this->withTag->validate($string, $config, $context);
    }
}
class HTMLPurifier_AttrDef_Text extends HTMLPurifier_AttrDef
{
    public function validate($string, $config, $context)
    {
        return $this->parseCDATA($string);
    }
}
class HTMLPurifier_AttrDef_URI extends HTMLPurifier_AttrDef
{
    protected $parser = NULL;
    protected $embedsResource = NULL;
    public function __construct($embeds_resource = false)
    {
        $this->parser = new HTMLPurifier_URIParser();
        $this->embedsResource = (bool) $embeds_resource;
    }
    public function make($string)
    {
        $embeds = $string === "embedded";
        return new HTMLPurifier_AttrDef_URI($embeds);
    }
    public function validate($uri, $config, $context)
    {
        if ($config->get("URI.Disable")) {
            return false;
        }
        $uri = $this->parseCDATA($uri);
        $uri = $this->parser->parse($uri);
        if ($uri === false) {
            return false;
        }
        $context->register("EmbeddedURI", $this->embedsResource);
        $ok = false;
        $result = $uri->validate($config, $context);
        if ($result) {
            $uri_def = $config->getDefinition("URI");
            $result = $uri_def->filter($uri, $config, $context);
            if ($result) {
                $scheme_obj = $uri->getSchemeObj($config, $context);
                if ($scheme_obj) {
                    if (!$this->embedsResource || $scheme_obj->browsable) {
                        $result = $scheme_obj->validate($uri, $config, $context);
                        if ($result) {
                            $result = $uri_def->postFilter($uri, $config, $context);
                            if ($result) {
                                $ok = true;
                                if (false) {
                                }
                            }
                        }
                    }
                }
            }
        }
        $context->destroy("EmbeddedURI");
        if (!$ok) {
            return false;
        }
        return $uri->toString();
    }
}
class HTMLPurifier_AttrDef_CSS_Number extends HTMLPurifier_AttrDef
{
    protected $non_negative = false;
    public function __construct($non_negative = false)
    {
        $this->non_negative = $non_negative;
    }
    public function validate($number, $config, $context)
    {
        $number = $this->parseCDATA($number);
        if ($number === "") {
            return false;
        }
        if ($number === "0") {
            return "0";
        }
        $sign = "";
        switch ($number[0]) {
            case "-":
                if ($this->non_negative) {
                    return false;
                }
                $sign = "-";
                break;
            case "+":
                $number = substr($number, 1);
                break;
            default:
                if (ctype_digit($number)) {
                    $number = ltrim($number, "0");
                    return $number ? $sign . $number : "0";
                }
                if (strpos($number, ".") === false) {
                    return false;
                }
                list($left, $right) = explode(".", $number, 2);
                if ($left === "" && $right === "") {
                    return false;
                }
                if ($left !== "" && !ctype_digit($left)) {
                    return false;
                }
                $left = ltrim($left, "0");
                $right = rtrim($right, "0");
                if ($right === "") {
                    return $left ? $sign . $left : "0";
                }
                if (!ctype_digit($right)) {
                    return false;
                }
                return $sign . $left . "." . $right;
        }
    }
}
class HTMLPurifier_AttrDef_CSS_AlphaValue extends HTMLPurifier_AttrDef_CSS_Number
{
    public function __construct()
    {
        parent::__construct(false);
    }
    public function validate($number, $config, $context)
    {
        $result = parent::validate($number, $config, $context);
        if ($result === false) {
            return $result;
        }
        $float = (double) $result;
        if ($float < 0) {
            $result = "0";
        }
        if (0 < $float) {
            $result = "1";
        }
        return $result;
    }
}
class HTMLPurifier_AttrDef_CSS_Background extends HTMLPurifier_AttrDef
{
    protected $info = NULL;
    public function __construct($config)
    {
        $def = $config->getCSSDefinition();
        $this->info["background-color"] = $def->info["background-color"];
        $this->info["background-image"] = $def->info["background-image"];
        $this->info["background-repeat"] = $def->info["background-repeat"];
        $this->info["background-attachment"] = $def->info["background-attachment"];
        $this->info["background-position"] = $def->info["background-position"];
    }
    public function validate($string, $config, $context)
    {
        $string = $this->parseCDATA($string);
        if ($string === "") {
            return false;
        }
        $string = $this->mungeRgb($string);
        $bits = explode(" ", $string);
        $caught = [];
        $caught["color"] = false;
        $caught["image"] = false;
        $caught["repeat"] = false;
        $caught["attachment"] = false;
        $caught["position"] = false;
        $i = 0;
        foreach ($bits as $bit) {
            if ($bit !== "") {
                foreach ($caught as $key => $status) {
                    if ($key != "position") {
                        if ($status === false) {
                            $r = $this->info["background-" . $key]->validate($bit, $config, $context);
                        }
                    } else {
                        $r = $bit;
                    }
                    if ($r !== false) {
                        if ($key == "position") {
                            if ($caught[$key] === false) {
                                $caught[$key] = "";
                            }
                            $caught[$key] .= $r . " ";
                        } else {
                            $caught[$key] = $r;
                        }
                        $i++;
                    }
                }
            }
        }
        if (!$i) {
            return false;
        }
        if ($caught["position"] !== false) {
            $caught["position"] = $this->info["background-position"]->validate($caught["position"], $config, $context);
        }
        $ret = [];
        foreach ($caught as $value) {
            if ($value !== false) {
                $ret[] = $value;
            }
        }
        if (empty($ret)) {
            return false;
        }
        return implode(" ", $ret);
    }
}
class HTMLPurifier_AttrDef_CSS_BackgroundPosition extends HTMLPurifier_AttrDef
{
    protected $length = NULL;
    protected $percentage = NULL;
    public function __construct()
    {
        $this->length = new HTMLPurifier_AttrDef_CSS_Length();
        $this->percentage = new HTMLPurifier_AttrDef_CSS_Percentage();
    }
    public function validate($string, $config, $context)
    {
        $string = $this->parseCDATA($string);
        $bits = explode(" ", $string);
        $keywords = [];
        $keywords["h"] = false;
        $keywords["v"] = false;
        $keywords["ch"] = false;
        $keywords["cv"] = false;
        $measures = [];
        $i = 0;
        $lookup = ["top" => "v", "bottom" => "v", "left" => "h", "right" => "h", "center" => "c"];
        foreach ($bits as $bit) {
            if ($bit !== "") {
                $lbit = ctype_lower($bit) ? $bit : strtolower($bit);
                if (isset($lookup[$lbit])) {
                    $status = $lookup[$lbit];
                    if ($status == "c") {
                        if ($i == 0) {
                            $status = "ch";
                        } else {
                            $status = "cv";
                        }
                    }
                    $keywords[$status] = $lbit;
                    $i++;
                }
                $r = $this->length->validate($bit, $config, $context);
                if ($r !== false) {
                    $measures[] = $r;
                    $i++;
                }
                $r = $this->percentage->validate($bit, $config, $context);
                if ($r !== false) {
                    $measures[] = $r;
                    $i++;
                }
            }
        }
        if (!$i) {
            return false;
        }
        $ret = [];
        if ($keywords["h"]) {
            $ret[] = $keywords["h"];
        } else {
            if ($keywords["ch"]) {
                $ret[] = $keywords["ch"];
                $keywords["cv"] = false;
            } else {
                if (count($measures)) {
                    $ret[] = array_shift($measures);
                }
            }
        }
        if ($keywords["v"]) {
            $ret[] = $keywords["v"];
        } else {
            if ($keywords["cv"]) {
                $ret[] = $keywords["cv"];
            } else {
                if (count($measures)) {
                    $ret[] = array_shift($measures);
                }
            }
        }
        if (empty($ret)) {
            return false;
        }
        return implode(" ", $ret);
    }
}
class HTMLPurifier_AttrDef_CSS_Border extends HTMLPurifier_AttrDef
{
    protected $info = [];
    public function __construct($config)
    {
        $def = $config->getCSSDefinition();
        $this->info["border-width"] = $def->info["border-width"];
        $this->info["border-style"] = $def->info["border-style"];
        $this->info["border-top-color"] = $def->info["border-top-color"];
    }
    public function validate($string, $config, $context)
    {
        $string = $this->parseCDATA($string);
        $string = $this->mungeRgb($string);
        $bits = explode(" ", $string);
        $done = [];
        $ret = "";
        foreach ($bits as $bit) {
            foreach ($this->info as $propname => $validator) {
                if (!isset($done[$propname])) {
                    $r = $validator->validate($bit, $config, $context);
                    if ($r !== false) {
                        $ret .= $r . " ";
                        $done[$propname] = true;
                    }
                }
            }
        }
        return rtrim($ret);
    }
}
class HTMLPurifier_AttrDef_CSS_Color extends HTMLPurifier_AttrDef
{
    protected $alpha = NULL;
    public function __construct()
    {
        $this->alpha = new HTMLPurifier_AttrDef_CSS_AlphaValue();
    }
    public function validate($color, $config, $context)
    {
        if ($colors === NULL) {
            $colors = $config->get("Core.ColorKeywords");
        }
        $color = trim($color);
        if ($color === "") {
            return false;
        }
        $lower = strtolower($color);
        if (isset($colors[$lower])) {
            return $colors[$lower];
        }
        if (preg_match("#(rgb|rgba|hsl|hsla)\\(#", $color, $matches) === 1) {
            $length = strlen($color);
            if (strpos($color, ")") !== $length - 1) {
                return false;
            }
            $function = $matches[1];
            $parameters_size = 3;
            $alpha_channel = false;
            if (substr($function, -1) === "a") {
                $parameters_size = 4;
                $alpha_channel = true;
            }
            $allowed_types = ["1" => ["percentage" => 100, "integer" => 255], "2" => ["percentage" => 100, "integer" => 255], "3" => ["percentage" => 100, "integer" => 255]];
            $allow_different_types = false;
            if (strpos($function, "hsl") !== false) {
                $allowed_types = ["1" => ["integer" => 360], "2" => ["percentage" => 100], "3" => ["percentage" => 100]];
                $allow_different_types = true;
            }
            $values = trim(str_replace($function, "", $color), " ()");
            $parts = explode(",", $values);
            if (count($parts) !== $parameters_size) {
                return false;
            }
            $type = false;
            $new_parts = [];
            $i = 0;
            foreach ($parts as $part) {
                $i++;
                $part = trim($part);
                if ($part === "") {
                    return false;
                }
                if ($alpha_channel === true && $i === count($parts)) {
                    $result = $this->alpha->validate($part, $config, $context);
                    if ($result === false) {
                        return false;
                    }
                    $new_parts[] = (string) $result;
                } else {
                    if (substr($part, -1) === "%") {
                        $current_type = "percentage";
                    } else {
                        $current_type = "integer";
                    }
                    if (!array_key_exists($current_type, $allowed_types[$i])) {
                        return false;
                    }
                    if (!$type) {
                        $type = $current_type;
                    }
                    if ($allow_different_types === false && $type != $current_type) {
                        return false;
                    }
                    $max_value = $allowed_types[$i][$current_type];
                    if ($current_type == "integer") {
                        $new_parts[] = (int) max(min($part, $max_value), 0);
                    } else {
                        if ($current_type == "percentage") {
                            $new_parts[] = (double) max(min(rtrim($part, "%"), $max_value), 0) . "%";
                        }
                    }
                }
            }
            $new_values = implode(",", $new_parts);
            $color = $function . "(" . $new_values . ")";
        } else {
            if ($color[0] === "#") {
                $hex = substr($color, 1);
            } else {
                $hex = $color;
                $color = "#" . $color;
            }
            $length = strlen($hex);
            if ($length !== 3 && $length !== 6) {
                return false;
            }
            if (!ctype_xdigit($hex)) {
                return false;
            }
        }
        return $color;
    }
}
class HTMLPurifier_AttrDef_CSS_Composite extends HTMLPurifier_AttrDef
{
    public $defs = NULL;
    public function __construct($defs)
    {
        $this->defs = $defs;
    }
    public function validate($string, $config, $context)
    {
        foreach ($this->defs as $i => $def) {
            $result = $this->defs[$i]->validate($string, $config, $context);
            if ($result !== false) {
                return $result;
            }
        }
        return false;
    }
}
class HTMLPurifier_AttrDef_CSS_DenyElementDecorator extends HTMLPurifier_AttrDef
{
    public $def = NULL;
    public $element = NULL;
    public function __construct($def, $element)
    {
        $this->def = $def;
        $this->element = $element;
    }
    public function validate($string, $config, $context)
    {
        $token = $context->get("CurrentToken", true);
        if ($token && $token->name == $this->element) {
            return false;
        }
        return $this->def->validate($string, $config, $context);
    }
}
class HTMLPurifier_AttrDef_CSS_Filter extends HTMLPurifier_AttrDef
{
    protected $intValidator = NULL;
    public function __construct()
    {
        $this->intValidator = new HTMLPurifier_AttrDef_Integer();
    }
    public function validate($value, $config, $context)
    {
        $value = $this->parseCDATA($value);
        if ($value === "none") {
            return $value;
        }
        $function_length = strcspn($value, "(");
        $function = trim(substr($value, 0, $function_length));
        if ($function !== "alpha" && $function !== "Alpha" && $function !== "progid:DXImageTransform.Microsoft.Alpha") {
            return false;
        }
        $cursor = $function_length + 1;
        $parameters_length = strcspn($value, ")", $cursor);
        $parameters = substr($value, $cursor, $parameters_length);
        $params = explode(",", $parameters);
        $ret_params = [];
        $lookup = [];
        foreach ($params as $param) {
            list($key, $value) = explode("=", $param);
            $key = trim($key);
            $value = trim($value);
            if (!isset($lookup[$key])) {
                if ($key === "opacity") {
                    $value = $this->intValidator->validate($value, $config, $context);
                    if ($value !== false) {
                        $int = (int) $value;
                        if (100 < $int) {
                            $value = "100";
                        }
                        if ($int < 0) {
                            $value = "0";
                        }
                        $ret_params[] = $key . "=" . $value;
                        $lookup[$key] = true;
                    }
                }
            }
        }
        $ret_parameters = implode(",", $ret_params);
        $ret_function = $function . "(" . $ret_parameters . ")";
        return $ret_function;
    }
}
class HTMLPurifier_AttrDef_CSS_Font extends HTMLPurifier_AttrDef
{
    protected $info = [];
    public function __construct($config)
    {
        $def = $config->getCSSDefinition();
        $this->info["font-style"] = $def->info["font-style"];
        $this->info["font-variant"] = $def->info["font-variant"];
        $this->info["font-weight"] = $def->info["font-weight"];
        $this->info["font-size"] = $def->info["font-size"];
        $this->info["line-height"] = $def->info["line-height"];
        $this->info["font-family"] = $def->info["font-family"];
    }
    public function validate($string, $config, $context)
    {
        $string = $this->parseCDATA($string);
        if ($string === "") {
            return false;
        }
        $lowercase_string = strtolower($string);
        if (isset($system_fonts[$lowercase_string])) {
            return $lowercase_string;
        }
        $bits = explode(" ", $string);
        $stage = 0;
        $caught = [];
        $stage_1 = ["font-style", "font-variant", "font-weight"];
        $final = "";
        $i = 0;
        for ($size = count($bits); $i < $size; $i++) {
            if ($bits[$i] !== "") {
                switch ($stage) {
                    case 0:
                        foreach ($stage_1 as $validator_name) {
                            if (!isset($caught[$validator_name])) {
                                $r = $this->info[$validator_name]->validate($bits[$i], $config, $context);
                                if ($r !== false) {
                                    $final .= $r . " ";
                                    $caught[$validator_name] = true;
                                    if (3 <= count($caught)) {
                                        $stage = 1;
                                    }
                                    if ($r === false) {
                                    }
                                }
                            }
                        }
                        break;
                    case 1:
                        $found_slash = false;
                        if (strpos($bits[$i], "/") !== false) {
                            list($font_size, $line_height) = explode("/", $bits[$i]);
                            if ($line_height === "") {
                                $line_height = false;
                                $found_slash = true;
                            }
                        } else {
                            $font_size = $bits[$i];
                            $line_height = false;
                        }
                        $r = $this->info["font-size"]->validate($font_size, $config, $context);
                        if ($r !== false) {
                            $final .= $r;
                            if ($line_height === false) {
                                for ($j = $i + 1; $j < $size; $j++) {
                                    if ($bits[$j] !== "") {
                                        if ($bits[$j] === "/") {
                                            if ($found_slash) {
                                                return false;
                                            }
                                            $found_slash = true;
                                        } else {
                                            $line_height = $bits[$j];
                                        }
                                    }
                                }
                            } else {
                                $found_slash = true;
                                $j = $i;
                            }
                            if ($found_slash) {
                                $i = $j;
                                $r = $this->info["line-height"]->validate($line_height, $config, $context);
                                if ($r !== false) {
                                    $final .= "/" . $r;
                                }
                            }
                            $final .= " ";
                            $stage = 2;
                        } else {
                            return false;
                        }
                        break;
                    case 2:
                        $font_family = implode(" ", array_slice($bits, $i, $size - $i));
                        $r = $this->info["font-family"]->validate($font_family, $config, $context);
                        if ($r !== false) {
                            $final .= $r . " ";
                            return rtrim($final);
                        }
                        return false;
                        break;
                }
            }
        }
        return false;
    }
}
class HTMLPurifier_AttrDef_CSS_FontFamily extends HTMLPurifier_AttrDef
{
    protected $mask = NULL;
    public function __construct()
    {
        $this->mask = "_- ";
        for ($c = "a"; $c <= "z"; $c++) {
            $this->mask .= $c;
        }
        for ($c = "A"; $c <= "Z"; $c++) {
            $this->mask .= $c;
        }
        for ($c = "0"; $c <= "9"; $c++) {
            $this->mask .= $c;
        }
        for ($i = 128; $i <= 255; $i++) {
            $this->mask .= chr($i);
        }
    }
    public function validate($string, $config, $context)
    {
        $allowed_fonts = $config->get("CSS.AllowedFonts");
        $fonts = explode(",", $string);
        $final = "";
        foreach ($fonts as $font) {
            $font = trim($font);
            if ($font !== "") {
                if (isset($generic_names[$font])) {
                    if ($allowed_fonts === NULL || isset($allowed_fonts[$font])) {
                        $final .= $font . ", ";
                    }
                } else {
                    if ($font[0] === "\"" || $font[0] === "'") {
                        $length = strlen($font);
                        if ($length > 2) {
                            $quote = $font[0];
                            if ($font[$length - 1] === $quote) {
                                $font = substr($font, 1, $length - 2);
                            }
                        }
                    }
                    $font = $this->expandCSSEscape($font);
                    if ($allowed_fonts === NULL || isset($allowed_fonts[$font])) {
                        if (ctype_alnum($font) && $font !== "") {
                            $final .= $font . ", ";
                        } else {
                            $font = str_replace(["\n", "\t", "\r", "\f"], " ", $font);
                            if (strspn($font, $this->mask) === strlen($font)) {
                                $final .= "'" . $font . "', ";
                            }
                        }
                    }
                }
            }
        }
        $final = rtrim($final, ", ");
        if ($final === "") {
            return false;
        }
        return $final;
    }
}
class HTMLPurifier_AttrDef_CSS_Ident extends HTMLPurifier_AttrDef
{
    public function validate($string, $config, $context)
    {
        $string = trim($string);
        if (!$string) {
            return false;
        }
        $pattern = "/^(-?[A-Za-z_][A-Za-z_\\-0-9]*)\$/";
        if (!preg_match($pattern, $string)) {
            return false;
        }
        return $string;
    }
}
class HTMLPurifier_AttrDef_CSS_ImportantDecorator extends HTMLPurifier_AttrDef
{
    public $def = NULL;
    public $allow = NULL;
    public function __construct($def, $allow = false)
    {
        $this->def = $def;
        $this->allow = $allow;
    }
    public function validate($string, $config, $context)
    {
        $string = trim($string);
        $is_important = false;
        if (9 <= strlen($string) && substr($string, -9) === "important") {
            $temp = rtrim(substr($string, 0, -9));
            if (1 <= strlen($temp) && substr($temp, -1) === "!") {
                $string = rtrim(substr($temp, 0, -1));
                $is_important = true;
            }
        }
        $string = $this->def->validate($string, $config, $context);
        if ($this->allow && $is_important) {
            $string .= " !important";
        }
        return $string;
    }
}
class HTMLPurifier_AttrDef_CSS_Length extends HTMLPurifier_AttrDef
{
    protected $min = NULL;
    protected $max = NULL;
    public function __construct($min = NULL, $max = NULL)
    {
        $this->min = $min !== NULL ? HTMLPurifier_Length::make($min) : NULL;
        $this->max = $max !== NULL ? HTMLPurifier_Length::make($max) : NULL;
    }
    public function validate($string, $config, $context)
    {
        $string = $this->parseCDATA($string);
        if ($string === "") {
            return false;
        }
        if ($string === "0") {
            return "0";
        }
        if (strlen($string) === 1) {
            return false;
        }
        $length = HTMLPurifier_Length::make($string);
        if (!$length->isValid()) {
            return false;
        }
        if ($this->min) {
            $c = $length->compareTo($this->min);
            if ($c === false) {
                return false;
            }
            if ($c < 0) {
                return false;
            }
        }
        if ($this->max) {
            $c = $length->compareTo($this->max);
            if ($c === false) {
                return false;
            }
            if (0 < $c) {
                return false;
            }
        }
        return $length->toString();
    }
}
class HTMLPurifier_AttrDef_CSS_ListStyle extends HTMLPurifier_AttrDef
{
    protected $info = NULL;
    public function __construct($config)
    {
        $def = $config->getCSSDefinition();
        $this->info["list-style-type"] = $def->info["list-style-type"];
        $this->info["list-style-position"] = $def->info["list-style-position"];
        $this->info["list-style-image"] = $def->info["list-style-image"];
    }
    public function validate($string, $config, $context)
    {
        $string = $this->parseCDATA($string);
        if ($string === "") {
            return false;
        }
        $bits = explode(" ", strtolower($string));
        $caught = [];
        $caught["type"] = false;
        $caught["position"] = false;
        $caught["image"] = false;
        $i = 0;
        $none = false;
        foreach ($bits as $bit) {
            if (3 <= $i) {
                return NULL;
            }
            if ($bit !== "") {
                foreach ($caught as $key => $status) {
                    if ($status === false) {
                        $r = $this->info["list-style-" . $key]->validate($bit, $config, $context);
                        if ($r !== false) {
                            if ($r === "none") {
                                if (!$none) {
                                    $none = true;
                                    if ($key != "image") {
                                    }
                                }
                            }
                            $caught[$key] = $r;
                            $i++;
                        }
                    }
                }
            }
        }
        if (!$i) {
            return false;
        }
        $ret = [];
        if ($caught["type"]) {
            $ret[] = $caught["type"];
        }
        if ($caught["image"]) {
            $ret[] = $caught["image"];
        }
        if ($caught["position"]) {
            $ret[] = $caught["position"];
        }
        if (empty($ret)) {
            return false;
        }
        return implode(" ", $ret);
    }
}
class HTMLPurifier_AttrDef_CSS_Multiple extends HTMLPurifier_AttrDef
{
    public $single = NULL;
    public $max = NULL;
    public function __construct($single, $max = 4)
    {
        $this->single = $single;
        $this->max = $max;
    }
    public function validate($string, $config, $context)
    {
        $string = $this->mungeRgb($this->parseCDATA($string));
        if ($string === "") {
            return false;
        }
        $parts = explode(" ", $string);
        $length = count($parts);
        $final = "";
        $i = 0;
        for ($num = 0; $i < $length && $num < $this->max; $i++) {
            if (!ctype_space($parts[$i])) {
                $result = $this->single->validate($parts[$i], $config, $context);
                if ($result !== false) {
                    $final .= $result . " ";
                    $num++;
                }
            }
        }
        if ($final === "") {
            return false;
        }
        return rtrim($final);
    }
}
class HTMLPurifier_AttrDef_CSS_Percentage extends HTMLPurifier_AttrDef
{
    protected $number_def = NULL;
    public function __construct($non_negative = false)
    {
        $this->number_def = new HTMLPurifier_AttrDef_CSS_Number($non_negative);
    }
    public function validate($string, $config, $context)
    {
        $string = $this->parseCDATA($string);
        if ($string === "") {
            return false;
        }
        $length = strlen($string);
        if ($length === 1) {
            return false;
        }
        if ($string[$length - 1] !== "%") {
            return false;
        }
        $number = substr($string, 0, $length - 1);
        $number = $this->number_def->validate($number, $config, $context);
        if ($number === false) {
            return false;
        }
        return $number . "%";
    }
}
class HTMLPurifier_AttrDef_CSS_TextDecoration extends HTMLPurifier_AttrDef
{
    public function validate($string, $config, $context)
    {
        $string = strtolower($this->parseCDATA($string));
        if ($string === "none") {
            return $string;
        }
        $parts = explode(" ", $string);
        $final = "";
        foreach ($parts as $part) {
            if (isset($allowed_values[$part])) {
                $final .= $part . " ";
            }
        }
        $final = rtrim($final);
        if ($final === "") {
            return false;
        }
        return $final;
    }
}
class HTMLPurifier_AttrDef_CSS_URI extends HTMLPurifier_AttrDef_URI
{
    public function __construct()
    {
        parent::__construct(true);
    }
    public function validate($uri_string, $config, $context)
    {
        $uri_string = $this->parseCDATA($uri_string);
        if (strpos($uri_string, "url(") !== 0) {
            return false;
        }
        $uri_string = substr($uri_string, 4);
        if (strlen($uri_string) == 0) {
            return false;
        }
        $new_length = strlen($uri_string) - 1;
        if ($uri_string[$new_length] != ")") {
            return false;
        }
        $uri = trim(substr($uri_string, 0, $new_length));
        if (!empty($uri) && ($uri[0] == "'" || $uri[0] == "\"")) {
            $quote = $uri[0];
            $new_length = strlen($uri) - 1;
            if ($uri[$new_length] !== $quote) {
                return false;
            }
            $uri = substr($uri, 1, $new_length - 1);
        }
        $uri = $this->expandCSSEscape($uri);
        $result = parent::validate($uri, $config, $context);
        if ($result === false) {
            return false;
        }
        $result = str_replace(["\"", "\\", "\n", "\f", "\r"], "", $result);
        $result = str_replace(["(", ")", "'"], ["%28", "%29", "%27"], $result);
        return "url(\"" . $result . "\")";
    }
}
class HTMLPurifier_AttrDef_HTML_Bool extends HTMLPurifier_AttrDef
{
    protected $name = NULL;
    public $minimized = true;
    public function __construct($name = false)
    {
        $this->name = $name;
    }
    public function validate($string, $config, $context)
    {
        return $this->name;
    }
    public function make($string)
    {
        return new HTMLPurifier_AttrDef_HTML_Bool($string);
    }
}
class HTMLPurifier_AttrDef_HTML_Nmtokens extends HTMLPurifier_AttrDef
{
    public function validate($string, $config, $context)
    {
        $string = trim($string);
        if (!$string) {
            return false;
        }
        $tokens = $this->split($string, $config, $context);
        $tokens = $this->filter($tokens, $config, $context);
        if (empty($tokens)) {
            return false;
        }
        return implode(" ", $tokens);
    }
    protected function split($string, $config, $context)
    {
        $pattern = "/(?:(?<=\\s)|\\A)((?:--|-?[A-Za-z_])[A-Za-z_\\-0-9]*)(?:(?=\\s)|\\z)/";
        preg_match_all($pattern, $string, $matches);
        return $matches[1];
    }
    protected function filter($tokens, $config, $context)
    {
        return $tokens;
    }
}
class HTMLPurifier_AttrDef_HTML_Class extends HTMLPurifier_AttrDef_HTML_Nmtokens
{
    protected function split($string, $config, $context)
    {
        $name = $config->getDefinition("HTML")->doctype->name;
        if ($name == "XHTML 1.1" || $name == "XHTML 2.0") {
            return parent::split($string, $config, $context);
        }
        return preg_split("/\\s+/", $string);
    }
    protected function filter($tokens, $config, $context)
    {
        $allowed = $config->get("Attr.AllowedClasses");
        $forbidden = $config->get("Attr.ForbiddenClasses");
        $ret = [];
        foreach ($tokens as $token) {
            if (($allowed === NULL || isset($allowed[$token])) && !isset($forbidden[$token]) && !in_array($token, $ret, true)) {
                $ret[] = $token;
            }
        }
        return $ret;
    }
}
class HTMLPurifier_AttrDef_HTML_Color extends HTMLPurifier_AttrDef
{
    public function validate($string, $config, $context)
    {
        if ($colors === NULL) {
            $colors = $config->get("Core.ColorKeywords");
        }
        $string = trim($string);
        if (empty($string)) {
            return false;
        }
        $lower = strtolower($string);
        if (isset($colors[$lower])) {
            return $colors[$lower];
        }
        if ($string[0] === "#") {
            $hex = substr($string, 1);
        } else {
            $hex = $string;
        }
        $length = strlen($hex);
        if ($length !== 3 && $length !== 6) {
            return false;
        }
        if (!ctype_xdigit($hex)) {
            return false;
        }
        if ($length === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        return "#" . $hex;
    }
}
class HTMLPurifier_AttrDef_HTML_FrameTarget extends HTMLPurifier_AttrDef_Enum
{
    public $valid_values = false;
    protected $case_sensitive = false;
    public function __construct()
    {
    }
    public function validate($string, $config, $context)
    {
        if ($this->valid_values === false) {
            $this->valid_values = $config->get("Attr.AllowedFrameTargets");
        }
        return parent::validate($string, $config, $context);
    }
}
class HTMLPurifier_AttrDef_HTML_ID extends HTMLPurifier_AttrDef
{
    protected $selector = NULL;
    public function __construct($selector = false)
    {
        $this->selector = $selector;
    }
    public function validate($id, $config, $context)
    {
        if (!$this->selector && !$config->get("Attr.EnableID")) {
            return false;
        }
        $id = trim($id);
        if ($id === "") {
            return false;
        }
        $prefix = $config->get("Attr.IDPrefix");
        if ($prefix !== "") {
            $prefix .= $config->get("Attr.IDPrefixLocal");
            if (strpos($id, $prefix) !== 0) {
                $id = $prefix . $id;
            }
        } else {
            if ($config->get("Attr.IDPrefixLocal") !== "") {
                trigger_error("%Attr.IDPrefixLocal cannot be used unless %Attr.IDPrefix is set", 512);
            }
        }
        if (!$this->selector) {
            $id_accumulator =& $context->get("IDAccumulator");
            if (isset($id_accumulator->ids[$id])) {
                return false;
            }
        }
        if ($config->get("Attr.ID.HTML5") === true) {
            if (preg_match("/[\\t\\n\\x0b\\x0c ]/", $id)) {
                return false;
            }
        } else {
            if (!ctype_alpha($id)) {
                if (!ctype_alpha($id[0])) {
                    return false;
                }
                $trim = trim($id, "A..Za..z0..9:-._");
                if ($trim !== "") {
                    return false;
                }
            }
        }
        $regexp = $config->get("Attr.IDBlacklistRegexp");
        if ($regexp && preg_match($regexp, $id)) {
            return false;
        }
        if (!$this->selector) {
            $id_accumulator->add($id);
        }
        return $id;
    }
}
class HTMLPurifier_AttrDef_HTML_Pixels extends HTMLPurifier_AttrDef
{
    protected $max = NULL;
    public function __construct($max = NULL)
    {
        $this->max = $max;
    }
    public function validate($string, $config, $context)
    {
        $string = trim($string);
        if ($string === "0") {
            return $string;
        }
        if ($string === "") {
            return false;
        }
        $length = strlen($string);
        if (substr($string, $length - 2) == "px") {
            $string = substr($string, 0, $length - 2);
        }
        if (!is_numeric($string)) {
            return false;
        }
        $int = (int) $string;
        if ($int < 0) {
            return "0";
        }
        if ($this->max !== NULL && $this->max < $int) {
            return (string) $this->max;
        }
        return (string) $int;
    }
    public function make($string)
    {
        if ($string === "") {
            $max = NULL;
        } else {
            $max = (int) $string;
        }
        $class = get_class($this);
        return new $class($max);
    }
}
class HTMLPurifier_AttrDef_HTML_Length extends HTMLPurifier_AttrDef_HTML_Pixels
{
    public function validate($string, $config, $context)
    {
        $string = trim($string);
        if ($string === "") {
            return false;
        }
        $parent_result = parent::validate($string, $config, $context);
        if ($parent_result !== false) {
            return $parent_result;
        }
        $length = strlen($string);
        $last_char = $string[$length - 1];
        if ($last_char !== "%") {
            return false;
        }
        $points = substr($string, 0, $length - 1);
        if (!is_numeric($points)) {
            return false;
        }
        $points = (int) $points;
        if ($points < 0) {
            return "0%";
        }
        if (100 < $points) {
            return "100%";
        }
        return (string) $points . "%";
    }
}
class HTMLPurifier_AttrDef_HTML_LinkTypes extends HTMLPurifier_AttrDef
{
    protected $name = NULL;
    public function __construct($name)
    {
        $configLookup = ["rel" => "AllowedRel", "rev" => "AllowedRev"];
        if (!isset($configLookup[$name])) {
            trigger_error("Unrecognized attribute name for link relationship.", 256);
        } else {
            $this->name = $configLookup[$name];
        }
    }
    public function validate($string, $config, $context)
    {
        $allowed = $config->get("Attr." . $this->name);
        if (empty($allowed)) {
            return false;
        }
        $string = $this->parseCDATA($string);
        $parts = explode(" ", $string);
        $ret_lookup = [];
        foreach ($parts as $part) {
            $part = strtolower(trim($part));
            if (isset($allowed[$part])) {
                $ret_lookup[$part] = true;
            }
        }
        if (empty($ret_lookup)) {
            return false;
        }
        $string = implode(" ", array_keys($ret_lookup));
        return $string;
    }
}
class HTMLPurifier_AttrDef_HTML_MultiLength extends HTMLPurifier_AttrDef_HTML_Length
{
    public function validate($string, $config, $context)
    {
        $string = trim($string);
        if ($string === "") {
            return false;
        }
        $parent_result = parent::validate($string, $config, $context);
        if ($parent_result !== false) {
            return $parent_result;
        }
        $length = strlen($string);
        $last_char = $string[$length - 1];
        if ($last_char !== "*") {
            return false;
        }
        $int = substr($string, 0, $length - 1);
        if ($int == "") {
            return "*";
        }
        if (!is_numeric($int)) {
            return false;
        }
        $int = (int) $int;
        if ($int < 0) {
            return false;
        }
        if ($int == 0) {
            return "0";
        }
        if ($int == 1) {
            return "*";
        }
        return (string) $int . "*";
    }
}
abstract class HTMLPurifier_AttrDef_URI_Email extends HTMLPurifier_AttrDef
{
    public function unpack($string)
    {
    }
}
class HTMLPurifier_AttrDef_URI_Host extends HTMLPurifier_AttrDef
{
    protected $ipv4 = NULL;
    protected $ipv6 = NULL;
    public function __construct()
    {
        $this->ipv4 = new HTMLPurifier_AttrDef_URI_IPv4();
        $this->ipv6 = new HTMLPurifier_AttrDef_URI_IPv6();
    }
    public function validate($string, $config, $context)
    {
        $length = strlen($string);
        if ($string === "") {
            return "";
        }
        if (1 < $length && $string[0] === "[" && $string[$length - 1] === "]") {
            $ip = substr($string, 1, $length - 2);
            $valid = $this->ipv6->validate($ip, $config, $context);
            if ($valid === false) {
                return false;
            }
            return "[" . $valid . "]";
        }
        $ipv4 = $this->ipv4->validate($string, $config, $context);
        if ($ipv4 !== false) {
            return $ipv4;
        }
        $underscore = $config->get("Core.AllowHostnameUnderscore") ? "_" : "";
        $a = "[a-z]";
        $an = "[a-z0-9]";
        $and = "[a-z0-9-" . $underscore . "]";
        $domainlabel = $an . "(?:" . $and . "*" . $an . ")?";
        $toplabel = $an . "(?:" . $and . "*" . $an . ")?";
        if (preg_match("/^(?:" . $domainlabel . "\\.)*(" . $toplabel . ")\\.?\$/i", $string, $matches) && !ctype_digit($matches[1])) {
            return $string;
        }
        if (function_exists("idn_to_ascii")) {
            if (defined("IDNA_NONTRANSITIONAL_TO_ASCII") && defined("INTL_IDNA_VARIANT_UTS46")) {
                $string = idn_to_ascii($string, IDNA_NONTRANSITIONAL_TO_ASCII, INTL_IDNA_VARIANT_UTS46);
            } else {
                $string = idn_to_ascii($string);
            }
        } else {
            if ($config->get("Core.EnableIDNA")) {
                $idna = new Net_IDNA2(["encoding" => "utf8", "overlong" => false, "strict" => true]);
                $parts = explode(".", $string);
                try {
                    $new_parts = [];
                    foreach ($parts as $part) {
                        $encodable = false;
                        $i = 0;
                        $c = strlen($part);
                        while ($i < $c) {
                            if (122 < ord($part[$i])) {
                                $encodable = true;
                            } else {
                                $i++;
                            }
                        }
                        if (!$encodable) {
                            $new_parts[] = $part;
                        } else {
                            $new_parts[] = $idna->encode($part);
                        }
                    }
                    $string = implode(".", $new_parts);
                } catch (Exception $e) {
                }
            }
        }
        if (preg_match("/^(" . $domainlabel . "\\.)*" . $toplabel . "\\.?\$/i", $string)) {
            return $string;
        }
        return false;
    }
}
class HTMLPurifier_AttrDef_URI_IPv4 extends HTMLPurifier_AttrDef
{
    protected $ip4 = NULL;
    public function validate($aIP, $config, $context)
    {
        if (!$this->ip4) {
            $this->_loadRegex();
        }
        if (preg_match("#^" . $this->ip4 . "\$#s", $aIP)) {
            return $aIP;
        }
        return false;
    }
    protected function _loadRegex()
    {
        $oct = "(?:25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9][0-9]|[0-9])";
        $this->ip4 = "(?:" . $oct . "\\." . $oct . "\\." . $oct . "\\." . $oct . ")";
    }
}
class HTMLPurifier_AttrDef_URI_IPv6 extends HTMLPurifier_AttrDef_URI_IPv4
{
    public function validate($aIP, $config, $context)
    {
        if (!$this->ip4) {
            $this->_loadRegex();
        }
        $original = $aIP;
        $hex = "[0-9a-fA-F]";
        $blk = "(?:" . $hex . "{1,4})";
        $pre = "(?:/(?:12[0-8]|1[0-1][0-9]|[1-9][0-9]|[0-9]))";
        if (strpos($aIP, "/") !== false) {
            if (preg_match("#" . $pre . "\$#s", $aIP, $find)) {
                $aIP = substr($aIP, 0, 0 - strlen($find[0]));
                unset($find);
            } else {
                return false;
            }
        }
        if (preg_match("#(?<=:)" . $this->ip4 . "\$#s", $aIP, $find)) {
            $aIP = substr($aIP, 0, 0 - strlen($find[0]));
            $ip = explode(".", $find[0]);
            $ip = array_map("dechex", $ip);
            $aIP .= $ip[0] . $ip[1] . ":" . $ip[2] . $ip[3];
            unset($find);
            unset($ip);
        }
        $aIP = explode("::", $aIP);
        $c = count($aIP);
        if (2 < $c) {
            return false;
        }
        if ($c == 2) {
            list($first, $second) = $aIP;
            $first = explode(":", $first);
            $second = explode(":", $second);
            if (8 < count($first) + count($second)) {
                return false;
            }
            while (count($first) < 8) {
                array_push($first, "0");
            }
            array_splice($first, 8 - count($second), 8, $second);
            $aIP = $first;
            unset($first);
            unset($second);
        } else {
            $aIP = explode(":", $aIP[0]);
        }
        $c = count($aIP);
        if ($c != 8) {
            return false;
        }
        foreach ($aIP as $piece) {
            if (!preg_match("#^[0-9a-fA-F]{4}\$#s", sprintf("%04s", $piece))) {
                return false;
            }
        }
        return $original;
    }
}
class HTMLPurifier_AttrDef_URI_Email_SimpleCheck extends HTMLPurifier_AttrDef_URI_Email
{
    public function validate($string, $config, $context)
    {
        if ($string == "") {
            return false;
        }
        $string = trim($string);
        $result = preg_match("/^[A-Z0-9._%-]+@[A-Z0-9.-]+\\.[A-Z]{2,4}\$/i", $string);
        return $result ? $string : false;
    }
}
class HTMLPurifier_AttrTransform_Background extends HTMLPurifier_AttrTransform
{
    public function transform($attr, $config, $context)
    {
        if (!isset($attr["background"])) {
            return $attr;
        }
        $background = $this->confiscateAttr($attr, "background");
        $this->prependCSS($attr, "background-image:url(" . $background . ");");
        return $attr;
    }
}
class HTMLPurifier_AttrTransform_BdoDir extends HTMLPurifier_AttrTransform
{
    public function transform($attr, $config, $context)
    {
        if (isset($attr["dir"])) {
            return $attr;
        }
        $attr["dir"] = $config->get("Attr.DefaultTextDir");
        return $attr;
    }
}
class HTMLPurifier_AttrTransform_BgColor extends HTMLPurifier_AttrTransform
{
    public function transform($attr, $config, $context)
    {
        if (!isset($attr["bgcolor"])) {
            return $attr;
        }
        $bgcolor = $this->confiscateAttr($attr, "bgcolor");
        $this->prependCSS($attr, "background-color:" . $bgcolor . ";");
        return $attr;
    }
}
class HTMLPurifier_AttrTransform_BoolToCSS extends HTMLPurifier_AttrTransform
{
    protected $attr = NULL;
    protected $css = NULL;
    public function __construct($attr, $css)
    {
        $this->attr = $attr;
        $this->css = $css;
    }
    public function transform($attr, $config, $context)
    {
        if (!isset($attr[$this->attr])) {
            return $attr;
        }
        unset($attr[$this->attr]);
        $this->prependCSS($attr, $this->css);
        return $attr;
    }
}
class HTMLPurifier_AttrTransform_Border extends HTMLPurifier_AttrTransform
{
    public function transform($attr, $config, $context)
    {
        if (!isset($attr["border"])) {
            return $attr;
        }
        $border_width = $this->confiscateAttr($attr, "border");
        $this->prependCSS($attr, "border:" . $border_width . "px solid;");
        return $attr;
    }
}
class HTMLPurifier_AttrTransform_EnumToCSS extends HTMLPurifier_AttrTransform
{
    protected $attr = NULL;
    protected $enumToCSS = [];
    protected $caseSensitive = false;
    public function __construct($attr, $enum_to_css, $case_sensitive = false)
    {
        $this->attr = $attr;
        $this->enumToCSS = $enum_to_css;
        $this->caseSensitive = (bool) $case_sensitive;
    }
    public function transform($attr, $config, $context)
    {
        if (!isset($attr[$this->attr])) {
            return $attr;
        }
        $value = trim($attr[$this->attr]);
        unset($attr[$this->attr]);
        if (!$this->caseSensitive) {
            $value = strtolower($value);
        }
        if (!isset($this->enumToCSS[$value])) {
            return $attr;
        }
        $this->prependCSS($attr, $this->enumToCSS[$value]);
        return $attr;
    }
}
class HTMLPurifier_AttrTransform_ImgRequired extends HTMLPurifier_AttrTransform
{
    public function transform($attr, $config, $context)
    {
        $src = true;
        if (!isset($attr["src"])) {
            if ($config->get("Core.RemoveInvalidImg")) {
                return $attr;
            }
            $attr["src"] = $config->get("Attr.DefaultInvalidImage");
            $src = false;
        }
        if (!isset($attr["alt"])) {
            if ($src) {
                $alt = $config->get("Attr.DefaultImageAlt");
                if ($alt === NULL) {
                    $attr["alt"] = basename($attr["src"]);
                } else {
                    $attr["alt"] = $alt;
                }
            } else {
                $attr["alt"] = $config->get("Attr.DefaultInvalidImageAlt");
            }
        }
        return $attr;
    }
}
class HTMLPurifier_AttrTransform_ImgSpace extends HTMLPurifier_AttrTransform
{
    protected $attr = NULL;
    protected $css = ["hspace" => ["left", "right"], "vspace" => ["top", "bottom"]];
    public function __construct($attr)
    {
        $this->attr = $attr;
        if (!isset($this->css[$attr])) {
            trigger_error(htmlspecialchars($attr) . " is not valid space attribute");
        }
    }
    public function transform($attr, $config, $context)
    {
        if (!isset($attr[$this->attr])) {
            return $attr;
        }
        $width = $this->confiscateAttr($attr, $this->attr);
        if (!isset($this->css[$this->attr])) {
            return $attr;
        }
        $style = "";
        foreach ($this->css[$this->attr] as $suffix) {
            $property = "margin-" . $suffix;
            $style .= $property . ":" . $width . "px;";
        }
        $this->prependCSS($attr, $style);
        return $attr;
    }
}
class HTMLPurifier_AttrTransform_Input extends HTMLPurifier_AttrTransform
{
    protected $pixels = NULL;
    public function __construct()
    {
        $this->pixels = new HTMLPurifier_AttrDef_HTML_Pixels();
    }
    public function transform($attr, $config, $context)
    {
        if (!isset($attr["type"])) {
            $t = "text";
        } else {
            $t = strtolower($attr["type"]);
        }
        if (isset($attr["checked"]) && $t !== "radio" && $t !== "checkbox") {
            unset($attr["checked"]);
        }
        if (isset($attr["maxlength"]) && $t !== "text" && $t !== "password") {
            unset($attr["maxlength"]);
        }
        if (isset($attr["size"]) && $t !== "text" && $t !== "password") {
            $result = $this->pixels->validate($attr["size"], $config, $context);
            if ($result === false) {
                unset($attr["size"]);
            } else {
                $attr["size"] = $result;
            }
        }
        if (isset($attr["src"]) && $t !== "image") {
            unset($attr["src"]);
        }
        if (!isset($attr["value"]) && ($t === "radio" || $t === "checkbox")) {
            $attr["value"] = "";
        }
        return $attr;
    }
}
class HTMLPurifier_AttrTransform_Lang extends HTMLPurifier_AttrTransform
{
    public function transform($attr, $config, $context)
    {
        $lang = isset($attr["lang"]) ? $attr["lang"] : false;
        $xml_lang = isset($attr["xml:lang"]) ? $attr["xml:lang"] : false;
        if ($lang !== false && $xml_lang === false) {
            $attr["xml:lang"] = $lang;
        } else {
            if ($xml_lang !== false) {
                $attr["lang"] = $xml_lang;
            }
        }
        return $attr;
    }
}
class HTMLPurifier_AttrTransform_Length extends HTMLPurifier_AttrTransform
{
    protected $name = NULL;
    protected $cssName = NULL;
    public function __construct($name, $css_name = NULL)
    {
        $this->name = $name;
        $this->cssName = $css_name ? $css_name : $name;
    }
    public function transform($attr, $config, $context)
    {
        if (!isset($attr[$this->name])) {
            return $attr;
        }
        $length = $this->confiscateAttr($attr, $this->name);
        if (ctype_digit($length)) {
            $length .= "px";
        }
        $this->prependCSS($attr, $this->cssName . ":" . $length . ";");
        return $attr;
    }
}
class HTMLPurifier_AttrTransform_Name extends HTMLPurifier_AttrTransform
{
    public function transform($attr, $config, $context)
    {
        if ($config->get("HTML.Attr.Name.UseCDATA")) {
            return $attr;
        }
        if (!isset($attr["name"])) {
            return $attr;
        }
        $id = $this->confiscateAttr($attr, "name");
        if (isset($attr["id"])) {
            return $attr;
        }
        $attr["id"] = $id;
        return $attr;
    }
}
class HTMLPurifier_AttrTransform_NameSync extends HTMLPurifier_AttrTransform
{
    public function __construct()
    {
        $this->idDef = new HTMLPurifier_AttrDef_HTML_ID();
    }
    public function transform($attr, $config, $context)
    {
        if (!isset($attr["name"])) {
            return $attr;
        }
        $name = $attr["name"];
        if (isset($attr["id"]) && $attr["id"] === $name) {
            return $attr;
        }
        $result = $this->idDef->validate($name, $config, $context);
        if ($result === false) {
            unset($attr["name"]);
        } else {
            $attr["name"] = $result;
        }
        return $attr;
    }
}
class HTMLPurifier_AttrTransform_Nofollow extends HTMLPurifier_AttrTransform
{
    private $parser = NULL;
    public function __construct()
    {
        $this->parser = new HTMLPurifier_URIParser();
    }
    public function transform($attr, $config, $context)
    {
        if (!isset($attr["href"])) {
            return $attr;
        }
        $url = $this->parser->parse($attr["href"]);
        $scheme = $url->getSchemeObj($config, $context);
        if ($scheme->browsable && !$url->isLocal($config, $context)) {
            if (isset($attr["rel"])) {
                $rels = explode(" ", $attr["rel"]);
                if (!in_array("nofollow", $rels)) {
                    $rels[] = "nofollow";
                }
                $attr["rel"] = implode(" ", $rels);
            } else {
                $attr["rel"] = "nofollow";
            }
        }
        return $attr;
    }
}
class HTMLPurifier_AttrTransform_SafeEmbed extends HTMLPurifier_AttrTransform
{
    public $name = "SafeEmbed";
    public function transform($attr, $config, $context)
    {
        $attr["allowscriptaccess"] = "never";
        $attr["allownetworking"] = "internal";
        $attr["type"] = "application/x-shockwave-flash";
        return $attr;
    }
}
class HTMLPurifier_AttrTransform_SafeObject extends HTMLPurifier_AttrTransform
{
    public $name = "SafeObject";
    public function transform($attr, $config, $context)
    {
        if (!isset($attr["type"])) {
            $attr["type"] = "application/x-shockwave-flash";
        }
        return $attr;
    }
}
class HTMLPurifier_AttrTransform_SafeParam extends HTMLPurifier_AttrTransform
{
    public $name = "SafeParam";
    private $uri = NULL;
    public function __construct()
    {
        $this->uri = new HTMLPurifier_AttrDef_URI(true);
        $this->wmode = new HTMLPurifier_AttrDef_Enum(["window", "opaque", "transparent"]);
    }
    public function transform($attr, $config, $context)
    {
        switch ($attr["name"]) {
            case "allowScriptAccess":
                $attr["value"] = "never";
                break;
            case "allowNetworking":
                $attr["value"] = "internal";
                break;
            case "allowFullScreen":
                if ($config->get("HTML.FlashAllowFullScreen")) {
                    $attr["value"] = $attr["value"] == "true" ? "true" : "false";
                } else {
                    $attr["value"] = "false";
                }
                break;
            case "wmode":
                $attr["value"] = $this->wmode->validate($attr["value"], $config, $context);
                break;
            case "movie":
            case "src":
                $attr["name"] = "movie";
                $attr["value"] = $this->uri->validate($attr["value"], $config, $context);
                break;
            case "flashvars":
            default:
                $attr["value"] = NULL;
                $attr["name"] = $attr["value"];
                return $attr;
        }
    }
}
class HTMLPurifier_AttrTransform_ScriptRequired extends HTMLPurifier_AttrTransform
{
    public function transform($attr, $config, $context)
    {
        if (!isset($attr["type"])) {
            $attr["type"] = "text/javascript";
        }
        return $attr;
    }
}
class HTMLPurifier_AttrTransform_TargetBlank extends HTMLPurifier_AttrTransform
{
    private $parser = NULL;
    public function __construct()
    {
        $this->parser = new HTMLPurifier_URIParser();
    }
    public function transform($attr, $config, $context)
    {
        if (!isset($attr["href"])) {
            return $attr;
        }
        $url = $this->parser->parse($attr["href"]);
        $scheme = $url->getSchemeObj($config, $context);
        if ($scheme->browsable && !$url->isBenign($config, $context)) {
            $attr["target"] = "_blank";
        }
        return $attr;
    }
}
class HTMLPurifier_AttrTransform_TargetNoopener extends HTMLPurifier_AttrTransform
{
    public function transform($attr, $config, $context)
    {
        if (isset($attr["rel"])) {
            $rels = explode(" ", $attr["rel"]);
        } else {
            $rels = [];
        }
        if (isset($attr["target"]) && !in_array("noopener", $rels)) {
            $rels[] = "noopener";
        }
        if (!empty($rels) || isset($attr["rel"])) {
            $attr["rel"] = implode(" ", $rels);
        }
        return $attr;
    }
}
class HTMLPurifier_AttrTransform_TargetNoreferrer extends HTMLPurifier_AttrTransform
{
    public function transform($attr, $config, $context)
    {
        if (isset($attr["rel"])) {
            $rels = explode(" ", $attr["rel"]);
        } else {
            $rels = [];
        }
        if (isset($attr["target"]) && !in_array("noreferrer", $rels)) {
            $rels[] = "noreferrer";
        }
        if (!empty($rels) || isset($attr["rel"])) {
            $attr["rel"] = implode(" ", $rels);
        }
        return $attr;
    }
}
class HTMLPurifier_AttrTransform_Textarea extends HTMLPurifier_AttrTransform
{
    public function transform($attr, $config, $context)
    {
        if (!isset($attr["cols"])) {
            $attr["cols"] = "22";
        }
        if (!isset($attr["rows"])) {
            $attr["rows"] = "3";
        }
        return $attr;
    }
}
class HTMLPurifier_ChildDef_Chameleon extends HTMLPurifier_ChildDef
{
    public $inline = NULL;
    public $block = NULL;
    public $type = "chameleon";
    public function __construct($inline, $block)
    {
        $this->inline = new HTMLPurifier_ChildDef_Optional($inline);
        $this->block = new HTMLPurifier_ChildDef_Optional($block);
        $this->elements = $this->block->elements;
    }
    public function validateChildren($children, $config, $context)
    {
        if ($context->get("IsInline") === false) {
            return $this->block->validateChildren($children, $config, $context);
        }
        return $this->inline->validateChildren($children, $config, $context);
    }
}
class HTMLPurifier_ChildDef_Custom extends HTMLPurifier_ChildDef
{
    public $type = "custom";
    public $allow_empty = false;
    public $dtd_regex = NULL;
    private $_pcre_regex = NULL;
    public function __construct($dtd_regex)
    {
        $this->dtd_regex = $dtd_regex;
        $this->_compileRegex();
    }
    protected function _compileRegex()
    {
        $raw = str_replace(" ", "", $this->dtd_regex);
        if ($raw[0] != "(") {
            $raw = "(" . $raw . ")";
        }
        $el = "[#a-zA-Z0-9_.-]+";
        $reg = $raw;
        preg_match_all("/" . $el . "/", $reg, $matches);
        foreach ($matches[0] as $match) {
            $this->elements[$match] = true;
        }
        $reg = preg_replace("/" . $el . "/", "(,\\0)", $reg);
        $reg = preg_replace("/([^,(|]\\(+),/", "\\1", $reg);
        $reg = preg_replace("/,\\(/", "(", $reg);
        $this->_pcre_regex = $reg;
    }
    public function validateChildren($children, $config, $context)
    {
        $list_of_children = "";
        $nesting = 0;
        foreach ($children as $node) {
            if (empty($node->is_whitespace)) {
                $list_of_children .= $node->name . ",";
            }
        }
        $list_of_children = "," . rtrim($list_of_children, ",");
        $okay = preg_match("/^,?" . $this->_pcre_regex . "\$/", $list_of_children);
        return (bool) $okay;
    }
}
class HTMLPurifier_ChildDef_Empty extends HTMLPurifier_ChildDef
{
    public $allow_empty = true;
    public $type = "empty";
    public function __construct()
    {
    }
    public function validateChildren($children, $config, $context)
    {
        return [];
    }
}
class HTMLPurifier_ChildDef_List extends HTMLPurifier_ChildDef
{
    public $type = "list";
    public $elements = ["li" => true, "ul" => true, "ol" => true];
    public function validateChildren($children, $config, $context)
    {
        $this->whitespace = false;
        if (empty($children)) {
            return false;
        }
        if (!isset($config->getHTMLDefinition()->info["li"])) {
            trigger_error("Cannot allow ul/ol without allowing li", 512);
            return false;
        }
        $result = [];
        $all_whitespace = true;
        $current_li = NULL;
        foreach ($children as $node) {
            if (!empty($node->is_whitespace)) {
                $result[] = $node;
            } else {
                $all_whitespace = false;
                if ($node->name === "li") {
                    $current_li = $node;
                    $result[] = $node;
                } else {
                    if ($current_li === NULL) {
                        $current_li = new HTMLPurifier_Node_Element("li");
                        $result[] = $current_li;
                    }
                    $current_li->children[] = $node;
                    $current_li->empty = false;
                }
            }
        }
        if (empty($result)) {
            return false;
        }
        if ($all_whitespace) {
            return false;
        }
        return $result;
    }
}
class HTMLPurifier_ChildDef_Required extends HTMLPurifier_ChildDef
{
    public $elements = [];
    protected $whitespace = false;
    public $allow_empty = false;
    public $type = "required";
    public function __construct($elements)
    {
        if (is_string($elements)) {
            $elements = str_replace(" ", "", $elements);
            $elements = explode("|", $elements);
        }
        $keys = array_keys($elements);
        if ($keys == array_keys($keys)) {
            $elements = array_flip($elements);
            foreach ($elements as $i => $x) {
                $elements[$i] = true;
                if (empty($i)) {
                    unset($elements[$i]);
                }
            }
        }
        $this->elements = $elements;
    }
    public function validateChildren($children, $config, $context)
    {
        $this->whitespace = false;
        if (empty($children)) {
            return false;
        }
        $result = [];
        $pcdata_allowed = isset($this->elements["#PCDATA"]);
        $all_whitespace = true;
        $stack = array_reverse($children);
        while (!empty($stack)) {
            $node = array_pop($stack);
            if (!empty($node->is_whitespace)) {
                $result[] = $node;
            } else {
                $all_whitespace = false;
                if (!isset($this->elements[$node->name])) {
                    if ($pcdata_allowed && $node instanceof HTMLPurifier_Node_Text) {
                        $result[] = $node;
                    } else {
                        if ($node instanceof HTMLPurifier_Node_Element) {
                            for ($i = count($node->children) - 1; 0 <= $i; $i--) {
                                $stack[] = $node->children[$i];
                            }
                        }
                    }
                } else {
                    $result[] = $node;
                }
            }
        }
        if (empty($result)) {
            return false;
        }
        if ($all_whitespace) {
            $this->whitespace = true;
            return false;
        }
        return $result;
    }
}
class HTMLPurifier_ChildDef_Optional extends HTMLPurifier_ChildDef_Required
{
    public $allow_empty = true;
    public $type = "optional";
    public function validateChildren($children, $config, $context)
    {
        $result = parent::validateChildren($children, $config, $context);
        if ($result === false) {
            if (empty($children)) {
                return true;
            }
            if ($this->whitespace) {
                return $children;
            }
            return [];
        }
        return $result;
    }
}
class HTMLPurifier_ChildDef_StrictBlockquote extends HTMLPurifier_ChildDef_Required
{
    protected $real_elements = NULL;
    protected $fake_elements = NULL;
    public $allow_empty = true;
    public $type = "strictblockquote";
    protected $init = false;
    public function getAllowedElements($config)
    {
        $this->init($config);
        return $this->fake_elements;
    }
    public function validateChildren($children, $config, $context)
    {
        $this->init($config);
        $this->elements = $this->fake_elements;
        $result = parent::validateChildren($children, $config, $context);
        $this->elements = $this->real_elements;
        if ($result === false) {
            return [];
        }
        if ($result === true) {
            $result = $children;
        }
        $def = $config->getHTMLDefinition();
        $block_wrap_name = $def->info_block_wrapper;
        $block_wrap = false;
        $ret = [];
        foreach ($result as $node) {
            if ($block_wrap === false) {
                if ($node instanceof HTMLPurifier_Node_Text && !$node->is_whitespace || $node instanceof HTMLPurifier_Node_Element && !isset($this->elements[$node->name])) {
                    $block_wrap = new HTMLPurifier_Node_Element($def->info_block_wrapper);
                    $ret[] = $block_wrap;
                }
            } else {
                if ($node instanceof HTMLPurifier_Node_Element && isset($this->elements[$node->name])) {
                    $block_wrap = false;
                }
            }
            if ($block_wrap) {
                $block_wrap->children[] = $node;
            } else {
                $ret[] = $node;
            }
        }
        return $ret;
    }
    private function init($config)
    {
        if (!$this->init) {
            $def = $config->getHTMLDefinition();
            $this->real_elements = $this->elements;
            $this->fake_elements = $def->info_content_sets["Flow"];
            $this->fake_elements["#PCDATA"] = true;
            $this->init = true;
        }
    }
}
class HTMLPurifier_ChildDef_Table extends HTMLPurifier_ChildDef
{
    public $allow_empty = false;
    public $type = "table";
    public $elements = ["tr" => true, "tbody" => true, "thead" => true, "tfoot" => true, "caption" => true, "colgroup" => true, "col" => true];
    public function __construct()
    {
    }
    public function validateChildren($children, $config, $context)
    {
        if (empty($children)) {
            return false;
        }
        $caption = false;
        $thead = false;
        $tfoot = false;
        $initial_ws = [];
        $after_caption_ws = [];
        $after_thead_ws = [];
        $after_tfoot_ws = [];
        $cols = [];
        $content = [];
        $tbody_mode = false;
        $ws_accum =& $initial_ws;
        foreach ($children as $node) {
            if ($node instanceof HTMLPurifier_Node_Comment) {
                $ws_accum[] = $node;
            } else {
                switch ($node->name) {
                    case "tbody":
                        $tbody_mode = true;
                        break;
                    case "tr":
                        $content[] = $node;
                        $ws_accum =& $content;
                        break;
                    case "caption":
                        if ($caption === false) {
                            $caption = $node;
                            $ws_accum =& $after_caption_ws;
                        }
                        break;
                    case "thead":
                        $tbody_mode = true;
                        if ($thead === false) {
                            $thead = $node;
                            $ws_accum =& $after_thead_ws;
                        } else {
                            $node->name = "tbody";
                            $content[] = $node;
                            $ws_accum =& $content;
                        }
                        break;
                    case "tfoot":
                        $tbody_mode = true;
                        if ($tfoot === false) {
                            $tfoot = $node;
                            $ws_accum =& $after_tfoot_ws;
                        } else {
                            $node->name = "tbody";
                            $content[] = $node;
                            $ws_accum =& $content;
                        }
                        break;
                    case "colgroup":
                    case "col":
                        $cols[] = $node;
                        $ws_accum =& $cols;
                        break;
                    case "#PCDATA":
                        if (!empty($node->is_whitespace)) {
                            $ws_accum[] = $node;
                        }
                        break;
                }
            }
        }
        if (empty($content)) {
            return false;
        }
        $ret = $initial_ws;
        if ($caption !== false) {
            $ret[] = $caption;
            $ret = array_merge($ret, $after_caption_ws);
        }
        if ($cols !== false) {
            $ret = array_merge($ret, $cols);
        }
        if ($thead !== false) {
            $ret[] = $thead;
            $ret = array_merge($ret, $after_thead_ws);
        }
        if ($tfoot !== false) {
            $ret[] = $tfoot;
            $ret = array_merge($ret, $after_tfoot_ws);
        }
        if ($tbody_mode) {
            $current_tr_tbody = NULL;
            foreach ($content as $node) {
                switch ($node->name) {
                    case "tbody":
                        $current_tr_tbody = NULL;
                        $ret[] = $node;
                        break;
                    case "tr":
                        if ($current_tr_tbody === NULL) {
                            $current_tr_tbody = new HTMLPurifier_Node_Element("tbody");
                            $ret[] = $current_tr_tbody;
                        }
                        $current_tr_tbody->children[] = $node;
                        break;
                    case "#PCDATA":
                        if ($current_tr_tbody === NULL) {
                            $ret[] = $node;
                        } else {
                            $current_tr_tbody->children[] = $node;
                        }
                        break;
                }
            }
        } else {
            $ret = array_merge($ret, $content);
        }
        return $ret;
    }
}
class HTMLPurifier_DefinitionCache_Decorator extends HTMLPurifier_DefinitionCache
{
    public $cache = NULL;
    public $name = NULL;
    public function __construct()
    {
    }
    public function decorate(&$cache)
    {
        $decorator = $this->copy();
        $decorator->cache =& $cache;
        $decorator->type = $cache->type;
        return $decorator;
    }
    public function copy()
    {
        return new HTMLPurifier_DefinitionCache_Decorator();
    }
    public function add($def, $config)
    {
        return $this->cache->add($def, $config);
    }
    public function set($def, $config)
    {
        return $this->cache->set($def, $config);
    }
    public function replace($def, $config)
    {
        return $this->cache->replace($def, $config);
    }
    public function get($config)
    {
        return $this->cache->get($config);
    }
    public function remove($config)
    {
        return $this->cache->remove($config);
    }
    public function flush($config)
    {
        return $this->cache->flush($config);
    }
    public function cleanup($config)
    {
        return $this->cache->cleanup($config);
    }
}
class HTMLPurifier_DefinitionCache_Null extends HTMLPurifier_DefinitionCache
{
    public function add($def, $config)
    {
        return false;
    }
    public function set($def, $config)
    {
        return false;
    }
    public function replace($def, $config)
    {
        return false;
    }
    public function remove($config)
    {
        return false;
    }
    public function get($config)
    {
        return false;
    }
    public function flush($config)
    {
        return false;
    }
    public function cleanup($config)
    {
        return false;
    }
}
class HTMLPurifier_DefinitionCache_Serializer extends HTMLPurifier_DefinitionCache
{
    public function add($def, $config)
    {
        if (!$this->checkDefType($def)) {
            return NULL;
        }
        $file = $this->generateFilePath($config);
        if (file_exists($file)) {
            return false;
        }
        if (!$this->_prepareDir($config)) {
            return false;
        }
        return $this->_write($file, serialize($def), $config);
    }
    public function set($def, $config)
    {
        if (!$this->checkDefType($def)) {
            return NULL;
        }
        $file = $this->generateFilePath($config);
        if (!$this->_prepareDir($config)) {
            return false;
        }
        return $this->_write($file, serialize($def), $config);
    }
    public function replace($def, $config)
    {
        if (!$this->checkDefType($def)) {
            return NULL;
        }
        $file = $this->generateFilePath($config);
        if (!file_exists($file)) {
            return false;
        }
        if (!$this->_prepareDir($config)) {
            return false;
        }
        return $this->_write($file, serialize($def), $config);
    }
    public function get($config)
    {
        $file = $this->generateFilePath($config);
        if (!file_exists($file)) {
            return false;
        }
        return unserialize(file_get_contents($file));
    }
    public function remove($config)
    {
        $file = $this->generateFilePath($config);
        if (!file_exists($file)) {
            return false;
        }
        return unlink($file);
    }
    public function flush($config)
    {
        if (!$this->_prepareDir($config)) {
            return false;
        }
        $dir = $this->generateDirectoryPath($config);
        $dh = opendir($dir);
        if (false === $dh) {
            return false;
        }
        while (false !== ($filename = readdir($dh))) {
            if (!empty($filename)) {
                if ($filename[0] !== ".") {
                    unlink($dir . "/" . $filename);
                }
            }
        }
        closedir($dh);
        return true;
    }
    public function cleanup($config)
    {
        if (!$this->_prepareDir($config)) {
            return false;
        }
        $dir = $this->generateDirectoryPath($config);
        $dh = opendir($dir);
        if (false === $dh) {
            return false;
        }
        while (false !== ($filename = readdir($dh))) {
            if (!empty($filename)) {
                if ($filename[0] !== ".") {
                    $key = substr($filename, 0, strlen($filename) - 4);
                    if ($this->isOld($key, $config)) {
                        unlink($dir . "/" . $filename);
                    }
                }
            }
        }
        closedir($dh);
        return true;
    }
    public function generateFilePath($config)
    {
        $key = $this->generateKey($config);
        return $this->generateDirectoryPath($config) . "/" . $key . ".ser";
    }
    public function generateDirectoryPath($config)
    {
        $base = $this->generateBaseDirectoryPath($config);
        return $base . "/" . $this->type;
    }
    public function generateBaseDirectoryPath($config)
    {
        $base = $config->get("Cache.SerializerPath");
        $base = is_null($base) ? HTMLPURIFIER_PREFIX . "/HTMLPurifier/DefinitionCache/Serializer" : $base;
        return $base;
    }
    private function _write($file, $data, $config)
    {
        $result = file_put_contents($file, $data);
        if ($result !== false) {
            $chmod = $config->get("Cache.SerializerPermissions");
            if ($chmod !== NULL) {
                chmod($file, $chmod & 438);
            }
        }
        return $result;
    }
    private function _prepareDir($config)
    {
        $directory = $this->generateDirectoryPath($config);
        $chmod = $config->get("Cache.SerializerPermissions");
        if ($chmod === NULL) {
            if (!@mkdir($directory) && !is_dir($directory)) {
                trigger_error("Could not create directory " . $directory . "", 512);
                return false;
            }
            return true;
        }
        if (!is_dir($directory)) {
            $base = $this->generateBaseDirectoryPath($config);
            if (!is_dir($base)) {
                trigger_error("Base directory " . $base . " does not exist,\r\n                    please create or change using %Cache.SerializerPath", 512);
                return false;
            }
            if (!$this->_testPermissions($base, $chmod)) {
                return false;
            }
            if (!@mkdir($directory, $chmod) && !is_dir($directory)) {
                trigger_error("Could not create directory " . $directory . "", 512);
                return false;
            }
            if (!$this->_testPermissions($directory, $chmod)) {
                return false;
            }
        } else {
            if (!$this->_testPermissions($directory, $chmod)) {
                return false;
            }
        }
        return true;
    }
    private function _testPermissions($dir, $chmod)
    {
        if (is_writable($dir)) {
            return true;
        }
        if (!is_dir($dir)) {
            trigger_error("Directory " . $dir . " does not exist", 512);
            return false;
        }
        if (function_exists("posix_getuid") && $chmod !== NULL) {
            if (fileowner($dir) === posix_getuid()) {
                $chmod = $chmod | 448;
                if (chmod($dir, $chmod)) {
                    return true;
                }
            } else {
                if (filegroup($dir) === posix_getgid()) {
                    $chmod = $chmod | 56;
                } else {
                    $chmod = $chmod | 511;
                }
            }
            trigger_error("Directory " . $dir . " not writable, " . "please chmod to " . decoct($chmod), 512);
        } else {
            trigger_error("Directory " . $dir . " not writable, " . "please alter file permissions", 512);
        }
        return false;
    }
}
class HTMLPurifier_DefinitionCache_Decorator_Cleanup extends HTMLPurifier_DefinitionCache_Decorator
{
    public $name = "Cleanup";
    public function copy()
    {
        return new HTMLPurifier_DefinitionCache_Decorator_Cleanup();
    }
    public function add($def, $config)
    {
        $status = parent::add($def, $config);
        if (!$status) {
            parent::cleanup($config);
        }
        return $status;
    }
    public function set($def, $config)
    {
        $status = parent::set($def, $config);
        if (!$status) {
            parent::cleanup($config);
        }
        return $status;
    }
    public function replace($def, $config)
    {
        $status = parent::replace($def, $config);
        if (!$status) {
            parent::cleanup($config);
        }
        return $status;
    }
    public function get($config)
    {
        $ret = parent::get($config);
        if (!$ret) {
            parent::cleanup($config);
        }
        return $ret;
    }
}
class HTMLPurifier_DefinitionCache_Decorator_Memory extends HTMLPurifier_DefinitionCache_Decorator
{
    protected $definitions = NULL;
    public $name = "Memory";
    public function copy()
    {
        return new HTMLPurifier_DefinitionCache_Decorator_Memory();
    }
    public function add($def, $config)
    {
        $status = parent::add($def, $config);
        if ($status) {
            $this->definitions[$this->generateKey($config)] = $def;
        }
        return $status;
    }
    public function set($def, $config)
    {
        $status = parent::set($def, $config);
        if ($status) {
            $this->definitions[$this->generateKey($config)] = $def;
        }
        return $status;
    }
    public function replace($def, $config)
    {
        $status = parent::replace($def, $config);
        if ($status) {
            $this->definitions[$this->generateKey($config)] = $def;
        }
        return $status;
    }
    public function get($config)
    {
        $key = $this->generateKey($config);
        if (isset($this->definitions[$key])) {
            return $this->definitions[$key];
        }
        $this->definitions[$key] = parent::get($config);
        return $this->definitions[$key];
    }
}
class HTMLPurifier_HTMLModule_Bdo extends HTMLPurifier_HTMLModule
{
    public $name = "Bdo";
    public $attr_collections = ["I18N" => ["dir" => false]];
    public function setup($config)
    {
        $bdo = $this->addElement("bdo", "Inline", "Inline", ["Core", "Lang"], ["dir" => "Enum#ltr,rtl"]);
        $bdo->attr_transform_post[] = new HTMLPurifier_AttrTransform_BdoDir();
        $this->attr_collections["I18N"]["dir"] = "Enum#ltr,rtl";
    }
}
class HTMLPurifier_HTMLModule_CommonAttributes extends HTMLPurifier_HTMLModule
{
    public $name = "CommonAttributes";
    public $attr_collections = ["Core" => [["Style"], "class" => "Class", "id" => "ID", "title" => "CDATA"], "Lang" => [], "I18N" => [["Lang"]], "Common" => [["Core", "I18N"]]];
}
class HTMLPurifier_HTMLModule_Edit extends HTMLPurifier_HTMLModule
{
    public $name = "Edit";
    public $defines_child_def = true;
    public function setup($config)
    {
        $contents = "Chameleon: #PCDATA | Inline ! #PCDATA | Flow";
        $attr = ["cite" => "URI"];
        $this->addElement("del", "Inline", $contents, "Common", $attr);
        $this->addElement("ins", "Inline", $contents, "Common", $attr);
    }
    public function getChildDef($def)
    {
        if ($def->content_model_type != "chameleon") {
            return false;
        }
        $value = explode("!", $def->content_model);
        return new HTMLPurifier_ChildDef_Chameleon($value[0], $value[1]);
    }
}
class HTMLPurifier_HTMLModule_Forms extends HTMLPurifier_HTMLModule
{
    public $name = "Forms";
    public $safe = false;
    public $content_sets = ["Block" => "Form", "Inline" => "Formctrl"];
    public function setup($config)
    {
        $form = $this->addElement("form", "Form", "Required: Heading | List | Block | fieldset", "Common", ["accept" => "ContentTypes", "accept-charset" => "Charsets", "action*" => "URI", "method" => "Enum#get,post", "enctype" => "Enum#application/x-www-form-urlencoded,multipart/form-data"]);
        $form->excludes = ["form" => true];
        $input = $this->addElement("input", "Formctrl", "Empty", "Common", ["accept" => "ContentTypes", "accesskey" => "Character", "alt" => "Text", "checked" => "Bool#checked", "disabled" => "Bool#disabled", "maxlength" => "Number", "name" => "CDATA", "readonly" => "Bool#readonly", "size" => "Number", "src" => "URI#embedded", "tabindex" => "Number", "type" => "Enum#text,password,checkbox,button,radio,submit,reset,file,hidden,image", "value" => "CDATA"]);
        $input->attr_transform_post[] = new HTMLPurifier_AttrTransform_Input();
        $this->addElement("select", "Formctrl", "Required: optgroup | option", "Common", ["disabled" => "Bool#disabled", "multiple" => "Bool#multiple", "name" => "CDATA", "size" => "Number", "tabindex" => "Number"]);
        $this->addElement("option", false, "Optional: #PCDATA", "Common", ["disabled" => "Bool#disabled", "label" => "Text", "selected" => "Bool#selected", "value" => "CDATA"]);
        $textarea = $this->addElement("textarea", "Formctrl", "Optional: #PCDATA", "Common", ["accesskey" => "Character", "cols*" => "Number", "disabled" => "Bool#disabled", "name" => "CDATA", "readonly" => "Bool#readonly", "rows*" => "Number", "tabindex" => "Number"]);
        $textarea->attr_transform_pre[] = new HTMLPurifier_AttrTransform_Textarea();
        $button = $this->addElement("button", "Formctrl", "Optional: #PCDATA | Heading | List | Block | Inline", "Common", ["accesskey" => "Character", "disabled" => "Bool#disabled", "name" => "CDATA", "tabindex" => "Number", "type" => "Enum#button,submit,reset", "value" => "CDATA"]);
        $button->excludes = $this->makeLookup("form", "fieldset", "input", "select", "textarea", "label", "button", "a", "isindex", "iframe");
        $this->addElement("fieldset", "Form", "Custom: (#WS?,legend,(Flow|#PCDATA)*)", "Common");
        $label = $this->addElement("label", "Formctrl", "Optional: #PCDATA | Inline", "Common", ["accesskey" => "Character"]);
        $label->excludes = ["label" => true];
        $this->addElement("legend", false, "Optional: #PCDATA | Inline", "Common", ["accesskey" => "Character"]);
        $this->addElement("optgroup", false, "Required: option", "Common", ["disabled" => "Bool#disabled", "label*" => "Text"]);
    }
}
class HTMLPurifier_HTMLModule_Hypertext extends HTMLPurifier_HTMLModule
{
    public $name = "Hypertext";
    public function setup($config)
    {
        $a = $this->addElement("a", "Inline", "Inline", "Common", ["href" => "URI", "rel" => new HTMLPurifier_AttrDef_HTML_LinkTypes("rel"), "rev" => new HTMLPurifier_AttrDef_HTML_LinkTypes("rev")]);
        $a->formatting = true;
        $a->excludes = ["a" => true];
    }
}
class HTMLPurifier_HTMLModule_Iframe extends HTMLPurifier_HTMLModule
{
    public $name = "Iframe";
    public $safe = false;
    public function setup($config)
    {
        if ($config->get("HTML.SafeIframe")) {
            $this->safe = true;
        }
        $this->addElement("iframe", "Inline", "Flow", "Common", ["src" => "URI#embedded", "width" => "Length", "height" => "Length", "name" => "ID", "scrolling" => "Enum#yes,no,auto", "frameborder" => "Enum#0,1", "longdesc" => "URI", "marginheight" => "Pixels", "marginwidth" => "Pixels"]);
    }
}
class HTMLPurifier_HTMLModule_Image extends HTMLPurifier_HTMLModule
{
    public $name = "Image";
    public function setup($config)
    {
        $max = $config->get("HTML.MaxImgLength");
        $img = $this->addElement("img", "Inline", "Empty", "Common", ["alt*" => "Text", "height" => "Pixels#" . $max, "width" => "Pixels#" . $max, "longdesc" => "URI", "src*" => new HTMLPurifier_AttrDef_URI(true)]);
        if ($max === NULL || $config->get("HTML.Trusted")) {
            $img->attr["width"] = "Length";
            $img->attr["height"] = $img->attr["width"];
        }
        $img->attr_transform_post[] = new HTMLPurifier_AttrTransform_ImgRequired();
        $img->attr_transform_pre[] = $img->attr_transform_post;
    }
}
class HTMLPurifier_HTMLModule_Legacy extends HTMLPurifier_HTMLModule
{
    public $name = "Legacy";
    public function setup($config)
    {
        $this->addElement("basefont", "Inline", "Empty", NULL, ["color" => "Color", "face" => "Text", "size" => "Text", "id" => "ID"]);
        $this->addElement("center", "Block", "Flow", "Common");
        $this->addElement("dir", "Block", "Required: li", "Common", ["compact" => "Bool#compact"]);
        $this->addElement("font", "Inline", "Inline", ["Core", "I18N"], ["color" => "Color", "face" => "Text", "size" => "Text"]);
        $this->addElement("menu", "Block", "Required: li", "Common", ["compact" => "Bool#compact"]);
        $s = $this->addElement("s", "Inline", "Inline", "Common");
        $s->formatting = true;
        $strike = $this->addElement("strike", "Inline", "Inline", "Common");
        $strike->formatting = true;
        $u = $this->addElement("u", "Inline", "Inline", "Common");
        $u->formatting = true;
        $align = "Enum#left,right,center,justify";
        $address = $this->addBlankElement("address");
        $address->content_model = "Inline | #PCDATA | p";
        $address->content_model_type = "optional";
        $address->child = false;
        $blockquote = $this->addBlankElement("blockquote");
        $blockquote->content_model = "Flow | #PCDATA";
        $blockquote->content_model_type = "optional";
        $blockquote->child = false;
        $br = $this->addBlankElement("br");
        $br->attr["clear"] = "Enum#left,all,right,none";
        $caption = $this->addBlankElement("caption");
        $caption->attr["align"] = "Enum#top,bottom,left,right";
        $div = $this->addBlankElement("div");
        $div->attr["align"] = $align;
        $dl = $this->addBlankElement("dl");
        $dl->attr["compact"] = "Bool#compact";
        for ($i = 1; $i <= 6; $i++) {
            $h = $this->addBlankElement("h" . $i);
            $h->attr["align"] = $align;
        }
        $hr = $this->addBlankElement("hr");
        $hr->attr["align"] = $align;
        $hr->attr["noshade"] = "Bool#noshade";
        $hr->attr["size"] = "Pixels";
        $hr->attr["width"] = "Length";
        $img = $this->addBlankElement("img");
        $img->attr["align"] = "IAlign";
        $img->attr["border"] = "Pixels";
        $img->attr["hspace"] = "Pixels";
        $img->attr["vspace"] = "Pixels";
        $li = $this->addBlankElement("li");
        $li->attr["value"] = new HTMLPurifier_AttrDef_Integer();
        $li->attr["type"] = "Enum#s:1,i,I,a,A,disc,square,circle";
        $ol = $this->addBlankElement("ol");
        $ol->attr["compact"] = "Bool#compact";
        $ol->attr["start"] = new HTMLPurifier_AttrDef_Integer();
        $ol->attr["type"] = "Enum#s:1,i,I,a,A";
        $p = $this->addBlankElement("p");
        $p->attr["align"] = $align;
        $pre = $this->addBlankElement("pre");
        $pre->attr["width"] = "Number";
        $table = $this->addBlankElement("table");
        $table->attr["align"] = "Enum#left,center,right";
        $table->attr["bgcolor"] = "Color";
        $tr = $this->addBlankElement("tr");
        $tr->attr["bgcolor"] = "Color";
        $th = $this->addBlankElement("th");
        $th->attr["bgcolor"] = "Color";
        $th->attr["height"] = "Length";
        $th->attr["nowrap"] = "Bool#nowrap";
        $th->attr["width"] = "Length";
        $td = $this->addBlankElement("td");
        $td->attr["bgcolor"] = "Color";
        $td->attr["height"] = "Length";
        $td->attr["nowrap"] = "Bool#nowrap";
        $td->attr["width"] = "Length";
        $ul = $this->addBlankElement("ul");
        $ul->attr["compact"] = "Bool#compact";
        $ul->attr["type"] = "Enum#square,disc,circle";
        $form = $this->addBlankElement("form");
        $form->content_model = "Flow | #PCDATA";
        $form->content_model_type = "optional";
        $form->attr["target"] = "FrameTarget";
        $input = $this->addBlankElement("input");
        $input->attr["align"] = "IAlign";
        $legend = $this->addBlankElement("legend");
        $legend->attr["align"] = "LAlign";
    }
}
class HTMLPurifier_HTMLModule_List extends HTMLPurifier_HTMLModule
{
    public $name = "List";
    public $content_sets = ["Flow" => "List"];
    public function setup($config)
    {
        $ol = $this->addElement("ol", "List", new HTMLPurifier_ChildDef_List(), "Common");
        $ul = $this->addElement("ul", "List", new HTMLPurifier_ChildDef_List(), "Common");
        $ol->wrap = "li";
        $ul->wrap = "li";
        $this->addElement("dl", "List", "Required: dt | dd", "Common");
        $this->addElement("li", false, "Flow", "Common");
        $this->addElement("dd", false, "Flow", "Common");
        $this->addElement("dt", false, "Inline", "Common");
    }
}
class HTMLPurifier_HTMLModule_Name extends HTMLPurifier_HTMLModule
{
    public $name = "Name";
    public function setup($config)
    {
        $elements = ["a", "applet", "form", "frame", "iframe", "img", "map"];
        foreach ($elements as $name) {
            $element = $this->addBlankElement($name);
            $element->attr["name"] = "CDATA";
            if (!$config->get("HTML.Attr.Name.UseCDATA")) {
                $element->attr_transform_post[] = new HTMLPurifier_AttrTransform_NameSync();
            }
        }
    }
}
class HTMLPurifier_HTMLModule_Nofollow extends HTMLPurifier_HTMLModule
{
    public $name = "Nofollow";
    public function setup($config)
    {
        $a = $this->addBlankElement("a");
        $a->attr_transform_post[] = new HTMLPurifier_AttrTransform_Nofollow();
    }
}
class HTMLPurifier_HTMLModule_NonXMLCommonAttributes extends HTMLPurifier_HTMLModule
{
    public $name = "NonXMLCommonAttributes";
    public $attr_collections = ["Lang" => ["lang" => "LanguageCode"]];
}
class HTMLPurifier_HTMLModule_Object extends HTMLPurifier_HTMLModule
{
    public $name = "Object";
    public $safe = false;
    public function setup($config)
    {
        $this->addElement("object", "Inline", "Optional: #PCDATA | Flow | param", "Common", ["archive" => "URI", "classid" => "URI", "codebase" => "URI", "codetype" => "Text", "data" => "URI", "declare" => "Bool#declare", "height" => "Length", "name" => "CDATA", "standby" => "Text", "tabindex" => "Number", "type" => "ContentType", "width" => "Length"]);
        $this->addElement("param", false, "Empty", NULL, ["id" => "ID", "name*" => "Text", "type" => "Text", "value" => "Text", "valuetype" => "Enum#data,ref,object"]);
    }
}
class HTMLPurifier_HTMLModule_Presentation extends HTMLPurifier_HTMLModule
{
    public $name = "Presentation";
    public function setup($config)
    {
        $this->addElement("hr", "Block", "Empty", "Common");
        $this->addElement("sub", "Inline", "Inline", "Common");
        $this->addElement("sup", "Inline", "Inline", "Common");
        $b = $this->addElement("b", "Inline", "Inline", "Common");
        $b->formatting = true;
        $big = $this->addElement("big", "Inline", "Inline", "Common");
        $big->formatting = true;
        $i = $this->addElement("i", "Inline", "Inline", "Common");
        $i->formatting = true;
        $small = $this->addElement("small", "Inline", "Inline", "Common");
        $small->formatting = true;
        $tt = $this->addElement("tt", "Inline", "Inline", "Common");
        $tt->formatting = true;
    }
}
class HTMLPurifier_HTMLModule_Proprietary extends HTMLPurifier_HTMLModule
{
    public $name = "Proprietary";
    public function setup($config)
    {
        $this->addElement("marquee", "Inline", "Flow", "Common", ["direction" => "Enum#left,right,up,down", "behavior" => "Enum#alternate", "width" => "Length", "height" => "Length", "scrolldelay" => "Number", "scrollamount" => "Number", "loop" => "Number", "bgcolor" => "Color", "hspace" => "Pixels", "vspace" => "Pixels"]);
    }
}
class HTMLPurifier_HTMLModule_Ruby extends HTMLPurifier_HTMLModule
{
    public $name = "Ruby";
    public function setup($config)
    {
        $this->addElement("ruby", "Inline", "Custom: ((rb, (rt | (rp, rt, rp))) | (rbc, rtc, rtc?))", "Common");
        $this->addElement("rbc", false, "Required: rb", "Common");
        $this->addElement("rtc", false, "Required: rt", "Common");
        $rb = $this->addElement("rb", false, "Inline", "Common");
        $rb->excludes = ["ruby" => true];
        $rt = $this->addElement("rt", false, "Inline", "Common", ["rbspan" => "Number"]);
        $rt->excludes = ["ruby" => true];
        $this->addElement("rp", false, "Optional: #PCDATA", "Common");
    }
}
class HTMLPurifier_HTMLModule_SafeEmbed extends HTMLPurifier_HTMLModule
{
    public $name = "SafeEmbed";
    public function setup($config)
    {
        $max = $config->get("HTML.MaxImgLength");
        $embed = $this->addElement("embed", "Inline", "Empty", "Common", ["src*" => "URI#embedded", "type" => "Enum#application/x-shockwave-flash", "width" => "Pixels#" . $max, "height" => "Pixels#" . $max, "allowscriptaccess" => "Enum#never", "allownetworking" => "Enum#internal", "flashvars" => "Text", "wmode" => "Enum#window,transparent,opaque", "name" => "ID"]);
        $embed->attr_transform_post[] = new HTMLPurifier_AttrTransform_SafeEmbed();
    }
}
class HTMLPurifier_HTMLModule_SafeObject extends HTMLPurifier_HTMLModule
{
    public $name = "SafeObject";
    public function setup($config)
    {
        $max = $config->get("HTML.MaxImgLength");
        $object = $this->addElement("object", "Inline", "Optional: param | Flow | #PCDATA", "Common", ["type" => "Enum#application/x-shockwave-flash", "width" => "Pixels#" . $max, "height" => "Pixels#" . $max, "data" => "URI#embedded", "codebase" => new HTMLPurifier_AttrDef_Enum(["http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0"])]);
        $object->attr_transform_post[] = new HTMLPurifier_AttrTransform_SafeObject();
        $param = $this->addElement("param", false, "Empty", false, ["id" => "ID", "name*" => "Text", "value" => "Text"]);
        $param->attr_transform_post[] = new HTMLPurifier_AttrTransform_SafeParam();
        $this->info_injector[] = "SafeObject";
    }
}
class HTMLPurifier_HTMLModule_SafeScripting extends HTMLPurifier_HTMLModule
{
    public $name = "SafeScripting";
    public function setup($config)
    {
        $allowed = $config->get("HTML.SafeScripting");
        $script = $this->addElement("script", "Inline", "Optional:", NULL, ["type" => "Enum#text/javascript", "src*" => new HTMLPurifier_AttrDef_Enum(array_keys($allowed), true)]);
        $script->attr_transform_post[] = new HTMLPurifier_AttrTransform_ScriptRequired();
        $script->attr_transform_pre[] = $script->attr_transform_post;
    }
}
class HTMLPurifier_HTMLModule_Scripting extends HTMLPurifier_HTMLModule
{
    public $name = "Scripting";
    public $elements = ["script", "noscript"];
    public $content_sets = ["Block" => "script | noscript", "Inline" => "script | noscript"];
    public $safe = false;
    public function setup($config)
    {
        $this->info["noscript"] = new HTMLPurifier_ElementDef();
        $this->info["noscript"]->attr = [["Common"]];
        $this->info["noscript"]->content_model = "Heading | List | Block";
        $this->info["noscript"]->content_model_type = "required";
        $this->info["script"] = new HTMLPurifier_ElementDef();
        $this->info["script"]->attr = ["defer" => new HTMLPurifier_AttrDef_Enum(["defer"]), "src" => new HTMLPurifier_AttrDef_URI(true), "type" => new HTMLPurifier_AttrDef_Enum(["text/javascript"])];
        $this->info["script"]->content_model = "#PCDATA";
        $this->info["script"]->content_model_type = "optional";
        $this->info["script"]->attr_transform_post[] = new HTMLPurifier_AttrTransform_ScriptRequired();
        $this->info["script"]->attr_transform_pre[] = $this->info["script"]->attr_transform_post;
    }
}
class HTMLPurifier_HTMLModule_StyleAttribute extends HTMLPurifier_HTMLModule
{
    public $name = "StyleAttribute";
    public $attr_collections = ["Style" => ["style" => false], "Core" => [["Style"]]];
    public function setup($config)
    {
        $this->attr_collections["Style"]["style"] = new HTMLPurifier_AttrDef_CSS();
    }
}
class HTMLPurifier_HTMLModule_Tables extends HTMLPurifier_HTMLModule
{
    public $name = "Tables";
    public function setup($config)
    {
        $this->addElement("caption", false, "Inline", "Common");
        $this->addElement("table", "Block", new HTMLPurifier_ChildDef_Table(), "Common", ["border" => "Pixels", "cellpadding" => "Length", "cellspacing" => "Length", "frame" => "Enum#void,above,below,hsides,lhs,rhs,vsides,box,border", "rules" => "Enum#none,groups,rows,cols,all", "summary" => "Text", "width" => "Length"]);
        $cell_align = ["align" => "Enum#left,center,right,justify,char", "charoff" => "Length", "valign" => "Enum#top,middle,bottom,baseline"];
        $cell_t = array_merge(["abbr" => "Text", "colspan" => "Number", "rowspan" => "Number", "scope" => "Enum#row,col,rowgroup,colgroup"], $cell_align);
        $this->addElement("td", false, "Flow", "Common", $cell_t);
        $this->addElement("th", false, "Flow", "Common", $cell_t);
        $this->addElement("tr", false, "Required: td | th", "Common", $cell_align);
        $cell_col = array_merge(["span" => "Number", "width" => "MultiLength"], $cell_align);
        $this->addElement("col", false, "Empty", "Common", $cell_col);
        $this->addElement("colgroup", false, "Optional: col", "Common", $cell_col);
        $this->addElement("tbody", false, "Required: tr", "Common", $cell_align);
        $this->addElement("thead", false, "Required: tr", "Common", $cell_align);
        $this->addElement("tfoot", false, "Required: tr", "Common", $cell_align);
    }
}
class HTMLPurifier_HTMLModule_Target extends HTMLPurifier_HTMLModule
{
    public $name = "Target";
    public function setup($config)
    {
        $elements = ["a"];
        foreach ($elements as $name) {
            $e = $this->addBlankElement($name);
            $e->attr = ["target" => new HTMLPurifier_AttrDef_HTML_FrameTarget()];
        }
    }
}
class HTMLPurifier_HTMLModule_TargetBlank extends HTMLPurifier_HTMLModule
{
    public $name = "TargetBlank";
    public function setup($config)
    {
        $a = $this->addBlankElement("a");
        $a->attr_transform_post[] = new HTMLPurifier_AttrTransform_TargetBlank();
    }
}
class HTMLPurifier_HTMLModule_TargetNoopener extends HTMLPurifier_HTMLModule
{
    public $name = "TargetNoopener";
    public function setup($config)
    {
        $a = $this->addBlankElement("a");
        $a->attr_transform_post[] = new HTMLPurifier_AttrTransform_TargetNoopener();
    }
}
class HTMLPurifier_HTMLModule_TargetNoreferrer extends HTMLPurifier_HTMLModule
{
    public $name = "TargetNoreferrer";
    public function setup($config)
    {
        $a = $this->addBlankElement("a");
        $a->attr_transform_post[] = new HTMLPurifier_AttrTransform_TargetNoreferrer();
    }
}
class HTMLPurifier_HTMLModule_Text extends HTMLPurifier_HTMLModule
{
    public $name = "Text";
    public $content_sets = ["Flow" => "Heading | Block | Inline"];
    public function setup($config)
    {
        $this->addElement("abbr", "Inline", "Inline", "Common");
        $this->addElement("acronym", "Inline", "Inline", "Common");
        $this->addElement("cite", "Inline", "Inline", "Common");
        $this->addElement("dfn", "Inline", "Inline", "Common");
        $this->addElement("kbd", "Inline", "Inline", "Common");
        $this->addElement("q", "Inline", "Inline", "Common", ["cite" => "URI"]);
        $this->addElement("samp", "Inline", "Inline", "Common");
        $this->addElement("var", "Inline", "Inline", "Common");
        $em = $this->addElement("em", "Inline", "Inline", "Common");
        $em->formatting = true;
        $strong = $this->addElement("strong", "Inline", "Inline", "Common");
        $strong->formatting = true;
        $code = $this->addElement("code", "Inline", "Inline", "Common");
        $code->formatting = true;
        $this->addElement("span", "Inline", "Inline", "Common");
        $this->addElement("br", "Inline", "Empty", "Core");
        $this->addElement("address", "Block", "Inline", "Common");
        $this->addElement("blockquote", "Block", "Optional: Heading | Block | List", "Common", ["cite" => "URI"]);
        $pre = $this->addElement("pre", "Block", "Inline", "Common");
        $pre->excludes = $this->makeLookup("img", "big", "small", "object", "applet", "font", "basefont");
        $this->addElement("h1", "Heading", "Inline", "Common");
        $this->addElement("h2", "Heading", "Inline", "Common");
        $this->addElement("h3", "Heading", "Inline", "Common");
        $this->addElement("h4", "Heading", "Inline", "Common");
        $this->addElement("h5", "Heading", "Inline", "Common");
        $this->addElement("h6", "Heading", "Inline", "Common");
        $p = $this->addElement("p", "Block", "Inline", "Common");
        $p->autoclose = array_flip(["address", "blockquote", "center", "dir", "div", "dl", "fieldset", "ol", "p", "ul"]);
        $this->addElement("div", "Block", "Flow", "Common");
    }
}
class HTMLPurifier_HTMLModule_Tidy extends HTMLPurifier_HTMLModule
{
    public $levels = ["none", "light", "medium", "heavy"];
    public $defaultLevel = NULL;
    public $fixesForLevel = ["light" => [], "medium" => [], "heavy" => []];
    public function setup($config)
    {
        $fixes = $this->makeFixes();
        $this->makeFixesForLevel($fixes);
        $level = $config->get("HTML.TidyLevel");
        $fixes_lookup = $this->getFixesForLevel($level);
        $add_fixes = $config->get("HTML.TidyAdd");
        $remove_fixes = $config->get("HTML.TidyRemove");
        foreach ($fixes as $name => $fix) {
            if (isset($remove_fixes[$name]) || !isset($add_fixes[$name]) && !isset($fixes_lookup[$name])) {
                unset($fixes[$name]);
            }
        }
        $this->populate($fixes);
    }
    public function getFixesForLevel($level)
    {
        if ($level == $this->levels[0]) {
            return [];
        }
        $activated_levels = [];
        $i = 1;
        $c = count($this->levels);
        while ($i < $c) {
            $activated_levels[] = $this->levels[$i];
            if ($this->levels[$i] != $level) {
                $i++;
            }
        }
        if ($i == $c) {
            trigger_error("Tidy level " . htmlspecialchars($level) . " not recognized", 512);
            return [];
        }
        $ret = [];
        foreach ($activated_levels as $level) {
            foreach ($this->fixesForLevel[$level] as $fix) {
                $ret[$fix] = true;
            }
        }
        return $ret;
    }
    public function makeFixesForLevel($fixes)
    {
        if (!isset($this->defaultLevel)) {
            return NULL;
        }
        if (!isset($this->fixesForLevel[$this->defaultLevel])) {
            trigger_error("Default level " . $this->defaultLevel . " does not exist", 256);
        } else {
            $this->fixesForLevel[$this->defaultLevel] = array_keys($fixes);
        }
    }
    public function populate($fixes)
    {
        foreach ($fixes as $name => $fix) {
            list($type, $params) = $this->getFixType($name);
            switch ($type) {
                case "attr_transform_pre":
                case "attr_transform_post":
                    $attr = $params["attr"];
                    if (isset($params["element"])) {
                        $element = $params["element"];
                        if (empty($this->info[$element])) {
                            $e = $this->addBlankElement($element);
                        } else {
                            $e = $this->info[$element];
                        }
                    } else {
                        $type = "info_" . $type;
                        $e = $this;
                    }
                    $f =& $e->{$type};
                    $f[$attr] = $fix;
                    break;
                case "tag_transform":
                    $this->info_tag_transform[$params["element"]] = $fix;
                    break;
                case "child":
                case "content_model_type":
                    $element = $params["element"];
                    if (empty($this->info[$element])) {
                        $e = $this->addBlankElement($element);
                    } else {
                        $e = $this->info[$element];
                    }
                    $e->{$type} = $fix;
                    break;
                default:
                    trigger_error("Fix type " . $type . " not supported", 256);
            }
        }
    }
    public function getFixType($name)
    {
        $property = $attr = NULL;
        if (strpos($name, "#") !== false) {
            list($name, $property) = explode("#", $name);
        }
        if (strpos($name, "@") !== false) {
            list($name, $attr) = explode("@", $name);
        }
        $params = [];
        if ($name !== "") {
            $params["element"] = $name;
        }
        if (!is_null($attr)) {
            $params["attr"] = $attr;
        }
        if (!is_null($attr)) {
            if (is_null($property)) {
                $property = "pre";
            }
            $type = "attr_transform_" . $property;
            return [$type, $params];
        }
        if (is_null($property)) {
            return ["tag_transform", $params];
        }
        return [$property, $params];
    }
    public function makeFixes()
    {
    }
}
class HTMLPurifier_HTMLModule_XMLCommonAttributes extends HTMLPurifier_HTMLModule
{
    public $name = "XMLCommonAttributes";
    public $attr_collections = ["Lang" => ["xml:lang" => "LanguageCode"]];
}
class HTMLPurifier_HTMLModule_Tidy_Name extends HTMLPurifier_HTMLModule_Tidy
{
    public $name = "Tidy_Name";
    public $defaultLevel = "heavy";
    public function makeFixes()
    {
        $r = [];
        $r["a@name"] = new HTMLPurifier_AttrTransform_Name();
        $r["img@name"] = $r["a@name"];
        return $r;
    }
}
class HTMLPurifier_HTMLModule_Tidy_Proprietary extends HTMLPurifier_HTMLModule_Tidy
{
    public $name = "Tidy_Proprietary";
    public $defaultLevel = "light";
    public function makeFixes()
    {
        $r = [];
        $r["table@background"] = new HTMLPurifier_AttrTransform_Background();
        $r["td@background"] = new HTMLPurifier_AttrTransform_Background();
        $r["th@background"] = new HTMLPurifier_AttrTransform_Background();
        $r["tr@background"] = new HTMLPurifier_AttrTransform_Background();
        $r["thead@background"] = new HTMLPurifier_AttrTransform_Background();
        $r["tfoot@background"] = new HTMLPurifier_AttrTransform_Background();
        $r["tbody@background"] = new HTMLPurifier_AttrTransform_Background();
        $r["table@height"] = new HTMLPurifier_AttrTransform_Length("height");
        return $r;
    }
}
class HTMLPurifier_HTMLModule_Tidy_XHTMLAndHTML4 extends HTMLPurifier_HTMLModule_Tidy
{
    public function makeFixes()
    {
        $r = [];
        $r["font"] = new HTMLPurifier_TagTransform_Font();
        $r["menu"] = new HTMLPurifier_TagTransform_Simple("ul");
        $r["dir"] = new HTMLPurifier_TagTransform_Simple("ul");
        $r["center"] = new HTMLPurifier_TagTransform_Simple("div", "text-align:center;");
        $r["u"] = new HTMLPurifier_TagTransform_Simple("span", "text-decoration:underline;");
        $r["s"] = new HTMLPurifier_TagTransform_Simple("span", "text-decoration:line-through;");
        $r["strike"] = new HTMLPurifier_TagTransform_Simple("span", "text-decoration:line-through;");
        $r["caption@align"] = new HTMLPurifier_AttrTransform_EnumToCSS("align", ["left" => "text-align:left;", "right" => "text-align:right;", "top" => "caption-side:top;", "bottom" => "caption-side:bottom;"]);
        $r["img@align"] = new HTMLPurifier_AttrTransform_EnumToCSS("align", ["left" => "float:left;", "right" => "float:right;", "top" => "vertical-align:top;", "middle" => "vertical-align:middle;", "bottom" => "vertical-align:baseline;"]);
        $r["table@align"] = new HTMLPurifier_AttrTransform_EnumToCSS("align", ["left" => "float:left;", "center" => "margin-left:auto;margin-right:auto;", "right" => "float:right;"]);
        $r["hr@align"] = new HTMLPurifier_AttrTransform_EnumToCSS("align", ["left" => "margin-left:0;margin-right:auto;text-align:left;", "center" => "margin-left:auto;margin-right:auto;text-align:center;", "right" => "margin-left:auto;margin-right:0;text-align:right;"]);
        $align_lookup = [];
        $align_values = ["left", "right", "center", "justify"];
        foreach ($align_values as $v) {
            $align_lookup[$v] = "text-align:" . $v . ";";
        }
        $r["div@align"] = new HTMLPurifier_AttrTransform_EnumToCSS("align", $align_lookup);
        $r["p@align"] = $r["div@align"];
        $r["h6@align"] = $r["p@align"];
        $r["h5@align"] = $r["h6@align"];
        $r["h4@align"] = $r["h5@align"];
        $r["h3@align"] = $r["h4@align"];
        $r["h2@align"] = $r["h3@align"];
        $r["h1@align"] = $r["h2@align"];
        $r["th@bgcolor"] = new HTMLPurifier_AttrTransform_BgColor();
        $r["td@bgcolor"] = $r["th@bgcolor"];
        $r["table@bgcolor"] = $r["td@bgcolor"];
        $r["img@border"] = new HTMLPurifier_AttrTransform_Border();
        $r["br@clear"] = new HTMLPurifier_AttrTransform_EnumToCSS("clear", ["left" => "clear:left;", "right" => "clear:right;", "all" => "clear:both;", "none" => "clear:none;"]);
        $r["th@height"] = new HTMLPurifier_AttrTransform_Length("height");
        $r["td@height"] = $r["th@height"];
        $r["img@hspace"] = new HTMLPurifier_AttrTransform_ImgSpace("hspace");
        $r["hr@noshade"] = new HTMLPurifier_AttrTransform_BoolToCSS("noshade", "color:#808080;background-color:#808080;border:0;");
        $r["th@nowrap"] = new HTMLPurifier_AttrTransform_BoolToCSS("nowrap", "white-space:nowrap;");
        $r["td@nowrap"] = $r["th@nowrap"];
        $r["hr@size"] = new HTMLPurifier_AttrTransform_Length("size", "height");
        $ul_types = ["disc" => "list-style-type:disc;", "square" => "list-style-type:square;", "circle" => "list-style-type:circle;"];
        $ol_types = ["1" => "list-style-type:decimal;", "i" => "list-style-type:lower-roman;", "I" => "list-style-type:upper-roman;", "a" => "list-style-type:lower-alpha;", "A" => "list-style-type:upper-alpha;"];
        $li_types = $ul_types + $ol_types;
        $r["ul@type"] = new HTMLPurifier_AttrTransform_EnumToCSS("type", $ul_types);
        $r["ol@type"] = new HTMLPurifier_AttrTransform_EnumToCSS("type", $ol_types, true);
        $r["li@type"] = new HTMLPurifier_AttrTransform_EnumToCSS("type", $li_types, true);
        $r["img@vspace"] = new HTMLPurifier_AttrTransform_ImgSpace("vspace");
        $r["hr@width"] = new HTMLPurifier_AttrTransform_Length("width");
        $r["th@width"] = $r["hr@width"];
        $r["td@width"] = $r["th@width"];
        return $r;
    }
}
class HTMLPurifier_HTMLModule_Tidy_Strict extends HTMLPurifier_HTMLModule_Tidy_XHTMLAndHTML4
{
    public $name = "Tidy_Strict";
    public $defaultLevel = "light";
    public $defines_child_def = true;
    public function makeFixes()
    {
        $r = parent::makeFixes();
        $r["blockquote#content_model_type"] = "strictblockquote";
        return $r;
    }
    public function getChildDef($def)
    {
        if ($def->content_model_type != "strictblockquote") {
            return parent::getChildDef($def);
        }
        return new HTMLPurifier_ChildDef_StrictBlockquote($def->content_model);
    }
}
class HTMLPurifier_HTMLModule_Tidy_Transitional extends HTMLPurifier_HTMLModule_Tidy_XHTMLAndHTML4
{
    public $name = "Tidy_Transitional";
    public $defaultLevel = "heavy";
}
class HTMLPurifier_HTMLModule_Tidy_XHTML extends HTMLPurifier_HTMLModule_Tidy
{
    public $name = "Tidy_XHTML";
    public $defaultLevel = "medium";
    public function makeFixes()
    {
        $r = [];
        $r["@lang"] = new HTMLPurifier_AttrTransform_Lang();
        return $r;
    }
}
class HTMLPurifier_Injector_AutoParagraph extends HTMLPurifier_Injector
{
    public $name = "AutoParagraph";
    public $needed = ["p"];
    private function _pStart()
    {
        $par = new HTMLPurifier_Token_Start("p");
        $par->armor["MakeWellFormed_TagClosedError"] = true;
        return $par;
    }
    public function handleText(&$token)
    {
        $text = $token->data;
        if ($this->allowsElement("p")) {
            if (empty($this->currentNesting) || strpos($text, "\n\n") !== false) {
                $i = $nesting = NULL;
                if ($this->forwardUntilEndToken($i, $current, $nesting) || !$token->is_whitespace) {
                    if (!$token->is_whitespace || $this->_isInline($current)) {
                        $token = [$this->_pStart()];
                        $this->_splitText($text, $token);
                    }
                }
            } else {
                if ($this->_pLookAhead()) {
                    $token = [$this->_pStart(), $token];
                }
            }
        } else {
            if (!empty($this->currentNesting) && $this->currentNesting[count($this->currentNesting) - 1]->name == "p") {
                $token = [];
                $this->_splitText($text, $token);
            }
        }
    }
    public function handleElement(&$token)
    {
        if ($this->allowsElement("p")) {
            if (!empty($this->currentNesting)) {
                if ($this->_isInline($token)) {
                    $i = NULL;
                    $this->backward($i, $prev);
                    if (!$prev instanceof HTMLPurifier_Token_Start) {
                        if ($prev instanceof HTMLPurifier_Token_Text && substr($prev->data, -2) === "\n\n") {
                            $token = [$this->_pStart(), $token];
                        }
                    } else {
                        if ($this->_pLookAhead()) {
                            $token = [$this->_pStart(), $token];
                        }
                    }
                }
            } else {
                if ($this->_isInline($token)) {
                    $token = [$this->_pStart(), $token];
                }
                $i = NULL;
                if ($this->backward($i, $prev) && !$prev instanceof HTMLPurifier_Token_Text) {
                    if (!is_array($token)) {
                        $token = [$token];
                    }
                    array_unshift($token, new HTMLPurifier_Token_Text("\n\n"));
                }
            }
        }
    }
    private function _splitText($data, &$result)
    {
        $raw_paragraphs = explode("\n\n", $data);
        $paragraphs = [];
        $needs_start = false;
        $needs_end = false;
        $c = count($raw_paragraphs);
        if ($c == 1) {
            $result[] = new HTMLPurifier_Token_Text($data);
        } else {
            for ($i = 0; $i < $c; $i++) {
                $par = $raw_paragraphs[$i];
                if (trim($par) !== "") {
                    $paragraphs[] = $par;
                } else {
                    if ($i == 0) {
                        if (empty($result)) {
                            $result[] = new HTMLPurifier_Token_End("p");
                            $result[] = new HTMLPurifier_Token_Text("\n\n");
                            $needs_start = true;
                        } else {
                            array_unshift($result, new HTMLPurifier_Token_Text("\n\n"));
                        }
                    } else {
                        if ($i + 1 == $c) {
                            $needs_end = true;
                        }
                    }
                }
            }
            if (empty($paragraphs)) {
                return NULL;
            }
            if ($needs_start) {
                $result[] = $this->_pStart();
            }
            foreach ($paragraphs as $par) {
                $result[] = new HTMLPurifier_Token_Text($par);
                $result[] = new HTMLPurifier_Token_End("p");
                $result[] = new HTMLPurifier_Token_Text("\n\n");
                $result[] = $this->_pStart();
            }
            array_pop($result);
            if (!$needs_end) {
                array_pop($result);
                array_pop($result);
            }
        }
    }
    private function _isInline($token)
    {
        return isset($this->htmlDefinition->info["p"]->child->elements[$token->name]);
    }
    private function _pLookAhead()
    {
        if ($this->currentToken instanceof HTMLPurifier_Token_Start) {
            $nesting = 1;
        } else {
            $nesting = 0;
        }
        $ok = false;
        $i = NULL;
        while ($this->forwardUntilEndToken($i, $current, $nesting)) {
            $result = $this->_checkNeedsP($current);
            if ($result !== NULL) {
                $ok = $result;
            }
        }
        return $ok;
    }
    private function _checkNeedsP($current)
    {
        if ($current instanceof HTMLPurifier_Token_Start) {
            if (!$this->_isInline($current)) {
                return false;
            }
        } else {
            if ($current instanceof HTMLPurifier_Token_Text && strpos($current->data, "\n\n") !== false) {
                return true;
            }
        }
    }
}
class HTMLPurifier_Injector_DisplayLinkURI extends HTMLPurifier_Injector
{
    public $name = "DisplayLinkURI";
    public $needed = ["a"];
    public function handleElement(&$token)
    {
    }
    public function handleEnd(&$token)
    {
        if (isset($token->start->attr["href"])) {
            $url = $token->start->attr["href"];
            unset($token->start->attr["href"]);
            $token = [$token, new HTMLPurifier_Token_Text(" (" . $url . ")")];
        }
    }
}
class HTMLPurifier_Injector_Linkify extends HTMLPurifier_Injector
{
    public $name = "Linkify";
    public $needed = ["a" => ["href"]];
    public function handleText(&$token)
    {
        if (!$this->allowsElement("a")) {
            return NULL;
        }
        if (strpos($token->data, "://") === false) {
            return NULL;
        }
        $bits = preg_split("/\\b((?:[a-z][\\w\\-]+:(?:\\/{1,3}|[a-z0-9%])|www\\d{0,3}[.]|[a-z0-9.\\-]+[.][a-z]{2,4}\\/)(?:[^\\s()<>]|\\((?:[^\\s()<>]|(?:\\([^\\s()<>]+\\)))*\\))+(?:\\((?:[^\\s()<>]|(?:\\([^\\s()<>]+\\)))*\\)|[^\\s`!()\\[\\]{};:'\".,<>?\\x{00ab}\\x{00bb}\\x{201c}\\x{201d}\\x{2018}\\x{2019}]))/iu", $token->data, -1, PREG_SPLIT_DELIM_CAPTURE);
        $token = [];
        $i = 0;
        $c = count($bits);
        $l = false;
        while ($i < $c) {
            if (!$l) {
                if ($bits[$i] !== "") {
                    $token[] = new HTMLPurifier_Token_Text($bits[$i]);
                }
            } else {
                $token[] = new HTMLPurifier_Token_Start("a", ["href" => $bits[$i]]);
                $token[] = new HTMLPurifier_Token_Text($bits[$i]);
                $token[] = new HTMLPurifier_Token_End("a");
            }
            $i++;
            $l = !$l;
        }
    }
}
class HTMLPurifier_Injector_PurifierLinkify extends HTMLPurifier_Injector
{
    public $name = "PurifierLinkify";
    public $docURL = NULL;
    public $needed = ["a" => ["href"]];
    public function prepare($config, $context)
    {
        $this->docURL = $config->get("AutoFormat.PurifierLinkify.DocURL");
        return parent::prepare($config, $context);
    }
    public function handleText(&$token)
    {
        if (!$this->allowsElement("a")) {
            return NULL;
        }
        if (strpos($token->data, "%") === false) {
            return NULL;
        }
        $bits = preg_split("#%([a-z0-9]+\\.[a-z0-9]+)#Si", $token->data, -1, PREG_SPLIT_DELIM_CAPTURE);
        $token = [];
        $i = 0;
        $c = count($bits);
        $l = false;
        while ($i < $c) {
            if (!$l) {
                if ($bits[$i] !== "") {
                    $token[] = new HTMLPurifier_Token_Text($bits[$i]);
                }
            } else {
                $token[] = new HTMLPurifier_Token_Start("a", ["href" => str_replace("%s", $bits[$i], $this->docURL)]);
                $token[] = new HTMLPurifier_Token_Text("%" . $bits[$i]);
                $token[] = new HTMLPurifier_Token_End("a");
            }
            $i++;
            $l = !$l;
        }
    }
}
class HTMLPurifier_Injector_RemoveEmpty extends HTMLPurifier_Injector
{
    private $context = NULL;
    private $config = NULL;
    private $attrValidator = NULL;
    private $removeNbsp = NULL;
    private $removeNbspExceptions = NULL;
    private $exclude = NULL;
    public function prepare($config, $context)
    {
        parent::prepare($config, $context);
        $this->config = $config;
        $this->context = $context;
        $this->removeNbsp = $config->get("AutoFormat.RemoveEmpty.RemoveNbsp");
        $this->removeNbspExceptions = $config->get("AutoFormat.RemoveEmpty.RemoveNbsp.Exceptions");
        $this->exclude = $config->get("AutoFormat.RemoveEmpty.Predicate");
        foreach ($this->exclude as $key => $attrs) {
            if (!is_array($attrs)) {
                $this->exclude[$key] = explode(";", $attrs);
            }
        }
        $this->attrValidator = new HTMLPurifier_AttrValidator();
    }
    public function handleElement(&$token)
    {
        if (!$token instanceof HTMLPurifier_Token_Start) {
            return NULL;
        }
        $next = false;
        $deleted = 1;
        $i = count($this->inputZipper->back) - 1;
        while (0 <= $i) {
            $next = $this->inputZipper->back[$i];
            if ($next instanceof HTMLPurifier_Token_Text) {
                if (!$next->is_whitespace) {
                    if ($this->removeNbsp && !isset($this->removeNbspExceptions[$token->name])) {
                        $plain = str_replace(" ", "", $next->data);
                        $isWsOrNbsp = $plain === "" || ctype_space($plain);
                        if (!$isWsOrNbsp) {
                        }
                    }
                }
                $i--;
                $deleted++;
            }
        }
        if (!$next || $next instanceof HTMLPurifier_Token_End && $next->name == $token->name) {
            $this->attrValidator->validateToken($token, $this->config, $this->context);
            $token->armor["ValidateAttributes"] = true;
            if (isset($this->exclude[$token->name])) {
                $r = true;
                foreach ($this->exclude[$token->name] as $elem) {
                    if (!isset($token->attr[$elem])) {
                        $r = false;
                    }
                }
                if ($r) {
                    return NULL;
                }
            }
            if (isset($token->attr["id"]) || isset($token->attr["name"])) {
                return NULL;
            }
            $token = $deleted + 1;
            $b = 0;
            $c = count($this->inputZipper->front);
            while ($b < $c) {
                $prev = $this->inputZipper->front[$b];
                if ($prev instanceof HTMLPurifier_Token_Text && $prev->is_whitespace) {
                    $b++;
                }
            }
            $this->rewindOffset($b + $deleted);
        }
    }
}
class HTMLPurifier_Injector_RemoveSpansWithoutAttributes extends HTMLPurifier_Injector
{
    public $name = "RemoveSpansWithoutAttributes";
    public $needed = ["span"];
    private $attrValidator = NULL;
    private $config = NULL;
    private $context = NULL;
    public function prepare($config, $context)
    {
        $this->attrValidator = new HTMLPurifier_AttrValidator();
        $this->config = $config;
        $this->context = $context;
        return parent::prepare($config, $context);
    }
    public function handleElement(&$token)
    {
        if ($token->name !== "span" || !$token instanceof HTMLPurifier_Token_Start) {
            return NULL;
        }
        $this->attrValidator->validateToken($token, $this->config, $this->context);
        $token->armor["ValidateAttributes"] = true;
        if (!empty($token->attr)) {
            return NULL;
        }
        $nesting = 0;
        do {
        } while (!$this->forwardUntilEndToken($i, $current, $nesting));
        if ($current instanceof HTMLPurifier_Token_End && $current->name === "span") {
            $current->markForDeletion = true;
            $token = false;
        }
    }
    public function handleEnd(&$token)
    {
        if ($token->markForDeletion) {
            $token = false;
        }
    }
}
class HTMLPurifier_Injector_SafeObject extends HTMLPurifier_Injector
{
    public $name = "SafeObject";
    public $needed = ["object", "param"];
    protected $objectStack = [];
    protected $paramStack = [];
    protected $addParam = ["allowScriptAccess" => "never", "allowNetworking" => "internal"];
    protected $allowedParam = ["wmode" => true, "movie" => true, "flashvars" => true, "src" => true, "allowfullscreen" => true];
    public function prepare($config, $context)
    {
        parent::prepare($config, $context);
    }
    public function handleElement(&$token)
    {
        if ($token->name == "object") {
            $this->objectStack[] = $token;
            $this->paramStack[] = [];
            $new = [$token];
            foreach ($this->addParam as $name => $value) {
                $new[] = new HTMLPurifier_Token_Empty("param", ["name" => $name, "value" => $value]);
            }
            $token = $new;
        } else {
            if ($token->name == "param") {
                $nest = count($this->currentNesting) - 1;
                if (0 <= $nest && $this->currentNesting[$nest]->name === "object") {
                    $i = count($this->objectStack) - 1;
                    if (!isset($token->attr["name"])) {
                        $token = false;
                        return NULL;
                    }
                    $n = $token->attr["name"];
                    if (!isset($this->objectStack[$i]->attr["data"]) && ($token->attr["name"] == "movie" || $token->attr["name"] == "src")) {
                        $this->objectStack[$i]->attr["data"] = $token->attr["value"];
                    }
                    if (!isset($this->paramStack[$i][$n]) && isset($this->addParam[$n]) && $token->attr["name"] === $this->addParam[$n]) {
                        $this->paramStack[$i][$n] = true;
                    } else {
                        if (!isset($this->allowedParam[strtolower($n)])) {
                            $token = false;
                        }
                    }
                } else {
                    $token = false;
                }
            }
        }
    }
    public function handleEnd(&$token)
    {
        if ($token->name == "object") {
            array_pop($this->objectStack);
            array_pop($this->paramStack);
        }
    }
}
class HTMLPurifier_Lexer_DOMLex extends HTMLPurifier_Lexer
{
    private $factory = NULL;
    public function __construct()
    {
        parent::__construct();
        $this->factory = new HTMLPurifier_TokenFactory();
    }
    public function tokenizeHTML($html, $config, $context)
    {
        $html = $this->normalize($html, $config, $context);
        if ($config->get("Core.AggressivelyFixLt")) {
            $char = "[^a-z!\\/]";
            $comment = "/<!--(.*?)(-->|\\z)/is";
            do {
                $html = preg_replace_callback($comment, [$this, "callbackArmorCommentEntities"], $html);
                $old = $html;
                $html = preg_replace("/<(" . $char . ")/i", "&lt;\\1", $html);
            } while ($html === $old);
            $html = preg_replace_callback($comment, [$this, "callbackUndoCommentSubst"], $html);
        }
        $html = $this->wrapHTML($html, $config, $context);
        $doc = new DOMDocument();
        $doc->encoding = "UTF-8";
        $options = 0;
        if ($config->get("Core.AllowParseManyTags") && defined("LIBXML_PARSEHUGE")) {
            $options |= LIBXML_PARSEHUGE;
        }
        set_error_handler([$this, "muteErrorHandler"]);
        if ($options) {
            $doc->loadHTML($html, $options);
        } else {
            $doc->loadHTML($html);
        }
        restore_error_handler();
        $body = $doc->getElementsByTagName("html")->item(0)->getElementsByTagName("body")->item(0);
        $div = $body->getElementsByTagName("div")->item(0);
        $tokens = [];
        $this->tokenizeDOM($div, $tokens, $config);
        if ($div->nextSibling) {
            $body->removeChild($div);
            $this->tokenizeDOM($body, $tokens, $config);
        }
        return $tokens;
    }
    protected function tokenizeDOM($node, &$tokens, $config)
    {
        $level = 0;
        $nodes = [$level => new HTMLPurifier_Queue([$node])];
        do {
            $closingNodes = [];
            while (!$nodes[$level]->isEmpty()) {
                $node = $nodes[$level]->shift();
                $collect = 0 < $level ? true : false;
                $needEndingTag = $this->createStartNode($node, $tokens, $collect, $config);
                if ($needEndingTag) {
                    $closingNodes[$level][] = $node;
                }
                if ($node->childNodes && $node->childNodes->length) {
                    $level++;
                    $nodes[$level] = new HTMLPurifier_Queue();
                    foreach ($node->childNodes as $childNode) {
                        $nodes[$level]->push($childNode);
                    }
                }
            }
            $level--;
            if ($level && isset($closingNodes[$level])) {
                while ($node = array_pop($closingNodes[$level])) {
                    $this->createEndNode($node, $tokens);
                }
            }
        } while (0 >= $level);
    }
    protected function getTagName($node)
    {
        if (isset($node->tagName)) {
            return $node->tagName;
        }
        if (isset($node->nodeName)) {
            return $node->nodeName;
        }
        if (isset($node->localName)) {
            return $node->localName;
        }
        return NULL;
    }
    protected function getData($node)
    {
        if (isset($node->data)) {
            return $node->data;
        }
        if (isset($node->nodeValue)) {
            return $node->nodeValue;
        }
        if (isset($node->textContent)) {
            return $node->textContent;
        }
        return NULL;
    }
    protected function createStartNode($node, &$tokens, $collect, $config)
    {
        if ($node->nodeType === XML_TEXT_NODE) {
            $data = $this->getData($node);
            if ($data !== NULL) {
                $tokens[] = $this->factory->createText($data);
            }
            return false;
        }
        if ($node->nodeType === XML_CDATA_SECTION_NODE) {
            $last = end($tokens);
            $data = $node->data;
            if ($last instanceof HTMLPurifier_Token_Start && ($last->name == "script" || $last->name == "style")) {
                $new_data = trim($data);
                if (substr($new_data, 0, 4) === "<!--") {
                    $data = substr($new_data, 4);
                    if (substr($data, -3) === "-->") {
                        $data = substr($data, 0, -3);
                    }
                }
            }
            $tokens[] = $this->factory->createText($this->parseText($data, $config));
            return false;
        }
        if ($node->nodeType === XML_COMMENT_NODE) {
            $tokens[] = $this->factory->createComment($node->data);
            return false;
        }
        if ($node->nodeType !== XML_ELEMENT_NODE) {
            return false;
        }
        $attr = $node->hasAttributes() ? $this->transformAttrToAssoc($node->attributes) : [];
        $tag_name = $this->getTagName($node);
        if (empty($tag_name)) {
            return (bool) $node->childNodes->length;
        }
        if (!$node->childNodes->length) {
            if ($collect) {
                $tokens[] = $this->factory->createEmpty($tag_name, $attr);
            }
            return false;
        }
        if ($collect) {
            $tokens[] = $this->factory->createStart($tag_name, $attr);
        }
        return true;
    }
    protected function createEndNode($node, &$tokens)
    {
        $tag_name = $this->getTagName($node);
        $tokens[] = $this->factory->createEnd($tag_name);
    }
    protected function transformAttrToAssoc($node_map)
    {
        if ($node_map->length === 0) {
            return [];
        }
        $array = [];
        foreach ($node_map as $attr) {
            $array[$attr->name] = $attr->value;
        }
        return $array;
    }
    public function muteErrorHandler($errno, $errstr)
    {
    }
    public function callbackUndoCommentSubst($matches)
    {
        return "<!--" . strtr($matches[1], ["&amp;" => "&", "&lt;" => "<"]) . $matches[2];
    }
    public function callbackArmorCommentEntities($matches)
    {
        return "<!--" . str_replace("&", "&amp;", $matches[1]) . $matches[2];
    }
    protected function wrapHTML($html, $config, $context, $use_div = true)
    {
        $def = $config->getDefinition("HTML");
        $ret = "";
        if (!empty($def->doctype->dtdPublic) || !empty($def->doctype->dtdSystem)) {
            $ret .= "<!DOCTYPE html ";
            if (!empty($def->doctype->dtdPublic)) {
                $ret .= "PUBLIC \"" . $def->doctype->dtdPublic . "\" ";
            }
            if (!empty($def->doctype->dtdSystem)) {
                $ret .= "\"" . $def->doctype->dtdSystem . "\" ";
            }
            $ret .= ">";
        }
        $ret .= "<html><head>";
        $ret .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />";
        $ret .= "</head><body>";
        if ($use_div) {
            $ret .= "<div>";
        }
        $ret .= $html;
        if ($use_div) {
            $ret .= "</div>";
        }
        $ret .= "</body></html>";
        return $ret;
    }
}
class HTMLPurifier_Lexer_DirectLex extends HTMLPurifier_Lexer
{
    public $tracksLineNumbers = true;
    protected $_whitespace = " \t\r\n";
    protected function scriptCallback($matches)
    {
        return $matches[1] . htmlspecialchars($matches[2], ENT_COMPAT, "UTF-8") . $matches[3];
    }
    public function tokenizeHTML($html, $config, $context)
    {
        if ($config->get("HTML.Trusted")) {
            $html = preg_replace_callback("#(<script[^>]*>)(\\s*[^<].+?)(</script>)#si", [$this, "scriptCallback"], $html);
        }
        $html = $this->normalize($html, $config, $context);
        $cursor = 0;
        $inside_tag = false;
        $array = [];
        $maintain_line_numbers = $config->get("Core.MaintainLineNumbers");
        if ($maintain_line_numbers === NULL) {
            $maintain_line_numbers = $config->get("Core.CollectErrors");
        }
        if ($maintain_line_numbers) {
            $current_line = 1;
            $current_col = 0;
            $length = strlen($html);
        } else {
            $current_line = false;
            $current_col = false;
            $length = false;
        }
        $context->register("CurrentLine", $current_line);
        $context->register("CurrentCol", $current_col);
        $nl = "\n";
        $synchronize_interval = $config->get("Core.DirectLexLineNumberSyncInterval");
        $e = false;
        if ($config->get("Core.CollectErrors")) {
            $e =& $context->get("ErrorCollector");
        }
        $loops = 0;
        while (++$loops) {
            if ($maintain_line_numbers) {
                $rcursor = $cursor - (int) $inside_tag;
                $nl_pos = strrpos($html, $nl, $rcursor - $length);
                $current_col = $rcursor - (is_bool($nl_pos) ? 0 : $nl_pos + 1);
                if ($synchronize_interval && 0 < $cursor && $loops % $synchronize_interval === 0) {
                    $current_line = 1 + $this->substrCount($html, $nl, 0, $cursor);
                }
            }
            $position_next_lt = strpos($html, "<", $cursor);
            $position_next_gt = strpos($html, ">", $cursor);
            if ($position_next_lt === $cursor) {
                $inside_tag = true;
                $cursor++;
            }
            if (!$inside_tag && $position_next_lt !== false) {
                $token = new HTMLPurifier_Token_Text($this->parseText(substr($html, $cursor, $position_next_lt - $cursor), $config));
                if ($maintain_line_numbers) {
                    $token->rawPosition($current_line, $current_col);
                    $current_line += $this->substrCount($html, $nl, $cursor, $position_next_lt - $cursor);
                }
                $array[] = $token;
                $cursor = $position_next_lt + 1;
                $inside_tag = true;
            } else {
                if (!$inside_tag) {
                    if ($cursor !== strlen($html)) {
                        $token = new HTMLPurifier_Token_Text($this->parseText(substr($html, $cursor), $config));
                        if ($maintain_line_numbers) {
                            $token->rawPosition($current_line, $current_col);
                        }
                        $array[] = $token;
                    }
                } else {
                    if ($inside_tag && $position_next_gt !== false) {
                        $strlen_segment = $position_next_gt - $cursor;
                        if ($strlen_segment < 1) {
                            $token = new HTMLPurifier_Token_Text("<");
                            $cursor++;
                        } else {
                            $segment = substr($html, $cursor, $strlen_segment);
                            if ($segment !== false) {
                                if (substr($segment, 0, 3) === "!--") {
                                    $position_comment_end = strpos($html, "-->", $cursor);
                                    if ($position_comment_end === false) {
                                        if ($e) {
                                            $e->send(2, "Lexer: Unclosed comment");
                                        }
                                        $position_comment_end = strlen($html);
                                        $end = true;
                                    } else {
                                        $end = false;
                                    }
                                    $strlen_segment = $position_comment_end - $cursor;
                                    $segment = substr($html, $cursor, $strlen_segment);
                                    $token = new HTMLPurifier_Token_Comment(substr($segment, 3, $strlen_segment - 3));
                                    if ($maintain_line_numbers) {
                                        $token->rawPosition($current_line, $current_col);
                                        $current_line += $this->substrCount($html, $nl, $cursor, $strlen_segment);
                                    }
                                    $array[] = $token;
                                    $cursor = $end ? $position_comment_end : $position_comment_end + 3;
                                    $inside_tag = false;
                                } else {
                                    $is_end_tag = strpos($segment, "/") === 0;
                                    if ($is_end_tag) {
                                        $type = substr($segment, 1);
                                        $token = new HTMLPurifier_Token_End($type);
                                        if ($maintain_line_numbers) {
                                            $token->rawPosition($current_line, $current_col);
                                            $current_line += $this->substrCount($html, $nl, $cursor, $position_next_gt - $cursor);
                                        }
                                        $array[] = $token;
                                        $inside_tag = false;
                                        $cursor = $position_next_gt + 1;
                                    } else {
                                        if (!ctype_alpha($segment[0])) {
                                            if ($e) {
                                                $e->send(8, "Lexer: Unescaped lt");
                                            }
                                            $token = new HTMLPurifier_Token_Text("<");
                                            if ($maintain_line_numbers) {
                                                $token->rawPosition($current_line, $current_col);
                                                $current_line += $this->substrCount($html, $nl, $cursor, $position_next_gt - $cursor);
                                            }
                                            $array[] = $token;
                                            $inside_tag = false;
                                        } else {
                                            $is_self_closing = strrpos($segment, "/") === $strlen_segment - 1;
                                            if ($is_self_closing) {
                                                $strlen_segment--;
                                                $segment = substr($segment, 0, $strlen_segment);
                                            }
                                            $position_first_space = strcspn($segment, $this->_whitespace);
                                            if ($strlen_segment <= $position_first_space) {
                                                if ($is_self_closing) {
                                                    $token = new HTMLPurifier_Token_Empty($segment);
                                                } else {
                                                    $token = new HTMLPurifier_Token_Start($segment);
                                                }
                                                if ($maintain_line_numbers) {
                                                    $token->rawPosition($current_line, $current_col);
                                                    $current_line += $this->substrCount($html, $nl, $cursor, $position_next_gt - $cursor);
                                                }
                                                $array[] = $token;
                                                $inside_tag = false;
                                                $cursor = $position_next_gt + 1;
                                            } else {
                                                $type = substr($segment, 0, $position_first_space);
                                                $attribute_string = trim(substr($segment, $position_first_space));
                                                if ($attribute_string) {
                                                    $attr = $this->parseAttributeString($attribute_string, $config, $context);
                                                } else {
                                                    $attr = [];
                                                }
                                                if ($is_self_closing) {
                                                    $token = new HTMLPurifier_Token_Empty($type, $attr);
                                                } else {
                                                    $token = new HTMLPurifier_Token_Start($type, $attr);
                                                }
                                                if ($maintain_line_numbers) {
                                                    $token->rawPosition($current_line, $current_col);
                                                    $current_line += $this->substrCount($html, $nl, $cursor, $position_next_gt - $cursor);
                                                }
                                                $array[] = $token;
                                                $cursor = $position_next_gt + 1;
                                                $inside_tag = false;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        if ($e) {
                            $e->send(2, "Lexer: Missing gt");
                        }
                        $token = new HTMLPurifier_Token_Text("<" . $this->parseText(substr($html, $cursor), $config));
                        if ($maintain_line_numbers) {
                            $token->rawPosition($current_line, $current_col);
                        }
                        $array[] = $token;
                    }
                }
            }
        }
        $context->destroy("CurrentLine");
        $context->destroy("CurrentCol");
        return $array;
    }
    protected function substrCount($haystack, $needle, $offset, $length)
    {
        if ($oldVersion === NULL) {
            $oldVersion = version_compare(PHP_VERSION, "5.1", "<");
        }
        if ($oldVersion) {
            $haystack = substr($haystack, $offset, $length);
            return substr_count($haystack, $needle);
        }
        return substr_count($haystack, $needle, $offset, $length);
    }
    public function parseAttributeString($string, $config, $context)
    {
        $string = (string) $string;
        if ($string == "") {
            return [];
        }
        $e = false;
        if ($config->get("Core.CollectErrors")) {
            $e =& $context->get("ErrorCollector");
        }
        $num_equal = substr_count($string, "=");
        $has_space = strpos($string, " ");
        if ($num_equal === 0 && !$has_space) {
            return [$string => $string];
        }
        if ($num_equal === 1 && !$has_space) {
            list($key, $quoted_value) = explode("=", $string);
            $quoted_value = trim($quoted_value);
            if (!$key) {
                if ($e) {
                    $e->send(1, "Lexer: Missing attribute key");
                }
                return [];
            }
            if (!$quoted_value) {
                return [$key => ""];
            }
            $first_char = $quoted_value[0];
            $last_char = $quoted_value[@strlen($quoted_value) - 1];
            $same_quote = $first_char == $last_char;
            $open_quote = $first_char == "\"" || $first_char == "'";
            if ($same_quote && $open_quote) {
                $value = substr($quoted_value, 1, strlen($quoted_value) - 2);
            } else {
                if ($open_quote) {
                    if ($e) {
                        $e->send(1, "Lexer: Missing end quote");
                    }
                    $value = substr($quoted_value, 1);
                } else {
                    $value = $quoted_value;
                }
            }
            if ($value === false) {
                $value = "";
            }
            return [$key => $this->parseAttr($value, $config)];
        }
        $array = [];
        $cursor = 0;
        $size = strlen($string);
        $string .= " ";
        $old_cursor = -1;
        while ($cursor < $size) {
            if ($cursor <= $old_cursor) {
                throw new Exception("Infinite loop detected");
            }
            $old_cursor = $cursor;
            $cursor += $value = strspn($string, $this->_whitespace, $cursor);
            $key_begin = $cursor;
            $cursor += strcspn($string, $this->_whitespace . "=", $cursor);
            $key_end = $cursor;
            $key = substr($string, $key_begin, $key_end - $key_begin);
            if (!$key) {
                if ($e) {
                    $e->send(1, "Lexer: Missing attribute key");
                }
                $cursor += 1 + strcspn($string, $this->_whitespace, $cursor + 1);
            } else {
                $cursor += strspn($string, $this->_whitespace, $cursor);
                if ($size <= $cursor) {
                    $array[$key] = $key;
                } else {
                    $first_char = $string[$cursor];
                    if ($first_char == "=") {
                        $cursor++;
                        $cursor += strspn($string, $this->_whitespace, $cursor);
                        if ($cursor === false) {
                            $array[$key] = "";
                        } else {
                            $char = $string[$cursor];
                            if ($char == "\"" || $char == "'") {
                                $cursor++;
                                $value_begin = $cursor;
                                $cursor = strpos($string, $char, $cursor);
                                $value_end = $cursor;
                            } else {
                                $value_begin = $cursor;
                                $cursor += strcspn($string, $this->_whitespace, $cursor);
                                $value_end = $cursor;
                            }
                            if ($cursor === false) {
                                $cursor = $size;
                                $value_end = $cursor;
                            }
                            $value = substr($string, $value_begin, $value_end - $value_begin);
                            if ($value === false) {
                                $value = "";
                            }
                            $array[$key] = $this->parseAttr($value, $config);
                            $cursor++;
                        }
                    } else {
                        if ($key !== "") {
                            $array[$key] = $key;
                        } else {
                            if ($e) {
                                $e->send(1, "Lexer: Missing attribute key");
                            }
                        }
                    }
                }
            }
        }
        return $array;
    }
}
class HTMLPurifier_Node_Comment extends HTMLPurifier_Node
{
    public $data = NULL;
    public $is_whitespace = true;
    public function __construct($data, $line = NULL, $col = NULL)
    {
        $this->data = $data;
        $this->line = $line;
        $this->col = $col;
    }
    public function toTokenPair()
    {
        return [new HTMLPurifier_Token_Comment($this->data, $this->line, $this->col), NULL];
    }
}
class HTMLPurifier_Node_Element extends HTMLPurifier_Node
{
    public $name = NULL;
    public $attr = [];
    public $children = [];
    public $empty = false;
    public $endCol = NULL;
    public $endLine = NULL;
    public $endArmor = [];
    public function __construct($name, $attr = [], $line = NULL, $col = NULL, $armor = [])
    {
        $this->name = $name;
        $this->attr = $attr;
        $this->line = $line;
        $this->col = $col;
        $this->armor = $armor;
    }
    public function toTokenPair()
    {
        if ($this->empty) {
            return [new HTMLPurifier_Token_Empty($this->name, $this->attr, $this->line, $this->col, $this->armor), NULL];
        }
        $start = new HTMLPurifier_Token_Start($this->name, $this->attr, $this->line, $this->col, $this->armor);
        $end = new HTMLPurifier_Token_End($this->name, [], $this->endLine, $this->endCol, $this->endArmor);
        return [$start, $end];
    }
}
class HTMLPurifier_Node_Text extends HTMLPurifier_Node
{
    public $name = "#PCDATA";
    public $data = NULL;
    public $is_whitespace = NULL;
    public function __construct($data, $is_whitespace, $line = NULL, $col = NULL)
    {
        $this->data = $data;
        $this->is_whitespace = $is_whitespace;
        $this->line = $line;
        $this->col = $col;
    }
    public function toTokenPair()
    {
        return [new HTMLPurifier_Token_Text($this->data, $this->line, $this->col), NULL];
    }
}
abstract class HTMLPurifier_Strategy_Composite extends HTMLPurifier_Strategy
{
    protected $strategies = [];
    public function execute($tokens, $config, $context)
    {
        foreach ($this->strategies as $strategy) {
            $tokens = $strategy->execute($tokens, $config, $context);
        }
        return $tokens;
    }
}
class HTMLPurifier_Strategy_Core extends HTMLPurifier_Strategy_Composite
{
    public function __construct()
    {
        $this->strategies[] = new HTMLPurifier_Strategy_RemoveForeignElements();
        $this->strategies[] = new HTMLPurifier_Strategy_MakeWellFormed();
        $this->strategies[] = new HTMLPurifier_Strategy_FixNesting();
        $this->strategies[] = new HTMLPurifier_Strategy_ValidateAttributes();
    }
}
class HTMLPurifier_Strategy_FixNesting extends HTMLPurifier_Strategy
{
    public function execute($tokens, $config, $context)
    {
        $top_node = HTMLPurifier_Arborize::arborize($tokens, $config, $context);
        $definition = $config->getHTMLDefinition();
        $excludes_enabled = !$config->get("Core.DisableExcludes");
        $is_inline = $definition->info_parent_def->descendants_are_inline;
        $context->register("IsInline", $is_inline);
        $e =& $context->get("ErrorCollector", true);
        $exclude_stack = [$definition->info_parent_def->excludes];
        $node = $top_node;
        list($token, $d) = $node->toTokenPair();
        $context->register("CurrentNode", $node);
        $context->register("CurrentToken", $token);
        $parent_def = $definition->info_parent_def;
        $stack = [[$top_node, $parent_def->descendants_are_inline, $parent_def->excludes, 0]];
        while (!empty($stack)) {
            list($node, $is_inline, $excludes, $ix) = array_pop($stack);
            $go = false;
            $def = empty($stack) ? $definition->info_parent_def : $definition->info[$node->name];
            while (isset($node->children[$ix])) {
                $child = $node->children[$ix++];
                if ($child instanceof HTMLPurifier_Node_Element) {
                    $go = true;
                    $stack[] = [$node, $is_inline, $excludes, $ix];
                    $stack[] = [$child, $is_inline || $def->descendants_are_inline, empty($def->excludes) ? $excludes : array_merge($excludes, $def->excludes), 0];
                }
            }
            if (!$go) {
                list($token, $d) = $node->toTokenPair();
                if ($excludes_enabled && isset($excludes[$node->name])) {
                    $node->dead = true;
                    if ($e) {
                        $e->send(1, "Strategy_FixNesting: Node excluded");
                    }
                } else {
                    $children = [];
                    foreach ($node->children as $child) {
                        if (!$child->dead) {
                            $children[] = $child;
                        }
                    }
                    $result = $def->child->validateChildren($children, $config, $context);
                    if ($result === true) {
                        $node->children = $children;
                    } else {
                        if ($result === false) {
                            $node->dead = true;
                            if ($e) {
                                $e->send(1, "Strategy_FixNesting: Node removed");
                            }
                        } else {
                            $node->children = $result;
                            if ($e) {
                                if (empty($result) && !empty($children)) {
                                    $e->send(1, "Strategy_FixNesting: Node contents removed");
                                } else {
                                    if ($result != $children) {
                                        $e->send(2, "Strategy_FixNesting: Node reorganized");
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $context->destroy("IsInline");
        $context->destroy("CurrentNode");
        $context->destroy("CurrentToken");
        return HTMLPurifier_Arborize::flatten($node, $config, $context);
    }
}
class HTMLPurifier_Strategy_MakeWellFormed extends HTMLPurifier_Strategy
{
    protected $tokens = NULL;
    protected $token = NULL;
    protected $zipper = NULL;
    protected $stack = NULL;
    protected $injectors = NULL;
    protected $config = NULL;
    protected $context = NULL;
    public function execute($tokens, $config, $context)
    {
        $definition = $config->getHTMLDefinition();
        $generator = new HTMLPurifier_Generator($config, $context);
        $escape_invalid_tags = $config->get("Core.EscapeInvalidTags");
        $global_parent_allowed_elements = $definition->info_parent_def->child->getAllowedElements($config);
        $e = $context->get("ErrorCollector", true);
        $i = false;
        list($zipper, $token) = HTMLPurifier_Zipper::fromArray($tokens);
        if ($token === NULL) {
            return [];
        }
        $reprocess = false;
        $stack = [];
        $this->stack =& $stack;
        $this->tokens =& $tokens;
        $this->token =& $token;
        $this->zipper =& $zipper;
        $this->config = $config;
        $this->context = $context;
        $context->register("CurrentNesting", $stack);
        $context->register("InputZipper", $zipper);
        $context->register("CurrentToken", $token);
        $this->injectors = [];
        $injectors = $config->getBatch("AutoFormat");
        $def_injectors = $definition->info_injector;
        $custom_injectors = $injectors["Custom"];
        unset($injectors["Custom"]);
        foreach ($injectors as $injector => $b) {
            if (strpos($injector, ".") === false) {
                $injector = "HTMLPurifier_Injector_" . $injector;
                if ($b) {
                    $this->injectors[] = new $injector();
                }
            }
        }
        foreach ($def_injectors as $injector) {
            $this->injectors[] = $injector;
        }
        foreach ($custom_injectors as $injector) {
            if ($injector) {
                if (is_string($injector)) {
                    $injector = "HTMLPurifier_Injector_" . $injector;
                    $injector = new $injector();
                }
                $this->injectors[] = $injector;
            }
        }
        foreach ($this->injectors as $ix => $injector) {
            $error = $injector->prepare($config, $context);
            if ($error) {
                array_splice($this->injectors, $ix, 1);
                trigger_error("Cannot enable " . $injector->name . " injector because " . $error . " is not allowed", 512);
            }
        }
        while (true) {
            if (is_int($i)) {
                $rewind_offset = $this->injectors[$i]->getRewindOffset();
                if (is_int($rewind_offset)) {
                    $j = 0;
                    while ($j < $rewind_offset) {
                        if (!empty($zipper->front)) {
                            $token = $zipper->prev($token);
                            unset($token->skip[$i]);
                            $token->rewind = $i;
                            if ($token instanceof HTMLPurifier_Token_Start) {
                                array_pop($this->stack);
                            } else {
                                if ($token instanceof HTMLPurifier_Token_End) {
                                    $this->stack[] = $token->start;
                                }
                            }
                            $j++;
                        }
                    }
                }
                $i = false;
            }
            if ($token === NULL) {
                if (!empty($this->stack)) {
                    $top_nesting = array_pop($this->stack);
                    $this->stack[] = $top_nesting;
                    if ($e && !isset($top_nesting->armor["MakeWellFormed_TagClosedError"])) {
                        $e->send(8, "Strategy_MakeWellFormed: Tag closed by document end", $top_nesting);
                    }
                    $token = new HTMLPurifier_Token_End($top_nesting->name);
                    $reprocess = true;
                }
            } else {
                if (empty($token->is_tag)) {
                    if ($token instanceof HTMLPurifier_Token_Text) {
                        foreach ($this->injectors as $i => $injector) {
                            if (!isset($token->skip[$i])) {
                                if (!($token->rewind !== NULL && $token->rewind !== $i)) {
                                    $r = $token;
                                    $injector->handleText($r);
                                    $token = $this->processToken($r, $i);
                                    $reprocess = true;
                                }
                            }
                        }
                    }
                } else {
                    if (isset($definition->info[$token->name])) {
                        $type = $definition->info[$token->name]->child->type;
                    } else {
                        $type = false;
                    }
                    $ok = false;
                    if ($type === "empty" && $token instanceof HTMLPurifier_Token_Start) {
                        $token = new HTMLPurifier_Token_Empty($token->name, $token->attr, $token->line, $token->col, $token->armor);
                        $ok = true;
                    } else {
                        if ($type && $type !== "empty" && $token instanceof HTMLPurifier_Token_Empty) {
                            $old_token = $token;
                            $token = new HTMLPurifier_Token_End($token->name);
                            $token = $this->insertBefore(new HTMLPurifier_Token_Start($old_token->name, $old_token->attr, $old_token->line, $old_token->col, $old_token->armor));
                            $reprocess = true;
                        } else {
                            if ($token instanceof HTMLPurifier_Token_Empty) {
                                $ok = true;
                            } else {
                                if ($token instanceof HTMLPurifier_Token_Start) {
                                    if (!empty($this->stack)) {
                                        $parent = array_pop($this->stack);
                                        $this->stack[] = $parent;
                                        $parent_def = NULL;
                                        $parent_elements = NULL;
                                        $autoclose = false;
                                        if (isset($definition->info[$parent->name])) {
                                            $parent_def = $definition->info[$parent->name];
                                            $parent_elements = $parent_def->child->getAllowedElements($config);
                                            $autoclose = !isset($parent_elements[$token->name]);
                                        }
                                        if ($autoclose && $definition->info[$token->name]->wrap) {
                                            $wrapname = $definition->info[$token->name]->wrap;
                                            $wrapdef = $definition->info[$wrapname];
                                            $elements = $wrapdef->child->getAllowedElements($config);
                                            if (isset($elements[$token->name]) && isset($parent_elements[$wrapname])) {
                                                $newtoken = new HTMLPurifier_Token_Start($wrapname);
                                                $token = $this->insertBefore($newtoken);
                                                $reprocess = true;
                                            }
                                        }
                                        $carryover = false;
                                        if ($autoclose && $parent_def->formatting) {
                                            $carryover = true;
                                        }
                                        if ($autoclose) {
                                            $autoclose_ok = isset($global_parent_allowed_elements[$token->name]);
                                            if (!$autoclose_ok) {
                                                foreach ($this->stack as $ancestor) {
                                                    $elements = $definition->info[$ancestor->name]->child->getAllowedElements($config);
                                                    if (isset($elements[$token->name])) {
                                                        $autoclose_ok = true;
                                                    } else {
                                                        if ($definition->info[$token->name]->wrap) {
                                                            $wrapname = $definition->info[$token->name]->wrap;
                                                            $wrapdef = $definition->info[$wrapname];
                                                            $wrap_elements = $wrapdef->child->getAllowedElements($config);
                                                            if (isset($wrap_elements[$token->name]) && isset($elements[$wrapname])) {
                                                                $autoclose_ok = true;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                            if ($autoclose_ok) {
                                                $new_token = new HTMLPurifier_Token_End($parent->name);
                                                $new_token->start = $parent;
                                                if ($e && !isset($parent->armor["MakeWellFormed_TagClosedError"])) {
                                                    if (!$carryover) {
                                                        $e->send(8, "Strategy_MakeWellFormed: Tag auto closed", $parent);
                                                    } else {
                                                        $e->send(8, "Strategy_MakeWellFormed: Tag carryover", $parent);
                                                    }
                                                }
                                                if ($carryover) {
                                                    $element = clone $parent;
                                                    $element->armor["MakeWellFormed_TagClosedError"] = true;
                                                    $element->carryover = true;
                                                    $token = $this->processToken([$new_token, $token, $element]);
                                                } else {
                                                    $token = $this->insertBefore($new_token);
                                                }
                                            } else {
                                                $token = $this->remove();
                                            }
                                            $reprocess = true;
                                        }
                                    }
                                    $ok = true;
                                }
                            }
                        }
                    }
                    if ($ok) {
                        foreach ($this->injectors as $i => $injector) {
                            if (!isset($token->skip[$i])) {
                                if (!($token->rewind !== NULL && $token->rewind !== $i)) {
                                    $r = $token;
                                    $injector->handleElement($r);
                                    $token = $this->processToken($r, $i);
                                    $reprocess = true;
                                    if (!$reprocess) {
                                        if ($token instanceof HTMLPurifier_Token_Start) {
                                            $this->stack[] = $token;
                                        } else {
                                            if ($token instanceof HTMLPurifier_Token_End) {
                                                throw new HTMLPurifier_Exception("Improper handling of end tag in start code; possible error in MakeWellFormed");
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        if (!$token instanceof HTMLPurifier_Token_End) {
                            throw new HTMLPurifier_Exception("Unaccounted for tag token in input stream, bug in HTML Purifier");
                        }
                        if (empty($this->stack)) {
                            if ($escape_invalid_tags) {
                                if ($e) {
                                    $e->send(2, "Strategy_MakeWellFormed: Unnecessary end tag to text");
                                }
                                $token = new HTMLPurifier_Token_Text($generator->generateFromToken($token));
                            } else {
                                if ($e) {
                                    $e->send(2, "Strategy_MakeWellFormed: Unnecessary end tag removed");
                                }
                                $token = $this->remove();
                            }
                            $reprocess = true;
                        } else {
                            $current_parent = array_pop($this->stack);
                            if ($current_parent->name == $token->name) {
                                $token->start = $current_parent;
                                foreach ($this->injectors as $i => $injector) {
                                    if (!isset($token->skip[$i])) {
                                        if (!($token->rewind !== NULL && $token->rewind !== $i)) {
                                            $r = $token;
                                            $injector->handleEnd($r);
                                            $token = $this->processToken($r, $i);
                                            $this->stack[] = $current_parent;
                                            $reprocess = true;
                                        }
                                    }
                                }
                            } else {
                                $this->stack[] = $current_parent;
                                $size = count($this->stack);
                                $skipped_tags = false;
                                $j = $size - 2;
                                while (0 <= $j) {
                                    if ($this->stack[$j]->name == $token->name) {
                                        $skipped_tags = array_slice($this->stack, $j);
                                    } else {
                                        $j--;
                                    }
                                }
                                if ($skipped_tags === false) {
                                    if ($escape_invalid_tags) {
                                        if ($e) {
                                            $e->send(2, "Strategy_MakeWellFormed: Stray end tag to text");
                                        }
                                        $token = new HTMLPurifier_Token_Text($generator->generateFromToken($token));
                                    } else {
                                        if ($e) {
                                            $e->send(2, "Strategy_MakeWellFormed: Stray end tag removed");
                                        }
                                        $token = $this->remove();
                                    }
                                    $reprocess = true;
                                } else {
                                    $c = count($skipped_tags);
                                    if ($e) {
                                        for ($j = $c - 1; 0 < $j; $j--) {
                                            if (!isset($skipped_tags[$j]->armor["MakeWellFormed_TagClosedError"])) {
                                                $e->send(8, "Strategy_MakeWellFormed: Tag closed by element end", $skipped_tags[$j]);
                                            }
                                        }
                                    }
                                    $replace = [$token];
                                    for ($j = 1; $j < $c; $j++) {
                                        $new_token = new HTMLPurifier_Token_End($skipped_tags[$j]->name);
                                        $new_token->start = $skipped_tags[$j];
                                        array_unshift($replace, $new_token);
                                        if (isset($definition->info[$new_token->name]) && $definition->info[$new_token->name]->formatting) {
                                            $element = clone $skipped_tags[$j];
                                            $element->carryover = true;
                                            $element->armor["MakeWellFormed_TagClosedError"] = true;
                                            $replace[] = $element;
                                        }
                                    }
                                    $token = $this->processToken($replace);
                                    $reprocess = true;
                                }
                            }
                        }
                    }
                }
            }
            $reprocess ? $reprocess = false : ($token = $zipper->next($token));
        }
        $context->destroy("CurrentToken");
        $context->destroy("CurrentNesting");
        $context->destroy("InputZipper");
        unset($this->injectors);
        unset($this->stack);
        unset($this->tokens);
        return $zipper->toArray($token);
    }
    protected function processToken($token, $injector = -1)
    {
        if (is_object($token)) {
            $tmp = $token;
            $token = [1, $tmp];
        }
        if (is_int($token)) {
            $tmp = $token;
            $token = [$tmp];
        }
        if ($token === false) {
            $token = [1];
        }
        if (!is_array($token)) {
            throw new HTMLPurifier_Exception("Invalid token type from injector");
        }
        if (!is_int($token[0])) {
            array_unshift($token, 1);
        }
        if ($token[0] === 0) {
            throw new HTMLPurifier_Exception("Deleting zero tokens is not valid");
        }
        $delete = array_shift($token);
        list($old, $r) = $this->zipper->splice($this->token, $delete, $token);
        if (-1 < $injector) {
            $oldskip = isset($old[0]) ? $old[0]->skip : [];
            foreach ($token as $object) {
                $object->skip = $oldskip;
                $object->skip[$injector] = true;
            }
        }
        return $r;
    }
    private function insertBefore($token)
    {
        $splice = $this->zipper->splice($this->token, 0, [$token]);
        return $splice[1];
    }
    private function remove()
    {
        return $this->zipper->delete();
    }
}
class HTMLPurifier_Strategy_RemoveForeignElements extends HTMLPurifier_Strategy
{
    public function execute($tokens, $config, $context)
    {
        $definition = $config->getHTMLDefinition();
        $generator = new HTMLPurifier_Generator($config, $context);
        $result = [];
        $escape_invalid_tags = $config->get("Core.EscapeInvalidTags");
        $remove_invalid_img = $config->get("Core.RemoveInvalidImg");
        $trusted = $config->get("HTML.Trusted");
        $comment_lookup = $config->get("HTML.AllowedComments");
        $comment_regexp = $config->get("HTML.AllowedCommentsRegexp");
        $check_comments = $comment_lookup !== [] || $comment_regexp !== NULL;
        $remove_script_contents = $config->get("Core.RemoveScriptContents");
        $hidden_elements = $config->get("Core.HiddenElements");
        if ($remove_script_contents === true) {
            $hidden_elements["script"] = true;
        } else {
            if ($remove_script_contents === false && isset($hidden_elements["script"])) {
                unset($hidden_elements["script"]);
            }
        }
        $attr_validator = new HTMLPurifier_AttrValidator();
        $remove_until = false;
        $textify_comments = false;
        $token = false;
        $context->register("CurrentToken", $token);
        $e = false;
        if ($config->get("Core.CollectErrors")) {
            $e =& $context->get("ErrorCollector");
        }
        foreach ($tokens as $token) {
            if (!($remove_until && (empty($token->is_tag) || $token->name !== $remove_until))) {
                if (!empty($token->is_tag)) {
                    if (isset($definition->info_tag_transform[$token->name])) {
                        $original_name = $token->name;
                        $token = $definition->info_tag_transform[$token->name]->transform($token, $config, $context);
                        if ($e) {
                            $e->send(8, "Strategy_RemoveForeignElements: Tag transform", $original_name);
                        }
                    }
                    if (isset($definition->info[$token->name])) {
                        if (($token instanceof HTMLPurifier_Token_Start || $token instanceof HTMLPurifier_Token_Empty) && $definition->info[$token->name]->required_attr && ($token->name != "img" || $remove_invalid_img)) {
                            $attr_validator->validateToken($token, $config, $context);
                            $ok = true;
                            foreach ($definition->info[$token->name]->required_attr as $name) {
                                if (!isset($token->attr[$name])) {
                                    $ok = false;
                                    if (!$ok) {
                                        if ($e) {
                                            $e->send(1, "Strategy_RemoveForeignElements: Missing required attribute", $name);
                                        }
                                    } else {
                                        $token->armor["ValidateAttributes"] = true;
                                    }
                                }
                            }
                        }
                        if (isset($hidden_elements[$token->name]) && $token instanceof HTMLPurifier_Token_Start) {
                            $textify_comments = $token->name;
                        } else {
                            if ($token->name === $textify_comments && $token instanceof HTMLPurifier_Token_End) {
                                $textify_comments = false;
                            }
                        }
                    } else {
                        if ($escape_invalid_tags) {
                            if ($e) {
                                $e->send(2, "Strategy_RemoveForeignElements: Foreign element to text");
                            }
                            $token = new HTMLPurifier_Token_Text($generator->generateFromToken($token));
                        } else {
                            if (isset($hidden_elements[$token->name])) {
                                if ($token instanceof HTMLPurifier_Token_Start) {
                                    $remove_until = $token->name;
                                } else {
                                    if (!$token instanceof HTMLPurifier_Token_Empty) {
                                        $remove_until = false;
                                    }
                                }
                                if ($e) {
                                    $e->send(1, "Strategy_RemoveForeignElements: Foreign meta element removed");
                                }
                            } else {
                                if ($e) {
                                    $e->send(1, "Strategy_RemoveForeignElements: Foreign element removed");
                                }
                            }
                        }
                    }
                } else {
                    if ($token instanceof HTMLPurifier_Token_Comment) {
                        if ($textify_comments !== false) {
                            $data = $token->data;
                            $token = new HTMLPurifier_Token_Text($data);
                        } else {
                            if ($trusted || $check_comments) {
                                $trailing_hyphen = false;
                                if ($e && substr($token->data, -1) == "-") {
                                    $trailing_hyphen = true;
                                }
                                $token->data = rtrim($token->data, "-");
                                $found_double_hyphen = false;
                                while (strpos($token->data, "--") !== false) {
                                    $found_double_hyphen = true;
                                    $token->data = str_replace("--", "-", $token->data);
                                }
                                if ($trusted || !empty($comment_lookup[trim($token->data)]) || $comment_regexp !== NULL && preg_match($comment_regexp, trim($token->data))) {
                                    if ($e) {
                                        if ($trailing_hyphen) {
                                            $e->send(8, "Strategy_RemoveForeignElements: Trailing hyphen in comment removed");
                                        }
                                        if ($found_double_hyphen) {
                                            $e->send(8, "Strategy_RemoveForeignElements: Hyphens in comment collapsed");
                                        }
                                    }
                                } else {
                                    if ($e) {
                                        $e->send(8, "Strategy_RemoveForeignElements: Comment removed");
                                    }
                                }
                            } else {
                                if ($e) {
                                    $e->send(8, "Strategy_RemoveForeignElements: Comment removed");
                                }
                            }
                        }
                    } else {
                        if (!$token instanceof HTMLPurifier_Token_Text) {
                        }
                    }
                }
                $result[] = $token;
            }
        }
        if ($remove_until && $e) {
            $e->send(1, "Strategy_RemoveForeignElements: Token removed to end", $remove_until);
        }
        $context->destroy("CurrentToken");
        return $result;
    }
}
class HTMLPurifier_Strategy_ValidateAttributes extends HTMLPurifier_Strategy
{
    public function execute($tokens, $config, $context)
    {
        $validator = new HTMLPurifier_AttrValidator();
        $token = false;
        $context->register("CurrentToken", $token);
        foreach ($tokens as $key => $token) {
            if ($token instanceof HTMLPurifier_Token_Start || $token instanceof HTMLPurifier_Token_Empty) {
                if (empty($token->armor["ValidateAttributes"])) {
                    $validator->validateToken($token, $config, $context);
                }
            }
        }
        $context->destroy("CurrentToken");
        return $tokens;
    }
}
class HTMLPurifier_TagTransform_Font extends HTMLPurifier_TagTransform
{
    public $transform_to = "span";
    protected $_size_lookup = ["xx-small", "xx-small", "small", "medium", "large", "x-large", "xx-large", "300%", "4294967295" => "smaller", "4294967294" => "60%", "+1" => "larger", "+2" => "150%", "+3" => "200%", "+4" => "300%"];
    public function transform($tag, $config, $context)
    {
        if ($tag instanceof HTMLPurifier_Token_End) {
            $new_tag = clone $tag;
            $new_tag->name = $this->transform_to;
            return $new_tag;
        }
        $attr = $tag->attr;
        $prepend_style = "";
        if (isset($attr["color"])) {
            $prepend_style .= "color:" . $attr["color"] . ";";
            unset($attr["color"]);
        }
        if (isset($attr["face"])) {
            $prepend_style .= "font-family:" . $attr["face"] . ";";
            unset($attr["face"]);
        }
        if (isset($attr["size"])) {
            if ($attr["size"] !== "") {
                if ($attr["size"][0] == "+" || $attr["size"][0] == "-") {
                    $size = (int) $attr["size"];
                    if ($size < -2) {
                        $attr["size"] = "-2";
                    }
                    if (4 < $size) {
                        $attr["size"] = "+4";
                    }
                } else {
                    $size = (int) $attr["size"];
                    if (7 < $size) {
                        $attr["size"] = "7";
                    }
                }
            }
            if (isset($this->_size_lookup[$attr["size"]])) {
                $prepend_style .= "font-size:" . $this->_size_lookup[$attr["size"]] . ";";
            }
            unset($attr["size"]);
        }
        if ($prepend_style) {
            $attr["style"] = isset($attr["style"]) ? $prepend_style . $attr["style"] : $prepend_style;
        }
        $new_tag = clone $tag;
        $new_tag->name = $this->transform_to;
        $new_tag->attr = $attr;
        return $new_tag;
    }
}
class HTMLPurifier_TagTransform_Simple extends HTMLPurifier_TagTransform
{
    protected $style = NULL;
    public function __construct($transform_to, $style = NULL)
    {
        $this->transform_to = $transform_to;
        $this->style = $style;
    }
    public function transform($tag, $config, $context)
    {
        $new_tag = clone $tag;
        $new_tag->name = $this->transform_to;
        if (!is_null($this->style) && ($new_tag instanceof HTMLPurifier_Token_Start || $new_tag instanceof HTMLPurifier_Token_Empty)) {
            $this->prependCSS($new_tag->attr, $this->style);
        }
        return $new_tag;
    }
}
class HTMLPurifier_Token_Comment extends HTMLPurifier_Token
{
    public $data = NULL;
    public $is_whitespace = true;
    public function __construct($data, $line = NULL, $col = NULL)
    {
        $this->data = $data;
        $this->line = $line;
        $this->col = $col;
    }
    public function toNode()
    {
        return new HTMLPurifier_Node_Comment($this->data, $this->line, $this->col);
    }
}
abstract class HTMLPurifier_Token_Tag extends HTMLPurifier_Token
{
    public $is_tag = true;
    public $name = NULL;
    public $attr = [];
    public function __construct($name, $attr = [], $line = NULL, $col = NULL, $armor = [])
    {
        $this->name = ctype_lower($name) ? $name : strtolower($name);
        foreach ($attr as $key => $value) {
            if (!ctype_lower($key)) {
                $new_key = strtolower($key);
                if (!isset($attr[$new_key])) {
                    $attr[$new_key] = $attr[$key];
                }
                if ($new_key !== $key) {
                    unset($attr[$key]);
                }
            }
        }
        $this->attr = $attr;
        $this->line = $line;
        $this->col = $col;
        $this->armor = $armor;
    }
    public function toNode()
    {
        return new HTMLPurifier_Node_Element($this->name, $this->attr, $this->line, $this->col, $this->armor);
    }
}
class HTMLPurifier_Token_Empty extends HTMLPurifier_Token_Tag
{
    public function toNode()
    {
        $n = parent::toNode();
        $n->empty = true;
        return $n;
    }
}
class HTMLPurifier_Token_End extends HTMLPurifier_Token_Tag
{
    public $start = NULL;
    public function toNode()
    {
        throw new Exception("HTMLPurifier_Token_End->toNode not supported!");
    }
}
class HTMLPurifier_Token_Start extends HTMLPurifier_Token_Tag
{
}
class HTMLPurifier_Token_Text extends HTMLPurifier_Token
{
    public $name = "#PCDATA";
    public $data = NULL;
    public $is_whitespace = NULL;
    public function __construct($data, $line = NULL, $col = NULL)
    {
        $this->data = $data;
        $this->is_whitespace = ctype_space($data);
        $this->line = $line;
        $this->col = $col;
    }
    public function toNode()
    {
        return new HTMLPurifier_Node_Text($this->data, $this->is_whitespace, $this->line, $this->col);
    }
}
class HTMLPurifier_URIFilter_DisableExternal extends HTMLPurifier_URIFilter
{
    public $name = "DisableExternal";
    protected $ourHostParts = false;
    public function prepare($config)
    {
        $our_host = $config->getDefinition("URI")->host;
        if ($our_host !== NULL) {
            $this->ourHostParts = array_reverse(explode(".", $our_host));
        }
    }
    public function filter(&$uri, $config, $context)
    {
        if (is_null($uri->host)) {
            return true;
        }
        if ($this->ourHostParts === false) {
            return false;
        }
        $host_parts = array_reverse(explode(".", $uri->host));
        foreach ($this->ourHostParts as $i => $x) {
            if (!isset($host_parts[$i])) {
                return false;
            }
            if ($host_parts[$i] != $this->ourHostParts[$i]) {
                return false;
            }
        }
        return true;
    }
}
class HTMLPurifier_URIFilter_DisableExternalResources extends HTMLPurifier_URIFilter_DisableExternal
{
    public $name = "DisableExternalResources";
    public function filter(&$uri, $config, $context)
    {
        if (!$context->get("EmbeddedURI", true)) {
            return true;
        }
        return parent::filter($uri, $config, $context);
    }
}
class HTMLPurifier_URIFilter_DisableResources extends HTMLPurifier_URIFilter
{
    public $name = "DisableResources";
    public function filter(&$uri, $config, $context)
    {
        return !$context->get("EmbeddedURI", true);
    }
}
class HTMLPurifier_URIFilter_HostBlacklist extends HTMLPurifier_URIFilter
{
    public $name = "HostBlacklist";
    protected $blacklist = [];
    public function prepare($config)
    {
        $this->blacklist = $config->get("URI.HostBlacklist");
        return true;
    }
    public function filter(&$uri, $config, $context)
    {
        foreach ($this->blacklist as $blacklisted_host_fragment) {
            if (strpos($uri->host, $blacklisted_host_fragment) !== false) {
                return false;
            }
        }
        return true;
    }
}
class HTMLPurifier_URIFilter_MakeAbsolute extends HTMLPurifier_URIFilter
{
    public $name = "MakeAbsolute";
    protected $base = NULL;
    protected $basePathStack = [];
    public function prepare($config)
    {
        $def = $config->getDefinition("URI");
        $this->base = $def->base;
        if (is_null($this->base)) {
            trigger_error("URI.MakeAbsolute is being ignored due to lack of value for URI.Base configuration", 512);
            return false;
        }
        $this->base->fragment = NULL;
        $stack = explode("/", $this->base->path);
        array_pop($stack);
        $stack = $this->_collapseStack($stack);
        $this->basePathStack = $stack;
        return true;
    }
    public function filter(&$uri, $config, $context)
    {
        if (is_null($this->base)) {
            return true;
        }
        if ($uri->path === "" && is_null($uri->scheme) && is_null($uri->host) && is_null($uri->query) && is_null($uri->fragment)) {
            $uri = clone $this->base;
            return true;
        }
        if (!is_null($uri->scheme)) {
            if (!is_null($uri->host)) {
                return true;
            }
            $scheme_obj = $uri->getSchemeObj($config, $context);
            if (!$scheme_obj) {
                return false;
            }
            if (!$scheme_obj->hierarchical) {
                return true;
            }
        }
        if (!is_null($uri->host)) {
            return true;
        }
        if ($uri->path === "") {
            $uri->path = $this->base->path;
        } else {
            if ($uri->path[0] !== "/") {
                $stack = explode("/", $uri->path);
                $new_stack = array_merge($this->basePathStack, $stack);
                if ($new_stack[0] !== "" && !is_null($this->base->host)) {
                    array_unshift($new_stack, "");
                }
                $new_stack = $this->_collapseStack($new_stack);
                $uri->path = implode("/", $new_stack);
            } else {
                $uri->path = implode("/", $this->_collapseStack(explode("/", $uri->path)));
            }
        }
        $uri->scheme = $this->base->scheme;
        if (is_null($uri->userinfo)) {
            $uri->userinfo = $this->base->userinfo;
        }
        if (is_null($uri->host)) {
            $uri->host = $this->base->host;
        }
        if (is_null($uri->port)) {
            $uri->port = $this->base->port;
        }
        return true;
    }
    private function _collapseStack($stack)
    {
        $result = [];
        $is_folder = false;
        for ($i = 0; isset($stack[$i]); $i++) {
            $is_folder = false;
            if (!($stack[$i] == "" && $i && isset($stack[$i + 1]))) {
                if ($stack[$i] == "..") {
                    if (!empty($result)) {
                        $segment = array_pop($result);
                        if ($segment === "" && empty($result)) {
                            $result[] = "";
                        } else {
                            if ($segment === "..") {
                                $result[] = "..";
                            }
                        }
                    } else {
                        $result[] = "..";
                    }
                    $is_folder = true;
                } else {
                    if ($stack[$i] == ".") {
                        $is_folder = true;
                    } else {
                        $result[] = $stack[$i];
                    }
                }
            }
        }
        if ($is_folder) {
            $result[] = "";
        }
        return $result;
    }
}
class HTMLPurifier_URIFilter_Munge extends HTMLPurifier_URIFilter
{
    public $name = "Munge";
    public $post = true;
    private $target = NULL;
    private $parser = NULL;
    private $doEmbed = NULL;
    private $secretKey = NULL;
    protected $replace = [];
    public function prepare($config)
    {
        $this->target = $config->get("URI." . $this->name);
        $this->parser = new HTMLPurifier_URIParser();
        $this->doEmbed = $config->get("URI.MungeResources");
        $this->secretKey = $config->get("URI.MungeSecretKey");
        if ($this->secretKey && !function_exists("hash_hmac")) {
            throw new Exception("Cannot use %URI.MungeSecretKey without hash_hmac support.");
        }
        return true;
    }
    public function filter(&$uri, $config, $context)
    {
        if ($context->get("EmbeddedURI", true) && !$this->doEmbed) {
            return true;
        }
        $scheme_obj = $uri->getSchemeObj($config, $context);
        if (!$scheme_obj) {
            return true;
        }
        if (!$scheme_obj->browsable) {
            return true;
        }
        if ($uri->isBenign($config, $context)) {
            return true;
        }
        $this->makeReplace($uri, $config, $context);
        $this->replace = array_map("rawurlencode", $this->replace);
        $new_uri = strtr($this->target, $this->replace);
        $new_uri = $this->parser->parse($new_uri);
        if ($uri->host === $new_uri->host) {
            return true;
        }
        $uri = $new_uri;
        return true;
    }
    protected function makeReplace($uri, $config, $context)
    {
        $string = $uri->toString();
        $this->replace["%s"] = $string;
        $this->replace["%r"] = $context->get("EmbeddedURI", true);
        $token = $context->get("CurrentToken", true);
        $this->replace["%n"] = $token ? $token->name : NULL;
        $this->replace["%m"] = $context->get("CurrentAttr", true);
        $this->replace["%p"] = $context->get("CurrentCSSProperty", true);
        if ($this->secretKey) {
            $this->replace["%t"] = hash_hmac("sha256", $string, $this->secretKey);
        }
    }
}
class HTMLPurifier_URIFilter_SafeIframe extends HTMLPurifier_URIFilter
{
    public $name = "SafeIframe";
    public $always_load = true;
    protected $regexp = NULL;
    public function prepare($config)
    {
        $this->regexp = $config->get("URI.SafeIframeRegexp");
        return true;
    }
    public function filter(&$uri, $config, $context)
    {
        if (!$config->get("HTML.SafeIframe")) {
            return true;
        }
        if (!$context->get("EmbeddedURI", true)) {
            return true;
        }
        $token = $context->get("CurrentToken", true);
        if (!($token && $token->name == "iframe")) {
            return true;
        }
        if ($this->regexp === NULL) {
            return false;
        }
        return preg_match($this->regexp, $uri->toString());
    }
}
class HTMLPurifier_URIScheme_data extends HTMLPurifier_URIScheme
{
    public $browsable = true;
    public $allowed_types = ["image/jpeg" => true, "image/gif" => true, "image/png" => true];
    public $may_omit_host = true;
    public function doValidate(&$uri, $config, $context)
    {
        $result = explode(",", $uri->path, 2);
        $is_base64 = false;
        $charset = NULL;
        $content_type = NULL;
        if (count($result) == 2) {
            list($metadata, $data) = $result;
            $metas = explode(";", $metadata);
            while (!empty($metas)) {
                $cur = array_shift($metas);
                if ($cur == "base64") {
                    $is_base64 = true;
                } else {
                    if (substr($cur, 0, 8) == "charset=") {
                        if ($charset === NULL) {
                            $charset = substr($cur, 8);
                        }
                    } else {
                        if ($content_type === NULL) {
                            $content_type = $cur;
                        }
                    }
                }
            }
        } else {
            $data = $result[0];
        }
        if ($content_type !== NULL && empty($this->allowed_types[$content_type])) {
            return false;
        }
        if ($charset !== NULL) {
            $charset = NULL;
        }
        $data = rawurldecode($data);
        if ($is_base64) {
            $raw_data = base64_decode($data);
        } else {
            $raw_data = $data;
        }
        if (strlen($raw_data) < 12) {
            return false;
        }
        if (function_exists("sys_get_temp_dir")) {
            $file = tempnam(sys_get_temp_dir(), "");
        } else {
            $file = tempnam("/tmp", "");
        }
        file_put_contents($file, $raw_data);
        if (function_exists("exif_imagetype")) {
            $image_code = exif_imagetype($file);
            unlink($file);
        } else {
            if (function_exists("getimagesize")) {
                set_error_handler([$this, "muteErrorHandler"]);
                $info = getimagesize($file);
                restore_error_handler();
                unlink($file);
                if (!$info) {
                    return false;
                }
                $image_code = $info[2];
            } else {
                trigger_error("could not find exif_imagetype or getimagesize functions", 256);
            }
        }
        $real_content_type = image_type_to_mime_type($image_code);
        if ($real_content_type != $content_type) {
            if (empty($this->allowed_types[$real_content_type])) {
                return false;
            }
            $content_type = $real_content_type;
        }
        $uri->userinfo = NULL;
        $uri->host = NULL;
        $uri->port = NULL;
        $uri->fragment = NULL;
        $uri->query = NULL;
        $uri->path = $content_type . ";base64," . base64_encode($raw_data);
        return true;
    }
    public function muteErrorHandler($errno, $errstr)
    {
    }
}
class HTMLPurifier_URIScheme_file extends HTMLPurifier_URIScheme
{
    public $browsable = false;
    public $may_omit_host = true;
    public function doValidate(&$uri, $config, $context)
    {
        $uri->userinfo = NULL;
        $uri->port = NULL;
        $uri->query = NULL;
        return true;
    }
}
class HTMLPurifier_URIScheme_ftp extends HTMLPurifier_URIScheme
{
    public $default_port = 21;
    public $browsable = true;
    public $hierarchical = true;
    public function doValidate(&$uri, $config, $context)
    {
        $uri->query = NULL;
        $semicolon_pos = strrpos($uri->path, ";");
        if ($semicolon_pos !== false) {
            $type = substr($uri->path, $semicolon_pos + 1);
            $uri->path = substr($uri->path, 0, $semicolon_pos);
            $type_ret = "";
            if (strpos($type, "=") !== false) {
                list($key, $typecode) = explode("=", $type, 2);
                if ($key !== "type") {
                    $uri->path .= "%3B" . $type;
                } else {
                    if ($typecode === "a" || $typecode === "i" || $typecode === "d") {
                        $type_ret = ";type=" . $typecode;
                    }
                }
            } else {
                $uri->path .= "%3B" . $type;
            }
            $uri->path = str_replace(";", "%3B", $uri->path);
            $uri->path .= $type_ret;
        }
        return true;
    }
}
class HTMLPurifier_URIScheme_http extends HTMLPurifier_URIScheme
{
    public $default_port = 80;
    public $browsable = true;
    public $hierarchical = true;
    public function doValidate(&$uri, $config, $context)
    {
        $uri->userinfo = NULL;
        return true;
    }
}
class HTMLPurifier_URIScheme_https extends HTMLPurifier_URIScheme_http
{
    public $default_port = 443;
    public $secure = true;
}
class HTMLPurifier_URIScheme_mailto extends HTMLPurifier_URIScheme
{
    public $browsable = false;
    public $may_omit_host = true;
    public function doValidate(&$uri, $config, $context)
    {
        $uri->userinfo = NULL;
        $uri->host = NULL;
        $uri->port = NULL;
        return true;
    }
}
class HTMLPurifier_URIScheme_news extends HTMLPurifier_URIScheme
{
    public $browsable = false;
    public $may_omit_host = true;
    public function doValidate(&$uri, $config, $context)
    {
        $uri->userinfo = NULL;
        $uri->host = NULL;
        $uri->port = NULL;
        $uri->query = NULL;
        return true;
    }
}
class HTMLPurifier_URIScheme_nntp extends HTMLPurifier_URIScheme
{
    public $default_port = 119;
    public $browsable = false;
    public function doValidate(&$uri, $config, $context)
    {
        $uri->userinfo = NULL;
        $uri->query = NULL;
        return true;
    }
}
class HTMLPurifier_URIScheme_tel extends HTMLPurifier_URIScheme
{
    public $browsable = false;
    public $may_omit_host = true;
    public function doValidate(&$uri, $config, $context)
    {
        $uri->userinfo = NULL;
        $uri->host = NULL;
        $uri->port = NULL;
        $uri->path = preg_replace("/(?!^\\+)[^\\dx]/", "", str_replace("X", "x", $uri->path));
        return true;
    }
}
class HTMLPurifier_VarParser_Flexible extends HTMLPurifier_VarParser
{
    protected function parseImplementation($var, $type, $allow_null)
    {
        if ($allow_null && $var === NULL) {
            return NULL;
        }
        switch ($type) {
            case self::C_MIXED:
            case self::ISTRING:
            case self::C_STRING:
            case self::TEXT:
            case self::ITEXT:
                return $var;
                break;
            case self::C_INT:
                if (is_string($var) && ctype_digit($var)) {
                    $var = (int) $var;
                }
                return $var;
                break;
            case self::C_FLOAT:
                if (is_string($var) && is_numeric($var) || is_int($var)) {
                    $var = (double) $var;
                }
                return $var;
                break;
            case self::C_BOOL:
                if (is_int($var) && ($var === 0 || $var === 1)) {
                    $var = (bool) $var;
                } else {
                    if (is_string($var)) {
                        if ($var == "on" || $var == "true" || $var == "1") {
                            $var = true;
                        } else {
                            if ($var == "off" || $var == "false" || $var == "0") {
                                $var = false;
                            } else {
                                throw new HTMLPurifier_VarParserException("Unrecognized value '" . $var . "' for " . $type);
                            }
                        }
                    }
                }
                return $var;
                break;
            case self::ALIST:
            case self::HASH:
            case self::LOOKUP:
                if (is_string($var)) {
                    if ($var == "") {
                        return [];
                    }
                    if (strpos($var, "\n") === false && strpos($var, "\r") === false) {
                        $var = explode(",", $var);
                    } else {
                        $var = preg_split("/(,|[\\n\\r]+)/", $var);
                    }
                    foreach ($var as $i => $j) {
                        $var[$i] = trim($j);
                    }
                    if ($type === self::HASH) {
                        $nvar = [];
                        foreach ($var as $keypair) {
                            $c = explode(":", $keypair, 2);
                            if (isset($c[1])) {
                                $nvar[trim($c[0])] = trim($c[1]);
                            }
                        }
                        $var = $nvar;
                    }
                }
                if (is_array($var)) {
                    $keys = array_keys($var);
                    if ($keys === array_keys($keys)) {
                        if ($type == self::ALIST) {
                            return $var;
                        }
                        if ($type == self::LOOKUP) {
                            $new = [];
                            foreach ($var as $key) {
                                $new[$key] = true;
                            }
                            return $new;
                        }
                    } else {
                        if ($type === self::ALIST) {
                            trigger_error("Array list did not have consecutive integer indexes", 512);
                            return array_values($var);
                        }
                        if ($type === self::LOOKUP) {
                            foreach ($var as $key => $value) {
                                if ($value !== true) {
                                    trigger_error("Lookup array has non-true value at key '" . $key . "'; " . "maybe your input array was not indexed numerically", 512);
                                }
                                $var[$key] = true;
                            }
                        }
                        return $var;
                    }
                }
                break;
            default:
                $this->errorInconsistent("HTMLPurifier_VarParser_Flexible", $type);
                $this->errorGeneric($var, $type);
        }
    }
}
class HTMLPurifier_VarParser_Native extends HTMLPurifier_VarParser
{
    protected function parseImplementation($var, $type, $allow_null)
    {
        return $this->evalExpression($var);
    }
    protected function evalExpression($expr)
    {
        $var = NULL;
        $result = eval("\$var = " . $expr . ";");
        if ($result === false) {
            throw new HTMLPurifier_VarParserException("Fatal error in evaluated code");
        }
        return $var;
    }
}

?>