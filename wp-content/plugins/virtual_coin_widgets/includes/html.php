<?php defined( 'VCW_INDEX' ) or die( '' );

if(!class_exists('VCW_HTML')) {

    class VCW_HTML
    {
        const NO_CLOSING_TAGS = 'area base br col command embed hr img input keygen link meta param source track wbr';

        protected $tag = '';
        protected $attrs = array();
        protected $children = array();
        protected $closing = true;

        static public function sanitizeName($name)
        {
            return str_replace('_','-',$name);
        }

        static public function __callStatic($tag, $arguments)
        {
            $tag_name = self::sanitizeName($tag);
            $closing = !preg_match("/\b($tag_name)\b/", self::NO_CLOSING_TAGS);
            $attrs_or_class = isset($arguments[0]) ? $arguments[0] : null;
            $children = isset($arguments[1]) ? $arguments[1] : null;

            return new VCW_HTML($tag_name, $attrs_or_class, $children, $closing);
        }

        public function __construct($tag, $attrs_or_class = null, $children = null, $closing = true)
        {
            $this->tag = $tag;
            $this->closing = $closing;

            if(is_array($attrs_or_class)){
                $this->attrs = $attrs_or_class;
            }
            else if(is_string($attrs_or_class)){
                $this->attrs['class'] = $attrs_or_class;
            }

            $this->addChildren($children);
        }

        public function __get($name)
        {
            $attr_name = self::sanitizeName($name);

            return array_key_exists($attr_name, $this->attrs) ?
                $this->attrs[$attr_name] : null;
        }

        public function __set($name, $value)
        {
            $attr_name = self::sanitizeName($name);

            if(is_string($value)) {
                $this->attrs[$attr_name] = $value;
            }
        }

        public function __unset($name)
        {
            $attr_name = self::sanitizeName($name);

            unset($this->attrs[$attr_name]);
        }

        public function __isset($name)
        {
            $attr_name = self::sanitizeName($name);

            return isset($this->attrs[$attr_name]);
        }

        public function __toString()
        {
            return $this->html();
        }

        public function addChildren($children)
        {
            if(!$children) return;

            if(is_array($children)) {
                $this->children = array_merge($this->children, $children);
            }
            else {
                $this->children[] = $children;
            }
        }

        public function removeChild($index)
        {
            unset($this->children[$index]);
        }

        public function attributes()
        {
            return $this->attrs;
        }

        public function add($name, $value)
        {
            if(isset($this->$name)) {
                $this->$name .= " $value";
            }
            else {
                $this->$name = $value;
            }
        }

        public function html()
        {
            $tag = $this->tag;
            $closing = $this->closing;
            $attrs_string = '';
            $content = '';

            if($closing) {
                foreach ($this->children as $child) {
                    $content .= is_string($child) ? $child : $child->html();
                }
            }

            foreach ($this->attrs as $attr => $val) {
                $attrs_string .= " $attr=\"$val\"";
            }

            return $closing ?
                "<$tag $attrs_string>$content</$tag>" :
                "<$tag $attrs_string>";
        }

    }

}
