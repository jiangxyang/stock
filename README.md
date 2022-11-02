# 股票技术指标计算,仅供参考学习
stock,stock calc,php stock,macd,rsi,boll,trix,roc,bias,dma,kdj,cci,wr,obv,psy,arbr,dmi
#### 基于 thinkphp 6.0.13LTS(也可以用别的框架或者原生,后续改写Factor.php文件引入即可) 和 PHP Trader类的函数
[thinkphp](https://github.com/top-think/framework)
[phpTraderNative](https://github.com/LupeCode/phpTraderNative)
```
// 1
composer create-project topthink/think tpstock
// 2
composer require lupecode/php-trader-native
// 安装php-trader-native后找到vendor/lupecode/php-trader-native/source/Trader.php文件第238行,改写$newOutReal[$index + $offset] = $inDouble;如下
$newOutReal[$index + $offset] = number_format($inDouble,3,'.',''); // 四舍五入保留3位,自行决定是否修改
```
# stock主表数据示例
![image](https://user-images.githubusercontent.com/30286467/199390581-db32635b-0ddc-4146-b2cf-14f8e55bada0.png)
# stock_daily表数据示例,每开市日及时更新即可
![image](https://user-images.githubusercontent.com/30286467/199390802-bf44ea0e-0c09-499e-8123-14fd82be93d9.png)
