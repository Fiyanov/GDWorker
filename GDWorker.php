<?php
class GDWorker
{
    public $extension = array('jpg', 'jpeg', 'png', 'gif', 'bmp');
    public $output_file = '';
    private $output_width = 100;
    private $output_height = 100;
    private $output_image = null;
    private $source_extension = '';
    private $source_file = '';
    private $source_width = 0;
    private $source_height = 0;
    private $source_image = null;
    private $mime = '';
    private $bits = 0;

    public function __constructor()
    {
        
    }

    static function &get_instance()
    {
        static $instance;

        if (!isset($instance)) {
            $instance = new GDWorker();
        }

        return $instance;
    }

    public function load($file)
    {
        if (file_exists($file)) {
            $this->source_file = $file;
            $this->source_extension = $this->_get_extention($file);
            $size = getimagesize($file);
            $this->source_width = $size[0];
            $this->source_height = $size[1];
            $this->mime = $size['mime'];
            $this->bits = $size['bits'];

            if ($this->source_extension == 'jpg' or $this->source_extension == 'jpeg') {
                $this->source_image = imagecreatefromjpeg($file);
            }
            if ($this->source_extension == 'png') {
                $this->source_image = imagecreatefrompng($file);
            }

            return $this->get_instance();
        } else {
            throw new Exception("Файл не существует!");
        }
    }

    public function save($file = '')
    {
        $this->output_image = imagecreatetruecolor($this->output_width, $this->output_height);
        imagecopyresized($this->output_image, $this->source_image, 0, 0, 0, 0, $this->output_width, $this->output_height, $this->source_width, $this->source_height);

        if ($file) {
            $ext = $this->_get_extention($file);
            if ($ext == 'jpg' or $ext == 'jpeg') {
                imagejpeg($this->output_image, $file, 100);
            }
            if ($ext == 'png') {
                imagepng($this->output_image, $file, 100);
            }
        } else {
            if ($this->source_extension == 'png') {
                imagepng($this->output_image, 'new.' . $this->source_file, 100);
            }
        }

        return $this->get_instance();
    }

    /**
     * Установить размер изображения
     *
     * Один из двух параметров может быть опущен, тогда его размер будет расчитан пропорционально заданному параметру
     *
     * @param int $width ширина
     * @param int $height высота
     * @return GDWorker
     * @throws Exception Неверный размер
     */
    public function set_size($width = 0, $height = 0)
    {
        if ($width <= 0 and $height <= 0) {
            throw new Exception('Неверно задан размер!');
        }

        if ($width > 0 and $height > 0) {
            $this->output_width = $width;
            $this->output_height = $height;

            return $this->get_instance();
        } else if ($width > 0) {
            return $this->set_size_by_width($width);
        } else if ($height > 0) {
            return $this->set_size_by_height($height);
        }
    }

    public function set_size_by_width($size)
    {
        if ($size <= 0) {
            throw new Exception('Неверно задан размер!');
        }

        $k = $size / $this->source_width;
        $this->output_width = $size;
        $this->output_height = $this->source_height * $k;

        return $this->get_instance();
    }

    public function set_size_by_height($size)
    {
        if ($size <= 0) {
            throw new Exception('Неверно задан размер!');
        }

        $k = $size / $this->source_height;
        $this->output_height = $size;
        $this->output_width = $this->source_width * $k;

        return $this->get_instance();
    }

    public function set_size_max($width = 0, $height = 0)
    {

    }

    public function get_extension()
    {
        return $this->file_extension;
    }

    public function set_extention($ext)
    {
        $this->file_extension = $ext;

        return $this->get_instance();
    }

    //service

    private function _get_extention($file)
    {
        if (preg_match("/[^\\.]+$/i", $file, $matches)) {
            return $matches[0];
        }
    }
}