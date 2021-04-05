<?php
namespace Web;

use Web\Url;

class Assets {

    /**
     * Get Css Code
     *
     * @param array $options
     * @return string
     */

    public static function css(array $options) : string{
        $css = "";
        foreach ($options as $option) {
            if (strstr($option, "http")) {
                $css .= '<link rel="stylesheet" href="' . $option . '" type="text/css">' . "\n";
            } else {
                $css .= '<link rel="stylesheet" href="' . Url::templatePath($option) . '" type="text/css">' . "\n";
            }
        }
        return $css;
    }

    /**
     * Get JS Code
     *
     * @param array $options
     * @return string
     */
    public static function script(array $options) : string {
        $script = "";
        foreach ($options as $option) {
            if (strstr($option, "http")) {
                $script .= '<script type="text/javascript" src="' . $option . '"></script>'. "\n";
            } else {
                $script .= '<script type="text/javascript" src="' . Url::templatePath($option) . '"></script>'. "\n";
            }
        }
        return $script;
    }

}
