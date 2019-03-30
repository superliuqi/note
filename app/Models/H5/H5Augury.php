<?php

    namespace App\Models\H5;

    use Illuminate\Database\Eloquent\Model;

    class H5Augury extends Model
    {
        protected $table = 'h5_augury';
        protected $guarded = ['id'];

        //星座查询查询时间限制
        const TIMEOUT = 15;

        const DEFAULT_OPTION = 6;

        //星座
        const STAR_ARIES = 0;
        const STAR_TAURUS = 1;
        const STAR_GEMINI = 2;
        const STAR_CACER = 3;
        const STAR_LEO = 4;
        const STAR_VIRGO = 5;
        const STAR_LIBRA = 6;
        const STAR_SCORPIO = 7;
        const STAR_SAGITTARIUS = 8;
        const STAR_CAPRICOM = 9;
        const STAR_AQUARIUS = 10;
        const STAR_PISCES = 11;


        const URANAINANDESU_DESC = [
            self::STAR_ARIES       => '白羊座',
            self::STAR_TAURUS      => '金牛座',
            self::STAR_GEMINI      => '双子座',
            self::STAR_CACER       => '巨蟹座',
            self::STAR_LEO         => '狮子座',
            self::STAR_VIRGO       => '处女座',
            self::STAR_LIBRA       => '天秤座',
            self::STAR_SCORPIO     => '天蝎座',
            self::STAR_SAGITTARIUS => '射手座',
            self::STAR_CAPRICOM    => '摩羯座',
            self::STAR_AQUARIUS    => '水瓶座',
            self::STAR_PISCES      => '双鱼座',
        ];

        const URANAINANDESU_RESULT = [
            self::STAR_ARIES       => '讲义气<br/>怕麻烦<br/>笑点低',
            self::STAR_TAURUS      => '居家必备<br/>美食专家<br/>对拥抱过分眷恋',
            self::STAR_GEMINI      => '脑子灵活<br/>热爱旅行<br/>有艺术细胞',
            self::STAR_CACER       => '脑洞超大<br/>创造力强<br/>骨灰级懒虫',
            self::STAR_LEO         => '霸道与温柔并存<br/>可爱大条不自私<br/>好胜心强',
            self::STAR_VIRGO       => '智商高<br/>细节控<br/>浑身都是才艺',
            self::STAR_LIBRA       => '颜值控<br/>不说话会死星人<br/>桃花运挡不住',
            self::STAR_SCORPIO     => '敢爱敢恨<br/>有点敏感<br/>人群中的小明星',
            self::STAR_SAGITTARIUS => '超级话痨<br/>神经大条<br/>异性缘超好',
            self::STAR_CAPRICOM    => '超强逻辑思维<br/>细节控<br/>注重感觉',
            self::STAR_AQUARIUS    => '小机灵鬼<br/>天生领导范<br/>外星生物',
            self::STAR_PISCES      => '恋爱全凭浪漫<br/>日子全靠演技<br/>生活全是脑洞',
        ];

        const OPTION_ZERO = 0;
        const OPTION_ONE = 1;
        const OPTION_TWO = 2;
        const OPTION_THREE = 3;
        const OPTION_FOUR = 4;
        const OPTION_FIVE = 5;
        const OPTION_SIX = 6;

        const OPTION_DESC = [
            self::OPTION_ZERO  => '痘痘肌',
            self::OPTION_ONE   => '色斑',
            self::OPTION_TWO   => '脸大',
            self::OPTION_THREE => '鼻子塌',
            self::OPTION_FOUR  => '小眼睛',
            self::OPTION_FIVE  => '双下巴',
            self::OPTION_SIX   => '完美脸',
        ];

        const OPTION_RESULT = [
            self::OPTION_ZERO  => '长痘不可怕<br/>不会护理才可怕',
            self::OPTION_ONE   => '遮瑕只是应急<br/>祛斑还需治本',
            self::OPTION_TWO   => '瘦脸如整容<br/>小巧更精致',
            self::OPTION_THREE => '你离女神只差了一个鼻子的距离',
            self::OPTION_FOUR  => '向小眼睛say no<br/>你也可以斩直男',
            self::OPTION_FIVE  => '小圆脸固然可爱<br/>但是双下巴还是影响上镜呦',
            self::OPTION_SIX   => '颜值与气质齐飞，吹爆你的朋友圈',
        ];


    }
