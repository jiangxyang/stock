<?php

namespace stock;

use LupeCode\phpTraderNative\Trader;
use think\facade\Db;

class Factor
{
    /*
     * macd,数据越多越接近准确
     * DIF:EMA(CLOSE,SHORT)-EMA(CLOSE,LONG);
     * DEA:EMA(DIF,MID);
     * MACD:(DIF-DEA)*2;
     */
    public static function macd($stock,int $is_all=0,int $short=12,int $long=26,$m=9){
        if(empty($stock)) return false;
        if(is_array($stock)){ // 判断是否为现成的数据,就无需查询数据了
            $close=$stock;
        }else{
            $stock_id=intval($stock);
            $close=Db::name('stock_daily')->where('stock_id',$stock_id)->order('trade_date')->column('close','trade_date');
        }
        if(empty($close) || count($close)<=$short) return false;
        $keys=array_keys($close);
        $rs=Trader::macd($close,$short,$long,$m);
        if(empty($rs)) return $rs;
        if($is_all){
            foreach ($rs as $key=>$item){
                foreach ($item as $k=>$v){
                    if($key=='MACD') $data['dif'][$keys[$k]]=$v;
                    if($key=='MACDSignal') $data['dea'][$keys[$k]]=$v;
                    if($key=='MACDHist') $data['macd'][$keys[$k]]=bcmul($v,2,3); // MACD翻倍*2
                }
            }
        }else{
            $kk=array_key_last($close);
            $data['dif'][$kk]=end($rs['MACD']);
            $data['dea'][$kk]=end($rs['MACDSignal']);
            $data['macd'][$kk]=bcmul(end($rs['MACDHist']),2,3); // MACD翻倍*2;
        }
        unset($close,$keys,$rs); // 销毁数据
        return $data;
    }
    // rsi
    public static function rsi($stock,int $is_all=0,int $rsi1=6,int $rsi2=12,$rsi3=24){
        if(empty($stock)) return false;
        if(is_array($stock)){ // 判断是否为现成的数据,就无需查询数据了
            $close=$stock;
        }else{
            $stock_id=intval($stock);
            $close=Db::name('stock_daily')->where('stock_id',$stock_id)->order('trade_date')->column('close','trade_date');
        }
        if(empty($close) || count($close)<=$rsi1) return false;
        $keys=array_keys($close);
        $rs4=Trader::rsi($close,$rsi1);
        $rs5=Trader::rsi($close,$rsi2);
        $rs6=Trader::rsi($close,$rsi3);
        $data['rsi1']=$data['rsi2']=$data['rsi3']=false;
        if($is_all){
            foreach ($rs6 as $k=>$v)$data['rsi3'][$keys[$k]]=$v;
            foreach ($rs5 as $k=>$v)$data['rsi2'][$keys[$k]]=$v;
            foreach ($rs4 as $k=>$v)$data['rsi1'][$keys[$k]]=$v;
        }else{
            $kk=array_key_last($close);
            if(!empty($rs4)) $data['rsi1'][$kk] = end($rs4);
            if(!empty($rs5)) $data['rsi2'][$kk] = end($rs5);
            if(!empty($rs6)) $data['rsi3'][$kk] = end($rs6);
        }
        unset($close,$keys,$rs4,$rs5,$rs6); // 销毁数据
        return $data;
    }
    // boll
    public static function boll($stock,int $is_all=0,int $n=20){
        if(empty($stock)) return false;
        if(is_array($stock)){ // 判断是否为现成的数据,就无需查询数据了
            $close=$stock;
        }else{
            $stock_id=intval($stock);
            $close=Db::name('stock_daily')->where('stock_id',$stock_id)->order('trade_date')->column('close','trade_date');
        }
        if(empty($close) || count($close)<=$n) return false;
        $rs=Trader::bbands($close,$n);
        if(empty($rs)) return $rs;
        $keys=array_keys($close);
        if($is_all){
            foreach ($rs['UpperBand'] as $k=>$v)$data['upper'][$keys[$k]]=$v;
            foreach ($rs['MiddleBand'] as $k=>$v)$data['boll'][$keys[$k]]=$v;
            foreach ($rs['LowerBand'] as $k=>$v)$data['lower'][$keys[$k]]=$v;
        }else{
            $kk=array_key_last($close);
            $data['upper'][$kk]=end($rs['UpperBand']);
            $data['boll'][$kk]=end($rs['MiddleBand']);
            $data['lower'][$kk]=end($rs['LowerBand']);
        }
        return $data;
    }
    // trix
    public static function trix($stock,int $is_all=0,$n=12,$m=9){
        if(empty($stock)) return false;
        if(is_array($stock)){ // 判断是否为现成的数据,就无需查询数据了
            $close=$stock;
        }else{
            $stock_id=intval($stock);
            $close=Db::name('stock_daily')->where('stock_id',$stock_id)->order('trade_date')->column('close','trade_date');
        }
        if(empty($close) || count($close)<$n) return false;
        $rs=Trader::trix($close,$n);
        $keys=array_keys($close);
        foreach ($rs as $k=>$v)$data['trix'][$keys[$k]]=$v;
        $matrix=Trader::ma($data['trix'],$m);
        if(!empty($matrix)){
            $k_name=array_keys($data['trix']);
            foreach ($matrix as $kk=>$vv)$data['matrix'][$k_name[$kk]]=$vv;
        }else{
            $data['matrix']=$matrix;
        }
        if($is_all==0){
            $kk=array_key_last($close);
            $data['trix']=[$kk=>end($rs)];
            if(!empty($matrix))$data['matrix']=[$kk=>end($matrix)];
        }
        return $data;
    }
    // roc
    public static function roc($stock,int $is_all=0,$n=12,$m=6){
        if(empty($stock)) return false;
        if(is_array($stock)){ // 判断是否为现成的数据,就无需查询数据了
            $close=$stock;
        }else{
            $stock_id=intval($stock);
            $close=Db::name('stock_daily')->where('stock_id',$stock_id)->order('trade_date')->column('close','trade_date');
        }
        if(empty($close) || count($close)<$n) return false;
        $roc=Trader::roc($close,$n);
        $keys=array_keys($close);
        foreach ($roc as $k=>$v)$data['roc'][$keys[$k]]=$v;
        $maroc=Trader::ma($data['roc'],$m);
        if(!empty($maroc)){
            $k_name=array_keys($data['roc']);
            foreach ($maroc as $kk=>$vv)$data['maroc'][$k_name[$kk]]=$vv;
        }else{
            $data['maroc']=$maroc;
        }
        if($is_all==0){
            $kk=array_key_last($close);
            $data['roc']=[$kk=>end($roc)];
            if(!empty($maroc))$data['maroc']=[$kk=>end($maroc)];
        }
        return $data;
    }
    // bias(6,12,24),乖离率=[(当日收盘价-N日平均价)/N日平均价]*100%,目前仅返回单个
    public static function bias($stock,int $is_all=0,$n=6){
        if(empty($stock)) return false;
        if(is_array($stock)){ // 判断是否为现成的数据,就无需查询数据了
            $close=$stock;
        }else{
            $stock_id=intval($stock);
            $close=Db::name('stock_daily')->where('stock_id',$stock_id)->order('trade_date')->column('close','trade_date');
        }
        if(empty($close) || count($close)<$n) return false;
        $ma=Trader::ma($close,$n);
        $index=0;
        $data=null;
        //bcscale(5); // 保留两位小数
        foreach ($close as $k=>$v){
            if($index>=$n-1){
                $data['bias'][$k]=bcmul(bcdiv(bcsub($v,$ma[$index],4),$ma[$index],4),'100',2);
            }
            $index++;
        }
        if($is_all==0){
            $data['bias']=[array_key_last($close)=>end($data['bias'])];
        }
        return $data;
    }
    /*
     * dma
     * DIF:MA(CLOSE,N1)-MA(CLOSE,N2);
     * AMA:MA(DIF,M);
     */
    public static function dma($stock,int $is_all=0,$n1=10,$n2=50,$m=6){
        if(empty($stock)) return false;
        if(is_array($stock)){ // 判断是否为现成的数据,就无需查询数据了
            $close=$stock;
        }else{
            $stock_id=intval($stock);
            $close=Db::name('stock_daily')->where('stock_id',$stock_id)->order('trade_date')->column('close','trade_date');
        }
        if(empty($close) || count($close)<$n2) return false;
        $kk=array_keys($close);
        $ma=Trader::ma($close,$n1);
        $ma1=Trader::ma($close,$n2);
        $data=null;
        foreach ($ma1 as $k=>$v){
            $data['dif'][$kk[$k]]=bcsub($ma[$k],$v,3);
        }
        $ama=Trader::ma($data['dif'],$m);
        if(!empty($ama)){
            $k_name=array_keys($data['dif']);
            foreach ($ama as $kk=>$vv)$data['ama'][$k_name[$kk]]=$vv;
        }else{
            $data['ama']=$ama;
        }
        if($is_all==0){
            $kk=array_key_last($close);
            $data['dif']=[$kk=>end($data['dif'])];
            if(!empty($ama))$data['ama']=[$kk=>end($data['ama'])];
        }
        return $data;
    }
    // kdj,有缺陷,差别太大
    /*public static function kdj(int $stock_id,int $is_all=0,$n=9){
        if(empty($stock_id)) return false;
        $high=Db::name('stock_daily')->where('stock_id',$stock_id)->order('trade_date')->column('high','trade_date');
        if(empty($high) || count($high)<=$n) return false;
        $low=Db::name('stock_daily')->where('stock_id',$stock_id)->order('trade_date')->column('low','trade_date');
        $close=Db::name('stock_daily')->where('stock_id',$stock_id)->order('trade_date')->column('close','trade_date');
        $rs=Trader::stoch($high,$low,$close,$n);
        if(empty($rs)) return $rs;
        $keys=array_keys($close);
        if($is_all){
            foreach ($rs['SlowK'] as $k=>$v)$data['k'][$keys[$k]]=$v;
            foreach ($rs['SlowD'] as $k=>$v){
                $data['d'][$keys[$k]]=$v;
                $data['j'][$keys[$k]]=(3*$rs['SlowK'][$k])-(2*$v);
            }
        }else{
            $kk=array_key_last($close);
            $data['k'][$kk]=end($rs['SlowK']);
            $data['d'][$kk]=end($rs['SlowD']);
            $data['j'][$kk]=(3*$data['k'][$kk])-(2*$data['d'][$kk]);
        }
        return $data;
    }*/
    /**
     *随机指标KDJ
     *N日RSV=（第N日收盘价-N日内最低价）/（N日内最高价-N日内最低价）×100
     *当日K值=2/3*前1日K值+1/3×当日RSV
     *当日D值=2/3*前1日D值+1/3×当日K
     *当日J值=3 ×当日K值-2×当日D值
     */
    public static function kdjNew(int $stock_id,int $is_all=0,$n=9){
        if(empty($stock_id)) return false;
        $list=Db::name('stock_daily')->where('stock_id',$stock_id)->order('trade_date')->column('trade_date,high,low,close');
        if(empty($list) || count($list)<$n) return false;
        $low=$high=[];
        $data=null;
        bcscale(2); // 保留两位小数
        foreach ($list as $key=>$v){
            $low[$key]=$v['low'];
            $high[$key]=$v['high'];
            if($key==($n-1)){
                $min=min($low);
                $max=max($high);
                $rsv=bcdiv(($v['close']-$min)*100,$max-$min);
                $k=bcdiv((2*50),3)+bcdiv($rsv,3);
                $d=bcdiv((2*50),3)+bcdiv($k,3);
                $j=3*$k-2*$d;
                $data['k'][$v['trade_date']]=$k;
                $data['d'][$v['trade_date']]=$d;
                $data['j'][$v['trade_date']]=$j;
            }
            if($key>($n-1)){
                array_shift($low); // 删除第一个元素
                array_shift($high);
                $min=min($low);
                $max=max($high);
                $rsv=bcdiv(($v['close']-$min)*100,$max-$min);
                $before_k=$data['k'][$list[$key-1]['trade_date']];
                $before_d=$data['d'][$list[$key-1]['trade_date']];
                $k=bcdiv(bcmul(2,$before_k),3)+bcdiv($rsv,3);
                $d=bcdiv(bcmul(2,$before_d),3)+bcdiv($k,3);
                $j=3*$k-2*$d;
                $data['k'][$v['trade_date']]=$k;
                $data['d'][$v['trade_date']]=$d;
                $data['j'][$v['trade_date']]=$j;
            }
        }
        // 仅返回当日
        if(!empty($data) && $is_all==0){
            $last_key=array_key_last($data['k']);
            $data['k']=[$last_key=>end($data['k'])];
            $data['d']=[$last_key=>end($data['d'])];
            $data['j']=[$last_key=>end($data['j'])];
        }
        return $data;
    }
    // cci
    public static function cci(int $stock_id,int $is_all=0,$n=14){
        if(empty($stock_id)) return false;
        $list=Db::name('stock_daily')->where('stock_id',$stock_id)->order('trade_date')->column('trade_date,high,low,close');
        if(empty($list) || count($list)<$n) return false;
        $rs=Trader::cci(array_column($list,'high'),array_column($list,'low'),array_column($list,'close'),$n);
        if(empty($rs)) return $rs;
        $keys=array_column($list,'trade_date');
        if($is_all){
            foreach ($rs as $k=>$v)$data[$keys[$k]]=$v;
        }else{
            $kk=end($keys);
            $data[$kk]=end($rs);
        }
        return $data;
    }
    // wr
    public static function wr(int $stock_id,int $is_all=0,$n=10){
        if(empty($stock_id)) return false;
        $list=Db::name('stock_daily')->where('stock_id',$stock_id)->order('trade_date')->column('trade_date,high,low,close');
        if(empty($list) || count($list)<$n) return false;
        $rs=Trader::willr(array_column($list,'high'),array_column($list,'low'),array_column($list,'close'),$n);
        if(empty($rs)) return $rs;
        $keys=array_column($list,'trade_date');
        if($is_all){
            foreach ($rs as $k=>$v)$data[$keys[$k]]=(-$v);
        }else{
            $kk=end($keys);
            $data[$kk]=(-end($rs));
        }
        return $data; // 返回wr1
    }

