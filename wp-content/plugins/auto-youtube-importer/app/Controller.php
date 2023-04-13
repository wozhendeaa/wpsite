<?php

namespace YoutubeImporterSecondLine;

class Controller {

  /**
   * @var Controller;
   */
  protected static $_instance;

  /**
   * @return Controller
   */
  public static function instance(): Controller {
    if( self::$_instance === null )
      self::$_instance = new self();

    return self::$_instance;
  }

  public function setup() {
    load_plugin_textdomain( 'auto-youtube-importer', false, YOUTUBE_IMPORTER_SECONDLINE_LANGUAGE_DIRECTORY );
  }

}