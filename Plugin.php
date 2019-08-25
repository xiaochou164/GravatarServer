<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * 提供替换Gravatar服务器，支持QQ头像加密地址。
 * 原作者 <a href="http://lt21.me">LT21</a> <a href="https://github.com/LT21/GravatarServer">GravatarServer</a>
 *
 * @package Gravatar Server
 * @author 权那他
 * @version 1.1.1
 * @link https://github.com/Kraity/GravatarServer
 */
class GravatarServer_Plugin implements Typecho_Plugin_Interface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     *
     * @access public
     * @return void
     */
    public static function activate()
    {
        Typecho_Plugin::factory('Widget_Abstract_Comments')->gravatar = array('GravatarServer_Plugin', 'render');
    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     *
     * @static
     * @access public
     * @return void
     */
    public static function deactivate()
    {
    }

    /**
     * 获取插件配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form)
    {
        /** 服务器 **/
        $server = new Typecho_Widget_Helper_Form_Element_Radio('server', array(
            'https://gravatar.loli.net/avatar' => 'Gravatar loli 镜像 ( https://gravatar.loli.net )',
            'https://gravatar.cat.net/avatar' => 'Gravatar cat 镜像 ( https://gravatar.cat.net )',
            'https://cdn.v2ex.com/gravatar' => 'Gravatar v2ex 镜像 ( https://cdn.v2ex.com )',
            'https://dn-qiniu-avatar.qbox.me/avatar/' => 'Gravatar qiniu 镜像 ( https://dn-qiniu-avatar.qbox.me )',
            'https://sdn.geekzu.org/avatar/' => 'Gravatar 极客 镜像 ( https://sdn.geekzu.org )',
            'http://cn.gravatar.com/avatar' => 'Gravatar CN ( http://cn.gravatar.com )',
            'https://secure.gravatar.com/avatar' => 'Gravatar Secure ( https://secure.gravatar.com )'),
            'https://gravatar.loli.net/avatar', _t('选择服务器'), _t('替换Typecho使用的Gravatar头像服务器（ www.gravatar.com ）'));
        $form->addInput($server->multiMode());

        $usePriority = new Typecho_Widget_Helper_Form_Element_Radio('usePriority',
            array(
                'qq' => _t('优先使用QQ头像'),
                'gr' => _t('优先使用Gravatar头像'),
            ),
            'qq', _t('优先原则'), _t('默认启用优先使用QQ头像。邮箱如是腾讯QQ邮箱时且为数字则使用QQ头像加密地址否则使用Gravatar头像.'));
        $form->addInput($usePriority);

        /** 默认头像 **/
        $default = new Typecho_Widget_Helper_Form_Element_Radio('default', array(
            'mm' => '<img src=https://gravatar.loli.net/avatar/926f6ea036f9236ae1ceec566c2760ea?s=32&r=G&forcedefault=1&d=mm height="32" width="32" alt=""/> 神秘人物',
            'blank' => '<img src=https://gravatar.loli.net/avatar/926f6ea036f9236ae1ceec566c2760ea?s=32&r=G&forcedefault=1&d=blank height="32" width="32"  alt=""/> 空白',
            '' => '<img src=https://gravatar.loli.net/avatar/926f6ea036f9236ae1ceec566c2760ea?s=32&r=G&forcedefault=1&d= height="32" width="32"  alt=""/> Gravatar 标志',
            'identicon' => '<img src=https://gravatar.loli.net/avatar/926f6ea036f9236ae1ceec566c2760ea?s=32&r=G&forcedefault=1&d=identicon height="32" width="32"  alt=""/> 抽象图形（自动生成）',
            'wavatar' => '<img src=https://gravatar.loli.net/avatar/926f6ea036f9236ae1ceec566c2760ea?s=32&r=G&forcedefault=1&d=wavatar height="32" width="32"  alt=""/> Wavatar（自动生成）',
            'monsterid' => '<img src=https://gravatar.loli.net/avatar/926f6ea036f9236ae1ceec566c2760ea?s=32&r=G&forcedefault=1&d=monsterid height="32" width="32"  alt=""/> 小怪物（自动生成）'),
            'mm', _t('选择默认头像'), _t('当评论者没有设置Gravatar头像时默认显示该头像'));
        $form->addInput($default->multiMode());
    }

    /**
     * 个人用户的配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form)
    {
    }

    /**
     * 插件实现方法
     *
     * @access public
     * @return void
     * @throws Typecho_Exception
     */
    public static function render($size, $rating, $default, $comments)
    {
        $default = Typecho_Widget::widget('Widget_Options')->plugin('GravatarServer')->default;
        $url = self::gravatarUrl($comments->mail, $size, $rating, $default, $comments->request->isSecure());
        echo '<img class="avatar" src="' . $url . '" alt="' . $comments->author . '" width="' . $size . '" height="' . $size . '" />';
    }

    /**
     * 获取gravatar头像地址
     *
     * @param string $mail
     * @param int $size
     * @param string $rating
     * @param string $default
     * @param bool $isSecure
     * @return string
     * @throws Typecho_Exception
     */
    public static function gravatarUrl($mail, $size, $rating, $default, $isSecure = false)
    {
        $hander = Typecho_Widget::widget('Widget_Options')->plugin('GravatarServer');
        $secure = $isSecure ? 'https://secure.gravatar.com/avatar/' : $hander->server . "/";
        $s = "?s=" . $size;
        $r = "&r=" . $rating;
        $d = "&d=" . $default;
        if (empty($mail)) {
            return $secure . $s . $r . $d;
        } else {
            $reg = "/^\d{5,11}@[qQ][Qq]\.(com)$/";
            if (preg_match($reg, $mail) && $hander->usePriority == "qq") {
                $object = explode("@", $mail)[0];
                $avatar = self::curl_file_get_contents("https://ptlogin2.qq.com/getface?appid=1006102&uin=" . $object . "&imgtype=3");
                $pattern2 = '/pt.setHeader\((.*)\)/is';
                preg_match($pattern2, $avatar, $result2);
                return json_decode($result2[1], true)["$object"];
            } else {
                return $secure . md5($mail) . $s . $r . $d;
            }
        }
    }

    public static function curl_file_get_contents($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);          //抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);         //设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
}
