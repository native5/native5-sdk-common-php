<?php

class Article {
    public  $_id, $title, $description, $pubDate, $link;
    private $images, $videos; 

    public function __construct() {
        $this->images = array();
        $this->videos = array();
    }

    public function addImage($article_image) {
        $this->images[$article_image->getResolution()]=$article_image;
    }

    public function addVideo($video) {
        $this->videos[]=$video;
    }

    public function getImage($res) {
        if(!$res) {
            $res = 'HIGH';
        }
        if(array_key_exists($res, $this->images))
            return $this->images[$res];
        return null;
    }

    public function getVideo() {
        return $this->videos[0];
    }
}

class ArticleImage {
    public $url, $width, $height, $res;

    public function __construct($image) {
        $this->url = $image;
        $this->computeResolution($image);
    }

    public function getResolution() {
        return $this->res;
    }

    private function computeResolution($image) {
        $this->res = "MED";
        if(preg_match("/HiRes/i", $image))
            $this->res = "HIGH";
        elseif (preg_match("/LowRes/i", $image))
            $this->res = "LOW";
        elseif (preg_match("/Thumb/i", $image))
            $this->res = "THUMB";
        elseif (preg_match("/MediumRes/i", $image))
            $this->res = "MED";
    }
}
?>
