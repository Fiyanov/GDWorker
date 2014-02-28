<?php
class GDWorker
{
    public $extensions = array('jpg', 'jpeg', 'png', 'gif', 'bmp');
    public $output_extension = '';
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
    private $quality = 100;

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

    /**
     * Загружает исходный файл
     * 
     * @param string $file путь к файлу
     * @return GDWorker
     * @throws Exception
     */
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

    /**
     * Сохраняет файл
     * 
     * @param string $file путь к файлу
     * @return GDWorker
     */
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
     * Выводит изображение
     */
    public function show()
    {
        
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

    /**
     * Задать размер по ширине (высота задаётся пропорционально)
     * 
     * @param type $size ширина
     * @return GDWorker
     * @throws Exception
     */
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

    /**
     * Задать размер по высоте (ширина задаётся пропорционально)
     * 
     * @param type $size высота
     * @return GDWorker
     * @throws Exception
     */
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

   /**
    * Устанавливает качество изображения (в процентах)
    * 
    * @param integer $quality качество
    * @return GDWorker
    */    
    public function set_quality($quality)
    {                
        $this->quality = intval($quality);
        
        return $this->get_instance();
    }
    
    /**
     * Возвращает расширение исходного файла (метод load())
     * 
     * @return GDWorker
     */
    public function get_extension()
    {
        return $this->source_extension;
    }

    /**
     * Устанавливает расширение сохраняемого файла (метод save())
     * 
     * @param string $ext расширение сохраняемого файла
     * @return GDWorker
     */
    public function set_extention($ext)
    {
        if (trim($ext) == '') {
            throw new Exception("Не задано расширение файла!");
        }
            
        if (!in_array($ext, $this->extensions)) {
            throw new Exception("Неверно задано расширение файла!");
        }
        
        $this->output_extension = $ext;

        return $this->get_instance();
    }

    //------------------------Сервисные (приватные) методы----------------------
    
    /**
     * Возвращает расширение имени файла
     * 
     * @param string $file имя файла
     * @return string расширение файла
     */
    private function _get_extention($file)
    {
        return pathinfo($file, PATHINFO_EXTENSION);
    }
}