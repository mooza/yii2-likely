<?php

/**
 * @copyright Copyright &copy; Mooza, Mooza.ru, 2016
 * @package yii2-widgets
 * @subpackage yii2-likely
 * @version 1.0.0
 */

namespace mooza\likely;

use Yii;
use yii\base\Widget;
use yii\helpers\Url;

/** The Ilya Birman's Likely widget is a wrapper for the Likely Plugin designed by Ilya Birman.
 * This plugin is a simple and beautiful created for adding social sharing buttons that aren’t shabby.
 *
 * @see https://github.com/ilyabirman/Likely
 * @see https://github.com/mooza/yii2-likely
 * @author Mooza <info@mooza.ru>
 * @since 1.0
 */
class Likely extends Widget
{
    /**
     * @var array
     */
    public $pluginOptions = [];

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->registerAssets();
        $this->initOptions();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (count($this->errors) == 0) {
            echo $this->render('likely', ['options' => $this->options]);
        } else {
            echo "Likely errors: ";
            foreach ($this->errors as $error) {
                echo $error . ";";
            }
        }
    }

    /**
     * Registers the needed assets
     */
    public function registerAssets()
    {
        $view = $this->getView();
        LikelyAsset::register($view);
    }

    /**
     * initOptions() checks PluginOptions property is correct and adds error to errors[] if it is not
     * and adds some initial values if PluginOptions[items] property is empty
     *
     * @return bool
     */
    public function initOptions()
    {
        if (array_key_exists('url', $this->pluginOptions) && $this->pluginOptions['url'] != '') {
            if (filter_var($this->pluginOptions['url'], FILTER_VALIDATE_URL) === FALSE) {
                $this->addError('url');
            } else {
                $this->options['url'] = 'data-url="' . $this->pluginOptions['url'] . '"';
            }
        } else {
            $this->options['url'] = '';
        }

        if (array_key_exists('title', $this->pluginOptions) && $this->pluginOptions['title'] != '') {
            $this->options['title'] = 'data-title="' . $this->pluginOptions['title'] . '"';
        } else {
            $this->options['title'] = '';
        }

        if (array_key_exists('colorClass', $this->pluginOptions) && $this->pluginOptions['colorClass'] != '') {
            if ($this->pluginOptions['colorClass'] == 'light') {
                $this->options['colorClass'] = 'likely-light';
            } else {
                $this->addError('colorClass');
            }
        } else {
            $this->options['colorClass'] = '';
        }

        if (array_key_exists('sizeClass', $this->pluginOptions) && $this->pluginOptions['sizeClass'] != '') {
            if ($this->pluginOptions['sizeClass'] == 'small') {
                $this->options['sizeClass'] = 'likely-small';
            } elseif ($this->pluginOptions['sizeClass'] == 'big') {
                $this->options['sizeClass'] = 'likely-big';
            } else {
                $this->addError('sizeClass');
            }
        } else {
            $this->options['sizeClass'] = '';
        }

        if (array_key_exists('items', $this->pluginOptions)) {
            if (is_array($this->pluginOptions['items']) && count($this->pluginOptions['items']) > 0) {
                foreach ($this->pluginOptions['items'] as $i => $item) {
                    if (is_array($item)) {
                        if (array_key_exists('class', $item) && array_key_exists($item['class'], $this->getSocialNetworksList())) {
                            $this->options['items'][$i]['class'] = $item['class'];
                            if (array_key_exists('title', $item)) {
                                $this->options['items'][$i]['title'] = $item['title'];
                            } else {
                                $this->options['items'][$i]['title'] = $this->getSocialNetworksList()[$item['class']];
                            }

                            if ($item['class'] == 'twitter' && array_key_exists('via', $item) && $item['via'] != '') {
                                $this->options['items'][$i]['via'] = 'data-via="' . $item['via'] . '"';
                            } else {
                                $this->options['items'][$i]['via'] = '';
                            }

                            if ($item['class'] == 'facebook' && array_key_exists('imagePath', $item) && $item['imagePath'] != '') {
                                $this->registerFacebookImageMeta($item['imagePath'], $i);
                            }

                            if ($item['class'] == 'telegram' && array_key_exists('text', $item) && $item['text'] != '') {
                                $this->options['items'][$i]['text'] = 'data-text="' . $item['text'] . '"';
                            } else {
                                $this->options['items'][$i]['text'] = '';
                            }

                            if ($item['class'] == 'pinterest' && $item['media'] != '') {
                                if (file_exists($item['media'])) {
                                    $this->options['items'][$i]['media'] = 'data-media="' . $item['media'] . '"';
                                } else {
                                    $this->addError('items', $i, 'media');
                                }
                            } else {
                                $this->options['items'][$i]['media'] = '';
                            }
                        } else {
                            $this->addError('items', $i, 'class');
                        }
                    } else {
                        $this->addError('items', '');
                    }
                }
            } else {
                $this->addError('items');
            }
        } else {
            $this->options['items'] = [
                [
                    'class' => 'twitter',
                    'title' => 'Tweet',
                    'text' => '',
                    'media' => '',
                    'via' => '',
                    'image' => '',
                ],
                [
                    'class' => 'facebook',
                    'title' => 'Share',
                    'text' => '',
                    'media' => '',
                    'via' => '',
                    'image' => '',
                ],
                [
                    'class' => 'gplus',
                    'title' => 'Share',
                    'text' => '',
                    'media' => '',
                    'via' => '',
                    'image' => '',
                ],
                [
                    'class' => 'vkontakte',
                    'title' => 'Поделиться',
                    'text' => '',
                    'media' => '',
                    'via' => '',
                    'image' => '',
                ],
                [
                    'class' => 'telegram',
                    'title' => 'Send',
                    'text' => '',
                    'media' => '',
                    'via' => '',
                    'image' => '',
                ],
                [
                    'class' => 'odnoklasskini',
                    'title' => 'Класснуть',
                    'text' => '',
                    'media' => '',
                    'via' => '',
                    'image' => '',
                ],
                [
                    'class' => 'pinterest',
                    'title' => 'Pin',
                    'text' => '',
                    'media' => '',
                    'via' => '',
                    'image' => '',
                ],
            ];
        }
        return true;
    }

    protected function generateHtml()
    {

    }

    /**
     * @return array
     */
    protected function getSocialNetworksList()
    {
        return [
            'twitter' => 'Tweet',
            'facebook' => 'Share',
            'gplus' => 'Share',
            'vkontakte' => 'Поделиться',
            'telegram' => 'Send',
            'odnoklassniki' => 'Класснуть',
            'pinterest' => 'Pin',
        ];
    }

    /**
     * @param $path
     * @param $i
     */
    protected function registerFacebookImageMeta($path, $i)
    {

        if (file_exists($path) || (Yii::getAlias('@webroot', false) && file_exists(Yii::getAlias('@webroot') . $path))) {
            $view = $this->getView();
            $view->registerMetaTag(['property' => 'og:image', 'content' => $path]);
        } else {
            $this->addError('items', $i, 'image');
        }
    }

    /**
     * @param $first
     * @param null $second
     * @param null $third
     */
    protected function addError($first, $second = null, $third = null)
    {
        if ($second === null) {
            $this->errors[] = "Incorrect value in pluginOptions['$first'] property";
        } elseif ($third === null) {
            $this->errors[] = "Incorrect value in pluginOptions['$first']['$second'] property";
        } else {
            $this->errors[] = "Incorrect value in pluginOptions['$first']['$second']['$third'] property";
        }
    }
}
