<?php

namespace App\Models\H5;

use Illuminate\Database\Eloquent\Model;

class H5SignRecord extends Model
{
    protected $table = 'h5_sign_record';
    protected $guarded = ['id'];

    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    const STATUS_DESC = [
        self::STATUS_OFF => '锁定',
        self::STATUS_ON  => '正常'
    ];

    const GROUP = [
        1=>'分组1',
        2=>'分组2',
        3=>'分组3',
        4=>'分组4',
        5=>'分组5',
        6=>'分组6',
        7=>'分组7',
        8=>'分组8',
        9=>'分组9',
        10=>'分组10',
        11=>'分组11',
        12=>'分组12',
        13=>'分组13',
        14=>'分组14',
        15=>'分组15',
        16=>'分组16',
        17=>'分组17',
        18=>'分组18',
        19=>'分组19',
        20=>'分组20',
        21=>'分组21',
        22=>'分组22',
        23=>'分组23',
        24=>'分组24',
        25=>'分组25',
        26=>'分组26',
        27=>'分组27',
        28=>'分组28',
        29=>'分组29',
        30=>'分组30',
        31=>'分组31',
        32=>'分组32',
    ];

}