    /**
     * obv
     * VA:=IF(CLOSE>REF(CLOSE,1),VOL,-VOL);
     * OBV:SUM(IF(CLOSE=REF(CLOSE,1),0,VA),0);
     * MAOBV:MA(OBV,M);
     */
    public static function obv(int $stock_id,int $is_all=0,$n=30){
        if(empty($stock_id)) return false;
        $list=Db::name('stock_daily')->where('stock_id',$stock_id)->order('trade_date')->column('trade_date,close,vol');
        if(empty($list)) return false;
        bcscale(2); // 保留两位小数
        $sum_vol = 0; // 总量和上个收盘价
        $data=null;
        foreach ($list as $key=>$v){
            if($key==0){
                $sum_vol=$v['vol'];
                $data['obv'][$v['trade_date']]=$sum_vol;
                continue;
            }
            $close=$list[$key-1]['close'];
            if($v['close'] > $close) $sum_vol=bcadd($sum_vol,$v['vol']);
            if($v['close'] < $close) $sum_vol=bcsub($sum_vol,$v['vol']);
            $data['obv'][$v['trade_date']]=$sum_vol;
            if($key>=$n-1){
                $rs=Trader::ma($data['obv']);
                $data['maobv'][$v['trade_date']]=end($rs);
            }
        }
        if($is_all){
            return $data;
        }else{
            $last_key=array_key_last($data['obv']);
            $temp['obv']=[$last_key=>end($data['obv'])];
            if(!empty($data['maobv'])) $temp['maobv']=[$last_key=>end($data['maobv'])];
            return $temp;
        }
    }
    /*
     * psy
     * PSY=(N日内上涨天数/N)*100
     * PSYMA:MA(PSY,M);
     */
    public static function psy(int $stock_id,int $is_all=0,$n=12,$m=6){
        if(empty($stock_id)) return false;
        $close=Db::name('stock_daily')->where('stock_id',$stock_id)->order('trade_date')->column('change','trade_date'); // 通过涨跌额判断
        if(empty($close) || count($close)<$n) return false;
        $data=null;
        $flag=$n-1; // 标识索引
        $index=$count=$psy=0; // 索引和上涨日统计以及psy
        $keys=array_keys($close);
        foreach ($close as $k=>$v){
            if($v>0) $count++;
            if ($index>=$flag){
                $kk=$keys[$index-$n] ?? -1; // 如果没对应下标默认为-1,仅会执行一次
                if($kk!=-1 && $close[$kk]>0)$count--; // 找到$n天前,进行判断
                $psy=bcmul(bcdiv($count,$n,5),'100',3);
                $data['psy'][$k]=$psy;
            }
            $index++;
        }
        $ma_psy=Trader::ma($data['psy'],$m);
        if(!empty($ma_psy)){
            $k_name=array_keys($data['psy']); // 提取键值
            foreach ($ma_psy as $k=>$v)$data['psyma'][$k_name[$k]]=$v;
        }else{
            $data['psyma']=$ma_psy;
        }
        if($is_all==0){
            $kk=array_key_last($close);
            $data['psy']=[$kk=>end($data['psy'])];
            if(!empty($ma_psy))$data['psyma']=[$kk=>end($data['psyma'])];
        }
        return $data;
    }
    /*
     * brar
     * AR:SUM(HIGH-OPEN,N)/SUM(OPEN-LOW,N)*100;
     * BR:SUM(MAX(0,HIGH-REF(CLOSE,1)),N)/SUM(MAX(0,REF(CLOSE,1)-LOW),N)*100; // 以东财为准和新浪不太一致
     */
    public static function arbr(int $stock_id,int $is_all=0,$n=26){
        if(empty($stock_id)) return false;
        $list=Db::name('stock_daily')->where('stock_id',$stock_id)->order('trade_date')->column('trade_date,open,high,low,close,pre_close');
        if(empty($list) || count($list)<$n) return false;
        $ho=$ol=$hcy=$cyl=0;
        $flag=$n-1;
        $data=null;
        bcscale(3);
        foreach ($list as $k=>$v){
            $ho=bcadd($ho,bcsub($v['high'],$v['open']));
            $ol=bcadd($ol,bcsub($v['open'],$v['low']));
            if($k>0){ // br第一天不做处理因为昨日收盘价不确定,排除
                $hcy=bcadd($hcy,max(0,bcsub($v['high'],$v['pre_close'])));
                $cyl=bcadd($cyl,max(0,bcsub($v['pre_close'],$v['low'])));
            }
            if($k>=$flag){
                if($k>$flag) {
                    $index=$k-$n;
                    $ho=bcsub($ho,bcsub($list[$index]['high'],$list[$index]['open']));
                    $ol=bcsub($ol,bcsub($list[$index]['open'],$list[$index]['low']));
                    if($index>0) { // br第一天不做处理因为昨日收盘价不确定,排除
                        $hcy=bcsub($hcy,max(0,bcsub($list[$index]['high'],$list[$index]['pre_close'])));
                        $cyl=bcsub($cyl,max(0,bcsub($list[$index]['pre_close'],$list[$index]['low'])));
                    }
                }
                if($ol==0){
                    $ar='0.0';
                }else {
                    $ar = bcmul(bcdiv($ho, $ol), '100', 1);
                }
                if($cyl==0){
                    $br='0.0';
                }else{
                    $br=bcmul(bcdiv($hcy,$cyl),'100',1);
                }
                $data['ar'][$v['trade_date']]=$ar;
                $data['br'][$v['trade_date']]=$br;

            }
        }
        if($is_all==0){
            $kk=array_key_last($data['br']);
            $data['ar']=[$kk=>end($data['ar'])];
            $data['br']=[$kk=>end($data['br'])];
        }
        return $data;
    }
    /*
     * dmi,以东财为准
     * MTR:= SUM(MAX(MAX(HIGH-LOW,ABS(HIGH-REF(CLOSE,1))),ABS(LOW-REF(CLOSE,1))),N);
     * HD := HIGH-REF(HIGH,1);
     * LD := REF(LOW,1)-LOW;
     * DMP:= SUM(IF(HD>0 AND HD>LD,HD,0),N);
     * DMM:= SUM(IF(LD>0 AND LD>HD,LD,0),N);
     * PDI: DMP*100/MTR;
     * MDI: DMM*100/MTR;
     * ADX: MA(ABS(MDI-PDI)/(MDI+PDI)*100,M);
     * ADXR:(ADX+REF(ADX,M))/2;
     */
    public static function dmi(int $stock_id,int $is_all=0,$n=14,$m=6){
        if(empty($stock_id)) return false;
        $list=Db::name('stock_daily')->where('stock_id',$stock_id)->order('trade_date')->column('trade_date,high,low,close,pre_close');
        if(empty($list) || count($list)<$n) return false;
        $sum_mtr=$sum_dmp=$sum_dmm=$index=0;
        $data=$mtr=$adx=$dmp=$dmm=$adxr=null;
        bcscale(5);
        foreach ($list as $k=>$v){
            if($k>0){
                $ref=$k-1;
                $mtr[$k]=max(max(bcsub($v['high'],$v['low']),abs(bcsub($v['high'],$v['pre_close']))),abs(bcsub($v['low'],$v['pre_close'])));
                $sum_mtr=bcadd($sum_mtr,$mtr[$k]);
                $hd=bcsub($v['high'],$list[$ref]['high']);
                $ld=bcsub($list[$ref]['low'],$v['low']);
                if($hd>0 && $hd>$ld) {
                    $dmp[$k]=$hd;
                    $sum_dmp=bcadd($sum_dmp,$hd);
                }else{
                    $dmp[$k]=0;
                }
                if($ld>0 && $ld>$hd){
                    $dmm[$k]=$ld;
                    $sum_dmm=bcadd($sum_dmm,$ld);
                }else{
                    $dmm[$k]=0;
                }
                if($k>=$n){
                    $sum_mtr=bcsub($sum_mtr,$mtr[$k-$n]);
                    $sum_dmp=bcsub($sum_dmp,$dmp[$k-$n]);
                    $sum_dmm=bcsub($sum_dmm,$dmm[$k-$n]);
                }
                $data['pdi'][$v['trade_date']]=$pdi=$data['mdi'][$v['trade_date']]=$mdi=$adx[$v['trade_date']]=0;
                if($sum_mtr!=0){
                    $data['pdi'][$v['trade_date']]=$pdi=bcdiv(bcmul($sum_dmp,'100'),$sum_mtr,3);
                    $data['mdi'][$v['trade_date']]=$mdi=bcdiv(bcmul($sum_dmm,'100'),$sum_mtr,3);
                }
                if(bcadd($pdi,$mdi)!=0) $adx[$v['trade_date']]=bcmul(bcdiv(abs(bcsub($mdi,$pdi)),bcadd($pdi,$mdi)),'100');
            }else{
                $sum_mtr=$mtr[$k]=bcsub($v['high'],$v['low']); // 第一天
                $dmp[$k]=$dmm[$k]=0;
            }
        }
        $madx=Trader::ma($adx,$m); // 真实的adx
        $keys=array_keys($adx);
        $index=0; // 重新开始的索引标识
        foreach ($madx as $k=>$v){
            $adxr[$index]=$k;
            $data['adx'][$keys[$k]]=$v;
            if($index>=$m) $data['adxr'][$keys[$k]]=bcdiv(bcadd($v,$madx[$adxr[$index-$m]]),2,3);
            $index++;
        }
        if(empty($madx)) $data['adx']=[];
        if(empty($data['adxr'])) $data['adxr']=[];
        if($is_all==0){
            $last_key=array_key_last($data['pdi']);
            $data['pdi']=[$last_key=>end($data['pdi'])];
            $data['mdi']=[$last_key=>end($data['mdi'])];
            if(!empty($madx)) {
                $data['adx']=[$last_key=>end($madx)];
            }else{
                $data['adx']=[];
            }
            if(!empty($data['adxr'])){
                $data['adxr']=[$last_key=>end($data['adxr'])];
            }else{
                $data['adxr']=[];
            }
        }
        return $data;
    }
}
