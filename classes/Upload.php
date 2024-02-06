<?php 

    namespace file_upload;
    final class Upload {
        private $target_dir;
        private $file;
        public $target_file;
        public $file_type;
        public $check;
        public $mime;
        public $size;
        public $name;
        public $auto_generated_name;
        public $generated_name;
        public $file_new_name;
        public function __construct($file)
        {
            $this->file = $file;
            $this->size = $file["size"];
            $this->name = htmlspecialchars(basename($file["name"]));
        }
        public function set_folder($x) {
            $this->target_dir = $x;
            $this->target_file = $this->target_dir . basename($this->file["name"]);
            $this->file_type = strtolower(pathinfo($this->target_file, PATHINFO_EXTENSION));
        }
        public function isExisting() {
            if(file_exists($this->target_file)) {
                return true;
            }
            return false;
        }
        public function maxFileSize($size) {
            if($this->size > $size) {
                return true;
            }
            return false;
        }
        public function permit($arr) {
            if(in_array($this->file_type, (array) $arr)) {
                return true;
            }
            return false;
        }
        public function move_to_folder() {
            if(move_uploaded_file($this->file["tmp_name"], $_SERVER['DOCUMENT_ROOT'] . "/" . $this->target_file)){
                return true;
            }
            return false;
        }
        public function rename($name, $type) {
            $type = ($type == null) ? "" : $type;
            $type = strtolower($type);
            if($name == null){
                $new_name = "";
                $letters = [
                    "a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z",
                    "A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z",
                    "0","1","2","3","4","5","6","7","8","9","_"
                ];
                $len = count($letters) - 1;
                for($i = 0; $i < $len; $i++) { 
                    $index = rand(0, $len);
                    $new_name .= $letters[$index]; 
                }
                $new_name .= "_" . time();
                $new_name .= "_" . str_replace(' ', '', str_replace('.', '', microtime()));
                $this->file_new_name = $new_name . "." . $type;
                $this->auto_generated_name = $this->target_dir . $new_name . "." . $type;
                if(rename($_SERVER['DOCUMENT_ROOT'] . "/" . $this->target_file, $_SERVER['DOCUMENT_ROOT'] . "/" . $this->auto_generated_name)){
                    return true;
                }
                return false;
            }
        }

        public function is_image() {
            if(@is_array(getimagesize("".$this->file["tmp_name"].""))){
                $this->check = getimagesize($this->file["tmp_name"]);
                //$this->mime = $this->check["mime"];
                return true;
            }
            return false;
        }
        public function is_video() {
            if(@strstr(mime_content_type($this->file["tmp_name"]), "video/")){
                return true;
            }
            return false;
        }
    }
    

    function move_file($file, $tmp, $dir) {
        if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $file)) {
            $path = str_replace($tmp, $dir, $file);
            if(rename($_SERVER['DOCUMENT_ROOT'] . "/" . $file, $_SERVER['DOCUMENT_ROOT'] . "/" . $path)) {
                return $path;
            } else {
                return false;
            }
        } else { return false; }
    }
?>