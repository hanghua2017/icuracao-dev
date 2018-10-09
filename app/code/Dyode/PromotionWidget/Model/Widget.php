<?php
namespace Dyode\PromotionWidget\Model;

use \Magento\Widget\Model\Widget as BaseWidget;

class Widget
{
    public function beforeGetWidgetDeclaration(BaseWidget $subject, $type, $params = [], $asIs = true)
    {
        // I rather do a check for a specific parameters
        if(key_exists("desktop_image", $params)) {

            $url = $params["desktop_image"];
            if(strpos($url,'/directive/___directive/') !== false) {

                $parts = explode('/', $url);
                $key   = array_search("___directive", $parts);
                if($key !== false) {

                    $url = $parts[$key+1];
                    $url = base64_decode(strtr($url, '-_,', '+/='));

                    $parts = explode('"', $url);
                    $key   = array_search("{{media url=", $parts);
                    $url   = $parts[$key+1];

                    $params["desktop_image"] = $url;
                }
            }
        }
        if(key_exists("mobile_image", $params)) {

            $url = $params["mobile_image"];
            if(strpos($url,'/directive/___directive/') !== false) {

                $parts = explode('/', $url);
                $key   = array_search("___directive", $parts);
                if($key !== false) {

                    $url = $parts[$key+1];
                    $url = base64_decode(strtr($url, '-_,', '+/='));

                    $parts = explode('"', $url);
                    $key   = array_search("{{media url=", $parts);
                    $url   = $parts[$key+1];

                    $params["mobile_image"] = $url;
                }
            }
        }
// double
        if(key_exists("desktop_image_double_first", $params)) {

            $url = $params["desktop_image_double_first"];
            if(strpos($url,'/directive/___directive/') !== false) {

                $parts = explode('/', $url);
                $key   = array_search("___directive", $parts);
                if($key !== false) {

                    $url = $parts[$key+1];
                    $url = base64_decode(strtr($url, '-_,', '+/='));

                    $parts = explode('"', $url);
                    $key   = array_search("{{media url=", $parts);
                    $url   = $parts[$key+1];

                    $params["desktop_image_double_first"] = $url;
                }
            }
        }
        if(key_exists("mobile_image_double_first", $params)) {

            $url = $params["mobile_image_double_first"];
            if(strpos($url,'/directive/___directive/') !== false) {

                $parts = explode('/', $url);
                $key   = array_search("___directive", $parts);
                if($key !== false) {

                    $url = $parts[$key+1];
                    $url = base64_decode(strtr($url, '-_,', '+/='));

                    $parts = explode('"', $url);
                    $key   = array_search("{{media url=", $parts);
                    $url   = $parts[$key+1];

                    $params["mobile_image_double_first"] = $url;
                }
            }
        }

        if(key_exists("desktop_image_double_second", $params)) {

            $url = $params["desktop_image_double_second"];
            if(strpos($url,'/directive/___directive/') !== false) {

                $parts = explode('/', $url);
                $key   = array_search("___directive", $parts);
                if($key !== false) {

                    $url = $parts[$key+1];
                    $url = base64_decode(strtr($url, '-_,', '+/='));

                    $parts = explode('"', $url);
                    $key   = array_search("{{media url=", $parts);
                    $url   = $parts[$key+1];

                    $params["desktop_image_double_second"] = $url;
                }
            }
        }
        if(key_exists("mobile_image_double_second", $params)) {

            $url = $params["mobile_image_double_second"];
            if(strpos($url,'/directive/___directive/') !== false) {

                $parts = explode('/', $url);
                $key   = array_search("___directive", $parts);
                if($key !== false) {

                    $url = $parts[$key+1];
                    $url = base64_decode(strtr($url, '-_,', '+/='));

                    $parts = explode('"', $url);
                    $key   = array_search("{{media url=", $parts);
                    $url   = $parts[$key+1];

                    $params["mobile_image_double_second"] = $url;
                }
            }
        }
//triple

        if(key_exists("desktop_image_triple_first", $params)) {

            $url = $params["desktop_image_triple_first"];
            if(strpos($url,'/directive/___directive/') !== false) {

                $parts = explode('/', $url);
                $key   = array_search("___directive", $parts);
                if($key !== false) {

                    $url = $parts[$key+1];
                    $url = base64_decode(strtr($url, '-_,', '+/='));

                    $parts = explode('"', $url);
                    $key   = array_search("{{media url=", $parts);
                    $url   = $parts[$key+1];

                    $params["desktop_image_triple_first"] = $url;
                }
            }
        }
        if(key_exists("mobile_image_triple_first", $params)) {

            $url = $params["mobile_image_triple_first"];
            if(strpos($url,'/directive/___directive/') !== false) {

                $parts = explode('/', $url);
                $key   = array_search("___directive", $parts);
                if($key !== false) {

                    $url = $parts[$key+1];
                    $url = base64_decode(strtr($url, '-_,', '+/='));

                    $parts = explode('"', $url);
                    $key   = array_search("{{media url=", $parts);
                    $url   = $parts[$key+1];

                    $params["mobile_image_triple_first"] = $url;
                }
            }
        }
        if(key_exists("desktop_image_triple_second", $params)) {

            $url = $params["desktop_image_triple_second"];
            if(strpos($url,'/directive/___directive/') !== false) {

                $parts = explode('/', $url);
                $key   = array_search("___directive", $parts);
                if($key !== false) {

                    $url = $parts[$key+1];
                    $url = base64_decode(strtr($url, '-_,', '+/='));

                    $parts = explode('"', $url);
                    $key   = array_search("{{media url=", $parts);
                    $url   = $parts[$key+1];

                    $params["desktop_image_triple_second"] = $url;
                }
            }
        }
        if(key_exists("mobile_image_triple_second", $params)) {

            $url = $params["mobile_image_triple_second"];
            if(strpos($url,'/directive/___directive/') !== false) {

                $parts = explode('/', $url);
                $key   = array_search("___directive", $parts);
                if($key !== false) {

                    $url = $parts[$key+1];
                    $url = base64_decode(strtr($url, '-_,', '+/='));

                    $parts = explode('"', $url);
                    $key   = array_search("{{media url=", $parts);
                    $url   = $parts[$key+1];

                    $params["mobile_image_triple_second"] = $url;
                }
            }
        }
        if(key_exists("desktop_image_triple_third", $params)) {

            $url = $params["desktop_image_triple_third"];
            if(strpos($url,'/directive/___directive/') !== false) {

                $parts = explode('/', $url);
                $key   = array_search("___directive", $parts);
                if($key !== false) {

                    $url = $parts[$key+1];
                    $url = base64_decode(strtr($url, '-_,', '+/='));

                    $parts = explode('"', $url);
                    $key   = array_search("{{media url=", $parts);
                    $url   = $parts[$key+1];

                    $params["desktop_image_triple_third"] = $url;
                }
            }
        }
        if(key_exists("mobile_image_triple_third", $params)) {

            $url = $params["mobile_image_triple_third"];
            if(strpos($url,'/directive/___directive/') !== false) {

                $parts = explode('/', $url);
                $key   = array_search("___directive", $parts);
                if($key !== false) {

                    $url = $parts[$key+1];
                    $url = base64_decode(strtr($url, '-_,', '+/='));

                    $parts = explode('"', $url);
                    $key   = array_search("{{media url=", $parts);
                    $url   = $parts[$key+1];

                    $params["mobile_image_triple_third"] = $url;
                }
            }
        }
        //quadruple
        if(key_exists("desktop_image_quadruple_first", $params)) {

            $url = $params["desktop_image_quadruple_first"];
            if(strpos($url,'/directive/___directive/') !== false) {

                $parts = explode('/', $url);
                $key   = array_search("___directive", $parts);
                if($key !== false) {

                    $url = $parts[$key+1];
                    $url = base64_decode(strtr($url, '-_,', '+/='));

                    $parts = explode('"', $url);
                    $key   = array_search("{{media url=", $parts);
                    $url   = $parts[$key+1];

                    $params["desktop_image_quadruple_first"] = $url;
                }
            }
        }
        if(key_exists("mobile_image_quadruple_first", $params)) {

            $url = $params["mobile_image_quadruple_first"];
            if(strpos($url,'/directive/___directive/') !== false) {

                $parts = explode('/', $url);
                $key   = array_search("___directive", $parts);
                if($key !== false) {

                    $url = $parts[$key+1];
                    $url = base64_decode(strtr($url, '-_,', '+/='));

                    $parts = explode('"', $url);
                    $key   = array_search("{{media url=", $parts);
                    $url   = $parts[$key+1];

                    $params["mobile_image_quadruple_first"] = $url;
                }
            }
        }
        if(key_exists("desktop_image_quadruple_second", $params)) {

            $url = $params["desktop_image_quadruple_second"];
            if(strpos($url,'/directive/___directive/') !== false) {

                $parts = explode('/', $url);
                $key   = array_search("___directive", $parts);
                if($key !== false) {

                    $url = $parts[$key+1];
                    $url = base64_decode(strtr($url, '-_,', '+/='));

                    $parts = explode('"', $url);
                    $key   = array_search("{{media url=", $parts);
                    $url   = $parts[$key+1];

                    $params["desktop_image_quadruple_second"] = $url;
                }
            }
        }
        if(key_exists("mobile_image_quadruple_second", $params)) {

            $url = $params["mobile_image_quadruple_second"];
            if(strpos($url,'/directive/___directive/') !== false) {

                $parts = explode('/', $url);
                $key   = array_search("___directive", $parts);
                if($key !== false) {

                    $url = $parts[$key+1];
                    $url = base64_decode(strtr($url, '-_,', '+/='));

                    $parts = explode('"', $url);
                    $key   = array_search("{{media url=", $parts);
                    $url   = $parts[$key+1];

                    $params["mobile_image_quadruple_second"] = $url;
                }
            }
        }
        if(key_exists("desktop_image_quadruple_third", $params)) {

            $url = $params["desktop_image_quadruple_third"];
            if(strpos($url,'/directive/___directive/') !== false) {

                $parts = explode('/', $url);
                $key   = array_search("___directive", $parts);
                if($key !== false) {

                    $url = $parts[$key+1];
                    $url = base64_decode(strtr($url, '-_,', '+/='));

                    $parts = explode('"', $url);
                    $key   = array_search("{{media url=", $parts);
                    $url   = $parts[$key+1];

                    $params["desktop_image_quadruple_third"] = $url;
                }
            }
        }
        if(key_exists("mobile_image_quadruple_third", $params)) {

            $url = $params["mobile_image_quadruple_third"];
            if(strpos($url,'/directive/___directive/') !== false) {

                $parts = explode('/', $url);
                $key   = array_search("___directive", $parts);
                if($key !== false) {

                    $url = $parts[$key+1];
                    $url = base64_decode(strtr($url, '-_,', '+/='));

                    $parts = explode('"', $url);
                    $key   = array_search("{{media url=", $parts);
                    $url   = $parts[$key+1];

                    $params["mobile_image_quadruple_third"] = $url;
                }
            }
        }
        if(key_exists("desktop_image_quadruple_fourth", $params)) {

            $url = $params["desktop_image_quadruple_fourth"];
            if(strpos($url,'/directive/___directive/') !== false) {

                $parts = explode('/', $url);
                $key   = array_search("___directive", $parts);
                if($key !== false) {

                    $url = $parts[$key+1];
                    $url = base64_decode(strtr($url, '-_,', '+/='));

                    $parts = explode('"', $url);
                    $key   = array_search("{{media url=", $parts);
                    $url   = $parts[$key+1];

                    $params["desktop_image_quadruple_fourth"] = $url;
                }
            }
        }
        if(key_exists("mobile_image_quadruple_fourth", $params)) {

            $url = $params["mobile_image_quadruple_fourth"];
            if(strpos($url,'/directive/___directive/') !== false) {

                $parts = explode('/', $url);
                $key   = array_search("___directive", $parts);
                if($key !== false) {

                    $url = $parts[$key+1];
                    $url = base64_decode(strtr($url, '-_,', '+/='));

                    $parts = explode('"', $url);
                    $key   = array_search("{{media url=", $parts);
                    $url   = $parts[$key+1];

                    $params["mobile_image_quadruple_fourth"] = $url;
                }
            }
        }
        return array($type, $params, $asIs);
    }
}
